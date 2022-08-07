<?php
class Users extends Cornerstone\Controller
{

  /**
   * Class Constructor
   */
  public function __construct($registry)
  {
    // Load the controller constructor
    parent::__construct($registry);

    // Check if user is allowed admin access
    checkAdminAccess();

    // Define the page type
    $this->pageType = 'user';

    // Load the user model
    $this->userModel = $this->load->model('cornerstone/user', 'admin');

    // Set Breadcrumbs
    $this->data['breadcrumbs'] = array(
      array(
        'text' => 'Dashboard',
        'href' => get_site_url('admin')
      ),
      array(
        'text' => 'Users',
        'href' => get_site_url('admin/users')
      )
    );
  }

  /**
   * All Users Page
   */
  public function index(...$params)
  {

    // Check user is allowed to view this
    if (!$this->role->canDo('view_user')) {
      // Redirect user with error
      flashMsg('admin_dashboard', '<strong>Error</strong> Sorry, you are not allowed to view users. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin');
      exit;
    }

    // Output user index page

    // Init list page
    $this->init_list_page('admin/users', ...$params);

    ############################
    #########  FILTERING #######
    ############################

    // Check for sort
    $this->get_sort_order(
      array('username' => 'user_login', 'name' => 'users_name', 'email' => 'user_email', 'role' => 'role_name', 'login' => 'login_dtm'), // Allowed sort fields
      array('sort' => 'user_login', 'order' => 'ASC'), // Fallback
      ...$params
    );

    // Output users list

    // Get users
    if ($dataList = $this->userModel->listUsers($this->params)) {

      // Count how many items match the results
      $this->params['count'] = TRUE;
      $this->data['totalResults'] = $this->userModel->listUsers($this->params);

      // Set data list output
      $dataListOut = '';

      // Set the pagination
      $pagination = new Cornerstone\Pagination;
      $pagination->set_props(
        (int) $this->data['totalResults'],
        (int) $this->params['page'],
        (int) $this->params['limit']
      );
      $this->data['pagination'] = $pagination->render();

      // Loop through data
      foreach ($dataList->results as $data) {

        // Set edit options
        $loginOutput = ($this->role->canDo('edit_user')) ? '<a href="' . get_site_url('admin/users/edit/' . $data->user_id) . '" data-tippy-content="Edit ' . htmlspecialchars_decode($data->user_login) . '" class="has-hover-item">' . htmlspecialchars_decode($data->user_login) . ' <span class="hover-item"><i class="fas fa-edit"></i></span></a>' : htmlspecialchars_decode($data->user_login);

        // Set role fallback
        $outputRole = (!empty($data->role_name)) ? $data->role_name : 'n/a';

        // Get last login
        if (!empty($data->login_dtm)) {

          // Set timestamp
          $userLastLogin = new \Cornerstone\DateTime($_SESSION['_cs']['user'], $data->login_dtm);
          $userLastLogin = $userLastLogin->format('D, jS M Y h:ia');
        } else { // Unable to find last login. Set default value.

          // Set default value if group not found
          $userLastLogin = 'n/a';
        }

        // Set row output
        $dataListOut .= '<tr>
              <td>' . $loginOutput . '</td>
              <td><strong class="item--title">' . $data->users_name . '</strong></td>
              <td><a href="mailto:' . $data->user_email . '">' . $data->user_email . '</a></td>
              <td>' . $outputRole . '</td>
              <td>' . $userLastLogin . '</td>
            </tr>';
      }

      // Output User List
      $this->data['dataListOut'] = $dataListOut;
    } else { // No results. Output message.

      // Set the pagination
      $this->data['pagination'] = '';

      // Set dataListOut to message
      if ($this->data['showFilter']) {
        // No filter results. Output message.
        $outputMessage = $this->data['no_filter_results_msg'];
      } else if (!empty($this->data['search'])) {
        // No search results. Output message
        $outputMessage = $this->data['no_results_msg'];
      } else {
        // No results. Output default.
        $this->data['noData'] = TRUE;
        $outputMessage = '';
      }

      // Set output
      $this->data['dataListOut'] = '<tr><td colspan="5" id="no-results">' . $outputMessage . '</td></tr>';
    }

    // Trim filter data
    $this->data['filterData'] = rtrim($this->data['filterData'], ', ');

    // Load view
    $this->load->view('users/index', $this->data, 'admin');
    exit;
  }

