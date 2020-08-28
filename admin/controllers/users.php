<?php
class Users extends Controller
{

  /**
   * Class Constructor
   */
  public function __construct($registry)
  {
    // Load the controller constructor
    parent::__construct($registry);

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
   * Forgot Password Page
   */
  public function forgotpassword()
  {

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "request-password") {

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Get information submitted and validate
      if (isset($_POST['email'])) {

        // Try validating
        try {

          // Get email
          $this->data['email'] = htmlspecialchars(stripslashes(trim($_POST['email'])));
          if (empty($this->data['email'])) {
            // Data not set, return error
            $this->data['err']['email'] = 'Please enter your email address';
          } else if (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            // Data isn't a valid email address, return error
            $this->data['err']['email'] = 'Please enter a valid email address';
          }
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          flashMsg('admin_forgot_pwd', '<strong>Error</strong> There was an error with your email address. Please try again', 'warning');
        }
      } else { // If data not set, set errors

        $this->data['err']['email'] = 'Please enter your email address';
      }

      // Check if any errors are set
      if (empty($this->data['err'])) {
        // Validated

        // Check if user email exists
        if ($this->userModel->findUserByEmail($this->data['email'])) {
          // User exists

          // check if able to set user ID from email address
          if ($this->userModel->setUserIDFromEmail($this->data['email'])) {
            // User ID set

            // Set password reset request
            $resetObject = $this->userModel->setPasswordReset();

            // Check reset worked
            if ($resetObject != FALSE) {

              // Create the options
              $actionURL = get_site_url('admin/users/new-password/' . urlencode($resetObject->selector) . '/' . urlencode($resetObject->token));
              $resetExpireDtm = new \DateTime($resetObject->expires);
              if (!empty($resetObject->user_agent) || $resetObject->user_agent != '') {
                $browserInfo = 'For security, this relates to a reset request from a device using ' . $resetObject->user_agent . '. ';
              } else {
                $browserInfo = '';
              }

              // Load SendMail Class
              $sendMail = new \SendMail();

              // Set the HTML message from the template
              if ($message = $sendMail->createEmailTemplate(
                'password-reset.html',
                array(
                  'name' => $resetObject->user_name,
                  'action_url' => $actionURL,
                  'expire_dtm' => $resetExpireDtm->format('g:ia \o\n l, jS M Y T'),
                  'browser_security' => $browserInfo
                )
              )) {

                // Set the plaintext message from the template
                if ($plainEmail = $sendMail->createEmailTemplate(
                  'password-reset.txt',
                  array(
                    'name' => $resetObject->user_name,
                    'action_url' => $actionURL,
                    'expire_dtm' => $resetExpireDtm->format('g:ia \o\n l, jS M Y T'),
                    'browser_security' => $browserInfo
                  )
                )) {

                  // Send user their authorization email
                  $emailSubject = ' Reset your ' . $this->optn->get('site_name') . ' password';
                  if ($sendMail->sendPHPMail(
                    $this->optn->get('site_from_email'),
                    $this->optn->get('site_from_email'),
                    $this->optn->get('site_name'),
                    $this->data['email'],
                    '',
                    $emailSubject,
                    $message,
                    $plainEmail,
                    ''
                  )) { // Email sent

                    // Redirect user to login page
                    flashMsg('admin_login', 'Your password reset email has been sent. Please check your email account for the link to reset your password', 'success');
                    redirectTo('admin/login');
                    exit;
                  } // Unable to send email. Continue on to error

                } // Unable to set plain text message. Continue on to error

              } // Unable to set HTML message. Continue on to error

            } // Password reset request failed. Continue to error.

          } // Unable to set user ID from email. Continue to error.

        } else { // No user exists, send reset error email

          // Create the options
          $actionURL = get_site_url('admin/users/forgot-password');
          // Get browser info if browser tracking enabled
          if ($this->optn->get('browser_tracking')) {
            $browser = new \WhichBrowser\Parser(getallheaders());
            // Set browser "User Agent"
            $browserInfo = " from a device using " . $browser->toString();
          } else {
            $browserInfo = "";
          }

          // Load SendMail Class
          $sendMail = new \SendMail();

          // Set the HTML message from the template
          if ($message = $sendMail->createEmailTemplate(
            'password-reset-help.html',
            array(
              'email_address' => $this->data['email'],
              'action_url' => $actionURL,
              'browser_security' => $browserInfo
            )
          )) {

            // Set the plaintext message from the template
            if ($plainEmail = $sendMail->createEmailTemplate(
              'password-reset-help.txt',
              array(
                'email_address' => $this->data['email'],
                'action_url' => $actionURL,
                'browser_security' => $browserInfo
              )
            )) {

              // Send user their authorization email
              $emailSubject = $this->optn->get('site_name') . ' Password Reset Help';
              if ($sendMail->sendPHPMail(
                $this->optn->get('site_from_email'),
                $this->optn->get('site_from_email'),
                $this->optn->get('site_name'),
                $this->data['email'],
                '',
                $emailSubject,
                $message,
                $plainEmail,
                ''
              )) { // Email sent

                // Redirect user to login page
                flashMsg('admin_login', 'Your password reset email has been sent. Please check your email account for the link to reset your password', 'success');
                redirectTo('admin/login');
                exit;
              } // Unable to send email. Continue on to error

            } // Unable to set plain text message. Continue on to error

          } // Unable to set HTML message. Continue on to error

        }

        // Set error
        flashMsg('admin_forgot_pwd', '<strong>Error</strong> There was an error processing your password reset request. Please try again.', 'warning');
      } // Errors were set. Continue on to view.

      // Load view with data
      $this->load->view('users/forgot-password', $this->data, 'admin');
      exit;
    } else { // else load the forgot password page

      $this->load->view('users/forgot-password', '', 'admin');
      exit;
    }
  }

