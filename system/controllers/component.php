<?php

/**
 * Component Controller
 *
 * @package Cornerstone
 */

class Component extends Controller
{

  // Default Page
  public function error(...$params)
  {

    $this->data['component'] = explode('/', htmlspecialchars(stripslashes(trim(strtolower($_GET['url'])))))[1];
    $this->data['component'] = (!empty($this->data['component'])) ? $this->data['component'] : 'index';

    $this->load->view('components/' . $this->data['component'], $this->data);
  }
}
