<?php

/**
 * Component Controller
 *
 * @package Cornerstone
 */

class Component extends Cornerstone\Controller
{

  // Default Page
  public function error(...$params)
  {

    $this->data['component'] = (!empty($params[0])) ? $params[0] : 'index';

    $this->load->view('components/' . $this->data['component'], $this->data);
  }
}