  /**
   * Set Add / Edit Page Data
   *
   * @param int $setRoleID `[optional]` Assigned role. Defaults to "0" (Guest)
   */
  protected function createAEElements(
    int $setRoleID = 0
  ) {

    if (empty($this->data['id'])) {
      // Page Type
      $this->data['page_type'] = 'add';
      // Action URL
      $this->data['action_url'] = get_site_url('admin/users/add/');
      // H1
      $this->data['page_title'] = 'Add ' . ucfirst($this->pageType);
      // Set Breadcrumbs
      $this->data['breadcrumbs'][] = array(
        'text' => $this->data['page_title'],
        'href' => $this->data['action_url']
      );
      // Instructions
      $this->data['instructions'] = 'Enter the details for the new ' . $this->pageType . ' to add them. A password will be automatically generated and emailed to them.';
    } else {
      // Page Type
      $this->data['page_type'] = 'edit';
      // Action URL
      $this->data['action_url'] = get_site_url('admin/users/edit/' . $this->data['id']);
      // H1
      $this->data['page_title'] = 'Edit ' . ucfirst($this->data['login']);
      // Set Breadcrumbs
      $this->data['breadcrumbs'][] = array(
        'text' => $this->data['page_title'],
        'href' => $this->data['action_url']
      );
      // Instructions
      $this->data['instructions'] = 'Enter the new details for ' . ucfirst($this->data['login']) . ' to update them.';
      // Check for user meta data
      if ($userMetaData = $this->userModel->getUserMeta($this->data['id'])) {
        // Set data
        foreach ($userMetaData as $metaItem) {
          $this->data[$metaItem->umeta_key] = $metaItem->umeta_value;
        }
      }
    }
    // Cancel Button
    $this->data['cancel_btn'] = get_site_url('admin/users/');

    #########################
    ####    TIMEZONES    ####
    #########################

    // Load files required.
    require_once(DIR_HELPERS . 'fn.timezone.php'); // Load the timezone helper

    // Timezone options
    $this->data['timezone_options'] = "";
    $timezones = timezones_list();
    foreach ($timezones as $zone) {
      // Check if selected
      $selectedTZ = ((!empty($this->data['timezone']) && timezones_filter($this->data['timezone']) === $zone[1]) || !isset($this->data['timezone']) && $zone[1] === "Europe/London") ? ' selected' : '';
      $this->data['timezone_options'] .= '<option value="' . htmlspecialchars($zone[1]) . '"' . $selectedTZ . '>' . htmlspecialchars($zone[0]) . '</option>';
    }

    #####################
    ####    ROLES    ####
    #####################

    // Init list options
    $this->data['role_options'] = '<option value="0">Guest</option>';
    // Get list of options for assigning
    if ($returnedRolesData = $this->userModel->listUserRoles()) {

      // Loop through options
      foreach ($returnedRolesData as $roleData) {

        // Set selected if chosen
        $selected = (!empty($setRoleID) && $setRoleID == $roleData->role_id) ? ' selected' : '';

        // Set to output
        $this->data['role_options'] .= '<option value="' . $roleData->role_id . '"' . $selected . '>' . $roleData->role_name . '</option>';
      }
    } else {
      $this->data['role_options'] = '<option value="0" disabled>No roles available</option>';
    }

    ####################
    ####    META    ####
    ####################
    // SET ANY CUSTOMER USER META LOADING HERE
    // THIS WILL BE RESET ON UPDATE SO MAKE SURE YOU HAVE THIS BACKED UP
  }

