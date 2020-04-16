<?php

/**
 * Cornerstone Settings Model
 *
 * @package Cornerstone
 */

class Setting
{

  // Set the default properties
  private $conn;
  private $optn;

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

      $optionResults = $this->conn->dbh->selecting(
        DB_PREFIX . "options",
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
    $updateResult = $this->conn->dbh->update(
      DB_PREFIX . "options",
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
