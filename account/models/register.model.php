<?php

/**
 * ME Account Register Model
 *
 * @package Cornerstone
 * @subpackage Mission Equine
 */

use function ezsql\functions\{
  selecting,
  inserting,
  where,
  eq,
  like
};

class Register extends Cornerstone\ModelBase
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
   * Check email is unique
   *
   * @param string $email Email address to check
   *
   * @return int Will return how many results found
   */
  public function checkEmailUnique(string $email)
  {

    // Run query to find data
    $this->conn->dbh->tableSetup('users', DB_PREFIX);
    $customerResults = selecting(
      "user_id",
      where(
        eq("user_email", $email)
      )
    );

    // Return results
    return $this->conn->dbh->getNum_Rows();
  }

  /**
   * Check for unique login name
   *
   * @param string $loginName Login name to check
   * @param int $loginRound `[optional]` Current round checked. Defaults to "0"
   *
   * @return int Will return unique login
   */
  private function checkLoginUnique(string $loginName, int $loginRound = 0)
  {

    // Create login name checked
    $loginNameChecked = (!empty($loginRound)) ? $loginName . $loginRound : $loginName;

    // Run query to find data
    $this->conn->dbh->tableSetup('users', DB_PREFIX);
    $loginResults = selecting(
      "user_id",
      where(
        eq("user_login", $loginNameChecked)
      )
    );

    // Check results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($loginResults)) {
      // Check how many logins started with the same number suffix
      selecting(
        "user_id",
        where(
          like("user_login", $loginNameChecked . "%")
        )
      );
      // Increment login round
      $loginRound = $this->conn->dbh->getNum_Rows() + 1;
      return $this->checkLoginUnique($loginName, $loginRound);
    } else {
      return $loginNameChecked;
    };
  }

  /**
   * Add User
   *
   * @param string $firstName The users first name
   * @param string $lastName The users last name
   * @param string $email The users email address
   * @param string $password The users encrypted password
   * @param string $passwordKey The users password key
   *
   * @return bool|int Will return FALSE if failed or users ID if successful.
   */
  public function addUser(
    string $firstName,
    string $lastName,
    string $email,
    string $password,
    string $passwordKey
  ) {

    // Check for unique login
    $loginName = $this->checkLoginUnique(strtolower($firstName));

    // Add data into `me_customer`
    $this->conn->dbh->tableSetup('users', DB_PREFIX);
    inserting(
      array(
        'user_login' => $loginName,
        'user_display_name' => $firstName . " " . $lastName,
        'user_password' => $password,
        'user_password_key' => $passwordKey,
        'user_email' => $email,
        'user_first_name' => $firstName,
        'user_last_name' => $lastName,
        'user_status' => 1,
        'user_created_dtm' => date('Y-m-d H:i:s')
      )
    );

    // Check if added successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Return ID
      return $this->conn->dbh->getInsert_Id();
    } // Unable to add. Return FALSE.

    // Return FALSE
    return FALSE;
  }
}
