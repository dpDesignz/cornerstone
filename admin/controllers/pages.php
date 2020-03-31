<?php

class Pages extends Controller
{

  /**
   * Class Constructor
   */
  public function __construct($registry)
  {
    // Load the controller constructor
    parent::__construct($registry);

    // Check if user is logged in
    if (!userPageProtect()) {

      // If user is not logged in, show the login page
      flashMsg('admin_login', 'You need to log in first.', 'warning');
      $this->load->view('common/login', '', 'admin');
      exit;
    } else {

      // Define the page type
      $this->pageType = 'page';

      // Load the content model
      $this->contentModel = $this->load->model('sitecontent/content', 'admin');

      // Set Breadcrumbs
      $this->data['breadcrumbs'] = array(
        array(
          'text' => 'Dashboard',
          'href' => get_site_url('admin')
        ),
        array(
          'text' => 'Site Content',
          'href' => ''
        ),
        array(
          'text' => 'Pages',
          'href' => get_site_url('admin/pages')
        )
      );
    }
  }

  /**
   * Load the index table
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function loadIndexTable(...$params)
  {

    // Set parameters
    $this->request->set_params($params);
    $this->params = array();
    $this->data['showFilter'] = FALSE;
    $this->data['filterData'] = '';

    // Check for a search term
    if (isset($this->request->params['search']) && !empty($this->request->params['search'])) {
      $this->params['search'] = $this->request->params['search'];
      $this->data['search'] = $this->params['search'];
      $this->data['breadcrumbs'][] = array(
        'text' => 'Search: ' . $this->params['search'],
        'href' => get_site_url('admin/pages/search/' . urlencode($this->params['search']))
      );
    }

    ############################
    #########  FILTERING #######
    ############################

    // Check for status filter
    $this->data['filterStatus'] = '';
    if (isset($this->request->params['status']) && !empty($this->request->params['status'])) {
      $this->params['filter_status'] = strtolower($this->request->params['status']);
      $this->data['filterStatus'] = $this->params['filter_status'];
      // Set filter data
      $this->data['filterData'] .= 'Page Status, ';
      $this->data['showFilter'] = TRUE;
    }

    // Allowed sort fields
    $this->canSortBy = array('title' => 'content_title', 'creator' => 'section_added_id', 'section' => 'section_name', 'updated' => 'section_edited_id');

    // Check for sort
    $sortOrder = get_sort_order($this->canSortBy, array('sort' => 'content_title', 'order' => 'ASC'), ...$params);

    // Set sort to params
    foreach ($sortOrder as $key => $value) {
      $this->params[$key] = $value;
    }

    // Set the default sort item
    $this->data['defaultSort'] = 'title';

    // Set show filter
    if (!empty($this->params['showFilter'])) {
      $this->data['showFilter'] = $this->params['showFilter'];
      $this->data['filterData'] .= 'Sort by = ' . $this->params['sortFilter'] . ', ';
    }

    // Check for page number
    if (isset($this->request->params['page']) && !empty($this->request->params['page'])) {
      // Set page number
      $this->params['page'] = (int) $this->request->params['page'];
    } else { // No page number. Set page number
      $this->params['page'] = 1;
    }

    // Check for a page limit
    if (isset($this->request->params['limit']) && !empty($this->request->params['limit'])) {
      $this->params['limit'] = (int) $this->request->params['limit'];
    } else { // No page limit. Set page limit
      $this->params['limit'] = 25;
    }

    // Output pages list

    // Get pages
    if ($dataList = $this->contentModel->listPages($this->params)) {

      // Count how many items match the results
      $this->params['count'] = TRUE;
      $this->data['totalResults'] = ($this->contentModel->listPages($this->params))->results[0]->total_results;

      // Set data list output
      $dataListOut = '';

      // Set the pagination
      $pagination = new Pagination;
      $pagination->total_records = (int) $this->data['totalResults'];
      $pagination->current_page = (int) $this->params['page'];
      $pagination->items_per_page = (int) $this->params['limit'];
      $this->data['pagination'] = $pagination->render();

      // Loop through data
      foreach ($dataList->results as $data) {

        // Set fallback section name
        $sectionName = (empty($data->section_name)) ? 'Main Site' : $data->section_name;

        // Set fallback for section directory
        $sectionDirectory = (empty($data->section_directory_name)) ? '' : $data->section_directory_name . '/';

        // Set fallback for view link
        $viewLink = (!empty($data->content_slug)) ? ' | <a href="' . get_site_url($sectionDirectory . $data->content_slug) . '" target="_blank">View</a>' : '';

        // Set added by
        $addedDtmShort = (empty($data->content_added_dtm)) ? 'n/a' : date_format(date_create($data->content_added_dtm), "M j Y");
        $addedDtm = (empty($data->content_added_dtm)) ? 'n/a' : date_format(date_create($data->content_added_dtm), "D, jS M Y \@ g:ia");
        $addedBy = (empty($data->added_by)) ? 'n/a' : 'Created by ' . $data->added_by . ' on ' . $addedDtm;

        // Set last updated
        $lastUpdatedShort = (empty($data->content_edited_dtm)) ? $addedDtmShort : date_format(date_create($data->content_edited_dtm), "M j Y");
        $lastUpdated = (empty($data->content_edited_dtm)) ? $addedDtm : date_format(date_create($data->content_edited_dtm), "D, jS M Y \@ g:ia");
        $lastUpdatedBy = (empty($data->edited_by)) ? $addedBy : 'Last updated by ' . $data->edited_by . ' on ' . $lastUpdated;
        $dateType = (empty($data->edited_by)) ? 'Created:' : 'Last Updated:';

        // Output status
        switch ($data->content_status) {
          case '0':
            $status = 'draft';
            break;
          case '1':
            $status = '';
            break;
          case '2':
            $status = 'private';
            break;
          case '3':
            $status = 'archived';
            break;

          default:
            $status = 'draft';
            break;
        }

        // Set status output
        $statusOutput = (!empty($status)) ? ' <span class="cs-muted"> &mdash; ' . ucfirst($status) . '</span>' : '';

        // Set row output
        $dataListOut .= '<tr>
            <td class="has-hover-item">
              <strong class="item--title"><a href="' . get_site_url('admin/pages/edit/' . $data->content_id) . '" title="Edit ' . htmlspecialchars_decode($data->content_title) . '" class="tooltip">' . htmlspecialchars_decode($data->content_title) . '</a>' . $statusOutput . '</strong>
              <span class="hover-item cs-caption cs-muted"><a href="' . get_site_url('admin/pages/edit/' . $data->content_id) . '">Edit</a> ' . $viewLink . '</span>
            </td>
            <td>' . $data->added_by . '</td>
            <td>' . $sectionName . '</td>
            <td><span class="tooltip cs-muted" title="' . $lastUpdatedBy . '"><strong>' . $dateType . '</strong><br>' . $lastUpdatedShort . '</span></td>
          </tr>';
      }

      // Output data List
      $this->data['dataListOut'] = $dataListOut;
    } else { // No results. Output message.

      // Set the pagination
      $this->data['pagination'] = '';

      // Set dataListOut to message
      if ($this->data['showFilter']) {
        // No filter results. Output message.
        $outputMessage = '<p class="csc-body1">Sorry, there were no results that matched your filter.</p>';
      } else if (!empty($this->data['search'])) {
        // No search results. Output message
        $outputMessage = '<p class="csc-body1">Sorry, there were no results that matched your search for <em>"' . $this->data['search'] . '"</em>.</p><p class="csc-body2"><a href="' . get_site_url('admin/pages') . '" title="Clear search results">Clear search results</a></p>';
      } else {
        // No results. Output default.
        $this->data['noData'] = TRUE;
        $outputMessage = '';
      }

      // Set output
      $this->data['dataListOut'] = '<tr><td colspan="4" id="no-results">' . $outputMessage . '</td></tr>';
    }

    // Trim filter data
    $this->data['filterData'] = rtrim($this->data['filterData'], ', ');

    // Return as a json object if isset
    if (array_search('json', $params) !== FALSE) {
      header('Content-Type: application/json');
      echo json_encode(['StatusCode' => 200, 'tableData' => $this->data['dataListOut']]);
    }
  }

  /**
   * Index Page
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function index(...$params)
  {

    // Check for search and rebuild URL
    if (isset($this->request->get['search'])) {
      redirectTo('admin/pages/search/' . urlencode($this->request->get['search']));
      exit;
    }

    // Output pages list
    $this->loadIndexTable(...$params);

    // Load view
    $this->load->view('pages/index', $this->data, 'admin');
    exit;
  }

  /**
   * Set Add Page Data
   *
   * @param int $setStatus `[optional]` Status of page. Defaults to published.
   * @param int $setSection `[optional]` Section of page. Defaults to "0" (Main).
   */
  protected function setAddData(int $setStatus = 1, int $setSection = 0)
  {

    // Page Type
    $this->data['page_type'] = 'add';
    // Action URL
    $this->data['action_url'] = get_site_url('admin/pages/add/');
    // H1
    $this->data['page_title'] = 'Add Page';
    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => $this->data['page_title'],
      'href' => $this->data['action_url']
    );
    // Instructions
    $this->data['instructions'] = 'Enter the details for the new page to add it.';

