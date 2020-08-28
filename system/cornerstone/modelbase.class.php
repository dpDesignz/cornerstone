<?php

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) 2020, dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 */

/**
 * Base Model Class
 * Instantiates the database and options
 */

class ModelBase
{

  // Set the default properties
  protected $conn;
  protected $optn;

  /**
   * Constructor
   *
   * @param object $cdbh Base Cornerstone database connection
   * @param object $option Base Cornerstone options class
   */
  public function __construct($cdbh, $option)
  {

    // Set the database connection
    $this->conn = $cdbh;
    // Set the options
    $this->optn = $option;
  }
}
