<?php

/**
 * Account Core Model
 *
 * @package Cornerstone
 */

class AccountCore extends ModelBase
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
      $searchResult = $this->conn->dbh->selecting(
        DB_PREFIX . "users",
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
}