    ###########################
    ####    STATUS LIST    ####
    ###########################

    // Set status list options
    $statusOptionsData = array(0 => 'Draft', 1 => 'Published', 2 => 'Private', 3 => 'Archived');
    $statusOptions = '';
    // Get list of status for assigning
    foreach ($statusOptionsData as $value => $label) {

      // Set selected if chosen type
      $selected = (!empty($setStatus) && $setStatus == $value) ? ' selected' : '';

      // Set to output
      $statusOptions .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
    }
    // Set status list options to data
    $this->data['status_options'] = $statusOptions;

    ########################
    ####    SECTIONS    ####
    ########################

    // Init section options
    $sectionOptions = '<option></option>';
    // Get list of sections for assigning
    if ($sectionsData = $this->contentModel->listPageSections()) {
      // Sections data exists

      // Loop through sections data
      foreach ($sectionsData as $section) {

        // Set selected if chosen type
        $selected = (!empty($setSection) && $setSection == $section->section_id) ? ' selected' : '';

        // Set to output
        $sectionOptions .= '<option value="' . $section->section_id . '"' . $selected . '>' . $section->section_name . '</option>';
      }
    }
    // Set blank fallback
    $sectionOptions = ($sectionOptions !== '<option></option>') ? $sectionOptions : $sectionOptions . '<option disabled>There are currently no section available to assign this page to.</option>';
    // Set section list options to data
    $this->data['section_options'] = $sectionOptions;
  }

  /**
   * Add Page
   */
  public function add()
  {

    // Process "add"

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "save") {

      // Get content data
      // This is needing to be done before the sanitization to keep the markup
      $this->data['content'] = trim($_POST['content']);
      if (empty($this->data['content'])) {
        // Data not set. Return error.
        $this->data['err']['content'] = 'Please enter some content';
        flashMsg('pages_page', '<strong>Error</strong> There was an error adding the page. Please enter some content.', 'warning');
      }

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Get information submitted and validate
      if (isset($_POST['title'])) {

        // Try validating
        try {

          // Get title data
          $this->data['title'] = htmlspecialchars(stripslashes(trim($_POST['title'])));
          if (empty($this->data['title'])) {
            // Data not set. Return error.
            $this->data['err']['title'] = 'Please enter a page title';
          } else if (strlen($this->data['title']) < 3) {
            // Data is less than 3 characters. Return error.
            $this->data['err']['title'] = 'Please enter at least 3 characters';
          }

          // Get Meta Title
          $this->data['meta_title'] = htmlspecialchars(stripslashes(trim($_POST['meta_title'])));
          if (strlen($this->data['meta_title']) > 70) {
            // Data is more than 70 characters. Return error.
            $this->data['err']['meta_title'] = 'This is limited to 70 characters';
          }

          // Get Meta Description
          $this->data['meta_description'] = htmlspecialchars(stripslashes(trim($_POST['meta_description'])));
          if (strlen($this->data['meta_description']) > 168) {
            // Data is more than 168 characters. Return error.
            $this->data['err']['meta_description'] = 'This is limited to 168 characters';
          }

          // Get status data
          $this->data['status'] = htmlspecialchars(stripslashes(trim($_POST['status'])));
          if (!is_numeric($this->data['status'])) {
            // Data is not a valid type. Return error.
            $this->data['err']['status'] = 'Please select a valid page status';
          }

          // Get section data
          $this->data['section_id'] = htmlspecialchars(stripslashes(trim($_POST['section_id'])));
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          flashMsg('pages_page', '<strong>Error</strong> There was an error adding the page. Please try again', 'warning');
        }
      } else { // Required data not set. Set Errors.

        $this->data['err']['title'] = 'Please enter a page title';
      }

      // If valid, add new
      if (empty($this->data['err'])) {
        // Validated

        // Add new page
        if ($contentID = $this->contentModel->addPage($this->data['title'], $this->data['content'], (int) $this->data['status'], (int) $this->data['section_id'])) {
          // Added

          // Add page meta data
          $this->contentModel->addPageMetaData((int) $contentID, $this->data['meta_title'], $this->data['meta_description']);

          // Load the Cornerstone Core model
          $this->cornerstoneCoreModel = $this->load->model('common/cornerstonecore');

          // Set SEO URL
          $this->cornerstoneCoreModel->checkSEOURL((int) 0, (int) $contentID, $this->data['title']);

          // Set success message
          flashMsg('admin_pages', '<strong>Success</strong> The "' . $this->data['title'] . '" page was added successfully');

          // Return to list
          redirectTo('admin/pages/');
          exit;
        } // Unable to add. Return error.

        // Set error message
        flashMsg('pages_page', '<strong>Error</strong> There was an error adding the page. Please try again.', 'warning');
      }

      // If it's made it this far there were errors. Load add view with submitted data

      // Set Add Data
      $this->setAddData((int) $this->data['status'], $this->data['section_id']);
    } else { // Page wasn't posted. Load view.

      // Set Add Data
      $this->setAddData();
    }

    // Load add view
    $this->load->view('pages/page', $this->data, 'admin');
    exit;
  }

  /**
   * Set Edit Page Data
   *
   * @param int $setStatus Status of page.
   * @param int $setSection `[optional]` Section of page. Defaults to "0" (Main).
   */
  protected function setEditData(int $setStatus, int $setSection = 0)
  {

    // Page Type
    $this->data['page_type'] = 'edit';
    // Action URL
    $this->data['action_url'] = get_site_url('admin/pages/edit/' . $this->data['id']);
    // H1
    $this->data['page_title'] = 'Edit ' . $this->data['title'];
    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => $this->data['page_title'],
      'href' => $this->data['action_url']
    );
    // Instructions
    $this->data['instructions'] = 'Enter the new details for ' . $this->data['title'] . ' to update it.';

    ###########################
    ####    STATUS LIST    ####
    ###########################

    // Set status list options
    $statusOptionsData = array(0 => 'Draft', 1 => 'Published', 2 => 'Private', 3 => 'Archived');
    $statusOptions = '';
    // Get list of status for assigning
    foreach ($statusOptionsData as $value => $label) {

      // Set selected if chosen type
      $selected = (!empty($setStatus) && $setStatus == $value) ? ' selected' : '';

      // Set to output
      $statusOptions .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
    }
    // Set status list options to data
    $this->data['status_options'] = $statusOptions;

    ########################
    ####    SECTIONS    ####
    ########################

    // Init section options
    $sectionOptions = '<option></option>';
    // Get list of sections for assigning
    if ($sectionsData = $this->contentModel->listPageSections()) {
      // Sections data exists

      // Loop through sections data
      foreach ($sectionsData as $section) {

        // Set selected if chosen type
        $selected = (!empty($setSection) && $setSection == $section->section_id) ? ' selected' : '';

        // Set to output
        $sectionOptions .= '<option value="' . $section->section_id . '"' . $selected . '>' . $section->section_name . '</option>';
      }
    }
    // Set blank fallback
    $sectionOptions = ($sectionOptions !== '<option></option>') ? $sectionOptions : $sectionOptions . '<option disabled>There are currently no section available to assign this page to.</option>';
    // Set section list options to data
    $this->data['section_options'] = $sectionOptions;
  }

  /**
   * Edit Page
   */
  public function edit(...$params)
  {

    // Process "edit"

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "save") {

      // Get content data
      // This is needing to be done before the sanitization to keep the markup
      $this->data['content'] = trim($_POST['content']);
      if (empty($this->data['content'])) {
        // Data not set. Return error.
        $this->data['err']['content'] = 'Please enter some content';
        flashMsg('pages_page', '<strong>Error</strong> There was an error updating the page. Please enter some content.', 'warning');
      }

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Check ID
      if (!empty($params) && is_numeric($params[0]) && $params[0] == $_POST['id']) {

        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Get information submitted and validate
        if (isset($_POST['title'])) {

          // Try validating
          try {

            // Get ID
            $this->data['id'] = htmlspecialchars(stripslashes(trim($_POST['id'])));

            // Get title data
            $this->data['title'] = htmlspecialchars(stripslashes(trim($_POST['title'])));
            if (empty($this->data['title'])) {
              // Data not set. Return error.
              $this->data['err']['title'] = 'Please enter a page title';
            } else if (strlen($this->data['title']) < 3) {
              // Data is less than 3 characters. Return error.
              $this->data['err']['title'] = 'Please enter at least 3 characters';
            }

            // Get Meta Title
            $this->data['meta_title'] = htmlspecialchars(stripslashes(trim($_POST['meta_title'])));
            if (strlen($this->data['meta_title']) > 70) {
              // Data is more than 70 characters. Return error.
              $this->data['err']['meta_title'] = 'This is limited to 70 characters';
            }

            // Get Meta Description
            $this->data['meta_description'] = htmlspecialchars(stripslashes(trim($_POST['meta_description'])));
            if (strlen($this->data['meta_description']) > 168) {
              // Data is more than 168 characters. Return error.
              $this->data['err']['meta_description'] = 'This is limited to 168 characters';
            }

            // Get status data
            $this->data['status'] = htmlspecialchars(stripslashes(trim($_POST['status'])));
            if (!is_numeric($this->data['status'])) {
              // Data is not a valid type. Return error.
              $this->data['err']['status'] = 'Please select a valid page status';
            }

            // Get section data
            $this->data['section_id'] = htmlspecialchars(stripslashes(trim($_POST['section_id'])));
          } catch (Exception $e) {

            // Log error if any and set flash message
            error_log($e->getMessage(), 0);
            flashMsg('pages_page', '<strong>Error</strong> There was an error updating the page. Please try again', 'warning');
          }
        } else { // Required data not set. Set Errors.

          $this->data['err']['title'] = 'Please enter a page title';
        }

        // If valid, update
        if (empty($this->data['err'])) {
          // Validated

          // Set ID
          $dataID = $this->data['id'];

          // Update
          if ($this->contentModel->editPage((int) $dataID, $this->data['title'], $this->data['content'], (int) $this->data['status'], (int) $this->data['section_id'])) {
            // Updated

            // Edit page meta data
            $this->contentModel->editPageMetaData((int) $dataID, $this->data['meta_title'], $this->data['meta_description']);

            // Set success message
            flashMsg('admin_pages', '<strong>Success</strong> The "' . $this->data['title'] . '" page was updated successfully.');

            // Return to list view
            redirectTo('admin/pages');
            exit;
          } else { // Unable to update. Return error and redirect to edit view.

            // Set error message
            flashMsg('pages_page', '<strong>Error</strong> There was an error updating the page. Please contact your admin to get this fixed.', 'danger');
          }
        }

        // If it's made it this far there were errors. Load edit view with data

        // Set edit data
        $this->setEditData((int) $this->data['status'], (int) $this->data['section_id']);

        // Load view
        $this->load->view('pages/page', $this->data, 'admin');
        exit;
      } else { // Error with the ID. Redirect to list view with error.

        // Set Error
        flashMsg('admin_sections', '<strong>Error</strong> There was an error saving the section. Please try again', 'warning');
        redirectTo('admin/sections');
        exit;
      }
    } else { // Page wasn't posted. Load view.

      // Check ID
      if (!empty($params) && is_numeric($params[0])) {

        // Get data
        if ($contentData = $this->contentModel->getPage((int) $params[0])) {

          // Set data
          foreach ($contentData->content as $key => $data) {
            $this->data[str_replace(array('content_'), '', $key)] = $data;
          }
          foreach ($contentData->content_meta as $key => $data) {
            $this->data[str_replace(array('content_'), '', $key)] = $data;
          }

          // Set Edit Data
          $this->setEditData((int) $this->data['status'], (int) $this->data['section_id']);

          // Load view
          $this->load->view('pages/page', $this->data, 'admin');
          exit;
        } // Error getting the data. Redirect to list.

      } // No ID present. Redirect to list.

      // Redirect user
      redirectTo('admin/pages');
      exit;
    }
  }
}
