<?php
class Templates extends Controller
{

  /**
   * Class Constructor
   */
  public function __construct($registry)
  {
    // Load the controller constructor
    parent::__construct($registry);

    // Check if user is allowed admin access
    checkAdminAccess();

    // Set Breadcrumbs
    $this->data['breadcrumbs'] = array(
      array(
        'text' => 'Dashboard',
        'href' => get_site_url('admin')
      ),
      array(
        'text' => 'Templates',
        'href' => get_site_url('admin/templates')
      )
    );
  }

  /**
   * Friendly File Size
   */
  private function formatSizeUnits($bytes)
  {
    if ($bytes >= 1073741824) {
      $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
      $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
      $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
      $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
      $bytes = $bytes . ' byte';
    } else {
      $bytes = '0 bytes';
    }

    return $bytes;
  }

  /**
   * Index Page
   */
  public function index()
  {
    // Get list of email templates
    $emailFilePath = DIR_SYSTEM . "emails";
    $emailFiles = scandir($emailFilePath);
    if (!empty($emailFiles) && is_array($emailFiles) && count($emailFiles) > 2) {
      // Init output
      $this->data['email_files_op'] = '';
      // Loop through emails
      foreach ($emailFiles as $emailFile) {
        // Check is a file
        if (is_file($emailFilePath . _DS . $emailFile)) {
          // Get the file information
          $fileInformation = pathinfo($emailFilePath . _DS . $emailFile);
          // Get file name
          $fileName = ucwords(str_replace("-", " ", $fileInformation['filename']));
          // Check if .txt or .html
          switch (strtolower($fileInformation['extension'])) {
            case 'txt':
              $fileTypeOP = '<i class="fas fa-file-alt" data-tippy-content="TXT File"></i>';
              $linkType = '<a href="' . get_site_url('admin/templates/preview/emails/' . trim($emailFile)) . '" target="_blank" data-tippy-content="Preview ' . $fileName . ' txt file"><i class="fas fa-eye"></i></a>';
              break;
            case 'html':
              $fileTypeOP = '<i class="fab fa-html5" data-tippy-content="HTML File"></i>';
              $linkType = '<a href="' . get_site_url('admin/templates/preview/emails/' . trim($emailFile)) . '" target="_blank" data-tippy-content="Preview ' . $fileName . ' HTML file"><i class="fas fa-eye"></i></a>';
              break;
          }
          // Set to output
          $this->data['email_files_op'] .= '<article><p><strong>' . $fileName . '</strong><br><span class="cs-caption cs-muted"><strong>' . $fileTypeOP . '</strong> &middot; <strong data-tippy-content="Size">' . $this->formatSizeUnits(filesize($emailFilePath . _DS . $emailFile)) . '</strong></span></p><p>' . $linkType . '</p></article>';
        }
      }
    } else {
      // Set output
      $this->data['email_files_op'] = '<p class="cs-text-center csc-caption"><em>There are no email template files available</em></p>';
    }

    // Get list of PDF templates
    $pdfFilePath = DIR_SYSTEM . "pdfs";
    $pdfFiles = scandir($pdfFilePath);
    if (!empty($pdfFiles) && is_array($pdfFiles) && count($pdfFiles) > 2) {
      // Init output
      $this->data['pdf_files_op'] = '';
      // Loop through emails
      foreach ($pdfFiles as $pdfFile) {
        // Check is a file
        if (is_file($pdfFilePath . _DS . $pdfFile)) {
          // Get the file information
          $fileInformation = pathinfo($pdfFilePath . _DS . $pdfFile);
          // Get file name
          $fileName = ucwords(str_replace("-", " ", $fileInformation['filename']));
          // Set to output
          $this->data['pdf_files_op'] .= '<article><p><strong>' . $fileName . '</strong><br><span class="cs-caption cs-muted"><strong data-tippy-content="Size">' . $this->formatSizeUnits(filesize($pdfFilePath . _DS . $pdfFile)) . '</strong></span></p><p><a href="' . get_site_url('admin/templates/preview/pdfs/' . trim($pdfFile)) . '" target="_blank" data-tippy-content="Preview ' . $fileName . ' HTML file"><i class="fas fa-eye"></i></a></p></article>';
        }
      }
    } else {
      // Set output
      $this->data['pdf_files_op'] = '<p class="cs-text-center csc-caption"><em>There are no pdf template files available</em></p>';
    }
    // Load view
    $this->load->view('templates/index', $this->data, 'admin');
    exit;
  }

  // Preview Template
  public function preview(...$params)
  {
    // Check for the file type
    if (!empty($params[0])) {

      // Set the file type
      $fileType = strtolower(trim($params[0]));

      // Make sure the type is allowed
      if (in_array($fileType, array('emails', 'pdfs'))) {

        // check for the file
        if (!empty($params[1])) {

          // Set the file
          $fileName = trim($params[1]);

          // Check the file exists
          if (file_exists(DIR_SYSTEM . $fileType . _DS . $fileName)) {
            // Get the file
            $fileContents = file_get_contents(DIR_SYSTEM . $fileType . _DS . $fileName);
            // Replace any new lines and echo out contents
            echo str_replace("\\r\\n", "<br>", $fileContents);
            exit;
          } else { // File doesn't exist
            // Set error
            flashMsg('admin_templates', '<strong>Error</strong> Sorry, the template was not found. Please try again.', 'warning');
          }
        } else {
          // Set error
          flashMsg('admin_templates', '<strong>Error</strong> Sorry, no template was defined. Please try again.', 'warning');
        }
      } else {
        // Set error
        flashMsg('admin_templates', '<strong>Error</strong> Sorry, the template type "' . $fileType . '" is not allowed. Please try again.', 'warning');
      }
    } else {
      // Set error
      flashMsg('admin_templates', '<strong>Error</strong> Sorry, no template type was defined. Please try again.', 'warning');
    }
    // Redirect user
    redirectTo('admin/templates/');
    exit;
  }

  /**
   * Edit Page
   */
}
