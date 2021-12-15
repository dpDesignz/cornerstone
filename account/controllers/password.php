<?php

use Cornerstone\SendMail;

class Password extends Cornerstone\Controller
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
   * Forgot Password Page
   */
  public function forgot()
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
          $this->data['email'] = htmlspecialchars(trim($_POST['email']));
          if (empty($this->data['email'])) {
            // Data not set. Return error
            $this->data['err']['email'] = 'Please enter your email address';
          } else if (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            // Data isn't a valid email address. Return error
            $this->data['err']['email'] = 'Please enter a valid email address';
          }
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          flashMsg('account_forgot_pwd', '<strong>Error</strong> There was an error with your email address. Please try again', 'warning');
        }
      } else { // If data not set, set errors

        $this->data['err']['email'] = 'Please enter your email address';
      }

      // Check if any errors are set
      if (empty($this->data['err'])) {
        // Validated

        // Check if customer email exists
        if ($this->accountCoreModel->checkUserByEmail($this->data['email'])) {
          // User exists

          // Load the password model
          $this->passwordModel = $this->load->model('accpassword', 'account');

          // check if able to set user ID from email address
          if ($this->passwordModel->setUserIDFromEmail($this->data['email'])) {
            // Customer ID set

            // Set password reset request
            $resetObject = $this->passwordModel->setPasswordReset();

            // Check reset worked
            if ($resetObject !== FALSE) {

              // Create the options
              $actionURL = 'account/password/new/' . urlencode($resetObject->selector) . '/' . urlencode($resetObject->token);
              $resetExpireDtm = new \DateTime($resetObject->expires);
              if (!empty($resetObject->user_agent) || $resetObject->user_agent != '') {
                $browserInfo = 'For security, this relates to a reset request from a device using ' . $resetObject->user_agent . '. ';
              } else {
                $browserInfo = '';
              }

              // Load SendMail Class
              $sendMail = new SendMail();

              // Set the HTML message from the template
              if ($message = $sendMail->createEmailTemplate(
                'password-reset.html',
                array(
                  'name' => $resetObject->user_name,
                  'action_url' => get_site_url($actionURL),
                  'expire_dtm' => $resetExpireDtm->format('g:ia \o\n l, jS M Y T'),
                  'browser_security' => $browserInfo
                )
              )) {

                // Set the plaintext message from the template
                if (!$plainEmail = $sendMail->createEmailTemplate(
                  'password-reset.txt',
                  array(
                    'name' => $resetObject->user_name,
                    'action_url' => get_site_url($actionURL),
                    'expire_dtm' => $resetExpireDtm->format('g:ia \o\n l, jS M Y T'),
                    'browser_security' => $browserInfo
                  )
                )) {
                  // Unable to set plain text message. Continue on to error
                  $plainEmail = 'Sorry, there was an error generating a copy of this email in plain text. Please try reset your password again to request a new link.\r\n\r\n' . get_site_url();
                }

                // Send user their authorization email
                $emailSubject = 'Reset your ' . $this->optn->get('site_name') . ' password';
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
                  flashMsg('account_login', 'Your password reset email has been sent. Please check your email account for the link to reset your password.', 'success');
                  redirectTo('account/login');
                  exit;
                } // Unable to send email. Continue on to error

              } // Unable to set HTML message. Continue on to error

            } // Password reset request failed. Continue to error.

          } // Unable to set customer ID from email. Continue to error.

        } else { // No user exists, send reset error email

          // Create the options
          $actionURL = 'account/password/forgot';
          // Get browser info if browser tracking enabled
          if ($this->optn->get('browser_tracking')) {
            $browser = new \WhichBrowser\Parser(getallheaders());
            // Set browser "User Agent"
            $browserInfo = " from a device using " . $browser->toString();
          } else {
            $browserInfo = "";
          }

          // Load SendMail Class
          $sendMail = new SendMail();

          // Set the HTML message from the template
          if ($message = $sendMail->createEmailTemplate(
            'password-reset-help.html',
            array(
              'email_address' => $this->data['email'],
              'action_url' => get_site_url($actionURL),
              'browser_security' => $browserInfo
            )
          )) {

            // Set the plaintext message from the template
            if ($plainEmail = $sendMail->createEmailTemplate(
              'password-reset-help.txt',
              array(
                'email_address' => $this->data['email'],
                'action_url' => get_site_url($actionURL),
                'browser_security' => $browserInfo
              )
            )) {
              // Unable to set plain text message. Continue on to error
              $plainEmail = 'Sorry, there was an error generating a copy of this email in plain text. Please try reset your password again to request a new link.\r\n\r\n' . get_site_url();
            }

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
              flashMsg('account_login', 'Your password reset email has been sent. Please check your email account for the link to reset your password', 'success');
              redirectTo('account/login');
              exit;
            } // Unable to send email. Continue on to error

          } // Unable to set HTML message. Continue on to error

        }

        // Set error
        flashMsg('account_forgot_pwd', '<strong>Error</strong> There was an error processing your password reset request. Please try again.', 'warning');
      } // Errors were set. Continue on to view.
    } else { // else load the forgot password page

      // Set Breadcrumbs
      $this->data['breadcrumbs'][] = array(
        'text' => 'Forgot Password',
        'href' => get_site_url('account/password/forgot')
      );

      // Check for admin source
      $isAdminSource = (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], get_site_url() . 'admin/') !== FALSE) ? TRUE : FALSE;

      // Set return to login link
      if ($isAdminSource) {
        $this->data['login_link'] = get_site_url('admin/login');
      } else {
        $this->data['login_link'] = get_site_url('account/login');
      }
    }

    // Load view
    $this->load->view('password/forgot', $this->data, 'account');
    exit;
  }

  /**
   * New Password Page
   */
  public function new(...$params)
  {

    // Load the password model
    $this->passwordModel = $this->load->model('accpassword', 'account');

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
            flashMsg('account_forgot_pwd', '<strong>Error</strong> Sorry, your reset request seems to have glitched. Please request a new password reset link.', 'warning');

            // Redirect to forgot password page
            redirectTo('account/password/forgot');
            exit;
          }

          // Check if password reset is valid
          if (!$this->passwordModel->checkPasswordReset($this->data['selector'])) {
            // Password reset is not valid

            // Set error
            flashMsg('account_forgot_pwd', '<strong>Error</strong> Sorry, your reset request seems to have expired. Please request a new password reset link.', 'warning');

            // Redirect to forgot password page
            redirectTo('account/password/forgot');
            exit;
          }
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          flashMsg('account_new_pwd', '<strong>Error</strong> There was an error checking your request. Please try again', 'warning');
        }
      } else { // If data not set, set error and redirect to forgot password

        // Set error
        flashMsg('account_forgot_pwd', '<strong>Error</strong> Sorry, There was an issue processing your request. Please request a new password reset link.', 'danger');

        // Redirect to forgot password page
        redirectTo('account/password/forgot');
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

          // Check validation is all ok
          if (empty($this->data['password']) || !$uppercase || !$lowercase || !$number || strlen($this->data['password']) < 6 || strlen($this->data['password']) > 128) {

            // If password not set or doesn't match the requirements, return error
            $this->data['err']['password'] = 'Your password must be at least six characters long and contain at least one upper case letter and one number.';
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
          flashMsg('account_new_pwd', '<strong>Error</strong> There was an error checking your new password. Please try again', 'warning');
        }
      } else { // If data not set, set errors

        $this->data['err']['password'] = 'Please enter your new password';
        $this->data['err']['confirm-password'] = 'Please confirm your new password';
      }

      // Check if any errors are set
      if (empty($this->data['err'])) {
        // Validated

        // Check token validates
        if ($this->passwordModel->checkResetToken($this->data['selector'], $this->data['token'])) {
          // Token is valid

          // Generate new key
          $newKey = get_crypto_key();

          // Hash password with new key
          $password_encrypted = password_hash($this->data['password'] . $newKey, PASSWORD_DEFAULT);

          // Update users password with new key
          if ($this->passwordModel->setNewPassword($password_encrypted, $newKey)) {

            // Invalidate all existing sessions and cookies for the user
            $this->passwordModel->invalidateLogins();

            // Mark password reset as successful
            $this->passwordModel->markPasswordReset($this->data['selector']);

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
              $sendMail = new SendMail();

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

                  // Redirect to login page with success
                  flashMsg('account_login', 'Your password has been changed!');
                  redirectTo($actionURL);
                  exit;
                } // Unable to send email. Continue on to error

              } // Unable to set HTML message. Continue on to error

            } // Unable to get user email. Continue on to error

          } // Failed to update password. Continue on to error

        } else { // Token invalid. Redirect to get new link

          // Set error
          flashMsg('account_forgot_pwd', '<strong>Error</strong> Sorry, There was an issue with your token. Please request a new password reset link.', 'danger');

          // Redirect to forgot password page
          redirectTo('account/password/forgot');
          exit;
        }

        // Set error
        flashMsg('account_new_pwd', '<strong>Error</strong> Sorry, there was an issue changing your password. Please try again.', 'warning');
      } // Errors were set. Continue on to view.
    } else { // else load the new password page

      // Get selector
      $this->data['selector'] = (!empty($params[0]) && is_string($params[0]) && strlen($params[0]) == 34) ? $params[0] : '';

      // Get token
      $this->data['token'] = (!empty($params[1]) && is_string($params[1]) && strlen($params[1]) == 16) ? $params[1] : '';

      // Check if either selector or token are empty
      if (empty($this->data['selector']) || empty($this->data['token'])) {
        // Data is empty.

        // Set error
        flashMsg('account_forgot_pwd', '<strong>Error</strong> Sorry, something went wrong.<br>Please request a new password reset link.', 'danger');

        // Redirect to forgot password page
        redirectTo('account/password/forgot');
        exit;
      } else if (!$this->passwordModel->checkPasswordReset($this->data['selector'])) { // Check if password reset is valid
        // Password reset is not valid

        // Set error
        flashMsg('account_forgot_pwd', '<strong>Error</strong> Sorry, that request seems to have expired. Please request a new password reset link.', 'danger');

        // Redirect to forgot password page
        redirectTo('account/password/forgot');
        exit;
      } else { // Data is set. Load view

        // Set Breadcrumbs
        $this->data['breadcrumbs'][] = array(
          'text' => 'New Password',
          'href' => get_site_url('account/password/new')
        );
      }
    }

    // Load view
    $this->load->view('password/new', $this->data, 'account');
    exit;
  }
}
