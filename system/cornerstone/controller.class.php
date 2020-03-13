<?php

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) 2019-2020, dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 */

/**
 * Base Controller Class
 * Loads the models and views
 */

abstract class Controller
{
  // Init $data
  protected $data = array();
  // Init $params
  protected $params = array();
  // Set the registry
  protected $registry;

  /**
   * Constructor
   */
  public function __construct($registry)
  {
    $this->registry = $registry;
  }


  /**
   * Get
   *
   * @param	string	$key
   *
   * @return	mixed
   */
  public function __get($key)
  {
    return $this->registry->get($key);
  }

  /**
   * Set
   *
   * @param	string	$key
   * @param	string	$value
   */
  public function __set($key, $value)
  {
    $this->registry->set($key, $value);
  }

  /**
   * Default - 404
   *
   * Used to load a 404 page if the requested method isn't valid
   * or an index method isn't defined in the loaded controller.
   *
   * (No params)
   */
  public function error(...$params)
  {
    $this->load->view('404');
  }
}
