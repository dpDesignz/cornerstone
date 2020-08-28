<?php

/**
 * Admin User Model
 *
 * @package Cornerstone
 */

class User extends ModelBase
{

  // Set the default properties
  private $uid;

  /**
   * Construct the User
   * No parameters required, nothing will be returned
   */
  public function __construct($cdbh, $option)
  {
    // Load the model base constructor
    parent::__construct($cdbh, $option);
  }

  /**
   * Set user ID
   *
   * @param int $userID ID of the user to set
   *
   * @return bool Will return TRUE if ID set, or FALSE if not
   */
  public function setUserID(int $userID)
  {

    // Check data is valid
    if (!empty($userID) && is_numeric($userID)) {

      // Set user ID
      $this->uid = $userID;

      // Return TRUE
      return TRUE;
    } // Data invalid. Return FALSE

    // Return FALSE
    return FALSE;
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

    // Run query
    $userIDResult = $this->conn->dbh->selecting(
      DB_PREFIX . "users",
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

    // Check data is valid
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Run query to find user key
      $userKeyNameResult = $this->conn->dbh->selecting(
        DB_PREFIX . "users",
        "user_password_key,
        user_first_name",
        where(
          eq("user_id", $this->uid)
        )
      );

      // Check if there are any results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($userKeyNameResult)) {

        // Return user data
        return $userKeyNameResult[0];;
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

    // Make sure data is valid
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Run query
      $emailResult = $this->conn->dbh->selecting(
        DB_PREFIX . "users",
        "user_email",
        where(
          eq("user_id", $this->uid)
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($emailResult)) {

        // Return user email
        return $emailResult[0]->user_email;
      } // No results. Return FALSE.

    } // Data invalid. Return FALSE.

    // Return FALSE
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
        $token_id = $this->conn->dbh->insert(
          'cs_password_reset',
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
            'user_name' => $userdata->user_first_name,
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
   * @return bool Will return TRUE if valid (and set uid), or FALSE if invalid
   */
  public function checkPasswordReset(string $selector)
  {

    // Check data is valid
    if (!empty($selector)) {

      // Set current dtm
      $now = new \DateTime();

      // Run query to find user key
      $resetResult = $this->conn->dbh->selecting(
        DB_PREFIX . "password_reset",
        "pwdreset_user_id",
        where(
          eq("pwdreset_selector", $selector, _AND),
          eq("pwdreset_status", "0", _AND),
          gt("pwdreset_expire", $now->format('Y-m-d H:i:s'))
        )
      );

      // Check if there are any results
      if ($this->conn->dbh->getNum_Rows() > 0) {

        // Set uid
        $this->uid = $resetResult[0]->pwdreset_user_id;

        // Return TRUE
        return TRUE;
      } // No results. Return FALSE.

    } // Data invalid. Return FALSE.

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
      $tokenResult = $this->conn->dbh->selecting(
        DB_PREFIX . "password_reset",
        "pwdreset_token",
        where(
          eq("pwdreset_selector", $selector, _AND),
          eq("pwdreset_status", "0", _AND)
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

    } // Data invalid. Return FALSE.

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

    // Make sure the data is valid
    if (!empty($this->uid) && is_numeric($this->uid) && !empty($password) && !empty($key)) {

      // Save new password to database
      $this->conn->dbh->update(
        DB_PREFIX . "users",
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

    } // Data invalid. Return FALSE.

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

    // Make sure the data is valid
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Delete session data for user
      $this->conn->dbh->delete(
        DB_PREFIX . 'session',
        eq('session_user_id', $this->uid)
      );

      // Delete cookie data for user
      $this->conn->dbh->delete(
        DB_PREFIX . 'auth_cookie',
        eq('cookie_user_id', $this->uid, _AND),
        eq('cookie_user_type', '1')
      );

      // Return TRUE
      return TRUE;
    } // Data invalid. Return FALSE.

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

    // Make sure the data is valid
    if (!empty($this->uid) && is_numeric($this->uid) && !empty($selector)) {

      // Set current dtm
      $now = new \DateTime();

      // Update password reset as successful in database
      $this->conn->dbh->update(
        DB_PREFIX . 'password_reset',
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

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Get list of users
   *
   * @param array $params Multiple parameters as required
   *
   * @return object Return object with list of users
   */
  public function listUsers($params = array())
  {

    // Set if counting or not
    $countResults = (!empty($params['count']) && $params['count'] == TRUE) ? TRUE : FALSE;

    // Build query
    $this->sql = "";
    $this->sqlItems = array();

    // Start SQL based on count results
    if ($countResults) {
      $this->sql .= "SELECT COUNT(u.user_id) AS total_results";
    } else {
      $this->sql .= "SELECT u.user_id,
      u.user_login,
      CONCAT(u.user_first_name, ' ', u.user_last_name) AS users_name,
      u.user_email,
      r.role_id,
      r.role_name,
      l.login_dtm";
    }

    // Set FROM
    $this->sql .= " FROM " . DB_PREFIX . "users AS u";

    // Set LEFT JOIN for the user roles
    $this->sql .= " LEFT JOIN " . DB_PREFIX . "roles AS r ON r.role_id = u.user_role_id";

    // Set LEFT JOIN for the last login
    $this->sql .= " LEFT JOIN (SELECT login_user_id, MAX(login_dtm) AS login_dtm FROM " . DB_PREFIX . "login_log WHERE login_user_type = login_status = '1' GROUP BY login_user_id) AS l ON l.login_user_id = u.user_id";

    // Set WHERE
    $this->sql .= " WHERE u.user_status = '1'";

    // Set search
    if (!empty($params['search'])) {
      $this->sqlItems[':searchTerm'] = "%" . strtolower($this->conn->dbh->escape($params['search'])) . "%";
      $this->sql .= " AND (
      (LOWER(u.user_login) LIKE :searchTerm) OR
      (LOWER(CONCAT(u.user_first_name, ' ' , u.user_last_name)) LIKE :searchTerm) OR
      (LOWER(u.user_last_name) LIKE :searchTerm) OR
      (LOWER(u.user_email) LIKE :searchTerm)
    )";
    }

    // Check for sort
    if (!$countResults && !empty($params['sort']) && !empty($params['order'])) {
      $this->sql .= " " . orderBy($params['sort'], $params['order']);
    } else if ($countResults) {
      $this->sql .= "";
    } else {
      $this->sql .= " " . orderBy("user_first_name", "ASC");
    }

    // Check for page number/limit
    if (!$countResults && !empty($params['limit'])) {
      // Check for page number
      if (!empty($params['page'])) {
        $offset = ($params['page'] - 1) * $params['limit'];
      } else {
        $offset = 0;
      }
      $this->sql .= " " . limit($params['limit'], $offset);
    }

    // Flush any previous results
    $this->conn->dbh->flush();

    // Run query
    $this->conn->dbh->query_prepared(
      $this->sql,
      $this->sqlItems
    );

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0) {

      // Check if wanting the count returned
      if ($countResults) {

        // Return the total count
        return $this->conn->dbh->get_row(NULL)->total_results;
        exit;
      } else { // Wanting to return data

        // Get the results
        $userResults = $this->conn->dbh->queryResult();

        // Return results
        return json_decode(json_encode(array('count' => $this->conn->dbh->getNum_Rows(), 'results' => json_decode(json_encode($userResults)), FALSE)), FALSE);
      }
    } // No results. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Get basic list of users
   * For use with assigning scripts
   *
   * @param array $params Multiple parameters as required
   *
   * @return object Return object with list of users
   */
  public function listUsersBasic()
  {

    // Run query to find users
    $usersResults = $this->conn->dbh->selecting(
      DB_PREFIX . "users",
      "user_id,
      user_display_name,
      user_first_name,
      user_last_name,
      CONCAT(user_first_name, ' ', user_last_name) AS users_name,
      user_email"
    );

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($usersResults)) {

      // Return result
      return $usersResults;
    } // No results. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Add User
   *
   * @param string $login The username of the user
   * @param string $displayName The display name of the user
   * @param string $password The users generated password
   * @param string $passwordKey The users generated password key
   * @param string $email The email of the user
   * @param string $firstName The first name of the user
   * @param string $lastName The last name of the user
   * @param int $roleID The ID of the role the user is assigned to
   * @param int $authRqd Set if the user is required to use 2FA
   *
   * @return bool|int Will return FALSE if failed or inserted ID if successful.
   */
  public function addUser(
    string $login,
    string $displayName,
    string $password,
    string $passwordKey,
    string $email,
    string $firstName,
    string $lastName,
    int $roleID,
    int $authRqd
  ) {

    // Add data
    $this->conn->dbh->insert(
      DB_PREFIX . "users",
      array(
        'user_login' => $login,
        'user_display_name' => $displayName,
        'user_password' => $password,
        'user_password_key' => $passwordKey,
        'user_email ' => $email,
        'user_first_name' => $firstName,
        'user_last_name' => $lastName,
        'user_role_id' => $roleID,
        'user_auth_rqd' => $authRqd,
        'user_status' => '1',
        'user_created_dtm' => date('Y-m-d H:i:s')
      )
    );

    // Check if added successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Return new ID
      return $this->conn->dbh->getInsert_Id();
    } // Unable to add. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  #####################
  ####    ROLES    ####
  #####################

  /**
   * Get list of user roles
   *
   * @return object Return object with list of user roles
   */
  public function listUserRoles()
  {

    // Run query to find data
    $roleResults = $this->conn->dbh->selecting(
      DB_PREFIX . "roles",
      "role_id,
      role_name",
      orderBy("role_name", "ASC")
    );

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($roleResults)) {

      // Return result
      return $roleResults;
    } // No results. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Get role name
   *
   * @param int $roleID ID of user role
   *
   * @return string Return string of users role name
   */
  public function getRoleName(int $roleID)
  {

    // Make sure the data is valid
    if (!empty($roleID) && is_numeric($roleID)) {

      // Run query to find users group
      $roleResults = $this->conn->dbh->selecting(
        DB_PREFIX . "roles",
        "role_name",
        where(
          eq("role_id", $roleID)
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($roleResults)) {

        // Return first result
        return $roleResults[0]->role_id;
      } // No results. Return FALSE.

    } // Data invalid. Return FALSE.

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

    // Make sure the data is valid
    if (!empty($userID) && is_numeric($userID)) {

      // Run query to find users last successful login
      $loginResults = $this->conn->dbh->selecting(
        DB_PREFIX . "login_log",
        "login_dtm",
        where(
          eq("login_user_id", $userID, _AND),
          eq("login_status", "1")
        ),
        orderBy("login_dtm", "DESC"),
        limit(1)
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($loginResults)) {

        // Return first result
        return $loginResults[0]->login_dtm;
      } // No results. Return FALSE.

    } // Data invalid. Return FALSE.

    // Return FALSE
    return false;
  }
}
