<?php

/**
 * Admin User Model
 *
 * @package Cornerstone
 */

use function ezsql\functions\{
  selecting,
  inserting,
  updating,
  leftJoin,
  where,
  grouping,
  eq,
  like,
  orderBy,
  limit
};

class User extends Cornerstone\ModelBase
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
    $this->conn->dbh->tableSetup('users', DB_PREFIX);
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
   * Get user
   *
   * @param int $userID ID of the user to retrieve
   *
   * @return object Return object with user details
   */
  public function getUser(
    int $userID
  ) {

    // Check data is valid
    if (!empty($userID) && is_numeric($userID)) {

      // Run query to find data
      $this->conn->dbh->tableSetup('users', DB_PREFIX);
      $userData = selecting(
        "user_id,
        user_login,
        user_display_name,
        user_email,
        user_first_name,
        user_last_name,
        user_role_id,
        user_auth_rqd,
        user_status",
        where(
          eq("user_id", $userID)
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($userData)) {

        // Return results
        return $userData[0];
      } // No results. Return FALSE.

    } // Data invalid. Return FALSE

    // Return FALSE
    return false;
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
    $this->sql = array();
    $this->whereArray = array();

    // Set user status
    $this->whereArray[] = eq("u.user_status", '1');

    // Check for search
    if (!empty($params['search'])) {
      $this->whereArray[] = grouping(
        like("LOWER(u.user_login)", "%" . strtolower($params['search']) . "%", _OR),
        like("LOWER(CONCAT(u.user_first_name, ' ' , u.user_last_name))", "%" . strtolower($params['search']) . "%", _OR),
        like("LOWER(u.user_last_name)", "%" . strtolower($params['search']) . "%", _OR),
        like("LOWER(u.user_email)", "%" . strtolower($params['search']) . "%", _OR)
      );
    }

    // Combine where
    if (!empty($this->whereArray)) {
      $this->sql[] = where(...$this->whereArray);
    }

    // Check for sort
    if (!$countResults && !empty($params['sort']) && !empty($params['order'])) {
      $this->sql[] = orderBy($params['sort'], $params['order']);
    }

    // Check for page number/limit
    if (!$countResults && !empty($params['limit'])) {
      // Check for page number
      if (!empty($params['page'])) {
        $offset = ($params['page'] - 1) * $params['limit'];
      } else {
        $offset = 0;
      }
      $this->sql[] = limit($params['limit'], $offset);
    }

    // Setup table
    $this->conn->dbh->tableSetup('users AS u', DB_PREFIX);

    if ($countResults) {

      // Run query to count data
      $userResults = selecting(
        "COUNT(u.user_id) AS total_results",
        ...$this->sql
      );
    } else {

      // Run query to find data
      $userResults = selecting(
        "u.user_id,
        u.user_login,
        CONCAT(u.user_first_name, ' ', u.user_last_name) AS users_name,
        u.user_email,
        r.role_id,
        r.role_name,
        l.login_dtm",
        leftJoin(
          "u",
          DB_PREFIX . "roles",
          "user_role_id",
          "role_id",
          "r"
        ),
        leftJoin(
          "u",
          "(SELECT login_user_id, MAX(login_dtm) AS login_dtm FROM " . DB_PREFIX . "login_log WHERE login_status = '1' GROUP BY login_user_id)",
          "user_id",
          "login_user_id",
          "l"
        ),
        ...$this->sql
      );
    }

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($userResults)) {

      // Check if wanting the count returned
      if ($countResults) {

        // Return the total count
        return $this->conn->dbh->get_row(NULL)->total_results;
        exit;
      } else { // Wanting to return data

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
    $this->conn->dbh->tableSetup('users', DB_PREFIX);
    $usersResults = selecting(
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
    $this->conn->dbh->tableSetup('users', DB_PREFIX);
    inserting(
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

  /**
   * Update User
   *
   * @param int $userID The ID of the user
   * @param string $login The username of the user
   * @param string $displayName The display name of the user
   * @param string $email The email of the user
   * @param string $firstName The first name of the user
   * @param string $lastName The last name of the user
   * @param int $roleID The ID of the role the user is assigned to
   * @param int $authRqd Set if the user is required to use 2FA
   * @param int $status The status of the user
   *
   * @return bool Will return FALSE if failed or TRUE if successful.
   */
  public function updateUser(
    int $userID,
    string $login,
    string $displayName,
    string $email,
    string $firstName,
    string $lastName,
    int $roleID,
    int $authRqd,
    int $status
  ) {

    // Set fallbacks
    $roleID = (empty($roleID)) ? NULL : $roleID;

    // Add data
    $this->conn->dbh->tableSetup('users', DB_PREFIX);
    updating(
      array(
        'user_login' => $login,
        'user_display_name' => $displayName,
        'user_email ' => $email,
        'user_first_name' => $firstName,
        'user_last_name' => $lastName,
        'user_role_id' => $roleID,
        'user_auth_rqd' => $authRqd,
        'user_status' => $status,
        'user_edited_id' => $_SESSION['_cs']['user']['uid'],
        'user_edited_dtm' => date('Y-m-d H:i:s')
      ),
      eq('user_id', $userID)
    );

    // Check if updated successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Return TRUE
      return TRUE;
    } // Unable to update. Return FALSE.

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
    $this->conn->dbh->tableSetup('roles', DB_PREFIX);
    $roleResults = selecting(
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

      // Get data
      $this->conn->dbh->tableSetup('roles', DB_PREFIX);
      $roleResults = selecting(
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
      $this->conn->dbh->tableSetup('login_log', DB_PREFIX);
      $loginResults = selecting(
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
