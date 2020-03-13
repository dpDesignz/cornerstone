<?php

/**
 * App Core Class
 * Creates URL & loads core controller
 * URL FORMAT - /controller/method/params
 *
 * @package Cornerstone
 */

class Core
{

  // Set default properties
  protected $url = array();
  protected $currentController = 'Page';
  protected $currentMethod = 'error';
  protected $params = [];
  protected $subFolders = ALLOWED_SUBFOLDERS;

  /**
   * Construct the core system
   * No params
   */
  public function __construct($registry)
  {

    // Get the url entered in the address bar
    $this->getUrl();

    // If no values, set current method to 'Index' to load index page
    if (empty($this->url)) {
      $this->currentMethod = 'index';
    }

    // Set starting index
    $s = 0;

    // Check for allowed sub-folders
    if (!empty($this->url[$s]) && array_key_exists($this->url[$s], $this->subFolders)) {

      // Set $rootDir to sub-folder
      $rootDir = DIR_ROOT . $this->url[$s] . _DS;

      // Get default controller and method values
      $defaults = explode('/', $this->subFolders[$this->url[$s]]);

      // Set currentController
      if (!empty($defaults[0])) {
        $this->currentController = $defaults[0];
      }

      // Set currentMethod
      if (!empty($defaults[1])) {
        $this->currentMethod = $defaults[1];
      }

      // Unset $s Index
      unset($this->url[$s]);

      // Increase Index for first value
      $s++;
    } else {

      // Set $rootDir to DIR_SYSTEM
      $rootDir = DIR_SYSTEM;
    }

    // Look in controllers for first value
    if (!empty($this->url[$s])) {

      // Remove any dashes
      $controller = trim(preg_replace('/\s+/', '', str_replace('-', '', $this->url[$s])));

      if (file_exists($rootDir . 'controllers' . _DS . strtolower($controller) . '.php')) {

        // If exists, set as controller
        $this->currentController = ucwords($controller);

        // Unset $s Index
        unset($this->url[$s]);

        // Increase Index for second part of url
        $s++;
      }
    }

    // Require the controller
    require_once($rootDir . 'controllers' . _DS . strtolower($this->currentController) . '.php');

    // Instantiate controller class
    $this->currentController = new $this->currentController($registry);

    // Check for second part of url
    if (!empty($this->url[$s])) {

      // Check to see if method exists in controller
      $method = trim(preg_replace('/\s+/', '', str_replace('-', '', $this->url[$s]))); // Remove any dashes
      if (method_exists($this->currentController, $method)) {

        $this->currentMethod = $method;

        // Unset $s index
        unset($this->url[$s]);
      }
    }

    // Check to see if default/set method exists in controller
    if (!method_exists($this->currentController, $this->currentMethod)) {

      // Set to error if not
      $this->currentMethod = 'error';
    }

    // Get left over params
    $this->params = $this->url ? array_values($this->url) : [];

    // Call a callback with array of params
    call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
  }

  /**
   * Get URL
   * Used to return the data entered into the url
   * No params
   *
   * @return array returns an array of the entered url
   */
  public function getUrl()
  {

    // Get the data in the global $_GET['url']
    if (isset($_GET['url'])) {
      $this->url = explode('?', $_GET['url']); // Trim down to anything before a ? to allow passing of full paths in queries
      $this->url = strip_tags($this->url[0]); // Strip any malicious tags from the url
      $this->url = rtrim($this->url, '/'); // Trim the slash off the end of the url if there is one
      // $this->url = filter_var($this->url, FILTER_SANITIZE_URL); // Sanitize the data so it only passes url safe data
      // $this->url = str_replace('-', '_', $this->url); // Replace any dashes with underscores
      $this->url = explode('/', $this->url); // Explode the url into an array and return
    }
  }
}
