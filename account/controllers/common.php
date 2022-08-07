<?php

/**
 * User Account Common Controller Class
 */
class Common extends Cornerstone\Controller
{

  /**
   * Class Constructor
   */
  public function __construct($registry)
  {
    // Load the controller constructor
    parent::__construct($registry);

    // Load the account core model
    $this->accountCoreModel = $this->load->model('accountcore', 'account');

    // Set menu items
    $this->data['menuitems'] = array(
      array(
        'path' => 0,
        'text' => 'Home',
        'href' => get_site_url()
      ),
      array(
        'path' => 0,
        'text' => 'Account',
        'href' => get_site_url('account')
      )
    );

    // Set Breadcrumbs
    $this->data['breadcrumbs'] = array(
      array(
        'text' => 'Home',
        'href' => get_site_url()
      ),
      array(
        'text' => 'Account',
        'href' => get_site_url('account')
      )
    );
  }

  /**
   * Index Page
   */
  public function index(...$params)
  {

    // Check if logged in
    if (!isLoggedInUser()) {
      // Not logged in. Redirect to login page
      redirectTo('account/login');
      exit;
    }

    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => 'My Account',
      'href' => get_site_url('account')
    );

    // Load view
    $this->load->view('common/index', $this->data, 'account');
    exit;
  }

  /**
   * Create Elements for Settings Page
   *
   * (no params)
   */
  private function createSettingsElements()
  {
    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => 'Settings',
      'href' => get_site_url('account/settings')
    );

    // Load files required.
    require_once(DIR_HELPERS . 'fn.timezone.php'); // Load the timezone helper

    // TODO Detect user timezone automatically and suggest

    // Timezones
    $this->data['timezone_options'] = "";
    $timezones = timezones_list();
    foreach ($timezones as $zone) {
      // Check if selected
      $selectedTZ = (!empty($this->data['timezone']) && timezones_filter($this->data['timezone']) === $zone[1]) ? ' selected' : '';
      $this->data['timezone_options'] .= '<option value="' . htmlspecialchars($zone[1]) . '"' . $selectedTZ . '>' . htmlspecialchars($zone[0]) . '</option>';
    }
  }

  /**
   * Settings Page
   */
  public function settings(...$params)
  {

    // Check if logged in
    if (!isLoggedInUser()) {
      // Not logged in. Redirect to login page
      redirectTo('account/login');
      exit;
    }

    // Process "edit"

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "update") {

      // Get information submitted and validate
      try {

        // Get first_name data
        $this->data['first_name'] = trim($_POST['first_name']);
        if (empty($this->data['first_name'])) {
          // Data is not set. Return error.
          $this->data['err']['first_name'] = 'Please enter your first name';
          throw new Exception("Your first name is missing. Please enter your first name.");
        } else if (!empty($this->data['first_name']) && strlen($this->data['first_name']) < 2) {
          // Data is less than 2 characters. Return error.
          $this->data['err']['first_name'] = 'Please enter at least 2 characters';
          throw new Exception("Your first name is less than 2 characters. Please enter at least 2 characters.");
        }

        // Get last_name data
        $this->data['last_name'] = trim($_POST['last_name']);
        if (empty($this->data['last_name'])) {
          // Data is not set. Return error.
          $this->data['err']['last_name'] = 'Please enter your last name';
          throw new Exception("Your last name is missing. Please enter your last name.");
        } else if (!empty($this->data['last_name']) && strlen($this->data['last_name']) < 3) {
          // Data is less than 3 characters. Return error.
          $this->data['err']['last_name'] = 'Please enter at least 3 characters';
          throw new Exception("Your last name is less than 2 characters. Please enter at least 2 characters.");
        }

        // Get display_name data
        $this->data['display_name'] = trim($_POST['display_name']);
        if (empty($this->data['display_name'])) {
          // Data is not set. Return error.
          $this->data['err']['display_name'] = 'Please enter your display name';
          throw new Exception("Your display name is missing. Please enter your display name.");
        } else if (!empty($this->data['display_name']) && strlen($this->data['display_name']) < 3) {
          // Data is less than 3 characters. Return error.
          $this->data['err']['display_name'] = 'Please enter at least 3 characters';
          throw new Exception("Your display name is less than 2 characters. Please enter at least 2 characters.");
        }

        // Get timezone data
        $this->data['timezone'] = trim($_POST['timezone']);
        if (empty($this->data['timezone'])) {
          // Data is not set. Return error.
          $this->data['err']['timezone'] = 'Please select a timezone.';
          throw new Exception("Your timezone is missing. Please select a timezone.");
        }

        // Get email data
        $this->data['email'] = trim($_POST['email']);
        if (empty($this->data['email'])) {
          // Data is not set. Return error.
          $this->data['err']['email'] = 'Please enter your email address';
          throw new Exception("Your email address is missing. Please enter your email address.");
        } else if (!empty($this->data['email']) && !filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
          // Data isn't a valid email address. Return error.
          $this->data['err']['email'] = 'Please enter a valid email address';
          throw new Exception("Your email address failed validation. Please enter a valid email address.");
        }

        // Get password data
        $this->data['password'] = $_POST['password'];

        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $this->data['password']);
        $lowercase = preg_match('@[a-z]@', $this->data['password']);
        $number    = preg_match('@[0-9]@', $this->data['password']);

        // Check validation is all ok
        if (!empty($this->data['password']) && (!$uppercase || !$lowercase || !$number || strlen($this->data['password']) < 6 || strlen($this->data['password']) > 128)) {
          // If password not set or doesn't match the requirements, return error
          $this->data['err']['password'] = 'Your password must be at least six characters long and contain at least one upper case letter and one number.';
          throw new Exception("Your password must be at least six characters long and contain at least one upper case letter and one number. Please enter a valid password.");
        }

        // Get confirm_password data
        $this->data['confirm_password'] = $_POST['confirm_password'];
        if (!empty($this->data['password']) && empty($this->data['confirm_password'])) {
          // Data is not set. Return error.
          $this->data['err']['password'] = 'Please confirm your password';
          throw new Exception("Your password confirmation was missing. Please enter your password confirmation.");
        } else if (!empty($this->data['password']) && $this->data['password'] !== $this->data['confirm_password']) {
          // Data is not set. Return error.
          $this->data['err']['password'] = 'Your passwords must match';
          throw new Exception("Your password confirmation didn't match. Please enter your password confirmation.");
        }
      } catch (Exception $e) {
        // Log error if any and set flash message
        error_log($e->getMessage(), 0);
        flashMsg('account_settings', '<strong>Error</strong> There was an error updating your settings - ' . $e->getMessage() . '. Please try again', 'warning');
      }

      // If valid, add new address
      if (empty($this->data['err'])) {
        // Validated

        // Update customer
        if ($this->accountCoreModel->editUser(
          $this->data['first_name'],
          $this->data['last_name'],
          $this->data['display_name'],
          $this->data['email'],
          $this->data['timezone']
        )) { // Settings updated successfully.

          // Update session preferences
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

          // Init password message
          $passwordMsg = '';

          // Check if password needs updating
          if (!empty($this->data['password'])) {
            // Load the password model
            $this->passwordModel = $this->load->model('accpassword', 'account');

            // Set the user ID
            $this->passwordModel->setUserID((int) $_SESSION['_cs']['user']['uid']);

            // Generate new key
            $newKey = get_crypto_key();

            // Hash password with new key
            $password_encrypted = password_hash($this->data['password'] . $newKey, PASSWORD_DEFAULT);

            // Update users password with new key
            if ($this->passwordModel->setNewPassword($password_encrypted, $newKey)) {

              // Set password message
              $passwordMsg = ' and your password was updated successfully';

              // Create the options
              $actionURL = 'account/login';
              $currentDtm = new \DateTime();
              // Get browser info if browser tracking enabled
              if ($this->optn->get('browser_tracking')) {
                $browser = new \WhichBrowser\Parser(getallheaders());
                // Set browser "User Agent"
                $browserInfo = "For security, you password was reset on a device using " . $browser->toString() . ". ";
              } else {
                $browserInfo = "";
              }

              // Get users email address
              if ($userEmail = $this->passwordModel->getUserEmail()) {

                // Load SendMail Class
                $sendMail = new Cornerstone\SendMail();

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
                    // Unable to set plain text message. Continue on to error
                    $plainEmail = 'Sorry, there was an error generating a copy of this email in plain text, however, Your password has been updated on the site.\r\n\r\n' . get_site_url();
                  }

                  // Send user email confirming password change
                  $emailSubject = "You've successfully reset your " . $this->optn->get('site_name') . " password";
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
                    // Set password message
                    $passwordMsg .= ' + an email was sent to you with this confirmation';
                  } // Unable to send email.

                } // Unable to set HTML message.

              } // Unable to get user email.
            } else {
              // Set password message
              $passwordMsg = ' but your password was unable to be updated';
            }
          }

          // Set success message
          flashMsg('account_settings', '<strong>Success</strong> Your settings were updated successfully' . $passwordMsg . '.');
          redirectTo('account/settings/');
          exit;
        } // Unable to update. Redirect to view with error

        // Set error message
        flashMsg('account_settings', '<strong>Error</strong> There was an error updating your settings. Please try again.', 'warning');
      }

      // Create elements
      $this->createSettingsElements();
    } else { // Page wasn't posted. Load view.
      // Get account details
      if ($returnedUserDetails = $this->accountCoreModel->getUserDetails()) {

        // Set details
        foreach ($returnedUserDetails as $key => $data) {
          $this->data[str_replace(array('user_'), '', $key)] = $data;
        }

        // Create elements
        $this->createSettingsElements();
      } else { // Unable to get account details. Load logout method

        // Load logout method
        $this->logout;
      }
    }

    // Load view
    $this->load->view('common/settings', $this->data, 'account');
    exit;
  }

  /**
   * Register Page
   */
  public function register(...$params)
  {

    if (!$this->optn->get('registration_active')) {
      // Redirect user with error
      flashMsg('account_login', '<strong>Error</strong> Sorry, registration is not currently available.', 'warning');
      redirectTo('account/login');
      exit;
    }

    // Load the register model
    $this->registerModel = $this->load->model('register', 'account');

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "register") {

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      if (!empty($this->optn->get('recaptcha_site_key'))) {

        // Build POST request:
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_secret = $this->optn->get('recaptcha_secret_key');
        $recaptcha_response = $_POST['recaptcha_response'];

        // Make and decode POST request:
        $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
        $recaptcha = json_decode($recaptcha);
      }

      // Take action based on the score returned:
      if (empty($this->optn->get('recaptcha_site_key')) || (!empty($recaptcha->score) && $recaptcha->score >= 0.5)) {

        // Get information submitted and validate
        if (isset($_POST['first_name']) || isset($_POST['last_name']) || isset($_POST['email']) || isset($_POST['password']) || isset($_POST['confirm-password'])) {

          // Try validating
          try {

            // Get first_name data
            $this->data['first_name'] = htmlspecialchars(trim($_POST['first_name']));
            if (empty($this->data['first_name'])) {
              // Data is not set. Return error.
              $this->data['err']['first_name'] = 'Please enter your first name';
            } else if (strlen($this->data['first_name']) < 3) {
              // Data is less than 3 characters. Return error.
              $this->data['err']['first_name'] = 'Please enter at least 3 characters';
            }

            // Get last_name data
            $this->data['last_name'] = htmlspecialchars(trim($_POST['last_name']));
            if (empty($this->data['last_name'])) {
              // Data is not set. Return error.
              $this->data['err']['last_name'] = 'Please enter your last name';
            } else if (strlen($this->data['last_name']) < 3) {
              // Data is less than 3 characters. Return error.
              $this->data['err']['last_name'] = 'Please enter at least 3 characters';
            }

            // Get email data
            $this->data['email'] = htmlspecialchars(trim($_POST['email']));
            if (empty($this->data['email'])) {
              // Data is not set. Return error.
              $this->data['err']['email'] = 'Please enter your email address';
            } else if (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
              // Data isn't a valid email address. Return error.
              $this->data['err']['email'] = 'Please enter a valid email address';
            } else if ($this->registerModel->checkEmailUnique($this->data['email']) > 0) {
              // Email address is already in the system.

              // Set error
              flashMsg('account_forgot_pwd', '<strong>Error</strong> There is already an account registered to that email address. Request a new password below to change your password.', 'info');

              // Redirect to forgot password
              redirectTo('account/password/forgot');
              exit;
            }

            // Get password
            $this->data['password'] = $_POST['password'];
            unset($_POST['password']);

            // Validate password strength
            $uppercase = preg_match('@[A-Z]@', $this->data['password']);
            $lowercase = preg_match('@[a-z]@', $this->data['password']);
            $number    = preg_match('@[0-9]@', $this->data['password']);

            // Check validation is all ok
            if (empty($this->data['password']) || !$uppercase || !$lowercase || !$number || strlen($this->data['password']) < 6 || strlen($this->data['password']) > 128) {

              // If password not set or doesn't match the requirements, return error
              $this->data['err']['password'] = 'Your password must be at least six characters long and contain at least one upper case letter and one number.';
            }

            // Get confirm password
            $this->data['confirm_password'] = $_POST['confirm_password'];
            unset($_POST['confirm_password']);

            // Check password isn't empty
            if (empty($this->data['confirm_password'])) {

              // It's empty. Return error
              $this->data['err']['confirm_password'] = 'Please confirm your password';
            } else if ($this->data['password'] != $this->data['confirm_password']) { // Check if passwords match

              // Passwords don't match. Return error
              $this->data['err']['confirm_password'] = 'Both passwords must match';
            }

            // Check T&Cs are accepted ~ Added 27/07/2020
          } catch (Exception $e) {

            // Log error if any and set flash message
            error_log($e->getMessage(), 0);
            flashMsg('account_register', '<strong>Error</strong> There was an error creating your account. Please try again', 'warning');
          }
        } else { // If data not set, set errors

          $this->data['err']['first_name'] = 'Please enter your first name';
          $this->data['err']['last_name'] = 'Please enter your last name';
          $this->data['err']['email'] = 'Please enter your email address';
          $this->data['err']['password'] = 'Please enter your password';
          $this->data['err']['confirm_password'] = 'Please confirm your password';
          $this->data['err']['accepted_conditions'] = 'Please accept the terms &amp; conditions';
        }

        // Check if any errors are set
        if (empty($this->data['err'])) {
          // Validated

          // Generate password key
          $pwdKey = get_crypto_key();

          // Hash password with new key
          $password_encrypted = password_hash($this->data['password'] . $pwdKey, PASSWORD_DEFAULT);

          // Add new user
          if ($userID = $this->registerModel->addUser(
            $this->data['first_name'],
            $this->data['last_name'],
            $this->data['email'],
            $password_encrypted,
            $pwdKey
          )) {
            // User added

            // Send welcome email

            // Create the options
            $actionURL = get_site_url('account/login');
            $supportEmail = $this->optn->get('site_from_email');
            // Set fail message
            $emailSent = ' but your login details were unable to be emailed to you';

            // Load SendMail Class
            $sendMail = new Cornerstone\SendMail();

            // Set the HTML message from the template
            if ($message = $sendMail->createEmailTemplate(
              'user-register.html',
              array(
                'action_url' => $actionURL,
                'first_name' => $this->data['first_name'],
                'email' => $this->data['email'],
                'support_email' => $supportEmail
              )
            )) {

              // Set the plaintext message from the template
              if (!$plainEmail = $sendMail->createEmailTemplate(
                'user-register.txt',
                array(
                  'action_url' => $actionURL,
                  'first_name' => $this->data['first_name'],
                  'email' => $this->data['email'],
                  'support_email' => $supportEmail
                )
              )) {
                // Set fallback plain text message
                $plainEmail = 'Sorry, there was an error generating your welcome email :(. The good news is your account has been created on the ' . $this->optn->get('site_name') . ' website! To set your password please head to our website at ' . get_site_url('account/login') . ' and press the "forgot password" button on the login page and you\'ll be able to set your password. Sorry for the inconvenience.';
              }

              // Send user email confirming password change
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

                // Set success message
                $emailSent = ' and your login details have been emailed to you';
              } // Unable to send email.

            } // Unable to set HTML message.

            // Set success message
            flashMsg('account_login', '<strong>Success</strong> Your account has been created' . $emailSent . '. Sign in below to get started!');

            // Redirect to login
            redirectTo('account/login');
            exit;
          } // Unable to add user. Return error

          // Set error
          flashMsg('account_register', '<strong>Error</strong> Sorry, there was an issue creating your account. Please try again.', 'warning');
        } // Errors were set. Continue on to view.
      } else { // reCAPTCHA didn't pass. Load error and re-load view

        // Set error message
        flashMsg('account_register', '<strong>Error</strong> There was an error with your verification. Please try again', 'warning');
      }
    } else { // else load the registration page

      // Set Breadcrumbs
      $this->data['breadcrumbs'][] = array(
        'text' => 'Create Account',
        'href' => get_site_url('account/register')
      );
    }

    // Load the reCAPTCHA site key
    $this->data['recaptcha_site_key'] = $this->optn->get('recaptcha_site_key');

    // Load view
    $this->load->view('common/register', $this->data, 'account');
    exit;
  }

  /**
   * Login Page
   */
  public function login()
  {
    // Check if logged in
    if (isLoggedInUser()) {
      // Logged in. Redirect to account page
      redirectTo('account/');
      exit;
    }

    // Check for admin source
    $isAdminSource = (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], get_site_url() . 'admin/') !== FALSE) ? TRUE : FALSE;

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "log-in") {

      // Load the account auth model
      $this->authModel = $this->load->model('accountauth', 'account');

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Get information submitted and validate
      if (isset($_POST['user']) && isset($_POST['password'])) {

        // Try validating
        try {

          // Get user data
          $this->data['user'] = htmlspecialchars(trim($_POST['user']));
          if (empty($this->data['user'])) {
            // Data not set, return error
            $this->data['err']['user'] = 'Please enter your email address';
          } else if (strlen($this->data['user']) < 3) {
            // Data less than 3 characters, return error
            $this->data['err']['user'] = 'Please enter at least 3 characters';
          } else if (!$this->authModel->checkForUserMatch($this->data['user'])) {
            // If no active account exists, return error
            $this->data['err']['user'] = 'Please enter your account email address';
          }

          // Get user password
          $this->data['password'] = $_POST['password'];
          if (empty($this->data['password'])) {
            // If password set, return error
            $this->data['err']['password'] = 'Please enter your password';
          } else if (strlen($this->data['password']) < 6) {
            // If password less than 6 characters, return error
            $this->data['err']['password'] = 'Please enter at least 6 characters';
          }

          // Get remember me
          $this->data['remember'] = (isset($_POST['remember']) && !empty($_POST['remember'])) ? TRUE : FALSE;
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          if ($isAdminSource) {
            flashMsg('admin_login', '<strong>Error</strong> There was an error with your login. Please try again', 'warning');
          } else {
            flashMsg('account_login', '<strong>Error</strong> There was an error with your login. Please try again', 'warning');
          }
        }
      } else { // If data not set, set errors

        $this->data['err']['user'] = 'Please enter an email address';
        $this->data['err']['password'] = 'Please enter a password';
      }

      // If valid, create new user auth and continue
      if (empty($this->data['err'])) {
        // Validated

        // Check and set logged in user
        $loggedInUser = $this->authModel->loginUser(
          $this->data['user'],
          $this->data['password']
        );

        // Check login valid
        if (is_int($loggedInUser) && $loggedInUser > 0) {

          // Check if Authorization required
          if ($loggedInUser === 2) {
            // Authorization required

            // Set the authorization token
            $authObject = $this->authModel->setAuthorization($this->data['remember']);

            // Check if setting authorization failed
            if ($authObject != FALSE) {

              // Create the options
              $actionURL = 'account/authorize/' . urlencode($authObject->selector);
              $authExpireDtm = new \DateTime($authObject->expires);
              if (!empty($authObject->user_agent) || $authObject->user_agent != '') {
                $browserInfo = 'For security, this relates to a login using ' . $authObject->user_agent . '. ';
              } else {
                $browserInfo = '';
              }

              // Get user email address
              if ($userEmail = $this->authModel->getUserEmail()) {

                // Load SendMail Class
                $sendMail = new Cornerstone\SendMail();

                // Set the HTML message from the template
                if ($message = $sendMail->createEmailTemplate(
                  'authorization.html',
                  array(
                    'action_url' => get_site_url($actionURL . '/' . $authObject->token),
                    'auth_code' => $authObject->token,
                    'expire_dtm' => $authExpireDtm->format('g:ia \o\n l, jS M Y T'),
                    'browser_security' => $browserInfo
                  )
                )) {

                  // Set the plaintext message from the template
                  if (!$plainEmail = $sendMail->createEmailTemplate(
                    'authorization.txt',
                    array(
                      'action_url' => get_site_url($actionURL . '/' . $authObject->token),
                      'auth_code' => $authObject->token,
                      'expire_dtm' => $authExpireDtm->format('g:ia \o\n l, jS M Y T'),
                      'browser_security' => $browserInfo
                    )
                  )) {
                    // Unable to set plain text message. Continue on to error
                    $plainEmail = 'Sorry, there was an error generating a copy of this email in plain text. Please try login again to request a new authorization code.\r\n\r\n' . get_site_url();
                  }

                  // Send user their authorization email
                  $emailSubject = $this->optn->get('site_name') . ' Authorization Code';
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

                    // Check if redirect set
                    $redirect = (!empty($_POST['redirect-to'])) ? '?redirect=' . filter_var($_POST['redirect-to'], FILTER_SANITIZE_URL) : '';

                    // Redirect to the authorization page
                    redirectTo($actionURL . $redirect);
                    exit;
                  } // Unable to send email. Continue on to error

                } // Unable to set HTML message. Continue on to error

              } // Unable to get user email. Continue on to error

            } // Unable to set authorization. Continue on to error

          } else if ($loggedInUser === 1) { // Login was a success

            // Authenticate User
            if ($this->authModel->authenticateUser()) {
              // If remember me checked, set cookies
              if ($this->data['remember']) {

                if (!$this->authModel->setAuthCookie()) { // Unable to set cookie

                  // Unset cookie and then continue on to redirect
                  $this->authModel->deleteAuthCookie();
                }
              }

              // Check if redirect set
              if (!empty($_POST['redirect-to'])) {

                // Redirect to requested path
                redirectTo(htmlspecialchars(filter_var($_POST['redirect-to'], FILTER_SANITIZE_URL)));
                exit;
              } else { // Redirect to dashboard
                if ($isAdminSource) {
                  // Redirect to dashboard
                  redirectTo('admin');
                } else {
                  // Redirect to dashboard
                  redirectTo('account');
                }
                exit;
              }
            } // Unable to authenticate user. Continue on to error

          } else if ($loggedInUser === 3) { // Max login attempts reached

            // Get login attempt time.
            if (!$lockDtm = $this->authModel->getLoginLock()) {
              $lockDtm = '';
            }
            $nextLogin = new \DateTime($lockDtm);
            $nextLogin->modify('+' . $this->optn->get('password_reset_expire') . ' seconds');
            $nextLogin = friendlyDtmDiff(date('Y-m-d H:i:s'), $nextLogin->format('Y-m-d H:i:s'));

            if ($isAdminSource) {

              // Set error
              flashMsg('admin_login', 'Sorry you have reached the maximum amount of login attempts for your account. Please try again in ' . $nextLogin . '.', 'danger');

              // Return view
              $this->load->view('common/login', $this->data, 'admin');
            } else {

              // Set error
              flashMsg('account_login', 'Sorry you have reached the maximum amount of login attempts for your account. Please try again in ' . $nextLogin . '.', 'danger');

              // Return view
              $this->load->view('common/login', $this->data, 'account');
            }
            exit;
          }

          // Result wasn't success ("1") or authorization ("2"). Set error and continue down to loading login view with data
          if ($isAdminSource) {
            flashMsg('admin_login', '<strong>Error</strong> There was an error logging you in. Please try again.', 'warning');
          } else {
            flashMsg('account_login', '<strong>Error</strong> There was an error logging you in. Please try again.', 'warning');
          }
        } else { // Set failed login and error and continue down to loading login view with data

          // Set failed login
          $this->authModel->setLoginLog(0);

          // Set error
          if ($isAdminSource) {
            flashMsg('admin_login', '<strong>Error</strong> There was an error logging you in. Please try again.', 'warning');
          } else {
            flashMsg('account_login', '<strong>Error</strong> There was an error logging you in. Please try again.', 'warning');
          }
        }
      }

      // If it's made it this far there were errors. Load login view with data
    } // else load the login page

    if ($isAdminSource) {
      $this->load->view('common/login', $this->data, 'admin');
    } else {

      // Set Breadcrumbs
      $this->data['breadcrumbs'][] = array(
        'text' => 'Sign In',
        'href' => get_site_url('account/login')
      );
      $this->load->view('common/login', $this->data, 'account');
    }
    exit;
  }

  /**
   * Authorization Page
   */
  public function authorize(...$params)
  {

    // Check for admin source
    $isAdminSource = (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], get_site_url() . 'admin/') !== FALSE) ? TRUE : FALSE;

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "authorize") {

      // Load the account auth model
      $this->authModel = $this->load->model('accountauth', 'account');

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Get information submitted and validate
      if (isset($_POST['selector']) && isset($_POST['token'])) {

        // Try validating
        try {

          // Get selector
          $this->data['selector'] = htmlspecialchars(stripslashes(trim($_POST['selector'])));
          if (empty($this->data['selector']) || strlen($this->data['selector']) != 16) {
            // Selector not set or 16 characters long. Redirect to login page and display error.
            if ($isAdminSource) {
              flashMsg('admin_login', 'Sorry there was an error with your authorization.<br>Please try logging in again.', 'warning');
              redirectTo('admin/login');
            } else {
              flashMsg('account_login', 'Sorry there was an error with your authorization.<br>Please try logging in again.', 'warning');
              redirectTo('account/login');
            }
            exit;
          }

          // Get token
          $this->data['token'] = htmlspecialchars(stripslashes(trim($_POST['token'])));
          if (empty($this->data['token'])) {

            // Token not set. Return error
            $this->data['err']['token'] = 'Please enter your token';
          } else if (strlen($this->data['token']) != 6 || !is_numeric($this->data['token'])) {

            // Token not 6 numbers. Return error
            $this->data['err']['token'] = 'Please enter a 6 number token';
          }
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          if ($isAdminSource) {
            flashMsg('admin_auth', 'There was an error with your authorization. Please try again', 'danger');
          } else {
            flashMsg('account_auth', 'There was an error with your authorization. Please try again', 'danger');
          }
        }
      } else { // If data not set, set errors

        $this->data['err']['token'] = 'Please enter a 6 number token';

        // Selector not set. Redirect to login page and display error.
        if (!isset($_POST['selector'])) {

          // Selector not set. Redirect to login page and display error.
          if ($isAdminSource) {
            flashMsg('admin_login', 'Sorry there was an error with your authorization.<br>Please try logging in again.', 'danger');
            redirectTo('admin/login');
          } else {
            flashMsg('account_login', 'Sorry there was an error with your authorization.<br>Please try logging in again.', 'danger');
            redirectTo('account/login');
          }
          exit;
        }
      }

      // If valid, create new user auth and continue
      if (empty($this->data['err'])) {
        // Validated

        // Check authorization
        $authorizedUser = $this->authModel->checkAuthorization(
          $this->data['selector'],
          $this->data['token']
        );

        // Check if authorizedUser successful
        if ($authorizedUser === TRUE) {
          // Authorization was a success

          // Authenticate User
          if ($this->authModel->authenticateUser()) {

            // If remember me checked, set cookies
            if ($this->authModel->checkRememberUser()) {

              if (!$this->authModel->setAuthCookie()) { // Unable to set cookie

                // Unset cookie and then continue on to redirect
                $this->authModel->deleteAuthCookie();
              }
            }

            // Check if redirect set
            if (!empty($_POST['redirect-to'])) {

              // Redirect to requested path
              redirectTo(htmlspecialchars(filter_var($_POST['redirect-to'], FILTER_SANITIZE_URL)));
              exit;
            } else { // Redirect to dashboard

              // Redirect to dashboard
              if ($isAdminSource) {
                redirectTo('admin');
              } else {
                redirectTo('account');
              }
              exit;
            }
          } // Unable to authenticate user. Continue on to error

        } // Unable to authorize user. Continue on to error

        // Set error and redirect to login
        if ($isAdminSource) {
          flashMsg('admin_login', 'There was an error with your authorization.<br>Please try logging in again.', 'warning');
          redirectTo('admin/login');
        } else {
          flashMsg('account_login', 'There was an error with your authorization.<br>Please try logging in again.', 'warning');
          redirectTo('account/login');
        }
        exit;
      }

      // If it's made it this far there were errors. Load login view with data
      if ($isAdminSource) {
        $this->load->view('common/login', $this->data, 'admin');
      } else {
        $this->load->view('common/login', $this->data, 'account');
      }
    } else { // else load the authorization page

      // Check if the selector is set
      if (!empty($params[0])) {

        // Get the selector
        $this->data['selector'] = $params[0];

        // Check if the token is set
        $this->data['token'] = (!empty($params[1]) && is_numeric($params[1]) && strlen($params[1]) == 6) ? $params[1] : '';
        $this->load->view('common/authorize', $this->data, 'account');
        exit;
      } else { // Selector not set. Continue on to error

        // Set error
        if ($isAdminSource) {
          flashMsg('admin_login', 'There was an error with your authorization. Please try logging in again', 'danger');
        } else {
          flashMsg('account_login', 'There was an error with your authorization. Please try logging in again', 'danger');
        }
      }

      // Errors with authorization. Redirect to login page
      if ($isAdminSource) {
        redirectTo('admin/login');
      } else {
        redirectTo('account/login');
      }
    }
  }

  /**
   * Log user out
   */
  public function logout()
  {

    // Check for admin source
    $isAdminSource = (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], get_site_url() . 'admin/') !== FALSE) ? TRUE : FALSE;

    // Load the account auth model
    $this->authModel = $this->load->model('accountauth', 'account');

    // Check if session set (just in case) and start if it isn't
    if (session_id() == '') {
      session_start();
    }

    // Delete the $_SESSION data set in `authenticateUser()`
    unset($_SESSION['_cs']);

    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
      );
    }

    // Destroy the session
    session_destroy();

    // Close session
    session_write_close();

    // Make sure cookie is cleared
    setcookie(session_name(), '', 0, '/');

    // Check if session set (just in case) and restart if it isn't
    if (session_id() == '') {
      session_start();
    }

    // Regenerate a new session ID just to be sure
    session_regenerate_id(true);

    // Check if the $_COOKIE data is set
    if (!empty($_COOKIE['_cs'])) {

      // Delete the cookie token from the database
      $this->authModel->deleteAuthCookie();
    }

    // Set message and redirect to user page
    if ($isAdminSource) {
      flashMsg('admin_login', 'You have been logged out.', 'info');
      redirectTo('admin');
    } else {
      flashMsg('account_login', 'You have been logged out.', 'info');
      redirectTo('account');
    }
    exit;
  }
}
