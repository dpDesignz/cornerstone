<?php

/**
 * Admin User Authentication Model
 *
 * @package Cornerstone
 */

class UserAuth extends ModelBase
{

  // Set the default properties
  private $uid;
  public $remember;

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
   * Find user by username or email
   *
   * @param string $udata Username or email address to check
   * @param int $active [optional] Check if user is active. Defaults to 1.
   *
   * @return bool Will return TRUE if user found, or FALSE if not found
   */
  public function findUserByEmail(string $udata, int $active = 1)
  {

    // Build query
    $this->sql = array();
    $this->whereArray = array();

    // Set where login
    $this->whereArray[] = grouping(
      eq("user_login", $udata, _OR),
      eq("user_email", $udata)
    );

    // Check for showing inactive
    if ($active) {
      $this->whereArray[] = eq("user_status", "1");
    }

    // Combine where
    if (!empty($this->whereArray)) {
      $this->sql[] = where(...$this->whereArray);
    }

    // Run query to find active user
    $userData = $this->conn->dbh->selecting(
      DB_PREFIX . "users",
      "user_id",
      ...$this->sql
    );

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($userData)) {

      // Return True
      return TRUE;
    } else {

      // Return False
      return FALSE;
    }
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
   * Set uid
   *
   * @param int $id ID of user you want to set
   */
  public function setUID(int $id)
  {
    $this->uid = $id;
  }

  /**
   * Log user in
   *
   * @param string $userData User entered data (Normally username or email)
   * @param string $userPassword User entered password
   *
   * @return int Will return "0" if failed, "1" if successful, "2" if successful but requires authorization, "3" if maximum login attempts reached.
   */
  public function loginUser(string $userData, string $userPassword)
  {

    // Try get user data
    try {

      // Get user data
      $userData = $this->conn->dbh->selecting(
        DB_PREFIX . "users",
        "user_id,
        user_password,
        user_password_key,
        user_auth_rqd",
        where(
          grouping(
            eq("user_login", $userData, _OR),
            eq("user_email", $userData)
          ),
          eq("user_status", "1")
        )
      );
    } catch (\PDOException $ex) {

      // Log error if any
      error_log($ex->getMessage(), 0);

      // Return failed
      return 0;
    }

    // If results returned, continue
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($userData)) {

      // Get the data
      $userData = $userData[0];

      // Set user ID to allow other methods to work
      $this->uid = $userData->user_id;

      // Check if max login attempts not reached
      if ($this->checkFailedLogins()) {

        // Check if password is valid
        if (password_verify($userPassword . $userData->user_password_key, $userData->user_password)) {

          // If authorization required, return "2"
          if ($userData->user_auth_rqd) {

            // Return "2"
            return 2;
          } else { // Authorization required. Return "1"

            // Return success
            return 1;
          }
        } // Password isn't valid, return "0"

      } else { // Maximum attempts reached. Return "3"

        // Return "3"
        return 3;
      }
    } // User didn't exist, return "0"

