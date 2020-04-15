<?php
class Common extends Controller
{

  /**
   * Class Constructor
   */
  public function __construct($registry)
  {
    // Load the controller constructor
    parent::__construct($registry);

    // Set role if logged in
    if (isLoggedInUser()) {
      // $this->role->setUserPermissions((int) $_SESSION['_cs']['user']['uid']);

      // echo ($this->role->isMasterUser()) ? 'You are a master' : 'You are not a master';
      // exit;
    }
  }

  /**
   * Index Page
   */
  public function index()
  {
    $this->dashboard();
  }

  /**
   * Dashboard Page
   */
  public function dashboard()
  {

    // Check if user is logged in
    if (!userPageProtect()) {

      // If user is not logged in, show the login page
      // Only show error if page isn't direct access
      if ($_SERVER['REQUEST_URI'] != "/admin/") {
        flashMsg('admin_login', 'You need to log in first.', 'warning');
      }
      $this->load->view('common/login', '', 'admin');
      exit;
    } else { // Output dashboard

      // Load Dashboard
      $this->load->view('common/dashboard', '', 'admin');
    }
  }

  /**
   * Login Page
   */
  public function login()
  {

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "log-in") {

      // Load the userauth model
      $this->userAuthModel = $this->load->model('cornerstone/userauth', 'admin');

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Get information submitted and validate
      if (isset($_POST['udata']) && isset($_POST['password'])) {

        // Try validating
        try {

          // Get user data
          $this->data['udata'] = htmlspecialchars(stripslashes(trim($_POST['udata'])));
          if (empty($this->data['udata'])) {
            // If user name or email not set, return error
            $this->data['err']['udata'] = 'Please enter your username or email address';
          } else if (strlen($this->data['udata']) < 3) {
            // If user name or email less than 3 characters, return error
            $this->data['err']['udata'] = 'Please enter at least 3 characters';
          } else if (!$this->userAuthModel->findUserByEmail($this->data['udata'])) {
            // If no active email account exists, return error
            $this->data['err']['udata'] = 'Please enter your account username or email address';
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
          (isset($_POST['remember']) && !empty($_POST['remember'])) ? $this->data['remember'] = TRUE : $this->data['remember'] = FALSE;
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          flashMsg('admin_login', '<strong>Error</strong> - There was an error with your login. Please try again', 'warning');
        }
      } else { // If data not set, set errors

        $this->data['err']['udata'] = 'Please enter a username or email address';
        $this->data['err']['password'] = 'Please enter a password';
      }

      // If valid, create new user auth and continue
      if (empty($this->data['err'])) {
        // Validated

        // Check and set logged in user
        $loggedInUser = $this->userAuthModel->loginUser($this->data['udata'], $this->data['password']);

        // Check loggedInUser is an integer and didn't fail
        if (is_int($loggedInUser) && $loggedInUser > 0) {

          // Check if Authorization required
          if ($loggedInUser == 2) {
            // Authorization required

            // Set the authorization token
            $authObject = $this->userAuthModel->setAuthorization($this->data['remember']);

            // Check if setting authorization failed
            if ($authObject != FALSE) {

              // Create the options
              $actionURL = 'admin/authorize/' . urlencode($authObject->selector);
              $authExpireDtm = new \DateTime($authObject->expires);
              if (!empty($authObject->user_agent) || $authObject->user_agent != '') {
                $browserInfo = 'For security, this relates to a login using ' . $authObject->user_agent . '. ';
              } else {
                $browserInfo = '';
              }

              // Get user email address
              if ($userEmail = $this->userAuthModel->getUserEmail()) {

                // Load SendMail Class
                $sendMail = new \SendMail();

                // Set the HTML message from the template
                if ($message = $sendMail->createEmailTemplate('authorization.html', array('action_url' => get_site_url($actionURL . '/' . $authObject->token), 'auth_code' => $authObject->token, 'expire_dtm' => $authExpireDtm->format('g:ia \o\n l, jS M Y T'), 'browser_security' => $browserInfo))) {

                  // Set the plaintext message from the template
                  if ($plainEmail = $sendMail->createEmailTemplate('authorization.txt', array('action_url' => get_site_url($actionURL . '/' . $authObject->token), 'auth_code' => $authObject->token, 'expire_dtm' => $authExpireDtm->format('g:ia \o\n l, jS M Y T'), 'browser_security' => $browserInfo))) {

                    // Send user their authorization email
                    $emailSubject = $this->optn->get('site_name') . ' Authorization Code';
                    if ($sendMail->sendPHPMail($this->optn->get('site_from_email'), $this->optn->get('site_from_email'), $this->optn->get('site_name'), $userEmail, '', $emailSubject, $message, $plainEmail, '')) { // Email sent

                      // Check if redirect set
                      $redirect = (!empty($_POST['redirect-to'])) ? '?redirect=' . filter_var($_POST['redirect-to'], FILTER_SANITIZE_URL) : '';

                      // Redirect to the authorization page
                      redirectTo($actionURL . $redirect);
                      exit;
                    } // Unable to send email. Continue on to error

                  } // Unable to set plain text message. Continue on to error

                } // Unable to set HTML message. Continue on to error

              } // Unable to get user email. Continue on to error

            } // Unable to set authorization. Continue on to error

          } else if ($loggedInUser == 1) { // Login was a success

            // Authenticate User
            if ($this->userAuthModel->authenticateUser()) {

              // If remember me checked, set cookies
              if ($this->data['remember']) {

                if (!$this->userAuthModel->setAuthCookie()) { // Unable to set cookie

                  // Unset cookie and then continue on to redirect
                  $this->userAuthModel->deleteAuthCookie();
                }
              }

              // Check if redirect set
              if (!empty($_POST['redirect-to'])) {

                // Redirect to requested path
                redirectTo(htmlspecialchars(filter_var($_POST['redirect-to'], FILTER_SANITIZE_URL)));
                exit;
              } else { // Redirect to dashboard

                // Redirect to dashboard
                redirectTo('admin');
                exit;
              }
            } // Unable to authenticate user. Continue on to error

          } else if ($loggedInUser == 3) { // Max login attempts reached

            // Get login attempt time.
            if (!$lockDtm = $this->userAuthModel->getLoginLock()) {
              $lockDtm = '';
            }
            $nextLogin = new \DateTime($lockDtm);
            $nextLogin->modify('+' . $this->optn->get('password_reset_expire') . ' seconds');
            $nextLogin = friendlyDtmDiff(date('Y-m-d H:i:s'), $nextLogin->format('Y-m-d H:i:s'));

            // Set error
            flashMsg('admin_login', 'Sorry you have reached the maximum amount of login attempts for your account. Please try again in ' . $nextLogin . '.', 'danger');

            // Return view
            $this->load->view('common/login', $this->data, 'admin');
            exit;
          }

          // Result wasn't success ("1") or authorization ("2"). Set error and continue down to loading login view with data
          flashMsg('admin_login', '<strong>Error</strong> - There was an error logging you in. Please try again.', 'warning');
        } else { // Set failed login and error and continue down to loading login view with data

          // Set failed login
          $this->userAuthModel->setLoginLog(0);

          // Set error
          flashMsg('admin_login', '<strong>Error</strong> - There was an error logging you in. Please try again.', 'warning');
        }
      }

      // If it's made it this far there were errors. Load login view with data
      $this->load->view('common/login', $this->data, 'admin');
    } else { // else load the login page

      $this->load->view('common/login', '', 'admin');
    }
  }

