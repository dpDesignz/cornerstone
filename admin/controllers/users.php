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

    // Load the user model
    $this->userModel = $this->load->model('cornerstone/user', 'admin');
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
            // If email not set, return error
            $this->data['err']['email'] = 'Please enter your email address';
          } else if (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            // If email address isn't a valid email address, return error
            $this->data['err']['udata'] = 'Please enter a valid email address';
          }
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          flashMsg('admin_forgot_pwd', '<strong>Error</strong> - There was an error with your email address. Please try again', 'warning');
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
              $actionURL = 'admin/users/new-password/' . urlencode($resetObject->selector) . '/' . urlencode($resetObject->token);
              $resetExpireDtm = new \DateTime($resetObject->expires);
              if (!empty($resetObject->user_agent) || $resetObject->user_agent != '') {
                $browserInfo = 'For security, this relates to a reset request from a device using ' . $resetObject->user_agent . '. ';
              } else {
                $browserInfo = '';
              }

              // Load SendMail Class
              $sendMail = new \SendMail();

              // Set the HTML message from the template
              if ($message = $sendMail->createEmailTemplate('password-reset.html', array('name' => $resetObject->user_name, 'action_url' => get_site_url($actionURL), 'expire_dtm' => $resetExpireDtm->format('g:ia \o\n l, jS M Y T'), 'browser_security' => $browserInfo))) {

                // Set the plaintext message from the template
                if ($plainEmail = $sendMail->createEmailTemplate('password-reset.txt', array('name' => $resetObject->user_name, 'action_url' => get_site_url($actionURL), 'expire_dtm' => $resetExpireDtm->format('g:ia \o\n l, jS M Y T'), 'browser_security' => $browserInfo))) {

                  // Send user their authorization email
                  $emailSubject = ' Reset your ' . $this->optn->get('site_name') . ' password';
                  if ($sendMail->sendPHPMail($this->optn->get('site_from_email'), $this->optn->get('site_from_email'), $this->optn->get('site_name'), $this->data['email'], '', $emailSubject, $message, $plainEmail, '')) { // Email sent

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
          $actionURL = 'admin/users/forgot-password';
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
          if ($message = $sendMail->createEmailTemplate('password-reset-help.html', array('email_address' => $this->data['email'], 'action_url' => get_site_url($actionURL), 'browser_security' => $browserInfo))) {

            // Set the plaintext message from the template
            if ($plainEmail = $sendMail->createEmailTemplate('password-reset-help.txt', array('email_address' => $this->data['email'], 'action_url' => get_site_url($actionURL), 'browser_security' => $browserInfo))) {

              // Send user their authorization email
              $emailSubject = $this->optn->get('site_name') . ' Password Reset Help';
              if ($sendMail->sendPHPMail($this->optn->get('site_from_email'), $this->optn->get('site_from_email'), $this->optn->get('site_name'), $this->data['email'], '', $emailSubject, $message, $plainEmail, '')) { // Email sent

                // Redirect user to login page
                flashMsg('admin_login', 'Your password reset email has been sent. Please check your email account for the link to reset your password', 'success');
                redirectTo('admin/login');
                exit;
              } // Unable to send email. Continue on to error

            } // Unable to set plain text message. Continue on to error

          } // Unable to set HTML message. Continue on to error

        }

        // Set error
        flashMsg('admin_forgot_pwd', '<strong>Error</strong> - There was an error processing your password reset request. Please try again.', 'warning');
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
            flashMsg('admin_forgot_pwd', '<strong>Error</strong> - Sorry, your reset request seems to have glitched. Please request a new password reset link.', 'warning');

            // Redirect to forgot password page
            redirectTo('admin/users/forgot-password');
            exit;
          }

          // Check if password reset is valid
          if (!$this->userModel->checkPasswordReset($this->data['selector'])) {
            // Password reset is not valid

            // Set error
            flashMsg('admin_forgot_pwd', '<strong>Error</strong> - Sorry, your reset request seems to have expired. Please request a new password reset link.', 'warning');

            // Redirect to forgot password page
            redirectTo('admin/users/forgot-password');
            exit;
          }
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          flashMsg('admin_new_pwd', '<strong>Error</strong> - There was an error checking your request. Please try again', 'warning');
        }
      } else { // If data not set, set error and redirect to forgot password

        // Set error
        flashMsg('admin_forgot_pwd', '<strong>Error</strong> - Sorry, There was an issue processing your request. Please request a new password reset link.', 'danger');

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
          flashMsg('admin_new_pwd', '<strong>Error</strong> - There was an error checking your new password. Please try again', 'warning');
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
              if ($message = $sendMail->createEmailTemplate('new-password.html', array('action_url' => get_site_url($actionURL), 'reset_dtm' => $currentDtm->format('g:ia \o\n l, jS M Y T'), 'browser_security' => $browserInfo))) {

                // Set the plaintext message from the template
                if ($plainEmail = $sendMail->createEmailTemplate('new-password.txt', array('action_url' => get_site_url($actionURL), 'reset_dtm' => $currentDtm->format('g:ia \o\n l, jS M Y T'), 'browser_security' => $browserInfo))) {

                  // Send user email confirming password change
                  $emailSubject = 'You\'ve successfully reset your ' . $this->optn->get('site_name') . 'password';
                  if ($sendMail->sendPHPMail($this->optn->get('site_from_email'), $this->optn->get('site_from_email'), $this->optn->get('site_name'), $userEmail, '', $emailSubject, $message, $plainEmail, '')) { // Email sent

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
          flashMsg('admin_forgot_pwd', '<strong>Error</strong> - Sorry, There was an issue with your token. Please request a new password reset link.', 'danger');

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
        flashMsg('admin_forgot_pwd', '<strong>Error</strong> - Sorry, something went wrong.<br>Please request a new password reset link.', 'danger');

        // Redirect to forgot password page
        redirectTo('admin/users/forgot-password');
        exit;
      } else if (!$this->userModel->checkPasswordReset($this->data['selector'])) { // Check if password reset is valid
        // Password reset is not valid

        // Set error
        flashMsg('admin_forgot_pwd', '<strong>Error</strong> - Sorry, that request seems to have expired. Please request a new password reset link.', 'danger');

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
  public function index()
  {

    // Check if user is logged in
    if (!userPageProtect()) {

      // If user is not logged in, show the login page
      flashMsg('admin_login', 'You need to log in first.', 'warning');
      $this->load->view('common/login', '', 'admin');
      exit;
    } else { // Output user index page

      // Get users
      if ($userList = $this->userModel->listUsers()) {

        // Set user list output
        $userListOut = '<div class="csc-col csc-col12 csc-data-table csc-container">
            <section class="csc-data-table__table">
              <table>
                <thead class="csc-table-header">
                  <tr>
                    <th><span class="csc-table-header__title">Username</span></th>
                    <th><span class="csc-table-header__title">Name</span></th>
                    <th><span class="csc-table-header__title">Email</span></th>
                    <th><span class="csc-table-header__title">User Group</span></th>
                    <th><span class="csc-table-header__title">Last Login</span></th>
                  </tr>
                </thead>
                <tbody class="csc-table-body">';

        // Loop through user data
        foreach ($userList as $userData) {

          // Get user group
          if (!$userGroupName = $this->userModel->getGroupName($userData->user_group_id)) {

            // Set default value if group not found
            $userGroupName = 'n/a';
          }

          // Get last login
          if ($userLastLogin = $this->userModel->getLastLogin($userData->user_id)) {

            // Set timestamp
            $userLastLogin = new \DateTime($userLastLogin);
            $userLastLogin = $userLastLogin->format('D, jS M Y h:ia');
          } else { // Unable to find last login. Set default value.

            // Set default value if group not found
            $userLastLogin = 'n/a';
          }

          // Set row output
          $userListOut .= '<tr>
              <td>' . $userData->user_login . '</td>
              <td>' . $userData->user_first_name . ' ' . $userData->user_last_name . '</td>
              <td><a href="mailto:' . $userData->user_email . '">' . $userData->user_email . '</a></td>
              <td>' . $userGroupName . '</td>
              <td>' . $userLastLogin . '</td>
            </tr>';
        }

        // Close user list output
        $userListOut .= '</tbody></table></section></div>';

        // Output User List
        $this->data['userListOut'] = $userListOut;
      } else { // Output message if no results

        // Set userListOut message
        $this->data['userListOut'] = '<div class="csc-col csc-col12 csc-container"><p class="cs-body1">There are no users yet.</p></div>';
      }

      // Load Dashboard
      $this->load->view('users/index', $this->data, 'admin');
    }
  }
}
