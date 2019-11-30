<?php
  /**
   * Base Controller Class
	 * Loads the models and views
   *
   * @package Cornerstone
   */

  class Controller {

    // Init $data
    protected $data = array();

    /**
		 * Load Model
		 * Used to load a defined model
     *
     * @param string $model: Name of model to load
     * @param string $directory: Name of directory to load from. Defaults to system. (optional)
		 *
		 * @return class - Returns new class of requested model
		 */
    public function loadModel($model, $directory = 'system'){

      // Check for allowed sub-folders
      if(!array_key_exists($directory, ALLOWED_SUBFOLDERS)) $directory = 'system';

      // Set model directory
      $directory = DIR_ROOT . trim( $directory, '/' ) . _DS;

      // Fix director seperator
      $model = str_replace('/', _DS, $model);

      // Require model file
      require_once($directory . 'models' . _DS . strtolower($model) . '.model.php');

      // Get model name
      $model = array_values(array_slice(explode(_DS, $model), -1))[0];

      // Instatiate model
      return new $model();

    }

    /**
		 * Load View
		 * Used to load a defined view
     *
     * @param string $view: Name of view to load
     * @param array $data: Any extra data required for the view (optional)
     * @param string $directory: Name of directory to load from. Defaults to system. (optional)
		 */
    public function loadView($view, $data = [], $directory = 'system'){

      // Convert the data array into an object
      // This code converts multi-dimensional array
      $data = json_decode(json_encode($data));

      // Check for allowed sub-folders
      if(!array_key_exists($directory, ALLOWED_SUBFOLDERS)) $directory = 'system';

      // Set view directory
      $directory = DIR_ROOT . _DS . trim( $directory, '/' ) . _DS;

      // Set the file path
      $filePath = $directory . 'views' . _DS . str_replace(array('\\', '/'), _DS, strtolower($view)) . '.vw.php';

      // Check for view file
      if(file_exists($filePath)){

        // Load the view if it exists
        require_once($filePath);

      } else {

        // Load 404 if view doesn't exist
        $this->loadView('404');

      }

    }

    /**
		 * Default - 404
     *
		 * Used to load a 404 page if the requested method isn't valid
     * or an index method isn't defined in the loaded controller.
     *
     * (No params)
		 */
    public function error(...$params) {
      $this->loadView('404');
    }
  }