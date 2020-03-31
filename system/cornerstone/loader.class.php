<?php

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) 2019-2020, dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 */

/**
 * Loader Class
 */

final class Loader
{
  // Define the page type
  protected $pageType = 'page';
  // Set the registry
  protected $registry;
  // Set the options
  protected $option;

  /**
   * Constructor
   *
   * @param	object	$registry
   * @param	object	$option
   */
  public function __construct($registry, $option)
  {
    $this->registry = $registry;
    $this->option = $option;
  }

  /**
   * Load Model
   * Used to load a defined model
   *
   * @param string $model Name of model to load
   * @param string $directory `[optional]` Name of directory to load from. Defaults to "system".
   *
   * @return class - Returns new class of requested model
   */
  public function model($model, $directory = 'system')
  {

    // Check for allowed sub-folders
    $directory =  (!array_key_exists($directory, ALLOWED_SUBFOLDERS)) ? 'system' : $directory;

    // Set model directory
    $directory = DIR_ROOT . trim($directory, '/') . _DS;

    // Fix director seperator
    $model = str_replace('/', _DS, $model);

    // Set the file
    $file = $directory . 'models' . _DS . strtolower($model) . '.model.php';

    // Check the file exists
    if (is_file($file)) {

      // Require model file
      require_once($file);

      // Get model name
      $model = array_values(array_slice(explode(_DS, $model), -1))[0];

      // Instatiate model
      return new $model($this->option);
    } else {
      throw new \Exception('Error: Could not load model ' . $file . '!');
    }
  }

  /**
   * Load View
   * Used to load a defined view
   *
   * @param string $view Name of view to load
   * @param array $data `[optional]` Any extra data required for the view
   * @param string $directory `[optional]` Name of directory to load from. Defaults to "system".
   * @param string $debug `[optional]` Whether to output data information or not. Defaults to "FALSE".
   */
  public function view($view, $data = [], $directory = 'system', $debug = FALSE)
  {

    // Set fallback data
    $data = (empty($data)) ? array() : $data;

    // Set fallback directory
    $directory = (empty($directory)) ? 'system' : $directory;

    // Add options to data
    foreach ($this->option->data as $key => $value) {
      $data[strtolower($key)] = $value;
    }

    // Check if logged in
    $this->data['isAdmin'] = (isset($_SESSION['_cs']['user']['uid']) && !empty($_SESSION['_cs']['user']['uid'])) ? TRUE : FALSE;

    // Convert the data array into an object
    // This code converts multi-dimensional array
    $data = json_decode(json_encode($data));

    if ($debug) {
      echo '<pre>';
      print_r($data);
      echo '</pre>';
      exit;
    }

    // Pass the option data onto the view
    $option = $this->option;

    // Check for allowed sub-folders
    $directory =  (!array_key_exists($directory, ALLOWED_SUBFOLDERS)) ? 'system' : $directory;

    // Set view directory
    $directory = DIR_ROOT . _DS . trim($directory, '/') . _DS;

    // Set the file path
    $filePath = $directory . 'views' . _DS . str_replace(array('\\', '/'), _DS, strtolower($view)) . '.vw.php';

    // Check for view file
    if (file_exists($filePath)) {

      // Load the view if it exists
      require_once($filePath);
    } else {

      // Load 404 if view doesn't exist
      $this->view('404');
    }
  }

  /**
   * Load Helper
   * Used to load a defined helper
   *
   * @param string $helper Name of helper to load
   */
  public function helper($helper)
  {
    $file = DIR_HELPERS . 'fn.' . preg_replace('/[^a-zA-Z0-9_\/]/', '', (string) $helper) . '.php';

    if (is_file($file)) {
      include_once($file);
    } else {
      throw new \Exception('Error: Could not load helper ' . $helper . '!');
    }
  }
}
