<?php

/**
 * Account Core Model
 *
 * @package Cornerstone
 */

use function ezsql\functions\{
  selecting,
  updating,
  where,
  eq
};

class AccountCore extends Cornerstone\ModelBase
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

    // Set the user ID if available
    if (!empty($_SESSION['_cs']['user']['uid']) && is_numeric($_SESSION['_cs']['user']['uid'])) {
      $this->setUserID((int) $_SESSION['_cs']['user']['uid']);
    }
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
   * Check user by email
   *
   * @param string $email email address to check
   * @param int $active `[optional]` Check if user is active. Defaults to "1"
   *
   * @return bool Will return TRUE if user found, or FALSE if not found
   */
  public function checkUserByEmail(
    string $email,
    int $active = 1
  ) {

    // Check data is valid
    if (!empty($email)) {

      // Build query
      $this->sql = array();
      $this->whereArray = array();

      // Check for email
      $this->whereArray[] = eq("user_email", $email);

      // Check for active users
      if ($active) {
        $this->whereArray[] = eq("user_status", "1");
      }

      // Combine where
      if (!empty($this->whereArray)) {
        $this->sql[] = where(...$this->whereArray);
      }

      // Run query to find user by email
      $this->conn->dbh->tableSetup('users', DB_PREFIX);
      $searchResult = selecting(
        "user_id",
        ...$this->sql
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($searchResult)) {

        // Return True
        return TRUE;
      } // No results found. Return FALSE
    } // Data invalid. Return FALSE

    // Return False
    return FALSE;
  }

  /**
   * Get user details
   *
   * @return bool Will return data if found, or FALSE if not found
   */
  public function getUserDetails()
  {

    // Check if the ID is set
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Run query to find data
      $this->conn->dbh->tableSetup('users', DB_PREFIX);
      $detailsResults = selecting(
        "user_id,
        user_first_name,
        user_last_name,
        user_display_name,
        CONCAT(user_first_name, ' ', user_last_name) AS users_name,
        user_login,
        user_email",
        where(
          eq("user_id", $this->uid)
        )
      );

      // Check if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($detailsResults)) {

        // Return True
        return $detailsResults[0];
      } // No results. Return FALSE
    } // Data invalid. Return FALSE

    // Return False
    return FALSE;
  }

  /**
   * Edit User
   *
   * @param string $firstName The first name of the user
   * @param string $lastName The last name of the user
   * @param string $displayName The display name of the user
   * @param string $email The email address of the user
   *
   * @return int Will return FALSE if failed or TRUE if successful.
   */
  public function editUser(
    string $firstName,
    string $lastName,
    string $displayName,
    string $email
  ) {

    // Check data is valid
    if (!empty($this->uid) && is_numeric($this->uid)) {

      // Update data
      $this->conn->dbh->tableSetup('users', DB_PREFIX);
      updating(
        array(
          'user_first_name' => $firstName,
          'user_last_name' => $lastName,
          'user_display_name' => $displayName,
          'user_email' => $email,
          'user_edited_id' => $this->uid,
          'user_edited_dtm' => date('Y-m-d H:i:s')
        ),
        eq("user_id", $this->uid)
      );

      // Check if updated successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // Unable to edit. Return FALSE.

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }
}
