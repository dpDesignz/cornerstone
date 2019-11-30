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

    // Get changelog contents
    public function changelog() {

      if(file_exists(DIR_ROOT . 'CHANGELOG.md')) {
      $data['contents'] = str_replace('`', '\`', addslashes(file_get_contents(DIR_ROOT . 'CHANGELOG.md')));

      $this->loadView('pages/changelog', $data);

      } else { // File doesn't exist. Redirect to error page.

        $this->error();

      }

    }

    // Testing page
    public function cstest() {

      $this->loadView('pages/cs-test');

    }
  }