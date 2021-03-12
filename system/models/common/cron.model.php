<?php

/**
 * Cron Job Model
 *
 * @package Cornerstone
 */

class Cron extends ModelBase
{

  // Set the default properties

  /**
   * Construct the model
   * No parameters required, nothing will be returned
   */
  public function __construct($cdbh, $option)
  {
    // Load the model base constructor
    parent::__construct($cdbh, $option);
  }
}