  /**
   * Add Page
   */
  public function add()
  {

    // Check user is allowed to view this
    if (!$this->role->canDo('add_user')) {
      // Redirect user with error
      flashMsg('admin_users', '<strong>Error</strong> Sorry, you are not allowed to add users. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/users/');
      exit;
    }

    // Process "add"

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "save") {
      // Get information submitted and validate
      try {
        // Get login data
        $this->data['login'] = trim($_POST['login']);
        if (empty($this->data['login'])) {
          // Data is not set. Return error.
          $this->data['err']['login'] = 'Please enter a login (username) value';
          throw new Exception("The login (username) is missing. Please enter a login (username).");
        } else if (strlen($this->data['login']) < 3) {
          // Data is less than 3 characters. Return error.
          $this->data['err']['login'] = 'Please enter at least 3 characters';
          throw new Exception("The login (username) is less than 3 characters. Please enter at least 3 characters.");
        }

        // Get display_name data
        $this->data['display_name'] = trim($_POST['display_name']);
        if (empty($this->data['display_name'])) {
          // Data is not set. Return error.
          $this->data['err']['display_name'] = 'Please enter a display name';
          throw new Exception("The display name is missing. Please enter your display name.");
        } else  if (strlen($this->data['display_name']) < 2) {
          // Data is less than 3 characters. Return error.
          $this->data['err']['display_name'] = 'Please enter at least 2 characters';
          throw new Exception("The display name is less than 2 characters. Please enter at least 2 characters.");
        }

        // Get first_name data
        $this->data['first_name'] = trim($_POST['first_name']);
        if (empty($this->data['first_name'])) {
          // Data is not set. Return error.
          $this->data['err']['first_name'] = 'Please enter a first name';
          throw new Exception("The first name is missing. Please enter a first name.");
        } else  if (strlen($this->data['first_name']) < 2) {
          // Data is less than 3 characters. Return error.
          $this->data['err']['first_name'] = 'Please enter at least 2 characters';
          throw new Exception("The first name is less than 2 characters. Please enter at least 2 characters.");
        }

        // Get last_name data
        $this->data['last_name'] = trim($_POST['last_name']);
        if (empty($this->data['last_name'])) {
          // Data is not set. Return error.
          $this->data['err']['last_name'] = 'Please enter a last name';
          throw new Exception("The last name is missing. Please enter a last name.");
        } else  if (strlen($this->data['last_name']) < 2) {
          // Data is less than 3 characters. Return error.
          $this->data['err']['last_name'] = 'Please enter at least 2 characters';
          throw new Exception("The last name is less than 2 characters. Please enter at least 2 characters.");
        }

        // Get email data
        $this->data['email'] = trim($_POST['email']);
        if (empty($this->data['email'])) {
          // Data is not set. Return error.
          $this->data['err']['email'] = 'Please enter an email address';
          throw new Exception("The email address is missing. Please enter an email address.");
        } else if (!empty($this->data['email']) && !filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
          // Data isn't a valid email address. Return error.
          $this->data['err']['email'] = 'Please enter a valid email address';
          throw new Exception("The email address failed validation. Please enter a valid email address.");
        }

        // Get timezone data
        $this->data['timezone'] = trim($_POST['timezone']);
        if (empty($this->data['timezone'])) {
          // Data is not set. Return error.
          $this->data['err']['timezone'] = 'Please select a timezone.';
          throw new Exception("The timezone is missing. Please select a timezone.");
        }

        // Get role_id data
        $this->data['role_id'] = trim($_POST['role_id']);
        if (empty($this->data['role_id'])) {
          // Data is not set. Return error.
          $this->data['err']['role_id'] = 'Please select a role';
          throw new Exception("The role is missing. Please select a role.");
        }

        // Get send auth_rqd flag
        $this->data['auth_rqd'] = (isset($_POST['auth_rqd']) && !empty($_POST['auth_rqd'])) ? TRUE : FALSE;

        // Get meta data if any set
        $this->data['meta'] = (isset($_POST['meta']) && !empty($_POST['meta'])) ? $_POST['meta'] : null;
      } catch (Exception $e) {

        // Log error if any and set flash message
        error_log($e->getMessage(), 0);
        flashMsg('users_user', '<strong>Error</strong> There was an error adding the user - ' . $e->getMessage() . '. Please try again', 'warning');
      }

      // If valid, add new
      if (empty($this->data['err'])) {
        // Validated

        // Generate password
        $userPassword = get_crypto_key(8);

        // Generate password key
        $pwdKey = get_crypto_key();

        // Hash password with key
        $password_encrypted = password_hash($userPassword . $pwdKey, PASSWORD_DEFAULT);

        // Add new user
        if ($userID = $this->userModel->addUser(
          $this->data['login'],
          $this->data['display_name'],
          $password_encrypted,
          $pwdKey,
          $this->data['email'],
          $this->data['first_name'],
          $this->data['last_name'],
          (int) $this->data['role_id'],
          (int) $this->data['auth_rqd'],
          $this->data['timezone']
        )) {
          // User Added

          // Add user meta if any
          if (!empty($this->data['meta']))
            $this->addMeta((int) $userID, $this->data['meta']);

          // Set the user ID
          if ($this->userModel->setUserID((int) $userID)) {

            // Load the password model
            $this->passwordModel = $this->load->model('accpassword', 'account');

            // Set the user ID
            $this->passwordModel->setUserID((int) $userID);

            // Set password reset request
            $resetObject = $this->passwordModel->setPasswordReset();

            // Check reset worked
            if ($resetObject !== FALSE) {

              // Create the options
              $actionURL = get_site_url('admin/users/new-password/' . urlencode($resetObject->selector) . '/' . urlencode($resetObject->token));
              $loginURL = get_site_url('admin/login');
              $resetExpireDtm = new \DateTime($resetObject->expires);
              $supportEmail = $this->optn->get('site_from_email');

              // Load SendMail Class
              $sendMail = new Cornerstone\SendMail();

              // Set the HTML message from the template
              if ($message = $sendMail->createEmailTemplate(
                'user-welcome.html',
                array(
                  'action_url' => $actionURL,
                  'login_url' => $loginURL,
                  'user_name' => $this->data['login'],
                  'first_name' => $this->data['first_name'],
                  'email' => $this->data['email'],
                  'expire_dtm' => $resetExpireDtm->format('g:ia \o\n l, jS M Y T'),
                  'support_email' => $supportEmail
                )
              )) {

                // Set the plaintext message from the template
                if ($plainEmail = $sendMail->createEmailTemplate(
                  'user-welcome.txt',
                  array(
                    'action_url' => $actionURL,
                    'login_url' => $loginURL,
                    'user_name' => $this->data['login'],
                    'first_name' => $this->data['first_name'],
                    'email' => $this->data['email'],
                    'expire_dtm' => $resetExpireDtm->format('g:ia \o\n l, jS M Y T'),
                    'support_email' => $supportEmail
                  )
                )) {

                  // Send user their authorization email
                  $emailSubject = "Welcome to the " . $this->optn->get('site_name') . " website!";
                  if ($sendMail->sendPHPMail(
                    $this->optn->get('site_from_email'),
                    $this->optn->get('site_from_email'),
                    $this->optn->get('site_name'),
                    $this->data['email'],
                    $this->data['first_name'] . ' ' . $this->data['last_name'],
                    $emailSubject,
                    $message,
                    $plainEmail,
                    ''
                  )) { // Email sent

                    // Add to the success message
                    $addToSuccess = " with their welcome email sent";
                  } else { // Unable to send email.
                    // Add to the success message
                    $addToSuccess = " but their welcome email failed to send,";
                  }
                } else { // Unable to set plain text message.
                  // Add to the success message
                  $addToSuccess = " but their welcome email failed to send,";
                }
              } else { // Unable to set HTML message.
                // Add to the success message
                $addToSuccess = " but their welcome email failed to send,";
              }
            } else { // Password reset request failed.
              // Add to the success message
              $addToSuccess = " but their password failed to set.";
            }
          } else { // Password reset failed.
            // Add to the success message
            $addToSuccess = " but their password failed to set.";
          }

          // Set success message
          flashMsg('admin_users', '<strong>Success</strong> ' . $this->data['display_name'] . ' was added successfully' . $addToSuccess . '.');

          // Redirect to index
          redirectTo('admin/users/');
          exit;
        } // Unable to add. Return error.

        // Set error message
        flashMsg('users_user', '<strong>Error</strong> There was an error adding the user. Please try again.', 'warning');
      }

      // If it's made it this far there were errors. Load add view with submitted data

      // Set Add Data
      $this->createAEElements((int) $this->data['role_id']);
    } else { // Page wasn't posted. Load view.

      // Set Add Data
      $this->createAEElements();
    }