  /**
   * New Password Page
   */
  public function newpassword(...$params)
  {

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "save-password") {

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Check selector and token
      if (isset($_POST['selector']) || isset($_POST['token'])) {

        // Try validating
        try {

          // Get selector and token
          $this->data['selector'] = $_POST['selector'];
          $this->data['token'] = $_POST['token'];

          // Check selector and token aren't empty
          if (empty($this->data['selector']) || empty($this->data['token'])) {
            // Password selector or token is empty

            // Set error
            flashMsg('admin_forgot_pwd', '<strong>Error</strong> Sorry, your reset request seems to have glitched. Please request a new password reset link.', 'warning');

            // Redirect to forgot password page
            redirectTo('admin/users/forgot-password');
            exit;
          }

          // Check if password reset is valid
          if (!$this->userModel->checkPasswordReset($this->data['selector'])) {
            // Password reset is not valid

            // Set error
            flashMsg('admin_forgot_pwd', '<strong>Error</strong> Sorry, your reset request seems to have expired. Please request a new password reset link.', 'warning');

            // Redirect to forgot password page
            redirectTo('admin/users/forgot-password');
            exit;
          }
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          flashMsg('admin_new_pwd', '<strong>Error</strong> There was an error checking your request. Please try again', 'warning');
        }
      } else { // If data not set, set error and redirect to forgot password

        // Set error
        flashMsg('admin_forgot_pwd', '<strong>Error</strong> Sorry, There was an issue processing your request. Please request a new password reset link.', 'danger');

        // Redirect to forgot password page
        redirectTo('admin/users/forgot-password');
        exit;
      }

      // Get information submitted and validate
      if (isset($_POST['password']) || isset($_POST['confirm-password'])) {

        // Try validating
        try {

          // Get password
          $this->data['password'] = $_POST['password'];

          // Validate password strength
          $uppercase = preg_match('@[A-Z]@', $this->data['password']);
          $lowercase = preg_match('@[a-z]@', $this->data['password']);
          $number    = preg_match('@[0-9]@', $this->data['password']);
          $specialChars = preg_match('@[^\w]@', $this->data['password']);

          // Check validation is all ok
          if (empty($this->data['password']) || !$uppercase || !$lowercase || !$number || !$specialChars || strlen($this->data['password']) < 8 || strlen($this->data['password']) > 128) {

            // If password not set or doesn't match the requirements, return error
            $this->data['err']['password'] = 'Please enter a valid password';
          }

          // Get confirm password
          $this->data['confirm-password'] = $_POST['confirm-password'];

          // Check password isn't empty
          if (empty($this->data['confirm-password'])) {

            // It's empty. Return error
            $this->data['err']['confirm-password'] = 'Please confirm your new password';
          } else if ($this->data['password'] != $this->data['confirm-password']) { // Check if passwords match

            // Passwords don't match. Return error
            $this->data['err']['confirm-password'] = 'Both passwords must match';
          }
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          flashMsg('admin_new_pwd', '<strong>Error</strong> There was an error checking your new password. Please try again', 'warning');
        }
      } else { // If data not set, set errors

        $this->data['err']['password'] = 'Please enter your new password';
        $this->data['err']['confirm-password'] = 'Please confirm your new password';
      }

      // Check if any errors are set
      if (empty($this->data['err'])) {
        // Validated

        // Check token validates
        if ($this->userModel->checkResetToken($this->data['selector'], $this->data['token'])) {
          // Token is valid

          // Generate new key
          $newKey = get_crypto_key();

          // Hash password with new key
          $password_encrypted = password_hash($this->data['password'] . $newKey, PASSWORD_DEFAULT);

          // Update users password with new key
          if ($this->userModel->setNewPassword($password_encrypted, $newKey)) {

            // Invalidate all existing sessions and cookies for the user
            $this->userModel->invalidateLogins();

            // Mark password reset as successful
            $this->userModel->markPasswordReset($this->data['selector']);

            // Create the options
            $actionURL = 'admin/login';
            $currentDtm = new \DateTime();
            // Get browser info if browser tracking enabled
            if ($this->optn->get('browser_tracking')) {
              $browser = new \WhichBrowser\Parser(getallheaders());
              // Set browser "User Agent"
              $browserInfo = "For security, you password was reset on a device using " . $browser->toString() . ". ";
            } else {
              $browserInfo = "";
            }

            // Get user email address
            if ($userEmail = $this->userModel->getUserEmail()) {

              // Load SendMail Class
              $sendMail = new \SendMail();

              // Set the HTML message from the template
              if ($message = $sendMail->createEmailTemplate(
                'new-password.html',
                array(
                  'action_url' => get_site_url($actionURL),
                  'reset_dtm' => $currentDtm->format('g:ia \o\n l, jS M Y T'),
                  'browser_security' => $browserInfo
                )
              )) {

                // Set the plaintext message from the template
                if ($plainEmail = $sendMail->createEmailTemplate(
                  'new-password.txt',
                  array(
                    'action_url' => get_site_url($actionURL),
                    'reset_dtm' => $currentDtm->format('g:ia \o\n l, jS M Y T'),
                    'browser_security' => $browserInfo
                  )
                )) {

                  // Send user email confirming password change
                  $emailSubject = 'You\'ve successfully reset your ' . $this->optn->get('site_name') . 'password';
                  if ($sendMail->sendPHPMail(
                    $this->optn->get('site_from_email'),
                    $this->optn->get('site_from_email'),
                    $this->optn->get('site_name'),
                    $userEmail,
                    '',
                    $emailSubject,
                    $message,
                    $plainEmail,
                    ''
                  )) { // Email sent

                    // Redirect to login page with success
                    flashMsg('admin_login', 'Your password has been changed!');
                    redirectTo($actionURL);
                    exit;
                  } // Unable to send email. Continue on to error

                } // Unable to set plain text message. Continue on to error

              } // Unable to set HTML message. Continue on to error

            } // Unable to get user email. Continue on to error

          } // Failed to update password. Continue on to error

        } else { // Token invalid. Redirect to get new link

          // Set error
          flashMsg('admin_forgot_pwd', '<strong>Error</strong> Sorry, There was an issue with your token. Please request a new password reset link.', 'danger');

          // Redirect to forgot password page
          redirectTo('admin/users/forgot-password');
          exit;
        }

        // Set error
        flashMsg('admin_new_pwd', '<strong>Error</strong> - Sorry, there was an issue changing your password. Please try again.', 'warning');
      } // Errors were set. Continue on to view.

      // Load view with data
      $this->load->view('users/new-password', $this->data, 'admin');
      exit;
    } else { // else load the new password page

      // Get selector
      $this->data['selector'] = (!empty($params[0]) && is_string($params[0]) && strlen($params[0]) == 34) ? $params[0] : '';

      // Get token
      $this->data['token'] = (!empty($params[1]) && is_string($params[1]) && strlen($params[1]) == 16) ? $params[1] : '';

      // Check if either selector or token are empty
      if (empty($this->data['selector']) || empty($this->data['token'])) {
        // Data is empty.

        // Set error
        flashMsg('admin_forgot_pwd', '<strong>Error</strong> Sorry, something went wrong.<br>Please request a new password reset link.', 'danger');

        // Redirect to forgot password page
        redirectTo('admin/users/forgot-password');
        exit;
      } else if (!$this->userModel->checkPasswordReset($this->data['selector'])) { // Check if password reset is valid
        // Password reset is not valid

        // Set error
        flashMsg('admin_forgot_pwd', '<strong>Error</strong> Sorry, that request seems to have expired. Please request a new password reset link.', 'danger');

        // Redirect to forgot password page
        redirectTo('admin/users/forgot-password');
        exit;
      } else { // Data is set. Load view

        // Load view
        $this->load->view('users/new-password', $this->data, 'admin');
        exit;
      }
    }
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

