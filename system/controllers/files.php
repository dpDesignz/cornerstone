<?php
class Files extends Cornerstone\Controller
{

  /**
   * Class Constructor
   */
  public function __construct()
  {
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
   * Output File
   *
   * @param mixed $params
   */
  public function output(...$params)
  {

    // Check params aren't empty
    if (!empty($params)) {

      // Set root path
      $rootPath = DIR_SYSTEM . 'storage' . _DS . 'uploads' . _DS;

      // Set the file path
      $filePath = str_replace('/', _DS, implode(_DS, $params));

      // check the file exists
      if (file_exists($rootPath . $filePath)) {

        // Get file information
        $fileInformation = pathinfo($rootPath . $filePath);

        // Check file is a PDF
        if (strtolower($fileInformation['extension']) == 'pdf') {
          // Open the file
          header("Content-type: application/pdf");
          header("Content-Disposition: inline; filename=" . $fileInformation['basename']);
          @readfile($rootPath . $filePath);
          exit;
        } // File is not a PDF. Redirect to 404
      } // File doesn't exist. Redirect to 404
    } // Params were empty. Redirect to 404

    // Redirect to root index
    parent::error();
    exit;
  }
}
