<?php

/**
 * Cornerstone Settings Model
 *
 * @package Cornerstone
 */

use function ezsql\functions\{
  selecting,
  updating,
  where,
  eq
};

class Setting extends ModelBase
{

  // Set the default properties

  /**
   * Construct the User
   * No parameters required, nothing will be returned
   */
  public function __construct($cdbh, $option)
  {
    // Load the model base constructor
    parent::__construct($cdbh, $option);
    $this->conn->dbh->tableSetup('options', DB_PREFIX);
  }

  /**
   * Get options
   *
   * @param string $type Type of option to retrieve
   *
   * @return object|bool Will return options if found, or FALSE if none found
   */
  public function getOptions($type)
  {

    // Check data is valid
    if (!empty($type)) {

      // Get data
      $this->conn->dbh->tableSetup('options', DB_PREFIX);
      $optionResults = selecting(
        "option_name,
        option_value",
        where(
          eq('option_type', trim($type))
        )
      );

      // Check if data exists
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($optionResults)) {

        // Return options
        return $optionResults;
      } // No results. Return FALSE.

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Edit Option
   *
   * @param string $optionName Name of the option
   * @param string $optionValue Value of the option
   *
   * @return int Will return FALSE if failed or TRUE if successful.
   */
  public function editOption(string $optionName, string $optionValue)
  {

    // Update row
    $this->conn->dbh->tableSetup('options', DB_PREFIX);
    $updateResult = updating(
      array(
        'option_value' => $optionValue,
        'option_edited_id' => $_SESSION['_cs']['user']['uid'],
        'option_edited_dtm' => date('Y-m-d H:i:s')
      ),
      eq("option_name", $optionName)
    );

    // Check if updated successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Return TRUE
      return TRUE;
    } // Unable to edit. Return FALSE.

    // Return FALSE
    return FALSE;
  }
}
