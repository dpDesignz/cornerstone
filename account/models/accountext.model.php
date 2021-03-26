<?php

/**
 * ME Account Extended Model
 *
 * @package Cornerstone
 * @subpackage Mission Equine
 */

class AccountExt extends ModelBase
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
}