    // Load add view
    $this->load->view('users/user', $this->data, 'admin');
    exit;
  }

  /**
   * Add Meta from Add page
   */
  private function addMeta($userID, $meta_data)
  {
  }

  /**
   * Edit Page
   */
  public function edit(...$params)
  {

    // Check user is allowed to view this
    if (!$this->role->canDo('edit_user')) {
      // Redirect user with error
      flashMsg('admin_users', '<strong>Error</strong> Sorry, you are not allowed to edit users. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/users/');
      exit;
    }

    // Process "edit"

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "save") {

      // Get information submitted and validate
      try {

        // Get ID
        $this->data['id'] = htmlspecialchars(stripslashes(trim($_POST['id'])));

        // Get login data
        $this->data['login'] = trim($_POST['login']);
        if (empty($this->data['login'])) {
          // Data is not set. Return error.
          $this->data['err']['login'] = 'Please enter a login (username) value';
          throw new Exception("The login (username) is missing. Please enter a login (username).");
        } else if (strlen($this->data['login']) < 3) {
          // Data is less than 3 characters. Return error.
          $this->data['err']['login'] = 'Please enter at least 3 characters';
          throw new Exception("The login (username) is less than 3 characters. Please enter at least 3 characters.");
        }

        // Get display_name data
        $this->data['display_name'] = trim($_POST['display_name']);
        if (empty($this->data['display_name'])) {
          // Data is not set. Return error.
          $this->data['err']['display_name'] = 'Please enter a display name';
          throw new Exception("The display name is missing. Please enter your display name.");
        } else  if (strlen($this->data['display_name']) < 2) {
          // Data is less than 3 characters. Return error.
          $this->data['err']['display_name'] = 'Please enter at least 2 characters';
          throw new Exception("The display name is less than 2 characters. Please enter at least 2 characters.");
        }

        // Get first_name data
        $this->data['first_name'] = trim($_POST['first_name']);
        if (empty($this->data['first_name'])) {
          // Data is not set. Return error.
          $this->data['err']['first_name'] = 'Please enter a first name';
          throw new Exception("The first name is missing. Please enter a first name.");
        } else  if (strlen($this->data['first_name']) < 2) {
          // Data is less than 3 characters. Return error.
          $this->data['err']['first_name'] = 'Please enter at least 2 characters';
          throw new Exception("The first name is less than 2 characters. Please enter at least 2 characters.");
        }

        // Get last_name data
        $this->data['last_name'] = trim($_POST['last_name']);
        if (empty($this->data['last_name'])) {
          // Data is not set. Return error.
          $this->data['err']['last_name'] = 'Please enter a last name';
          throw new Exception("The last name is missing. Please enter a last name.");
        } else  if (strlen($this->data['last_name']) < 2) {
          // Data is less than 3 characters. Return error.
          $this->data['err']['last_name'] = 'Please enter at least 2 characters';
          throw new Exception("The last name is less than 2 characters. Please enter at least 2 characters.");
        }

        // Get email data
        $this->data['email'] = trim($_POST['email']);
        if (empty($this->data['email'])) {
          // Data is not set. Return error.
          $this->data['err']['email'] = 'Please enter an email address';
          throw new Exception("The email address is missing. Please enter an email address.");
        } else if (!empty($this->data['email']) && !filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
          // Data isn't a valid email address. Return error.
          $this->data['err']['email'] = 'Please enter a valid email address';
          throw new Exception("The email address failed validation. Please enter a valid email address.");
        }

        // Get timezone data
        $this->data['timezone'] = trim($_POST['timezone']);
        if (empty($this->data['timezone'])) {
          // Data is not set. Return error.
          $this->data['err']['timezone'] = 'Please select a timezone.';
          throw new Exception("The timezone is missing. Please select a timezone.");
        }

        // Get role_id data
        $this->data['role_id'] = trim($_POST['role_id']);
        if (empty($this->data['role_id'])) {
          // Data is not set. Return error.
          $this->data['err']['role_id'] = 'Please select a role';
          throw new Exception("The role is missing. Please select a role.");
        }

        // Get auth_rqd flag
        $this->data['auth_rqd'] = (isset($_POST['auth_rqd']) && !empty($_POST['auth_rqd'])) ? TRUE : FALSE;

        // Get status flag
        $this->data['status'] = (isset($_POST['status']) && !empty($_POST['status'])) ? TRUE : FALSE;

        // Get meta data if any set
        $this->data['meta'] = (isset($_POST['meta']) && !empty($_POST['meta'])) ? $_POST['meta'] : null;
      } catch (Exception $e) {

        // Log error if any and set flash message
        error_log($e->getMessage(), 0);
        flashMsg('users_user', '<strong>Error</strong> There was an error editing the user - ' . $e->getMessage() . '. Please try again', 'warning');
      }

      // If valid, add new
      if (empty($this->data['err'])) {
        // Validated

        // Update user
        if ($this->userModel->updateUser(
          (int) $this->data['id'],
          $this->data['login'],
          $this->data['display_name'],
          $this->data['email'],
          $this->data['first_name'],
          $this->data['last_name'],
          (int) $this->data['role_id'],
          (int) $this->data['auth_rqd'],
          $this->data['timezone'],
          (int) $this->data['status']
        )) {
          // User updated

          // Update user meta if any
          if (!empty($this->data['meta']))
            $this->editMeta((int) $this->data['id'], $this->data['meta']);

          // Update session preferences
          if ((int) $this->data['id'] === (int)$_SESSION['_cs']['user']['uid']) {
            // Set user email address
            $_SESSION['_cs']['user']['email'] = $this->data['email'];
            // Set user display name
            $_SESSION['_cs']['user']['name'] = ucwords($this->data['display_name']);
            // Set user language
            // $_SESSION['_cs']['user']['lang'] = $this->data['language'];
            // Set user timezone
            $_SESSION['_cs']['user']['timezone'] = new \DateTimeZone($this->data['timezone']);
            // Set user date format
            // $_SESSION['_cs']['user']['date_format'] = $this->data['date_format'];
          }

          // Set success message
          flashMsg('admin_users', '<strong>Success</strong> ' . $this->data['display_name'] . ' was updated successfully.');

          // Redirect to index
          redirectTo('admin/users/');
          exit;
        } // Unable to add contact. Return error.

        // Set error message
        flashMsg('users_user', '<strong>Error</strong> There was an error updating the user. Please try again.', 'warning');
      }

      // If it's made it this far there were errors. Load add view with submitted data

      // Set edit Data
      $this->createAEElements(
        (int) $this->data['role_id']
      );
    } else { // Page wasn't posted. Load view.

      // Check ID
      if (!empty($params) && is_numeric($params[0])) {

        // Get Information
        if ($userData = $this->userModel->getUser($params[0])) {

          // Set data
          foreach ($userData as $key => $data) {
            $this->data[str_replace(array('user_'), '', $key)] = $data;
          }

          // Set Edit Data
          $this->createAEElements(
            (int) $this->data['role_id']
          );

          // Load view
          $this->load->view('users/user', $this->data, 'admin');
          exit;
        } // Error getting the data. Redirect to index list.

      } // No ID present. Redirect to index list.

      // Redirect user
      redirectTo('admin/brands');
      exit;
    }
  }


  /**
   * Edit Meta from Edit page
   */
  private function editMeta($userID, $meta_data)
  {
  }

  /**
   * Set Permissions Page Data
   */
  protected function createPermsElements()
  {
    // Page Type
    $this->data['page_type'] = 'edit';
    // Action URL
    $this->data['action_url'] = get_site_url('admin/users/permissions/' . $this->data['id']);
    // H1
    $this->data['page_title'] = 'Edit ' . ucfirst($this->data['login']) . ' Permissions';
    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => ucfirst($this->data['login']),
      'href' => get_site_url('admin/users/edit/' . $this->data['id'])
    );
    $this->data['breadcrumbs'][] = array(
      'text' => 'Edit Permissions',
      'href' => $this->data['action_url']
    );
    // Instructions
    $this->data['instructions'] = 'Select the permissions to allow / deny for ' . ucfirst($this->data['login']) . ' to update them over their assigned role.';

    #################################
    ####    USER PERMISSIONS    ####
    ################################
    // Get list of users permissions
    $permissionsList = $this->userModel->listUserPermissions((int) $this->data['id']);

    ###########################
    ####    PERMISSIONS    ####
    ###########################

    // Load the role model
    $this->roleModel = $this->load->model('cornerstone/userrole', 'admin');

    // Init permission options
    $this->data['viewOptions'] = '';
    $this->data['addOptions'] = '';
    $this->data['editOptions'] = '';
    $this->data['deleteOptions'] = '';
    $this->data['otherOptions'] = '';
    $this->data['permissionsAllowedList'] = '<option></option>';
    $this->data['permissionsDisallowedList'] = '<option></option>';
    // Get list of permissions for assigning
    if ($permissionsData = $this->roleModel->listPermissions()) {

      // Loop through data and create options
      foreach ($permissionsData as $permission) {

        // Check if allowed
        $allowedSelected = (isset($permissionsList->user_permissions[$permission->rp_id]) && $permissionsList->user_permissions[$permission->rp_id] === 1) ? ' selected' : '';
        $disallowedSelected = (isset($permissionsList->user_permissions[$permission->rp_id]) && $permissionsList->user_permissions[$permission->rp_id] === 2) ? ' selected' : '';

        // Add to lists
        $this->data['permissionsAllowedList'] .= '<option value="' . $permission->rp_id . '"' . $allowedSelected . '>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . ' (' . $permission->rp_key . ')</option>';
        $this->data['permissionsDisallowedList'] .=
          '<option value="' . $permission->rp_id . '"' . $disallowedSelected . '>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . ' (' . $permission->rp_key . ')</option>';

        // Check if in role permissions
        if (isset($permissionsList->role_permissions[$permission->rp_id])) {
          // Get type
          $permissionType = explode('_', $permission->rp_key);
          // Check which option to add to
          switch ($permissionType[0]) {
            case 'view':
              $this->data['viewOptions'] .= '<p>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</p>';
              break;
            case 'add':
              $this->data['addOptions'] .= '<p>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</p>';
              break;
            case 'edit':
              $this->data['editOptions'] .= '<p>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</p>';
              break;
            case 'delete':
            case 'archive':
              $this->data['deleteOptions'] .= '<p>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</p>';
              break;

            default:
              $this->data['otherOptions'] .= '<p>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</p>';
              break;
          }
        }
      }
    } else {
      // Set blank options
      $this->data['no_perm_options'] = '<p class="cs-body2">There are currently no permissions available to assign to this user.</p>';
    }

    // Set fallbacks
    $this->data['viewOptions'] = (!empty($this->data['viewOptions'])) ? $this->data['viewOptions'] : '<p class="cs-caption">No view permissions set</p>';
    $this->data['addOptions'] = (!empty($this->data['addOptions'])) ? $this->data['addOptions'] : '<p class="cs-caption">No add permissions set</p>';
    $this->data['editOptions'] = (!empty($this->data['editOptions'])) ? $this->data['editOptions'] : '<p class="cs-caption">No edit permissions set</p>';
    $this->data['deleteOptions'] = (!empty($this->data['deleteOptions'])) ? $this->data['deleteOptions'] : '<p class="cs-caption">No delete permissions set</p>';
    $this->data['otherOptions'] = (!empty($this->data['otherOptions'])) ? $this->data['otherOptions'] : '<p class="cs-caption">No other permissions set</p>';
  }

  /**
   * Permissions Page
   */
  public function permissions(...$params)
  {

    // Check user is allowed to view this
    if (!$this->role->canDo('edit_user')) {
      // Redirect user with error
      flashMsg('admin_users', '<strong>Error</strong> Sorry, you are not allowed to edit users. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/users/');
      exit;
    }

    // Process "edit"

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "save") {

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Check ID
      if (!empty($params) && is_numeric($params[0]) && $params[0] == $_POST['id']) {

        // Get information submitted and validate
        if (isset($_POST['id'])) {

          // Try validating
          try {

            // Get ID
            $this->data['id'] = htmlspecialchars(stripslashes(trim($_POST['id'])));

            // Get allowed data
            $this->data['allowed'] = (isset($_POST['allowed'])) ? $_POST['allowed'] : array();

            // Get disallowed data
            $this->data['disallowed'] = (isset($_POST['disallowed'])) ? $_POST['disallowed'] : array();
          } catch (Exception $e) {

            // Log error if any and set flash message
            error_log($e->getMessage(), 0);
            flashMsg('users_permissions', '<strong>Error</strong> There was an error editing the users permissions. Please try again', 'warning');
          }
        } else { // Required data not set. Set Errors.

          $this->data['err']['id'] = 'ID is missing';
        }

        // If valid, add new
        if (empty($this->data['err'])) {
          // Validated

          // Get existing role permissions
          $existingData = array();
          if ($userPermissionsList = $this->userModel->listUserPermissions((int) $this->data['id'])->user_permissions) {
            // Loop through already assigned permissions
            foreach ($userPermissionsList as $permID => $permStatus) {
              // Check if permission exists in posted data
              if (!in_array($permID, $this->data['allowed']) && !in_array($permID, $this->data['disallowed'])) {
                // Permission doesn't exist. Delete link
                $this->userModel->deleteOPLink((int) $this->data['id'], (int) $permID);
              } else {
                // Add to existing data array
                $existingData[$permID] = (int) $permStatus;
              }
            }
          }

          // Loop through submitted data
          foreach ($this->data['allowed'] as $allowedPermID) {
            // Check if already assigned
            if (empty($existingData) || !array_key_exists($allowedPermID, $existingData)) {
              // Permissions isn't already allowed. Allow it
              $this->userModel->addOPLink(
                (int) $this->data['id'],
                (int) $allowedPermID,
                1
              );
            } else if (!empty($existingData) && array_key_exists($allowedPermID, $existingData) && (int) $existingData[$allowedPermID] !== 1) {
              // Permissions already exists. Update it
              $this->userModel->updateOPLink(
                (int) $this->data['id'],
                (int) $allowedPermID,
                1
              );
            }
          }
          foreach ($this->data['disallowed'] as $disallowedPermID) {
            // Check if already assigned
            if (
              empty($existingData) || !array_key_exists($disallowedPermID, $existingData)
            ) {
              // Permissions isn't already disallowed. Disallow it
              $this->userModel->addOPLink(
                (int) $this->data['id'],
                (int) $disallowedPermID,
                2
              );
            } else if (!empty($existingData) && array_key_exists($disallowedPermID, $existingData) && (int) $existingData[$disallowedPermID] !== 2) {
              // Permissions already exists. Update it
              $this->userModel->updateOPLink(
                (int) $this->data['id'],
                (int) $disallowedPermID,
                2
              );
            }
          }

          // Set success message
          flashMsg('admin_users', '<strong>Success</strong>The user permissions were saved successfully.');

          // Redirect to index
          redirectTo('admin/users/');
          exit;
        }

        // Set error message
        flashMsg('users_permissions', '<strong>Error</strong> There was an error updating the user permissions. Please try again.', 'warning');

        // If it's made it this far there were errors. Load view with submitted data

        // Set Permissions Data
        $this->createPermsElements();

        // Load view
        $this->load->view('users/permissions', $this->data, 'admin');
        exit;
      } else { // Error with the ID. Redirect to list view with error.

        // Set Error
        flashMsg('admin_users', '<strong>Error</strong> There was an error saving the user permissions. Please try again', 'warning');
        redirectTo('admin/users/');
        exit;
      }
    } else { // Page wasn't posted. Load view.

      // Check ID
      if (!empty($params) && is_numeric($params[0])) {

        // Get Information
        if ($userData = $this->userModel->getUser($params[0])) {

          // Set data
          foreach ($userData as $key => $data) {
            $this->data[str_replace(array('user_'), '', $key)] = $data;
          }

          // Set Permissions Data
          $this->createPermsElements();

          // Load view
          $this->load->view('users/permissions', $this->data, 'admin');
          exit;
        } // Error getting the data. Redirect to index list.

      } // No ID present. Redirect to index list.

      // Redirect user
      redirectTo('admin/brands');
      exit;
    }
  }
}
