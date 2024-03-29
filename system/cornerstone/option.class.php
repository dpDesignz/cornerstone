<?php

namespace Cornerstone;

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) 2019-2020, dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 */

use function ezsql\functions\{
  selecting,
  updating,
  eq
};

/**
 * Option Class
 */

class Option
{
  // Define properties
  public $data = array();
  private $conn;

  /**
   * Class Constructor
   */
  public function __construct($cdbh)
  {

    // Set the database connection
    $this->conn = $cdbh;
    $this->conn->dbh->tableSetup('options', DB_PREFIX);

    // Load option data
    foreach (selecting(
      array('option_name', 'option_value'),
      eq('autoload', '1')
    ) as $option) {
      // Set option to data
      $this->set($option->option_name, $option->option_value);
    }
  }

  /**
   *
   *
   * @param	string|array	$key Name of option(s) to retrieve from the database, either a string or array (not case sensitive)
   * @param null|string $default `[optional]` Default value to return if the option does not exist. Defaults to "null"
   *
   * @return	mixed
   */
  public function get($key, $default = null)
  {
    // Set table setup
    $this->conn->dbh->tableSetup('options', DB_PREFIX);

    // Check if only 1 option
    if (is_string($key)) {

      // Lower $key
      $key = strtolower($key);

      // Check if the option is not empty
      if (!empty(trim($key))) {
        // Check if value is set already
        if (isset($this->data[$key])) {
          // Value is set. Return value
          return $this->data[$key];
          exit;
        } else {
          // Check for value in the database
          $optionResults = selecting(
            array('option_name', 'option_value'),
            eq('option_name', $key)
          );
          if ($this->conn->dbh->getNum_Rows() > 0 && !empty($optionResults)) {
            // Set value to data
            $this->set(strtolower($optionResults[0]->option_name), $optionResults[0]->option_value);
            // Return data
            return $this->data[$optionResults[0]->option_name];
            exit;
          } // Value can't be found. Return $default value
        }
      } // Option is empty. Return $default value

      // Return $default value
      return $default;
      exit;
    } else if (is_array($key)) { // Check if array of options
      // Check if the option array is not empty
      if (count($key) > 0) {

        // Create array to return
        $returnArray = array();

        // Get the options from the database
        foreach ($key as $optionName) {

          // Lower $optionName
          $optionName = strtolower($optionName);

          // Check if value is set already
          if (isset($this->data[$optionName])) {

            // Value is set. Return value
            $returnArray[$optionName] = $this->data[$optionName];
          } else {

            // Check for value in the database
            $optionResults = selecting(
              array('option_name', 'option_value'),
              eq('option_name', $optionName)
            );
            if ($this->conn->dbh->getNum_Rows() > 0 && !empty($optionResults)) {

              // Set value to data
              $this->set($optionName, $optionResults[0]->option_value);

              // If option is empty, return $default value, else return $key=>$value from table
              $returnArray[$optionName] = (empty(trim($optionResults[0]->option_value))) ? $default : $optionResults[0]->option_value;
            } else { // Value can't be found. Return $default value
              $returnArray[$optionName] = $default;
            }
          }
        }
        // Return object of data
        return (object) $returnArray;
        exit;
      } // Array is empty. Return $default value

      // Return $default value
      return $default;
      exit;
    }

    // Return null
    return null;
    exit;
  }

  /**
   *
   *
   * @param int $userID ID of the user to get for
   * @param	string|array	$key Name of option(s) to retrieve from the database, either a string or array (not case sensitive)
   * @param null|string $default `[optional]` Default value to return if the option does not exist. Defaults to "null"
   *
   * @return	mixed
   */
  public function getUser(
    $userID,
    $key,
    $default = null
  ) {
    // Set table setup
    $this->conn->dbh->tableSetup('user_meta', DB_PREFIX);

    // Check if only 1 item
    if (is_string($key)) {

      // Lower $key
      $key = strtolower($key);

      // Check if the key is not empty
      if (!empty(trim($key))) {
        // Check for value in the database
        $metaResults = selecting(
          array('umeta_value'),
          eq('umeta_key', $key),
          eq('umeta_user_id', $userID)
        );
        if ($this->conn->dbh->getNum_Rows() > 0 && !empty($metaResults)) {
          // Return data
          return $metaResults[0]->umeta_value;
          exit;
        } // Value can't be found. Return $default value
      } // key is empty. Return $default value

      // Return $default value
      return $default;
      exit;
    } else if (is_array($key)) { // Check if array of items
      // Check if the key array is not empty
      if (count($key) > 0) {

        // Create array to return
        $returnArray = array();

        // Get the items from the database
        foreach ($key as $metaName) {

          // Lower $metaName
          $metaName = strtolower($metaName);

          // Check for value in the database
          $metaResults = selecting(
            array('umeta_value'),
            eq('umeta_key', $key),
            eq('umeta_user_id', $userID)
          );
          if ($this->conn->dbh->getNum_Rows() > 0 && !empty($metaResults)) {

            // If option is empty, return $default value, else return $key=>$value from table
            $returnArray[$metaName] = (empty(trim($metaResults[0]->umeta_value))) ? $default : $metaResults[0]->umeta_value;
          } else { // Value can't be found. Return $default value
            $returnArray[$metaName] = $default;
          }
        }
        // Return object of data
        return (object) $returnArray;
        exit;
      } // Array is empty. Return $default value

      // Return $default value
      return $default;
      exit;
    }

    // Return null
    return null;
    exit;
  }

  /**
   *
   *
   * @param	string	$key
   * @param	string	$value
   */
  public function set($key, $value)
  {
    $this->data[$key] = $value;
  }

  /**
   *
   *
   * @param	string	$key
   *
   * @return	mixed
   */
  public function has($key)
  {
    return isset($this->data[$key]);
  }

  /**
   *
   *
   * @param	string	$key
   * @param	string	$value
   */
  public function update($key, $value)
  {
    // Set table setup
    $this->conn->dbh->tableSetup('options', DB_PREFIX);

    // Check key and value are set
    if (!empty($key) && !empty($value)) {

      // Set user ID
      $userID = (!empty($_SESSION['_cs']['user']['uid'])) ? $_SESSION['_cs']['user']['uid'] : 0;

      // Update row in `xxx_options`
      $updateResult = updating(
        array(
          'option_value' => $value,
          'option_edited_id' => $userID,
          'option_edited_dtm' => date('Y-m-d H:i:s'),
        ),
        eq("option_name", strtolower($key))
      );

      // Check if updated successfully
      if ($this->conn->dbh->affectedRows() > 0 && !empty($updateResult)) {

        // Update the data in the key if isset
        if (isset($this->data[$key])) {
          $this->data[$key] = $value;
        }

        // Return TRUE
        return TRUE;
        exit;
      } // Data didn't update. Return FALSE
    } // Data isn't set. Return FALSE
    // Return FALSE
    return FALSE;
    exit;
  }
}
