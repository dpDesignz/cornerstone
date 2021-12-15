<?php

namespace Cornerstone;

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) 2019-2020, dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 */

/**
 * Request Class
 */

class Request
{

  // Public Properties
  public $server = array();
  public $get = array();
  public $params = array();
  public $post = array();
  public $files = array();
  public $cookie = array();

  /**
   * Constructor
   */
  public function __construct()
  {
    // Clean each predefined variable
    $this->server = $this->clean($_SERVER);
    $this->get = $this->clean($_GET);
    $this->post = $this->clean($_POST);
    $this->files = $this->clean($_FILES);
    $this->request = $this->clean($_REQUEST);
    $this->cookie = $this->clean($_COOKIE);
  }

  /**
   * Clean data
   *
   * @param	array	$data
   *
   * @return	array
   */
  public function clean($data)
  {
    // Check if data is an array
    if (is_array($data)) {
      // Data is an array

      // Loop through data array
      foreach ($data as $key => $value) {
        // Unset the data item
        unset($data[$key]);

        // Clean and set the new item
        $data[$this->clean($key)] = $this->clean($value);
      }
    } else { // Data is not an array. Clean data item.

      // Clean data item
      $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
    }

    // Return the data
    return $data;
  }

  /**
   * Set
   *
   * @param	string	$key
   * @param	string	$value
   */
  public function set_params($data)
  {
    foreach ($data as $data_index => $data_item) {
      // Process if $data_item isn't an object or an array
      if (!is_array($data_item) && !is_object($data_item)) {
        $this->params[$this->clean($data_item)] = (isset($data[($data_index + 1)]) && !empty($data[($data_index + 1)]) && !is_array($data[($data_index + 1)]) && !is_object($data[($data_index + 1)])) ? $this->clean($data[($data_index + 1)]) : '';
      }
    }
  }
}