    // Check if user is logged in
    if (!userPageProtect()) {

      // If user is not logged in, show the login page
      flashMsg('admin_login', 'You need to log in first.', 'warning');
      $this->load->view('common/login', '', 'admin');
      exit;
    } else { // Output user index page

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
        $pagination = new Pagination;
        $pagination->set_props((int) $this->data['totalResults'], (int) $this->params['page'], (int) $this->params['limit']);
        $this->data['pagination'] = $pagination->render();

        // Loop through data
        foreach ($dataList->results as $data) {

          // Set role fallback
          $outputRole = (!empty($data->role_name)) ? $data->role_name : 'n/a';

          // Get last login
          if (!empty($data->login_dtm)) {

            // Set timestamp
            $userLastLogin = new \DateTime($data->login_dtm);
            $userLastLogin = $userLastLogin->format('D, jS M Y h:ia');
          } else { // Unable to find last login. Set default value.

            // Set default value if group not found
            $userLastLogin = 'n/a';
          }

          // Set row output
          $dataListOut .= '<tr>
              <td>' . $data->user_login . '</td>
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
  }

  /**
   * Set Add Page Data
   *
   * @param int $setRoleID `[optional]` Assigned role. Defaults to "2".
   */
  protected function setAddData(int $setRoleID = 2)
  {

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
    // Cancel Button
    $this->data['cancel_btn'] = get_site_url('admin/users/');

    #####################
    ####    ROLES    ####
    #####################

    // Init list options
    $this->data['role_options'] = '';
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
      $this->data['role_options'] .= '<option value="0" disabled>No roles available</option>';
    }
  }

