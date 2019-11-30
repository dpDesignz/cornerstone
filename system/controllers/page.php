<?php
  /**
   * Pages Controller
   *
   * @package Cornerstone
   */

  class Page extends Controller {

    // Index Page
    public function index(...$params) {

      $this->loadView('pages/index', $params);

    }

    // Testing page
    public function cstest() {

      $this->loadView('pages/cs-test');

    }
  }