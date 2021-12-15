<?php

namespace Cornerstone;

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) 2019-2020, dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 */

/**
 * Registry Class
 */

final class Registry
{
  // Init $data
  private $data = array();

  /**
   * Get
   *
   * @param	string	$key
   *
   * @return	mixed
   */
  public function get($key)
  {
    return (isset($this->data[$key]) ? $this->data[$key] : null);
  }

  /**
   * Set
   *
   * @param	string	$key
   * @param	string	$value
   */
  public function set($key, $value)
  {
    $this->data[$key] = $value;
  }

  /**
   * Has
   *
   * @param	string	$key
   *
   * @return	bool
   */
  public function has($key)
  {
    return isset($this->data[$key]);
  }
}
