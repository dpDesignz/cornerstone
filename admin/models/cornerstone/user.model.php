<?php

/**
 * Admin User Model
 *
 * @package Cornerstone
 */

class User
{

  // Set the default properties
  private $conn;
  private $optn;
  private $uid;

  /**
   * Construct the User
   * No parameters required, nothing will be returned
   */
  public function __construct($option)
  {

    // Create a database connection
    $this->conn = new CornerstoneDBH;
    // Set the options
    $this->optn = $option;
  }

  /**
   * Find user by email
   *
   * @param string $email: Email address to check
   * @param int $active: Check if user is active. Defaults to 1 (optional)
   *
   * @return bool Will return TRUE if user found, or FALSE if not found
   */
  public function findUserByEmail(string $email, int $active = 1)
  {

    // Create array of objects to bind
    $bind = [];
    $bind[':email'] = $email; // Bind udata
    // If active, check
    if ($active) {
      $bind[':status'] = $active;
      $addToSql = ' AND user_status=:status';
    } else {
      $addToSql = '';
    }

    // Run query to find user by email
    $this->conn->dbh->query_prepared("SELECT * FROM " . DB_PREFIX . "users WHERE user_email=:email" . $addToSql, $bind);

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0) {

      // Return True
      return TRUE;
    } else {

      // Return False
      return FALSE;
    }
  }

  /**
   * Set user ID by Email
   *
   * @param string $email: Email address to set user ID for
   *
   * @return bool Will return TRUE if user found and ID set, or FALSE if not found
   */
  public function setUserIDFromEmail(string $email)
  {

    // Run query to find user id by email
    $this->conn->dbh->query_prepared("SELECT user_id FROM " . DB_PREFIX . "users WHERE user_email=:email", array(':email' => $email));

    // Check if there are any results
    if ($this->conn->dbh->getNum_Rows() > 0) {

      // Get the data
      $userData = $this->conn->dbh->get_row(NULL);

      // Set user ID
      $this->uid = $userData->user_id;

      // Return True
      return TRUE;
    } else {

      // Return False
      return FALSE;
    }
  }

  /**
   * Get user Key and user First name
   *
   * (No params)
   *
   * @return object|bool Will return key and first name if user found, or FALSE if not found
   */
  private function getUserKeyName()
  {

    // Check uid set
    if (!empty($this->uid)) {

      // Run query to find user key
      $this->conn->dbh->query_prepared("SELECT user_password_key, user_first_name FROM " . DB_PREFIX . "users WHERE user_id=:uid", array(':uid' => $this->uid));

      // Check if there are any results
      if ($this->conn->dbh->getNum_Rows() > 0) {

        // Return user data
        return $this->conn->dbh->get_row(NULL);
      } // No results. Return FALSE.

    } // uid not set. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Get user email details
   *
   * @return string Return string with user email address
   */
  public function getUserEmail()
  {

    // Set $userID
    $userID = $this->uid;

    // Make sure the $userID is a number
    if (!empty($userID) && is_numeric($userID)) {

      // Run query to find active user
      $this->conn->dbh->query_prepared("SELECT user_email FROM " . DB_PREFIX . "users WHERE user_id=:uid", [":uid" => $userID]);

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0) {

        // Get the data
        $userData = $this->conn->dbh->get_row(NULL);

        // Return user email
        return $userData->user_email;
      } // No results. Return FALSE.

    } // $userID is empty or not a number. Return FALSE.

    // Return false if $userID is empty or not a number
    return false;
  }

  /**
   * Set Password Reset Request
   *
   * @return bool|object Will return FALSE if there is an error, else will return an object with the user name, reset selector, token, user agent, and expiry
   */
  public function setPasswordReset()
  {

    // Set $userID
    $userID = $this->uid;

    // Make sure the $userID is a number
    if (!empty($userID) && is_numeric($userID)) {

      // Get and set selector for link
      $selector = get_crypto_key(34);

      // Get and set random token
      $random_token = get_crypto_key(16);

      // Check able to get user key
      if ($userdata = $this->getUserKeyName()) {
        // User key obtained

        // Hash random token
        $random_token_hash = password_hash($random_token . $userdata->user_password_key, PASSWORD_DEFAULT);

        // Get browser info if browser tracking enabled
        if ($this->optn->get('browser_tracking')) {
          $browser = new \WhichBrowser\Parser(getallheaders());
          // Set browser "User Agent"
          $browser = $browser->toString();
        } else {
          $browser = "";
        }

        // Set expiry datetime
        $expireDtm = new \DateTime();
        $expireDtm->modify('+' . (int) $this->optn->get("password_reset_expire") . ' seconds');

        // Save reset request in database
        $token_id = $this->conn->dbh->insert('cs_password_reset', array('pwdreset_user_id' => $userID, 'pwdreset_selector' => $selector, 'pwdreset_token' => $random_token_hash, 'pwdreset_request_ip' => $_SERVER['REMOTE_ADDR'], 'pwdreset_user_agent' => $browser, 'pwdreset_dtm ' => date('Y-m-d H:i:s'), 'pwdreset_expire ' => $expireDtm->format('Y-m-d H:i:s')));

        // Check if token added to the database
        if ($token_id != false) {

          // Return array with user name, reset selector, token, user agent, and expiry
          return (object) array('user_name' => $userdata->user_first_name, 'selector' => $selector, 'token' => $random_token, 'user_agent' => $browser, 'expires' => $expireDtm->format('Y-m-d H:i:s'));
        } // Token failed to save. Return FALSE.

      } // Unable to get user key and name. Return FALSE

    } // $userID is empty or not a number. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Check Password Reset is valid
   *
   * @param string $selector Selector to check
   *
   * @return bool Will return TRUE if valid (and set uid), or FALSE if invalid
   */
  public function checkPasswordReset(string $selector)
  {

    // Check selector set
    if (!empty($selector)) {

      // Set current dtm
      $now = new \DateTime();

      // Run query to find user key
      $this->conn->dbh->query_prepared("SELECT pwdreset_user_id FROM " . DB_PREFIX . "password_reset WHERE pwdreset_selector=:selector AND pwdreset_status=:status AND pwdreset_expire > :currentDtm", array(':selector' => $selector, ':status' => '0', ':currentDtm' => $now->format('Y-m-d H:i:s')));

      // Check if there are any results
      if ($this->conn->dbh->getNum_Rows() > 0) {

        // Get id
        $userData = $this->conn->dbh->get_row(NULL);

        // Set uid
        $this->uid = $userData->pwdreset_user_id;

        // Return TRUE
        return TRUE;
      } // No results. Return FALSE.

    } // $selector not set. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Check Password Reset Token is valid
   *
   * @param string $selector Selector to check
   * @param string $token token to check
   *
   * @return bool Will return TRUE if valid, or FALSE if invalid
   */
  public function checkResetToken(string $selector, string $token)
  {

    // Check selector and token set
    if (!empty($selector) && !empty($token)) {

      // Run query to get token to check
      $this->conn->dbh->query_prepared("SELECT pwdreset_token FROM " . DB_PREFIX . "password_reset WHERE pwdreset_selector=:selector AND pwdreset_status=:status", array(':selector' => $selector, ':status' => '0'));

      // Check if there are any results
      if ($this->conn->dbh->getNum_Rows() > 0) {

        // Get token
        $resetToken = $this->conn->dbh->get_row(NULL);

        // Get user key
        if ($userData = $this->getUserKeyName()) {

          // Check token verifies
          if (password_verify($token . $userData->user_password_key, $resetToken->pwdreset_token)) {

            // Return TRUE
            return TRUE;
          } // Token doesn't match. Return FALSE.

        } // Unable to get key. Return FALSE.

      } // No results. Return FALSE.

    } // $selector or $token not set. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Set New Password Reset
   *
   * @param string $password Users new hashed password
   * @param string $key Users new password key
   *
   * @return bool Will return FALSE if there is an error, else will return TRUE
   */
  public function setNewPassword(string $password, string $key)
  {

    // Set $userID
    $userID = $this->uid;

    // Make sure the $userID is a number
    // and make sure $password and $key are set
    if (!empty($userID) && is_numeric($userID) && !empty($password) && !empty($key)) {

      // Save new password to database
      $this->conn->dbh->update(DB_PREFIX . "users", array('user_password' => $password, 'user_password_key' => $key), eq('user_id', $userID));

      // Check if password updated in the database
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // New password failed to save. Return FALSE.

    } // $userID is empty or not a number. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Invalidate user logins
   *
   * @return bool Will return FALSE if there is an error, else will return TRUE
   */
  public function invalidateLogins()
  {

    // Set $userID
    $userID = $this->uid;

    // Make sure the $userID is a number and make sure $selector is set
    if (!empty($userID) && is_numeric($userID) && !empty($selector)) {

      // Delete session data for user
      $this->conn->dbh->delete(DB_PREFIX . 'session', eq('session_user_id', $userID));

      // Delete cookie data for user
      $this->conn->dbh->delete(DB_PREFIX . 'auth_cookie', eq('cookie_user_id', $userID, _AND), eq('cookie_user_type', '1'));

      // Return TRUE
      return TRUE;
    } // $userID is empty or not a number. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Mark Password Reset Successful
   *
   * @param string $selector Selector for password reset
   *
   * @return bool Will return FALSE if there is an error, else will return TRUE
   */
  public function markPasswordReset(string $selector)
  {

    // Set $userID
    $userID = $this->uid;

    // Make sure the $userID is a number and make sure $selector is set
    if (!empty($userID) && is_numeric($userID) && !empty($selector)) {

      // Set current dtm
      $now = new \DateTime();

      // Update password reset as successful in database
      $this->conn->dbh->update(DB_PREFIX . 'password_reset', array('pwdreset_status' => '1', 'pwdreset_success_dtm' => $now->format('Y-m-d H:i:s')), eq('pwdreset_user_id', $userID, _AND), eq('pwdreset_selector', $selector));

      // Check if password updated in the database
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // Update failed to save. Return FALSE.

    } // $userID is empty or not a number. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Get list of users
   *
   * @return object Return object with list of users
   */
  public function listUsers()
  {

    // Run query to find active users
    $results = $this->conn->dbh->selecting(DB_PREFIX . "users", "user_id, user_login, user_first_name, user_last_name, user_email, user_group_id", where(eq("user_status", "1")), orderBy("user_first_name", "ASC"));

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0) {

      // Return results
      return $results;
    } // No results. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Get group display name
   *
   * @param int $groupID ID of user group
   *
   * @return string Return string of users group name
   */
  public function getGroupName(int $groupID)
  {

    // Make sure the $groupID is a number and isn't empty
    if (!empty($groupID) && is_numeric($groupID)) {

      // Run query to find users group
      $results = $this->conn->dbh->selecting(DB_PREFIX . "user_groups", "ugroup_display", where(eq("ugroup_id", $groupID)));

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0) {

        // Return first result
        return $results[0]->ugroup_display;
      } // No results. Return FALSE.

    } // $groupID was empty or not numeric. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Get last successful login for user
   *
   * @param int $userID ID of user to check
   *
   * @return string Return string of users last login timestamp
   */
  public function getLastLogin(int $userID)
  {

    // Make sure the $userID is a number and isn't empty
    if (!empty($userID) && is_numeric($userID)) {

      // Run query to find users last successful login
      $results = $this->conn->dbh->selecting(DB_PREFIX . "login_log", "login_dtm", where(eq("login_user_id", $userID, _AND), eq("login_status", "1")), orderBy("login_dtm", "DESC"), limit(1));

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0) {

        // Return first result
        return $results[0]->login_dtm;
      } // No results. Return FALSE.

    } // $userID was empty or not numeric. Return FALSE.

    // Return FALSE
    return false;
  }
}
