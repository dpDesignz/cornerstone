<?php
// import the Intervention Image Manager Class ~ http://image.intervention.io/
use Intervention\Image\ImageManager;

class Images extends Cornerstone\Controller
{

  private $manager;

  /**
   * Class Constructor
   */
  public function __construct()
  {

    // create an image manager instance with favored driver
    // $this->manager = new ImageManager(array('driver' => 'gd'));
    if (!extension_loaded('imagick')) {
      $this->manager = new ImageManager(array('driver' => 'gd'));
    } else {
      $this->manager = new ImageManager(array('driver' => 'imagick'));
    }

    // Load the images model
    // $this->imagesModel = $this->loadModel('common/image', 'admin');

  }

  /**
   * Error Default redirect
   *
   * @param mixed $params
   */
  public function error(...$params)
  {

    // Load the image output
    $this->output(...$params);
  }

  /**
   * Output Image
   *
   * @param mixed $params
   */
  public function output(...$params)
  {

    // Set root path
    $rootPath = DIR_SYSTEM . 'storage' . _DS . 'uploads' . _DS . 'images' . _DS;
    $imagePath = '';

    // Set placeholder defaults
    $img_w = 500;
    $img_h = 500;
    $ph_t = 'No image';

    // Set image folder path
    $imageFolder = implode(_DS, $params);
    if (!empty($params[count($params) - 1])) {
      $imageFolder = rtrim($imageFolder, $params[count($params) - 1]);
    }

    // Check for the image type
    if (!empty($params[0])) {

      // Get image type
      $imageType = $params[0];

      // Skip file loading
      $skipFile = FALSE;

      // Set image type specifics
      switch ($imageType) {
        case 'placeholder':
          // Check if dimensions set
          if (!empty($params[1])) {
            // Dimensions set

            // Check if multiplier set
            if (strpos(strtolower($params[1]), 'x')) {
              // Multiplier set

              // Split multiplier
              $dimensions = explode('x', $params[1]);

              // Set dimensions
              $img_w = $dimensions[0];
              $img_h = (!empty($dimensions[1])) ? $dimensions[1] : $dimensions[0];
            } else if (is_numeric($params[1])) { // Multiplier not set. Check if numeric
              // Param is numeric

              // Set dimensions
              $img_w = $params[1];
              $img_h = $img_w;
            }
          }

          // Check if text is set
          if (!empty($_GET['text'])) {
            // Text is set

            // Set text
            $ph_t = htmlspecialchars(stripslashes(trim(urldecode($_GET['text']))));
          }

          // Skip the file loading
          $skipFile = TRUE;
          break;

          // Add more case options here as required

        default:
          // check for image folder
          $imagePath = (!empty($imageFolder)) ? $rootPath . $imageFolder : $rootPath;
          // check for image
          if (!empty($params[count($params) - 1])) {
            $imagePath = $imagePath . urlencode(trim($params[count($params) - 1]));
          } else {
            // Set placeholder text
            $ph_t = 'No image requested';
            // Skip file loading
            $skipFile = TRUE;
          }
          break;
      }

      // Check if needing to skip the file
      if (!$skipFile) {

        // Check if the file exists
        if (file_exists($imagePath)) {

          // Check if wanting to get the thumb
          if (!empty($_GET['thumb']) && (strtolower($_GET['thumb']) == "s" || strtolower($_GET['thumb']) == "m")) {
            // Get image info
            $img_info = pathinfo($imagePath);

            // Get the thumb type
            $thumbType = (strtolower($_GET['thumb']) == "m") ? 'm' : '';

            // Patch together path
            $newImagePath = $img_info['dirname'] . _DS . $img_info['filename'] . '_' . $thumbType . 'thumb.' . $img_info['extension'];

            // Check the file exists
            if (file_exists($newImagePath)) {
              // Set new image path
              $imagePath = $newImagePath;
            }
          }

          // Create the image
          $cached_image = $this->manager->cache(
            function ($image) use ($imagePath) {

              $image = $image->make($imagePath);

              // Check for dimensions
              if (
                (!empty($_GET['w']) && is_numeric($_GET['w'])) || (!empty($_GET['h']) && is_numeric($_GET['h']))
              ) {
                // Dimensions set

                // Set default options
                $width = (!empty($_GET['w'])) ? (int) trim($_GET['w']) : null;
                $height = (!empty($_GET['h'])) ? (int) trim($_GET['h']) : null;

                // Resize and return the image
                return $image->resize($width, $height, function ($constraint) {
                  $constraint->aspectRatio();
                  // prevent possible upsizing
                  if (empty($_GET['e']) || trim($_GET['e']) !== 'y') {
                    $constraint->upsize();
                  }
                });
              } else {
                // Return the image
                return $image;
              }
            },
            10,
            TRUE
          );

          // Output the image
          echo $cached_image->response();
          exit;
        } // File doesn't exist. Output the placeholder

        $ph_t = 'Image unavailable';
      }
    } // No image type. Output placeholder

    // Check for dimensions
    if ((!empty($_GET['w']) && is_numeric($_GET['w'])) || (!empty($_GET['h']) && is_numeric($_GET['h']))) {
      // Dimensions set

      // Check if width AND height set
      if (!empty($_GET['w']) && !empty($_GET['h'])) {
        // Set the dimensions
        $img_w = (int) trim($_GET['w']);
        $img_h = (int) trim($_GET['h']);
      } else if (!empty($_GET['w'])) { // Check if just width set
        // Set the dimensions
        $img_w = (int) trim($_GET['w']);
        $img_h = (int) trim($_GET['w']);
      } else if (!empty($_GET['h'])) { // Check if just height set
        // Set the dimensions
        $img_w = (int) trim($_GET['h']);
        $img_h = (int) trim($_GET['h']);
      }
    }

    // Create the canvas
    $placeholder = $this->manager->canvas($img_w, $img_h, '#e0e0e0');

    // Write text at position. Use callback to define details
    $placeholder->text($ph_t, ($img_w / 2), ($img_h / 2), function ($font) use ($img_w) {
      $font->file(DIR_SYSTEM . 'storage' . _DS . 'resources' .  _DS . 'fonts' .  _DS . 'Roboto-Medium.ttf');
      $font->size(($img_w / 10));
      $font->color('#616161');
      $font->align('center');
      $font->valign('middle');
    });

    // send HTTP header and output image data
    echo $placeholder->response('png');
    exit;
  }
}