  /**
   * Log user out
   */
  public function logout()
  {

    // Load the userauth model
    $this->userAuthModel = $this->load->model('cornerstone/userauth', 'admin');

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
      $this->userAuthModel->deleteAuthCookie();
    }

    // Set message and redirect to user page
    flashMsg('admin_login', 'You have been logged out.', 'info');
    redirectTo('admin');
    exit;
  }

  /**
   * Authorization Page
   */
  public function authorize(...$params)
  {

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "authorize") {

      // Load the userauth model
      $this->userAuthModel = $this->load->model('cornerstone/userauth', 'admin');

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
            flashMsg('admin_login', 'Sorry there was an error with your authorization.<br>Please try logging in again.', 'warning');
            redirectTo('admin/login');
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
          flashMsg('admin_auth', 'There was an error with your authorization. Please try again', 'danger');
        }
      } else { // If data not set, set errors

        $this->data['err']['token'] = 'Please enter a 6 number token';

        // Selector not set. Redirect to login page and display error.
        if (!isset($_POST['selector'])) {

          // Selector not set. Redirect to login page and display error.
          flashMsg('admin_login', 'Sorry there was an error with your authorization.<br>Please try logging in again.', 'danger');
          redirectTo('admin/login');
          exit;
        }
      }

      // If valid, create new user auth and continue
      if (empty($this->data['err'])) {
        // Validated

        // Check authorization
        $authorizedUser = $this->userAuthModel->checkAuthorization($this->data['selector'], $this->data['token']);

        // Check if authorizedUser successful
        if ($authorizedUser === TRUE) {
          // Authorization was a success

          // Authenticate User
          if ($this->userAuthModel->authenticateUser()) {

            // If remember me checked, set cookies
            if ($this->userAuthModel->remember) {

              if (!$this->userAuthModel->setAuthCookie()) { // Unable to set cookie

                // Unset cookie and then continue on to redirect
                $this->userAuthModel->deleteAuthCookie();
              }
            }

            // Check if redirect set
            if (!empty($_POST['redirect-to'])) {

              // Redirect to requested path
              redirectTo(htmlspecialchars(filter_var($_POST['redirect-to'], FILTER_SANITIZE_URL)));
              exit;
            } else { // Redirect to dashboard

              // Redirect to dashboard
              redirectTo('admin');
              exit;
            }
          } // Unable to authenticate user. Continue on to error

        } // Unable to authorize user. Continue on to error

        // Set error and redirect to login
        flashMsg('admin_login', 'There was an error with your authorization.<br>Please try logging in again.', 'warning');
        redirectTo('admin/login');
        exit;
      }

      // If it's made it this far there were errors. Load login view with data
      $this->load->view('common/login', $this->data, 'admin');
    } else { // else load the authorization page

      // Check if the selector is set
      if (!empty($params[0])) {

        // Get the selector
        $this->data['selector'] = $params[0];

        // Check if the token is set
        $this->data['token'] = (!empty($params[1]) && is_numeric($params[1]) && strlen($params[1]) == 6) ? $params[1] : '';

        $this->load->view('common/authorize', $this->data, 'admin');
        exit;
      } else { // Selector not set. Continue on to error

        // Set error
        flashMsg('admin_login', 'There was an error with your authorization. Please try logging in again', 'danger');
      }

      // Errors with authorization. Redirect to login page
      redirectTo('admin/login');
    }
  }

  /**
   * Notifications Page
   */
  public function notifications(...$params)
  {
    // Check if page is posted
  }
}
