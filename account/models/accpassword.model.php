<?php

/**
 * ME Account Password Model
 *
 * @package Cornerstone
 * @subpackage Mission Equine
 */

use function ezsql\functions\{
  selecting,
  inserting,
  updating,
  deleting,
  where,
  eq,
  gt
};

class AccPassword extends ModelBase
{

  // Set the default properties
  private $uid;

  /**
   * Construct the model
   */
  public function __construct($cdbh, $option)
  {
    // Load the model base constructor
    parent::__construct($cdbh, $option);
    $this->conn->dbh->tableSetup('users', DB_PREFIX);
  }

  /**
   * Set user ID
   *
   * @param int $id ID of user you want to set
   */
  public function setUserID(int $id)
  {
    $this->uid = $id;
  }

  /**
   * Set user ID by Email
   *
   * @param string $email Email address to set user ID for
   *
   * @return bool Will return TRUE if user found and ID set, or FALSE if not
   */
  public function setUserIDFromEmail(string $email)
  {

    // Run query
    $this->conn->dbh->tableSetup('users', DB_PREFIX);
    $userIDResult = selecting(
      "user_id",
      where(
        eq("user_email", $email)
      )
    );

    // Check if there are any results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($userIDResult)) {

      // Set user ID
      $this->uid = $userIDResult[0]->user_id;

      // Return True
      return TRUE;
      exit;
    } else {

      // Return False
      return FALSE;
      exit;
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

    // Check data is valid
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Run query to find user key
      $this->conn->dbh->tableSetup('users', DB_PREFIX);
      $userKeyNameResult = selecting(
        "user_password_key,
        user_first_name",
        where(
          eq("user_id", $this->uid)
        )
      );

      // Check if there are any results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($userKeyNameResult)) {

        // Return user data
        return $userKeyNameResult[0];
      } // No results. Return FALSE.

    } // Data invalid. Return FALSE.

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

    // Make sure the ID is valid
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Run query
      $this->conn->dbh->tableSetup('users', DB_PREFIX);
      $emailResult = selecting(
        "user_email",
        where(
          eq("user_id", $this->uid)
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($emailResult)) {

        // Return customer email
        return $emailResult[0]->user_email;
      } // No results. Return FALSE.

    } // ID invalid. Return FALSE.

    // Return false
    return false;
  }

  /**
   * Set Password Reset Request
   *
   * @return bool|object Will return FALSE if there is an error, else will return an object with the user name, reset selector, token, user agent, and expiry
   */
  public function setPasswordReset()
  {

    // Make sure the data is valid
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Get and set selector for link
      $selector = get_crypto_key(34);

      // Get and set random token
      $random_token = get_crypto_key(16);

      // Check able to get user key
      if ($userData = $this->getUserKeyName()) {
        // User key obtained

        // Hash random token
        $random_token_hash = password_hash($random_token . $userData->user_password_key, PASSWORD_DEFAULT);

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
        $this->conn->dbh->tableSetup('password_reset', DB_PREFIX);
        $token_id = inserting(
          array(
            'pwdreset_user_id' => $this->uid,
            'pwdreset_selector' => $selector,
            'pwdreset_token' => $random_token_hash,
            'pwdreset_request_ip' => $_SERVER['REMOTE_ADDR'],
            'pwdreset_user_agent' => $browser,
            'pwdreset_dtm ' => date('Y-m-d H:i:s'),
            'pwdreset_expire ' => $expireDtm->format('Y-m-d H:i:s')
          )
        );

        // Check if token added to the database
        if ($this->conn->dbh->affectedRows() > 0 && $token_id != false) {

          // Return array with user name, reset selector, token, user agent, and expiry
          return (object) array(
            'user_name' => $userData->user_first_name,
            'selector' => $selector,
            'token' => $random_token,
            'user_agent' => $browser,
            'expires' => $expireDtm->format('Y-m-d H:i:s')
          );
        } // Token failed to save. Return FALSE.

      } // Unable to get user key and name. Return FALSE

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Check Password Reset is valid
   *
   * @param string $selector Selector to check
   *
   * @return bool Will return TRUE if valid (and set customerID), or FALSE if invalid
   */
  public function checkPasswordReset(string $selector)
  {

    // Check selector set
    if (!empty($selector)) {

      // Set current dtm
      $now = new \DateTime();

      // Run query to find customer key
      $this->conn->dbh->tableSetup('password_reset', DB_PREFIX);
      $resetResult = selecting(
        "pwdreset_user_id",
        where(
          eq("pwdreset_selector", $selector),
          eq("pwdreset_status", "0"),
          gt("pwdreset_expire", $now->format('Y-m-d H:i:s'))
        )
      );

      // Check if there are any results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($resetResult)) {

        // Set user ID
        $this->uid = $resetResult[0]->pwdreset_user_id;

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
      $this->conn->dbh->tableSetup('password_reset', DB_PREFIX);
      $tokenResult = selecting(
        "pwdreset_token",
        where(
          eq("pwdreset_selector", $selector),
          eq("pwdreset_status", "0")
        )
      );

      // Check if there are any results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($tokenResult)) {

        // Get user key
        if ($userData = $this->getUserKeyName()) {

          // Check token verifies
          if (password_verify($token . $userData->user_password_key, $tokenResult[0]->pwdreset_token)) {

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

    // Make sure the ID is valid
    // and make sure $password and $key are set
    if (!empty($this->uid) && is_numeric($this->uid) && !empty($password) && !empty($key)) {

      // Save new password to database
      $this->conn->dbh->tableSetup('users', DB_PREFIX);
      updating(
        array(
          'user_password' => $password,
          'user_password_key' => $key
        ),
        eq('user_id', $this->uid)
      );

      // Check if password updated in the database
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // New password failed to save. Return FALSE.

    } // $ID invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Invalidate customer logins
   *
   * @return bool Will return FALSE if there is an error, else will return TRUE
   */
  public function invalidateLogins()
  {

    // Make sure the ID is valid
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Delete session data for user
      $this->conn->dbh->tableSetup('session', DB_PREFIX);
      deleting(
        eq('session_user_id', $this->uid)
      );

      // Delete cookie data for user
      $this->conn->dbh->tableSetup('auth_cookie', DB_PREFIX);
      deleting(
        eq('cookie_user_id', $this->uid)
      );

      // Return TRUE
      return TRUE;
    } // ID invalid. Return FALSE.

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

    // Make sure the ID is valid and make sure $selector is set
    if (!empty($this->uid) && is_numeric($this->uid) && !empty($selector)) {

      // Set current dtm
      $now = new \DateTime();

      // Update password reset as successful in database
      $this->conn->dbh->tableSetup('password_reset', DB_PREFIX);
      updating(
        array(
          'pwdreset_status' => '1',
          'pwdreset_success_dtm' => $now->format('Y-m-d H:i:s')
        ),
        eq('pwdreset_user_id', $this->uid, _AND),
        eq('pwdreset_selector', $selector)
      );

      // Check if password updated in the database
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // Update failed to save. Return FALSE.

    } // Data is invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }
}