  /**
   * Add Page
   */
  public function add()
  {
    // Check if user is logged in
    if (!userPageProtect()) {

      // If user is not logged in, show the login page
      flashMsg('admin_users', 'You need to log in first.', 'warning');
      $this->load->view('common/login', '', 'admin');
      exit;
    } else {

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

        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Get information submitted and validate
        if (isset($_POST['login'])) {

          // Try validating
          try {

            // Get login data
            $this->data['login'] = htmlspecialchars(trim($_POST['login']));
            if (empty($this->data['login'])) {
              // Data is not set. Return error.
              $this->data['err']['login'] = 'Please enter a login (username) value';
            } else  if (strlen($this->data['login']) < 3) {
              // Data is less than 3 characters. Return error.
              $this->data['err']['login'] = 'Please enter at least 3 characters';
            }

            // Get display_name data
            $this->data['display_name'] = htmlspecialchars(trim($_POST['display_name']));
            if (empty($this->data['display_name'])) {
              // Data is not set. Return error.
              $this->data['err']['display_name'] = 'Please enter a display name';
            } else  if (strlen($this->data['display_name']) < 2) {
              // Data is less than 3 characters. Return error.
              $this->data['err']['display_name'] = 'Please enter at least 2 characters';
            }

            // Get first_name data
            $this->data['first_name'] = htmlspecialchars(trim($_POST['first_name']));
            if (empty($this->data['first_name'])) {
              // Data is not set. Return error.
              $this->data['err']['first_name'] = 'Please enter a first name';
            } else  if (strlen($this->data['first_name']) < 2) {
              // Data is less than 3 characters. Return error.
              $this->data['err']['first_name'] = 'Please enter at least 2 characters';
            }

            // Get last_name data
            $this->data['last_name'] = htmlspecialchars(trim($_POST['last_name']));
            if (empty($this->data['last_name'])) {
              // Data is not set. Return error.
              $this->data['err']['last_name'] = 'Please enter a last name';
            } else  if (strlen($this->data['last_name']) < 2) {
              // Data is less than 3 characters. Return error.
              $this->data['err']['last_name'] = 'Please enter at least 2 characters';
            }

            // Get email data
            $this->data['email'] = htmlspecialchars(trim($_POST['email']));
            if (!empty($this->data['email']) && !filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
              // Data isn't a valid email address. Return error.
              $this->data['err']['email'] = 'Please enter a valid email address';
            }

            // Get role_id data
            $this->data['role_id'] = htmlspecialchars(trim($_POST['role_id']));
            if (empty($this->data['role_id'])) {
              // Data is not set. Return error.
              $this->data['err']['role_id'] = 'Please select a role';
            }

            // Get send auth_rqd flag
            $this->data['auth_rqd'] = (isset($_POST['auth_rqd']) && !empty($_POST['auth_rqd'])) ? TRUE : FALSE;
          } catch (Exception $e) {

            // Log error if any and set flash message
            error_log($e->getMessage(), 0);
            flashMsg('users_user', '<strong>Error</strong> There was an error adding the user. Please try again', 'warning');
          }
        } else { // Required data not set. Set Errors.

          $this->data['err']['login'] = 'Please enter a login (username) value';
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
            (int) $this->data['auth_rqd']
          )) {
            // User Added

            // Set the user ID
            if ($this->userModel->setUserID((int) $userID)) {

              // Set password reset request
              $resetObject = $this->userModel->setPasswordReset();

              // Check reset worked
              if ($resetObject !== FALSE) {

                // Create the options
                $actionURL = get_site_url('admin/users/new-password/' . urlencode($resetObject->selector) . '/' . urlencode($resetObject->token));
                $loginURL = get_site_url('admin/login');
                $resetExpireDtm = new \DateTime($resetObject->expires);
                $supportEmail = $this->optn->get('site_from_email');

                // Load SendMail Class
                $sendMail = new \SendMail();

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
          } // Unable to add contact. Return error.

          // Set error message
          flashMsg('users_user', '<strong>Error</strong> There was an error adding the user. Please try again.', 'warning');
        }

        // If it's made it this far there were errors. Load add view with submitted data

        // Set Add Data
        $this->setAddData((int) $this->data['role_id']);
      } else { // Page wasn't posted. Load view.

        // Set Add Data
        $this->setAddData();
      }

      // Load add view
      $this->load->view('users/user', $this->data, 'admin');
      exit;
    }
  }
}
