<?php
class Files extends Cornerstone\Controller
{

  /**
   * Class Constructor
   */
  public function __construct($registry)
  {
    // Load the controller constructor
    parent::__construct($registry);
  }

  /**
   * Error Default redirect
   *
   * @param mixed $params
   */
  public function error(...$params)
  {

    // Load the file output
    $this->output(...$params);
  }

  /**
   * Serve File
   *
   * @param mixed $params
   */
  private function serve_file($filepath, $new_filename = null)
  {
    $filename = basename($filepath);
    if (!$new_filename) {
      $new_filename = $filename;
    }
    $mime_type = mime_content_type($filepath);
    header('Content-type: ' . $mime_type);
    header('Content-Disposition: inline; filename="' . $new_filename . '"');
    readfile($filepath);
  }

  /**
   * Output File
   *
   * @param mixed $params
   */
  public function output(...$params)
  {

    // Check params aren't empty
    if (!empty($params)) {

      // Set root path
      $rootPath = DIR_SYSTEM . 'storage' . _DS . 'files' . _DS;

      // Set the file path
      $filePath = str_replace('/', _DS, implode(_DS, $params));

      // check the file exists and isn't a directory
      if (!is_dir($rootPath . $filePath) && file_exists($rootPath . $filePath)) {

        // Serve the file
        $this->serve_file($rootPath . $filePath);
      } // File doesn't exist or is a directory. Redirect to 404
    } // Params were empty. Redirect to 404

    // Redirect to root index
    parent::error();
    exit;
  }
}
