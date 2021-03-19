<?php

/**
 * Pages Controller
 *
 * @package Cornerstone
 */

class Page extends Controller
{

  /**
   * Class Constructor
   */
  public function __construct($registry)
  {
    // Load the controller constructor
    parent::__construct($registry);

    // Set home menu item
    $this->data['menuitems'] = array(
      array(
        'path' => 0,
        'text' => 'Home',
        'href' => get_site_url()
      )
    );
  }

  // Index Page
  public function index(...$params)
  {

    $this->load->view('pages/index', $this->data);
  }

  // Load Page
  public function loadpage(...$params)
  {

    // Get the friendly URL if exists
    if (!empty($params[0])) {

      // Load the core model
      $this->coreModel = $this->load->model('common/cornerstonecore');

      // Check for the friendly URL type
      if ($seoData = $this->coreModel->getSEOData(trim($params[0]))) {
        // Keyword exists

        // Set seo data to the params array
        array_unshift($params, $seoData);

        // Set SEO Keyword to data
        $this->data['seo_keyword'] = $seoData->seo_keyword;

        // Check if the page exists
        if ($pageData = $this->coreModel->getContentData((int) $seoData->seo_type_id)) {
          // Page exists

          // Check if directory name is set
          if (!empty($pageData->content->section_location_name)) {
            // Directory set. Redirect user to correct page loader
            redirectTo($pageData->content->section_location_name . "/" . $seoData->seo_keyword);
            exit;
          }

          // Set page data
          foreach ($pageData->content as $key => $data) {
            $this->data[$key] = htmlspecialchars_decode($data);
          }

          // Set meta data
          foreach ($pageData->content_meta as $key => $data) {
            $this->data[$key] = htmlspecialchars_decode($data);
          }

          // Set page meta title
          $this->data['page_meta_title'] = (!empty($this->data['content_meta_title'])) ? $this->data['content_meta_title'] : $this->data['content_title'];

          // Set page meta description
          $this->data['page_meta_description'] = (empty($this->data['content_meta_description'])) ? trim(preg_replace('/\s+/', ' ', (new \Html2Text\Html2Text($this->data['content_content']))->getText())) : $this->data['content_meta_description'];
          $this->data['page_meta_description'] = (strlen($this->data['page_meta_description']) > 166) ? substr($this->data['page_meta_description'], 0, 165) . '...' : $this->data['page_meta_description']; // Trim if more than 166 characters
          $this->data['page_meta_description'] = filter_var($this->data['page_meta_description'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH); // Fix any unicode characters that slip in

          // Set page meta canonical
          $this->data['page_meta_canonical'] = (!empty($this->data['content_meta_canonical'])) ? $this->data['content_meta_canonical'] : $seoData->seo_keyword;

          // Set page head extras
          $this->data['page_head_extras'] = (!empty($this->data['content_head_extras'])) ? $this->data['content_head_extras'] : '';

          // Set page footer extras
          $this->data['page_footer_extras'] = (!empty($this->data['content_footer_extras'])) ? $this->data['content_footer_extras'] : '';

          // Check for FAQ data to output
          $opFAQSection = new OPContent;
          $this->data['content_content'] = $opFAQSection->checkStringFAQ($this->data['content_content']);

          // Load page
          $this->load->view('pages/page', $this->data);
          exit;
        } // Page doesn't exist. Redirect to 404
      } else { // Page doesn't exist. Redirect to 404
        // Load error page
        $this->error();
        exit;
      }
    } else { // Friendly URL doesn't exist. Load index page
      // Load index page
      $this->index();
      exit;
    }
  }

  // Get changelog contents
  public function changelog()
  {
    // Check if user is logged in
    if (!userPageProtect()) {

      // If user is not logged in, show the login page
      flashMsg('admin_login', 'You need to log in first.', 'warning');
      $this->load->view('common/login', '', 'admin');
      exit;
    } else { // Output dashboard

      if (file_exists(DIR_ROOT . 'CHANGELOG.md')) {
        $data['contents'] = str_replace('`', '\`', addslashes(file_get_contents(DIR_ROOT . 'CHANGELOG.md')));

        $this->load->view('pages/changelog', $data);
        exit;
      } else { // File doesn't exist. Redirect to error page.

        $this->error();
      }
    }
  }
}