    // Return failed
    return 0;
  }

  /**
   * Check Failed Logins
   *
   * (No params)
   *
   * @return int Will return TRUE if limit isn't reached, or FALSE if limit reached
   */
  public function checkFailedLogins()
  {

    // Make sure data is valid
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Get options from database to reduce calls to database
      $options = $this->optn->get(array('password_reset_expire', 'max_logins'));

      // Set limit time using 'password_reset_expire' value from the database
      $max_time_check = new DateTime();
      $max_time_check->modify('-' . $options->password_reset_expire . ' seconds');

      // Run query to check of locked login set within defined time
      $this->conn->dbh->selecting(
        DB_PREFIX . "login_log",
        "login_id",
        where(
          gt("login_dtm", $max_time_check->format('Y-m-d H:i:s')),
          eq("login_status", "3"),
          eq("login_user_id", $this->uid),
          eq("login_user_type", "1")
        )
      );

      // Check if locked login set
      if ($this->conn->dbh->getNum_Rows() < 1) {

        // Run query to find if limit is reached within defined time
        $this->conn->dbh->selecting(
          DB_PREFIX . "login_log",
          "login_id",
          where(
            gt("login_dtm", $max_time_check->format('Y-m-d H:i:s')),
            neq("login_status", "1"),
            eq("login_user_id", $this->uid),
            eq("login_user_type", "1")
          )
        );

        // Check if more than `max_logins` results
        if ($this->conn->dbh->getNum_Rows() < $options->max_logins) {

          // Return TRUE
          return TRUE;
        } else { // Max attempts reached. Return FALSE.

          // Set login log to "3" for max attempts
          $this->setLoginLog(3);
        }
      } // Locked login set. Return FALSE.

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Get time that login lock will be lifted
   *
   * (No params)
   *
   * @return string|bool Will return timestamp if found, or FALSE if not
   */
  public function getLoginLock()
  {

    // Make sure data is valid
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Run query to find the last locked login_dtm
      $lockResult = $this->conn->dbh->selecting(
        DB_PREFIX . "login_log",
        "login_dtm",
        where(
          eq("login_status", "3"),
          eq("login_user_id", $this->uid),
          eq("login_user_type", "1")
        ),
        orderBy("login_dtm", "DESC"),
        limit(1)
      );

      // Check if timestamp exists
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($lockResult)) {

        // Return timestamp
        return $lockResult[0]->login_dtm;
      } // Unable to find timestamp. Return FALSE.

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Set Login Log
   *
   * @param int $status Status of login. 0 = Failed, 1 = Success. Defaults to 0 (Failed).
   *
   * @return bool Will return FALSE if failed or TRUE if successful.
   */
  public function setLoginLog(int $status = 0)
  {

    // Make sure the data is valid
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Log user login into `cs_login_log`
      $this->conn->dbh->insert(
        DB_PREFIX . "login_log",
        array(
          'login_user_id' => $this->uid,
          'login_user_type' => '1',
          'login_dtm' => date('Y-m-d H:i:s'),
          'login_ip_address' => $_SERVER['REMOTE_ADDR'],
          'login_status ' => $status
        )
      );

      // Check if added to log successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // Unable to add login to log. Return FALSE.

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Set the authorization token for 2FA if option set in `cs_options` and `user_auth_rqd` set on user
   *
   * @param bool $remember: Set true if you want to set the Authorization to set the cookie on verification
   *
   * @return bool|object Will return FALSE if there is an error, else will return an object with the authorization selector, token, user agent, and expiry
   */
  public function setAuthorization(bool $remember = FALSE)
  {

    // Make sure the data is valid
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Delete all other authorization codes for this user
      $this->conn->dbh->delete(
        DB_PREFIX . 'authorization',
        eq('auth_user_id', $this->uid)
      );

      // Get and set selector for link
      $selector = get_crypto_key(16);

      // Get and set random token
      $random_token = get_pin(6);

      // Hash random token
      $random_token_hash = password_hash($random_token, PASSWORD_DEFAULT);

      // Set the cookie remember
      $remember = ($remember) ? 1 : 0;

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
      $expireDtm->modify('+' . (int) $this->optn->get("auth_expire") . ' seconds');

      // Save authorization token to database
      $token_id = $this->conn->dbh->insert(
        DB_PREFIX . 'authorization',
        array(
          'auth_user_id' => $this->uid,
          'auth_selector' => $selector,
          'auth_token' => $random_token_hash,
          'auth_remember' => $remember,
          'auth_ip_address' => $_SERVER['REMOTE_ADDR'],
          'auth_user_agent' => $browser,
          'auth_dtm ' => date('Y-m-d H:i:s'),
          'auth_expire ' => $expireDtm->format('Y-m-d H:i:s')
        )
      );

      // Check if token added to the database
      if ($token_id != false) {

        // Check if session set and start if it isn't
        if (session_id() == '') {
          session_start();
        }

        // Set the token variable and user ID in the $_SESSION for verification
        $_SESSION['_cs']['auth_check'] = $token_id . ':' . $this->uid;

        // Return array with authorization selector, token, user agent, and expiry
        return (object) array(
          'selector' => $selector,
          'token' => $random_token,
          'user_agent' => $browser,
          'expires' => $expireDtm->format('Y-m-d H:i:s')
        );
      } // There was an issue adding the token to the database. Return FALSE.

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Check the authorization token is valid
   *
   * @param string $authSelector Authorization selector to check
   * @param int $authToken Authorization token to check
   *
   * @return bool Will return FALSE if failed, or TRUE if success
   */
  public function checkAuthorization(string $authSelector, int $authToken)
  {

    // Make sure the data is valid
    if (!empty($authSelector) && !empty($authToken) && is_numeric($authToken)) {

      // Check the token information is in the $_SESSION
      if (!empty($_SESSION['_cs']['auth_check'])) {

        // Get token information from the $_SESSION
        $authInfo = explode(':', $_SESSION['_cs']['auth_check']);

        // Get the authorization token from the database
        $result = $this->conn->dbh->selecting(
          DB_PREFIX . 'authorization',
          array(
            'auth_token',
            'auth_remember',
            'auth_dtm'
          ),
          where(
            eq('auth_id', $authInfo[0]),
            eq('auth_selector', $authSelector),
            eq('auth_user_id', $authInfo[1])
          )
        );

        // Check if the token is available
        if ($result != false) {

          // Return token if available
          $result = $result[0];

          // Check token matches
          if (password_verify($authToken, $result->auth_token)) {

            // Get and set authorization expiry value
            $auth_expiration_time = new DateTime($result->auth_dtm);
            $auth_expiration_time->modify('+' . $this->optn->get('auth_expire') . ' seconds');

            // Check if token is expired
            if ($auth_expiration_time >= date('Y-m-d H:i:s')) {

              // Delete all authorization tokens for user from the table
              $this->deleteAuthorization($authInfo[1], 1);

              // Set uid
              $this->uid = $authInfo[1];

              // Set remember me
              $this->remember = $result->auth_remember;

              // Return TRUE
              return TRUE;
            } else { // Token is expired. Delete from table and return FALSE.

              // Delete the authorization token from the table
              $this->deleteAuthorization($authInfo[0]);
            }
          } // Token doesn't match. Return FALSE.

        } // Token isn't available. Return FALSE.

      } // Token information not in the session. Return FALSE.

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Delete the authorization token information from the database
   *
   * @param int $authID Token ID OR User ID to delete token for
   * @param bool $deleteAll If you want to delete all the tokens for the specified user ID (IMPORTANT: $authID must be set to the User ID for this option to run properly) (optional)
   *
   * @return bool Will return FALSE if failed, or TRUE if success
   */
  protected function deleteAuthorization($authID, $deleteAllForUser = 0)
  {

    // Make sure the data is valid
    if (!empty($authID) && is_numeric($authID)) {

      // Check if $deleteAllForUser is set
      if ($deleteAllForUser) {

        // Delete all authorization tokens for user ID
        $delete_status = $this->conn->dbh->delete(
          DB_PREFIX . 'authorization',
          eq('auth_user_id', $authID)
        );
      } else { // Delete authorization token

        // Delete authorization token
        $delete_status = $this->conn->dbh->delete(
          DB_PREFIX . 'authorization',
          eq('auth_id', $authID)
        );
      }

      // Check the delete didn't error
      if ($delete_status != FALSE) {

        // Unset the authorization $_SESSION value
        unset($_SESSION['_cs']['auth_check']);

        // Return TRUE
        return TRUE;
      } // Not deleted successfully. Return FALSE

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Authenticate the user
   *
   * (no params)
   *
   * @return bool
   */
  public function authenticateUser()
  {

    // Make sure the data is valid
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Check if session set (just in case) and restart if it isn't
      if (session_id() == '') {
        session_start();
      }

      // Regenerate a new session ID
      session_regenerate_id(true);

      // Get the user information
      $userResult = $this->conn->dbh->selecting(
        DB_PREFIX . 'users',
        'user_id,
        user_email,
        user_display_name',
        where(
          eq('user_id', $this->uid),
          neq('user_status', '0')
        )
      );
      // Check if the user is available
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($userResult)) {

        // If it is, set initial $_SESSION info
        $result = $userResult[0];

        // Set the Cornerstone array
        if (empty($_SESSION['_cs']))
          $_SESSION['_cs'] = array();
        // Set user agent to check for session hijacking later
        $_SESSION['_cs']['HTTP_USER_AGENT'] = password_hash($_SERVER['HTTP_USER_AGENT'], PASSWORD_DEFAULT);
        // Set the user array
        $_SESSION['_cs']['user'] = array();
        // Set user ID
        $_SESSION['_cs']['user']['uid'] = $result->user_id;
        // Set user email address
        $_SESSION['_cs']['user']['email'] = $result->user_email;
        // Set user display name
        $_SESSION['_cs']['user']['name'] = ucwords($result->user_display_name);

        /**
         * Get the "ext.userauth.php" file and run `setCustomAuth()` function
         * to add any custom set $_SESSION items
         */
        require_once(DIR_ROOT . 'admin/models/cornerstone/ext.userauth.php');
        setCustomAuth($this->uid);

        // Log user login into `cs_login_log`
        $this->setLoginLog(1);

        // Return true that all data set
        return TRUE;
      } // User not available. Return FALSE.

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Set the authentication $_COOKIE information for user on "Remember Me"
   *
   * @return bool
   */
  public function setAuthCookie()
  {

    // Make sure the data is valid
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Clear any residue authentication cookies already set
      $this->clearAuthCookie();

      // Set Cookie Expiration to current time
      $cookie_expiration = new \DateTime();

      // Set Cookie expiration for length as defined in `cs_options`
      $cookie_expire = explode(',', $this->optn->get('cookie_expire'));
      switch ($cookie_expire[0]) {
        case "1": // for `x` weeks
          $cookie_expiration = $cookie_expiration->add(new \DateInterval('P' . $cookie_expire[1] . 'W'));
          break;
        case "2": // for `x` months
          $cookie_expiration = $cookie_expiration->add(new \DateInterval('P' . $cookie_expire[1] . 'M'));
          break;
        case "3": // for `x` years
          $cookie_expiration = $cookie_expiration->add(new \DateInterval('P' . $cookie_expire[1] . 'Y'));
          break;
        default: // for `x` days
          $cookie_expiration = $cookie_expiration->add(new \DateInterval('P' . $cookie_expire[1] . 'D'));
          break;
      }

      // Check if HTTPS site
      $setSSL = ($this->optn->get('site_https')) ? TRUE : FALSE;

      // Get random password
      $random_password = get_crypto_token(16);

      // Get random key
      $random_key = get_crypto_key(32);

      // Set cookie
      setcookie(
        '_cs',
        $random_key . '.' . bin2hex($random_password),
        $cookie_expiration->format('U'),
        "/",
        str_replace('www', '', $this->optn->get('site_url')),
        $setSSL,
        true
      );

      // Hash random password
      $random_password_hash = password_hash($random_password, PASSWORD_DEFAULT);

      // Get browser info if browser tracking enabled
      if ($this->optn->get('browser_tracking')) {
        $browser = new \WhichBrowser\Parser(getallheaders());
        // Set browser "Friendly Name"
        $browser_friendly_name = $browser->browser->name . '-' . $browser->os->name . $browser->os->version->alias;
        // Set browser "User Agent"
        $browser = $browser->toString();
      } else {
        $browser = "";
        $browser_friendly_name = "";
      }

      // Set the current time
      $cookie_set = new \DateTime();

      // Save cookie information to database
      $this->conn->dbh->insert(
        DB_PREFIX . 'auth_cookie',
        array(
          'cookie_user_id' => $this->uid,
          'cookie_user_type' => '1',
          'cookie_password_hash' => $random_password_hash,
          'cookie_key' => $random_key,
          'cookie_ip_address' => $_SERVER['REMOTE_ADDR'],
          'cookie_user_agent' => $browser,
          'cookie_friendly_name' => $browser_friendly_name,
          'cookie_set_dtm' => $cookie_set->format('Y-m-d H:i:s'),
          'cookie_expiry_dtm' => $cookie_expiration->format('Y-m-d H:i:s')
        )
      );

      // Check if cookie added to the database
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } else { // Failed to save the cookie to the database. Return FALSE.

        // Unset the cookie
        $this->clearAuthCookie();

        // Return FALSE
        return FALSE;
      }
    } else { // Data invalid. Return FALSE.

      // Return FALSE
      return FALSE;
    }
  }

  /**
   * Check the authentication $_COOKIE is valid
   *
   * @param bool $returnBool If you want this function to return true instead of the user ID. Defaults to TRUE.
   *
   * @return bool|int Will either return false, or if valid will return the user ID
   */
  public function checkAuthCookie(bool $returnBool = TRUE)
  {

    // Check if the data is valid
    if (!empty($_COOKIE['_cs'])) {

      // Get cookie data
      $cookieData = explode('.', $_COOKIE['_cs']);

      // Set $cookieKey to $cookieData[0]
      $cookieKey = trim($cookieData[0]);

      // Make sure the $cookieKey is not empty and is 32 characters
      if (!empty($cookieKey) && strlen($cookieKey) == 32) {

        // Get the cookie token from the database
        $cookieToken = $this->conn->dbh->selecting(
          DB_PREFIX . 'auth_cookie',
          array(
            'cookie_id',
            'cookie_password_hash',
            'cookie_user_id',
            'cookie_expiry_dtm'
          ),
          eq('cookie_key', $cookieKey)
        );

        // Check if the token is available
        if ($this->conn->dbh->getNum_Rows() > 0) {

          // Set 1st token if available
          $cookieToken = $cookieToken[0];

          // Check random password matches
          if (password_verify(hex2bin($cookieData[1]), $cookieToken->cookie_password_hash)) {

            // Check if token is expired
            if ($cookieToken->cookie_expiry_dtm >= date('Y-m-d H:i:s')) {

              // Return user ID or true if not expired
              return ($returnBool) ? TRUE : $cookieToken->cookie_user_id;
            } else { // Token is expired. Delete the cookie from the table and return FALSE.

              // Delete the cookie from the table
              $this->deleteAuthCookie($cookieKey);
            }
          } // Randoms don't match. Return FALSE

        } // Token is not available. Return FALSE

      } // Data invalid. Return FALSE

    } // Cookie isn't set. Return FALSE

    // Return FALSE
    return FALSE;
  }

  /**
   * Unset the authentication $_COOKIE information
   */
  protected function clearAuthCookie()
  {

    // Check if HTTPS site
    $setSSL = ($this->optn->get('site_https')) ? TRUE : FALSE;

    // Reset Cookie
    unset($_COOKIE['_cs']);
    setcookie(
      '_cs',
      '',
      1,
      "/",
      str_replace('www', '', $this->optn->get('site_url')),
      $setSSL,
      true
    );
  }

  /**
   * Delete the authentication cookie information from the database and $_COOKIE
   *
   * @param string $cookieKey Cookie key to delete
   *
   * @return bool Will either return false if fails to run, or true if successful
   */
  public function deleteAuthCookie(string $cookieKey = '')
  {

    // Check data is valid
    if (empty($cookieKey) && !empty($_COOKIE['_cs'])) {
      $cookieKey = explode('.', $_COOKIE['_cs'])[0];
    }

    // Make sure the $cookieKey is not empty and is 32 characters
    if (!empty($cookieKey) && strlen($cookieKey) == 32) {

      // Delete cookie information from database
      if ($this->conn->dbh->delete(
        DB_PREFIX . 'auth_cookie',
        eq('cookie_key', $cookieKey)
      )) {

        // Unset cookies
        $this->clearAuthCookie();

        // Return TRUE
        return TRUE;
      } // Unable to delete Cookie. Return FALSE

    } // Data invalid. Return FALSE

    // Return FALSE
    return FALSE;
  }
}
