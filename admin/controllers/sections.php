<?php

class Sections extends Controller
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
      $this->pageType = 'section';

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
          'text' => 'Sections',
          'href' => get_site_url('admin/sections')
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
        'href' => get_site_url('admin/sections/search/' . urlencode($this->params['search']))
      );
    }

    ############################
    #########  FILTERING #######
    ############################

    // Allowed sort fields
    $this->canSortBy = array('name' => 'section_name', 'directory' => 'section_directory_name', 'type' => 'section_type');

    // Check for sort
    $sortOrder = get_sort_order($this->canSortBy, array('sort' => 'section_name', 'order' => 'ASC'), ...$params);

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
    if ($dataList = $this->contentModel->listSections($this->params)) {

      // Count how many items match the results
      $this->params['count'] = TRUE;
      $this->data['totalResults'] = ($this->contentModel->listSections($this->params))->results[0]->total_results;

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

        // Set fallback for section directory
        $sectionDirectory = (empty($data->section_directory_name)) ? 'Main Site' : $data->section_directory_name . '/';

        // Set section type
        switch ((int) $data->section_type) {
          case 1:
            $sectionType = 'FAQ';
            break;

          default:
            $sectionType = 'Page';
            break;
        }

        // Set row output
        $dataListOut .= '<tr class="has-hover-item">
            <td>
              <strong class="item--title"><a href="' . get_site_url('admin/sections/edit/' . $data->section_id) . '" title="Edit ' . htmlspecialchars_decode($data->section_name) . '" class="tooltip">' . htmlspecialchars_decode($data->section_name) . ' <span class="hover-item"><i class="fas fa-edit"></i></span></a></strong>

            </td>
            <td>' . $sectionDirectory . '</td>
            <td>' . $sectionType . '</td>
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
        $outputMessage = '<p class="csc-body1">Sorry, there were no results that matched your search for <em>"' . $this->data['search'] . '"</em>.</p><p class="csc-body2"><a href="' . get_site_url('admin/sections') . '" title="Clear search results">Clear search results</a></p>';
      } else {
        // No results. Output default.
        $this->data['noData'] = TRUE;
        $outputMessage = '';
      }

      // Set output
      $this->data['dataListOut'] = '<tr><td colspan="3" id="no-results">' . $outputMessage . '</td></tr>';
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
      redirectTo('admin/sections/search/' . urlencode($this->request->get['search']));
      exit;
    }

    // Output pages list
    $this->loadIndexTable(...$params);

    // Load view
    $this->load->view('sections/index', $this->data, 'admin');
    exit;
  }

  /**
   * Set Add Page Data
   *
   * @param int $setType `[optional]` Type of section. Defaults to page.
   * @param string $setDirectory `[optional]` Directory of section. Defaults to null (Main).
   */
  protected function setAddData(int $setType = 0, string $setDirectory = null)
  {

    // Page Type
    $this->data['page_type'] = 'add';
    // Action URL
    $this->data['action_url'] = get_site_url('admin/sections/add/');
    // H1
    $this->data['page_title'] = 'Add Section';
    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => $this->data['page_title'],
      'href' => $this->data['action_url']
    );
    // Instructions
    $this->data['instructions'] = 'Enter the details for the new section to add it.';

    #########################
    ####    TYPE LIST    ####
    #########################

    // Set type list options
    $typeOptionsData = array(0 => 'Pages', 1 => 'FAQ');
    $typeOptions = '';
    // Get list of types for assigning
    foreach ($typeOptionsData as $value => $label) {

      // Set selected if chosen type
      $selected = (!empty($setType) && $setType == $value) ? ' selected' : '';

      // Set to output
      $typeOptions .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
    }
    // Set type list options to data
    $this->data['type_options'] = $typeOptions;

    ###########################
    ####    DIRECTORIES    ####
    ###########################

    // Init directory options
    $directoryOptions = '<option></option>';
    // Get list of types for assigning
    foreach (ALLOWED_SUBFOLDERS as $directory => $defaults) {

      // Skip Admin folder
      if (strtolower($directory) !== 'admin') {

        // Set selected if chosen type
        $selected = (!empty($setDirectory) && $setDirectory == $directory) ? ' selected' : '';

        // Set to output
        $directoryOptions .= '<option value="' . strtolower($directory) . '"' . $selected . '>' . ucwords($directory) . '</option>';
      }
    }
    // Set blank fallback
    $directoryOptions = ($directoryOptions !== '<option></option>') ? $directoryOptions : $directoryOptions . '<option disabled>There are currently no directories available to assign this section to.</option>';
    // Set directory list options to data
    $this->data['directory_options'] = $directoryOptions;
  }

  /**
   * Add Page
   */
  public function add()
  {

    // Process "add"

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "save") {

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Get information submitted and validate
      if (isset($_POST['name'])) {

        // Try validating
        try {

          // Get name data
          $this->data['name'] = htmlspecialchars(stripslashes(trim($_POST['name'])));
          if (empty($this->data['name'])) {
            // Name not set. Return error.
            $this->data['err']['name'] = 'Please enter a section name';
          } else if (strlen($this->data['name']) < 3) {
            // Name is less than 3 characters. Return error.
            $this->data['err']['name'] = 'Please enter at least 3 characters';
          }

          // Get type data
          $this->data['type'] = htmlspecialchars(stripslashes(trim($_POST['type'])));
          if (!is_numeric($this->data['type'])) {
            // Data is not a valid type. Return error.
            $this->data['err']['type'] = 'Please select a valid section type';
          }

          // Get directory data
          $this->data['directory_name'] = htmlspecialchars(stripslashes(trim($_POST['directory_name'])));
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          flashMsg('sections_section', '<strong>Error</strong> There was an error adding the section. Please try again', 'warning');
        }
      } else { // Required data not set. Set Errors.

        $this->data['err']['name'] = 'Please enter a section name';
      }

      // If valid, add new
      if (empty($this->data['err'])) {
        // Validated

        // Add new section
        if ($this->contentModel->addSection($this->data['name'], (int) $this->data['type'], $this->data['directory_name'])) {
          // Added

          // Set success message
          flashMsg('admin_sections', '<strong>Success</strong> ' . $this->data['name'] . ' was added successfully');

          // Return to list
          redirectTo('admin/sections/');
          exit;
        } // Unable to add. Return error.

        // Set error message
        flashMsg('sections_section', '<strong>Error</strong> There was an error adding the section. Please try again.', 'warning');
      }

      // If it's made it this far there were errors. Load add view with submitted data

      // Set Add Data
      $this->setAddData((int) $this->data['type'], $this->data['directory_name']);
    } else { // Page wasn't posted. Load view.

      // Set Add Data
      $this->setAddData();
    }

    // Load add view
    $this->load->view('sections/section', $this->data, 'admin');
    exit;
  }

  /**
   * Set Edit Page Data
   *
   * @param int $setType Type of section.
   * @param string $setDirectory `[optional]` Directory of section. Defaults to null (Main).
   */
  protected function setEditData(int $setType, string $setDirectory = null)
  {

    // Page Type
    $this->data['page_type'] = 'edit';
    // Action URL
    $this->data['action_url'] = get_site_url('admin/sections/edit/' . $this->data['id']);
    // H1
    $this->data['page_title'] = 'Edit ' . $this->data['name'];
    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => $this->data['page_title'],
      'href' => $this->data['action_url']
    );
    // Instructions
    $this->data['instructions'] = 'Enter the new details for ' . $this->data['name'] . ' to update it.';

    #########################
    ####    TYPE LIST    ####
    #########################

    // Set type list options
    $typeOptionsData = array(0 => 'Pages', 1 => 'FAQ');
    $typeOptions = '';
    // Get list of types for assigning
    foreach ($typeOptionsData as $value => $label) {

      // Set selected if chosen type
      $selected = (!empty($setType) && $setType == $value) ? ' selected' : '';

      // Set to output
      $typeOptions .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
    }
    // Set type list options to data
    $this->data['type_options'] = $typeOptions;

    ###########################
    ####    DIRECTORIES    ####
    ###########################

    // Init directory options
    $directoryOptions = '<option></option>';
    // Get list of types for assigning
    foreach (ALLOWED_SUBFOLDERS as $directory => $defaults) {

      // Skip Admin folder
      if (strtolower($directory) !== 'admin') {

        // Set selected if chosen type
        $selected = (!empty($setDirectory) && $setDirectory == $directory) ? ' selected' : '';

        // Set to output
        $directoryOptions .= '<option value="' . strtolower($directory) . '"' . $selected . '>' . ucwords($directory) . '</option>';
      }
    }
    // Set blank fallback
    $directoryOptions = ($directoryOptions !== '<option></option>') ? $directoryOptions : $directoryOptions . '<option disabled>There are currently no directories available to assign this section to.</option>';
    // Set directory list options to data
    $this->data['directory_options'] = $directoryOptions;
  }

  /**
   * Edit Page
   */
  public function edit(...$params)
  {

    // Process "edit"

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "save") {

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Check ID
      if (!empty($params) && is_numeric($params[0]) && $params[0] == $_POST['id']) {

        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Get information submitted and validate
        if (isset($_POST['name'])) {

          // Try validating
          try {

            // Get ID
            $this->data['id'] = htmlspecialchars(stripslashes(trim($_POST['id'])));

            // Get name data
            $this->data['name'] = htmlspecialchars(stripslashes(trim($_POST['name'])));
            if (empty($this->data['name'])) {
              // Name not set. Return error.
              $this->data['err']['name'] = 'Please enter a section name';
            } else if (strlen($this->data['name']) < 3) {
              // Name is less than 3 characters. Return error.
              $this->data['err']['name'] = 'Please enter at least 3 characters';
            }

            // Get type data
            $this->data['type'] = htmlspecialchars(stripslashes(trim($_POST['type'])));
            if (!is_numeric($this->data['type'])) {
              // Data is not a valid type. Return error.
              $this->data['err']['type'] = 'Please select a valid section type';
            }

            // Get directory data
            $this->data['directory_name'] = htmlspecialchars(stripslashes(trim($_POST['directory_name'])));
          } catch (Exception $e) {

            // Log error if any and set flash message
            error_log($e->getMessage(), 0);
            flashMsg('sections_section', '<strong>Error</strong> There was an error updating the section. Please try again', 'warning');
          }
        } else { // Required data not set. Set Errors.

          $this->data['err']['name'] = 'Please enter a section name';
        }

        // If valid, update
        if (empty($this->data['err'])) {
          // Validated

          // Set ID
          $dataID = $this->data['id'];

          // Update
          if ($this->contentModel->editSection((int) $dataID, $this->data['name'], (int) $this->data['type'], $this->data['directory_name'])) {
            // Updated

            // Set success message
            flashMsg('admin_sections', '<strong>Success</strong> ' . $this->data['name'] . ' was updated successfully.');

            // Return to list view
            redirectTo('admin/sections');
            exit;
          } else { // Unable to update. Return error and redirect to edit view.

            // Set error message
            flashMsg('sections_section', '<strong>Error</strong> There was an error editing the section. Please contact your admin to get this fixed.', 'danger');
          }
        }

        // If it's made it this far there were errors. Load edit view with data

        // Set edit data
        $this->setEditData((int) $this->data['type'], $this->data['directory_name']);

        // Load view
        $this->load->view('sections/section', $this->data, 'admin');
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
        if ($contentData = $this->contentModel->getSection((int) $params[0])) {

          // Set data
          foreach ($contentData as $key => $data) {
            $this->data[str_replace(array('section_'), '', $key)] = $data;
          }

          // Set Edit Data
          $this->setEditData((int) $this->data['type'], $this->data['directory_name']);

          // Load view
          $this->load->view('sections/section', $this->data, 'admin');
          exit;
        } // Error getting the data. Redirect to list.

      } // No ID present. Redirect to list.

      // Redirect user
      redirectTo('admin/sections');
      exit;
    }
  }
}
