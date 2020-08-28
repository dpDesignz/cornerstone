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

    // Init list page
    $this->init_list_page('admin/pages', ...$params);

    ############################
    #########  FILTERING #######
    ############################

    // Check for sort
    $this->get_sort_order(
      array('name' => 'section_name', 'location' => 'section_location_name', 'type' => 'section_type'), // Allowed sort fields
      array('sort' => 'section_name', 'order' => 'ASC'), // Fallback
      ...$params
    );

    // Output data list

    // Get data
    if ($dataList = $this->contentModel->listSections($this->params)) {

      // Count how many items match the results
      $this->params['count'] = TRUE;
      $this->data['totalResults'] = ($this->contentModel->listSections($this->params))->results[0]->total_results;

      // Set data list output
      $dataListOut = '';

      // Set the pagination
      $pagination = new Pagination;
      $pagination->set_props((int) $this->data['totalResults'], (int) $this->params['page'], (int) $this->params['limit']);
      $this->data['pagination'] = $pagination->render();

      // Loop through data
      foreach ($dataList->results as $data) {

        // Set edit link
        $showTitle = ($this->role->canDo('edit_section')) ? '<a href="' . get_site_url('admin/sections/edit/' . $data->section_id) . '" data-tippy-content="Edit ' . htmlspecialchars_decode($data->section_name) . '">' . htmlspecialchars_decode($data->section_name) . '</a>' : htmlspecialchars_decode($data->section_name);
        $editLink = ($this->role->canDo('edit_section')) ? '<a href="' . get_site_url('admin/sections/edit/' . $data->section_id) . '">Edit</a>' : '';

        // Init extra link
        $extraLink = '';

        // Init item counts
        $itemCount = '';

        // Set fallback for section location
        $sectionLocation = (empty($data->section_location_name)) ? 'Main Site' : $data->section_location_name . '/';

        // Set section type
        switch ((int) $data->section_type) {
          case 1:
            $sectionType = 'FAQ';
            // FAQ location code
            $sectionLocation = '<code>[faqs:' . $data->section_id . ']</code>';
            // View FAQ link
            $extraLink = ($this->role->canDo('view_faq_section')) ? ' | <a href="' . get_site_url('admin/sections/faq/' . $data->section_id) . '">View FAQs</a>' : '';
            // Count how many FAQS are linked
            $itemCount = ' | <strong>Total Linked FAQs:</strong> ' . $this->contentModel->countFAQLinks((int) $data->section_id);
            break;
          case 5:
            // Section type
            $sectionType = 'Menu';
            // Menu location code
            $sectionLocation = '<code>[menu:' . $data->section_id . ']</code>';
            // View menu link
            $extraLink = ($this->role->canDo('view_menu')) ? ' | <a href="' . get_site_url('admin/sections/menu/' . $data->section_id) . '">View Menu</a>' : '';
            // Count how many menu items
            $itemCount = ' | <strong>Total Menu Items:</strong> ' . $this->contentModel->countMenuItems((int) $data->section_id);
            break;

          default:
            $sectionType = 'Page';
            break;
        }

        // Set row output
        $dataListOut .= '<tr class="has-hover-item">
            <td>
              <strong class="item--title">' . $showTitle . '</strong>
              <span class="hover-item cs-caption cs-muted">' . $editLink . $extraLink . $itemCount . '</span>
            </td>
            <td>' . $sectionLocation . '</td>
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
        $outputMessage = $this->data['no_filter_results_msg'];
      } else if (!empty($this->data['search'])) {
        // No search results. Output message
        $outputMessage = $this->data['no_results_msg'];
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
    // Check user is allowed to view this
    if (!$this->role->canDo('view_section')) {
      // Redirect user with error
      flashMsg('admin_dashboard', '<strong>Error</strong> Sorry, you are not allowed to view sections. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin');
      exit;
    }

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
   * @param string $setLocation `[optional]` Location of section. Defaults to null (Main).
   */
  protected function setAddData(int $setType = 0, string $setLocation = null)
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
    $typeOptionsData = array(0 => 'Pages', 1 => 'FAQ', 5 => 'Menu');
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

    #########################
    ####    LOCATIONS    ####
    #########################

    // Init location options
    $locationOptions = '<option></option>';
    // Get list of types for assigning
    foreach (ALLOWED_SUBFOLDERS as $location => $defaults) {

      // Skip Admin folder
      if (strtolower($location) !== 'admin') {

        // Set selected if chosen type
        $selected = (!empty($setLocation) && $setLocation == $location) ? ' selected' : '';

        // Set to output
        $locationOptions .= '<option value="' . strtolower($location) . '"' . $selected . '>' . ucwords($location) . '</option>';
      }
    }
    // Set blank fallback
    $locationOptions = ($locationOptions !== '<option></option>') ? $locationOptions : $locationOptions . '<option disabled>There are currently no directories available to assign this section to.</option>';
    // Set location list options to data
    $this->data['location_options'] = $locationOptions;
  }

  /**
   * Add Page
   */
  public function add()
  {
    // Check user is allowed to add_section this
    if (!$this->role->canDo('view_section')) {
      // Redirect user with error
      flashMsg('admin_sections', '<strong>Error</strong> Sorry, you are not allowed to add sections. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/sections');
      exit;
    }

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

          // Get location data
          $this->data['location_name'] = htmlspecialchars(stripslashes(trim($_POST['location_name'])));
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
        if ($this->contentModel->addSection($this->data['name'], (int) $this->data['type'], $this->data['location_name'])) {
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
      $this->setAddData((int) $this->data['type'], $this->data['location_name']);
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
   * @param string $setLocation `[optional]` Location of section. Defaults to null (Main).
   */
  protected function setEditData(int $setType, string $setLocation = null)
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
    $typeOptionsData = array(0 => 'Pages', 1 => 'FAQ', 5 => 'Menu');
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

    #########################
    ####    LOCATIONS    ####
    #########################

    // Init location options
    $locationOptions = '<option></option>';
    // Get list of types for assigning
    foreach (ALLOWED_SUBFOLDERS as $location => $defaults) {

      // Skip Admin folder
      if (strtolower($location) !== 'admin') {

        // Set selected if chosen type
        $selected = (!empty($setLocation) && $setLocation == $location) ? ' selected' : '';

        // Set to output
        $locationOptions .= '<option value="' . strtolower($location) . '"' . $selected . '>' . ucwords($location) . '</option>';
      }
    }
    // Set blank fallback
    $locationOptions = ($locationOptions !== '<option></option>') ? $locationOptions : $locationOptions . '<option disabled>There are currently no directories available to assign this section to.</option>';
    // Set location list options to data
    $this->data['location_options'] = $locationOptions;
  }

  /**
   * Edit Page
   */
  public function edit(...$params)
  {
    // Check user is allowed to add_section this
    if (!$this->role->canDo('edit_section')) {
      // Redirect user with error
      flashMsg('admin_sections', '<strong>Error</strong> Sorry, you are not allowed to edit sections. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/sections');
      exit;
    }

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

            // Get location data
            $this->data['location_name'] = htmlspecialchars(stripslashes(trim($_POST['location_name'])));
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
          if ($this->contentModel->editSection((int) $dataID, $this->data['name'], (int) $this->data['type'], $this->data['location_name'])) {
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
        $this->setEditData((int) $this->data['type'], $this->data['location_name']);

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
          $this->setEditData((int) $this->data['type'], $this->data['location_name']);

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

  /**
   * Set Menu Page Data
   */
  protected function setMenuData()
  {

    // Page Type
    $this->data['page_type'] = 'menu';
    // Action URL
    $this->data['action_url'] = get_site_url('admin/sections/menu/' . $this->data['id']);
    // H1
    $this->data['page_title'] = 'Menu: ' . $this->data['name'];
    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => $this->data['page_title'],
      'href' => $this->data['action_url']
    );
    // Instructions
    $this->data['instructions'] = 'Add your content to this menu and sort it.';

    ##########################
    ####    MENU ITEMS    ####
    ##########################

    // Init menu items
    $this->data['menu_items'] = '';
    $takenItems = array();
    // Get menu items
    if ($menuItemsData = $this->contentModel->listMenuItems((int) $this->data['id'])) {

      // Loop through data
      foreach ($menuItemsData as $menuItem) {

        // Set random row ID
        $randomID = mt_rand(500, 800);

        // Add to taken items array
        if (!empty($menuItem->menui_content_id)) {
          $takenItems[] = $menuItem->menui_content_id;
        }

        // Set links
        switch ((int) $menuItem->content_type) {
          case 0: // Page
            $contentLink = ($this->role->canDo('edit_page') && !empty($menuItem->menui_content_id)) ? '<a href="' . get_site_url('admin/pages/edit/' . $menuItem->menui_content_id) . '" data-tippy-content="Edit ' . htmlspecialchars_decode($menuItem->content_title) . '">' . htmlspecialchars_decode($menuItem->content_title) . '</a> <i class="fas fa-file" data-tippy-content="Content type: page"></i>' : htmlspecialchars_decode($menuItem->content_title) . ' <i class="fas fa-file" data-tippy-content="Content type: page"></i>';
            break;
          case 1: // FAQ
            $contentLink = ($this->role->canDo('edit_faq') && !empty($menuItem->menui_content_id)) ? '<a href="' . get_site_url('admin/faq/edit/' . $menuItem->menui_content_id) . '" data-tippy-content="Edit ' . htmlspecialchars_decode($menuItem->content_title) . '">' . htmlspecialchars_decode($menuItem->content_title) . '</a> <i class="fas fa-question-circle" data-tippy-content="Content type: FAQ"></i>' : htmlspecialchars_decode($menuItem->content_title) . ' <i class="fas fa-question-circle" data-tippy-content="Content type: FAQ"></i>';
            break;

          default:
            $contentLink = '<span class="cs-muted">not set</span>';
            break;
        }

        // Set fallback for custom URL
        $content = (!empty($menuItem->menui_content_id)) ? '<input type="hidden" name="item[' . $randomID . '][content_id]" id="item_' . $randomID . '_content_id" value="' . $menuItem->menui_content_id . '"><p id="item_' . $randomID . '_name" class="cs-body1">' . $contentLink . '</p>' : '<input type="text" name="item[' . $randomID . '][custom]" id="item_' . $randomID . '_custom" value="' . $menuItem->menui_custom_url . '" autocapitalize="off">
        <label for="item_' . $randomID . '_custom">Custom URL*</label>';

        // Set fallback for requiring the title
        $requireTitle = (empty($menuItem->menui_content_id)) ? ' required' : '';
        $requireTitleDisplay = (empty($menuItem->menui_content_id)) ? '*' : '';

        // Set to output
        $this->data['menu_items'] .= '<div id="item_' . $randomID . '_row" class="csc-row csc-row--no-gap">
        <div class="csc-col csc-col12 csc-col--md5 csc-col--align-middle csc-input-field">
          <input type="hidden" name="item[' . $randomID . '][item_id]" value="' . $menuItem->menui_id . '">
          ' . $content . '
        </div>
        <div class="csc-col csc-col12 csc-col--md4 csc-col--align-middle csc-input-field">
          <input type="text" name="item[' . $randomID . '][title]" id="item_' . $randomID . '_title" value="' . $menuItem->menui_custom_title . '" autocapitalize="off" ' . $requireTitle . '>
          <label for="item_' . $randomID . '_title">Custom Title' . $requireTitleDisplay . '</label>
        </div>
        <div class="csc-col csc-col12 csc-col--md2 csc-input-field">
          <input type="number" name="item[' . $randomID . '][sort]" id="item_' . $randomID . '_sort" value="' . $menuItem->menui_sort_order . '">
          <label for="item_' . $randomID . '_sort" data-tippy-content="Change the sort order of the menu item">Sort <i class="far fa-question-circle"></i></label>
        </div>
        <div class="csc-col csc-col12 csc-col--md1 csc-col--align-center csc-col--align-middle">
          <i class="fas fa-trash-alt csc-text-red delete-item" data-row="' . $randomID . '" data-tippy-content="Delete menu item"></i>
        </div>
      </div>';
      }
    } else { // No menu items. Output default message
      $this->data['menu_items'] = '<p>There are currently no items in this menu</p>';
    }

    #################################
    ####    AVAILABLE CONTENT    ####
    #################################

    // Init available content
    $this->data['available_content'] = '<option></option>';
    if ($contentData = $this->contentModel->listContentItems()) {

      // Loop through data
      foreach ($contentData as $content) {

        // Check if already assigned
        if (!in_array($content->content_id, $takenItems)) {

          // Set to output
          $this->data['available_content'] .= '<option value="' . $content->content_id . '">' . $content->content_title . '</option>';
        }
      }
    } // No items. Return default message

    if (empty($this->data['available_content']) || $this->data['available_content'] === '<option></option>') {
      $this->data['available_content'] = '<option></option><option value="none" disabled>There are currently no items available to assign.</option>';
    }
  }

  /**
   * Menu Page
   */
  public function menu(...$params)
  {
    // Check user has role permission to access
    if (!$this->role->canDo('view_menu')) {
      // Redirect user with error
      flashMsg('admin_sections', '<strong>Error</strong> Sorry, you are not allowed to view menus. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/sections');
      exit;
    }

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
        if (isset($_POST['id'])) {

          // Try validating
          try {

            // Get ID
            $this->data['id'] = htmlspecialchars(stripslashes(trim($_POST['id'])));

            // Get item data
            $this->data['item'] = (!empty($_POST['item'])) ? $_POST['item'] : array();
          } catch (Exception $e) {

            // Log error if any and set flash message
            error_log($e->getMessage(), 0);
            flashMsg('sections_menu', '<strong>Error</strong> There was an error editing the menu. Please try again', 'warning');
          }
        } else { // Required data not set. Set Errors.

          $this->data['err']['id'] = 'Please make sure the menu ID is set';
          flashMsg('sections_menu', '<strong>Error</strong> Please make sure the menu ID is set. Please try again', 'warning');
        }

        // If valid, update
        if (empty($this->data['err'])) {
          // Validated

          // Set ID
          $dataID = $this->data['id'];

          // Get existing values
          if ($existingValues = $this->contentModel->listMenuItems((int) $dataID)) {

            // Loop through values
            foreach ($existingValues as $eValue) {

              // Check if existing value is one of the posted values
              if (!in_array($eValue->menui_id, array_column($this->data['item'], 'item_id'))) {

                // Not in array. Delete value
                $this->contentModel->deleteMenuItem((int) $eValue->menui_id);
              }
            }
          }

          // Loop through posted items
          foreach ($this->data['item'] as $menuItem) {

            // Get menu item ID
            $itemID = (!empty($menuItem['item_id'])) ? htmlspecialchars(trim($menuItem['item_id'])) : '';

            // Get content ID
            $contentID = (!empty($menuItem['content_id'])) ? htmlspecialchars(trim($menuItem['content_id'])) : '';

            // Get custom data
            $customURL = (!empty($menuItem['custom'])) ? htmlspecialchars(str_replace(get_site_url(), '', trim($menuItem['custom']))) : '';
            $customTitle = (!empty($menuItem['title'])) ? htmlspecialchars(trim($menuItem['title'])) : '';

            // Get Sort Order
            $sortOrder = (!empty($menuItem['sort'])) ? htmlspecialchars(trim($menuItem['sort'])) : 0;

            // Check if updating existing item
            if (!empty($itemID)) {

              // Value exists. Update
              $this->contentModel->editMenuItem(
                (int) $itemID,
                (int) $contentID,
                $customURL,
                $customTitle,
                (int) $sortOrder
              );
            } else { // No such value. Add

              // Add menu item
              $this->contentModel->addMenuItem(
                (int) $dataID,
                (int) $contentID,
                $customURL,
                $customTitle,
                (int) $sortOrder
              );
            }
          }

          // Set success message
          flashMsg('admin_sections', '<strong>Success</strong> Menu updated successfully.');

          // Return to list view
          redirectTo('admin/sections');
          exit;
        }

        // If it's made it this far there were errors. Load edit view with data

        // Set menu data
        $this->setMenuData();

        // Load view
        $this->load->view('sections/menu', $this->data, 'admin');
        exit;
      } else { // Error with the ID. Redirect to list view with error.

        // Set Error
        flashMsg('admin_sections', '<strong>Error</strong> There was an error saving the menu. Please try again', 'warning');
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

          // Set Menu Data
          $this->setMenuData();

          // Load view
          $this->load->view('sections/menu', $this->data, 'admin');
          exit;
        } else { // Error getting the data. Redirect to list.
          flashMsg('admin_sections', '<strong>Error</strong> Sorry, there was an error getting that menu. Please try again.', 'warning');
        }
      } // No ID present. Redirect to list.

      // Redirect user
      redirectTo('admin/sections');
      exit;
    }
  }

  /**
   * Set FAQ Page Data
   */
  protected function setFAQData()
  {

    // Page Type
    $this->data['page_type'] = 'faq';
    // Action URL
    $this->data['action_url'] = get_site_url('admin/sections/faq/' . $this->data['id']);
    // H1
    $this->data['page_title'] = 'FAQ Section: ' . $this->data['name'];
    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => $this->data['page_title'],
      'href' => $this->data['action_url']
    );
    // Instructions
    $this->data['instructions'] = 'Add your content to this faq section and sort it.';

    #########################
    ####    FAQ ITEMS    ####
    #########################

    // Init faq items
    $this->data['faq_items'] = '';
    $takenItems = array();
    // Get faq items
    if ($faqItemsData = $this->contentModel->listAssignedFAQSectionFAQs((int) $this->data['id'])) {

      // Loop through data
      foreach ($faqItemsData as $faqItem) {

        // Set random row ID
        $randomID = mt_rand(500, 800);

        // Add to taken items array
        if (!empty($faqItem->content_id)) {
          $takenItems[] = $faqItem->content_id;
        }

        // Set links
        $contentLink = ($this->role->canDo('edit_faq') && !empty($faqItem->content_id)) ? '<a href="' . get_site_url('admin/faq/edit/' . $faqItem->content_id) . '" class="has-hover-item" data-tippy-content="Edit ' . htmlspecialchars_decode($faqItem->content_title) . '">' . htmlspecialchars_decode($faqItem->content_title) . '<small class="hover-item"> <i class="fas fa-edit"></i></small></a>' : htmlspecialchars_decode($faqItem->content_title);

        // Set to output
        $this->data['faq_items'] .= '<div id="item_' . $randomID . '_row" class="csc-row csc-row--no-gap">
        <div class="csc-col csc-col12 csc-col--md9 csc-col--align-middle csc-input-field">
          <input type="hidden" name="item[' . $randomID . '][link_id]" value="' . $faqItem->faqs_id . '">
          <input type="hidden" name="item[' . $randomID . '][content_id]" id="item_' . $randomID . '_content_id" value="' . $faqItem->content_id . '">
          <p id="item_' . $randomID . '_name" class="cs-body1"><strong>' . $contentLink . '</strong></p>
        </div>
        <div class="csc-col csc-col12 csc-col--md2 csc-input-field">
          <input type="number" name="item[' . $randomID . '][sort]" id="item_' . $randomID . '_sort" value="' . $faqItem->faqs_sort_order . '">
          <label for="item_' . $randomID . '_sort" data-tippy-content="Change the sort order of the FAQ item">Sort <i class="far fa-question-circle"></i></label>
        </div>
        <div class="csc-col csc-col12 csc-col--md1 csc-col--align-center csc-col--align-middle">
          <i class="fas fa-trash-alt csc-text-red delete-item" data-row="' . $randomID . '" data-tippy-content="Remove FAQ"></i>
        </div>
      </div>';
      }
    } else { // No faq items. Output default message
      $this->data['faq_items'] = '<p>There are currently no items in this FAQ section</p>';
    }

    ##############################
    ####    AVAILABLE FAQs    ####
    ##############################

    // Init available faqs
    $this->data['available_faqs'] = '<option></option>';
    if ($faqData = $this->contentModel->listFAQs()) {

      // Loop through data
      foreach ($faqData->results as $faq) {

        // Check if already assigned
        if (!in_array($faq->content_id, $takenItems)) {

          // Set to output
          $this->data['available_faqs'] .= '<option value="' . $faq->content_id . '">' . $faq->content_title . '</option>';
        }
      }
    } // No items. Return default message

    if (empty($this->data['available_faqs']) || $this->data['available_faqs'] === '<option></option>') {
      $this->data['available_faqs'] = '<option></option><option value="none" disabled>There are currently no faq items available to assign.</option>';
    }
  }

  /**
   * FAQ Page
   */
  public function faq(...$params)
  {
    // Check user has role permission to access
    if (!$this->role->canDo('view_faq_section')) {
      // Redirect user with error
      flashMsg('admin_sections', '<strong>Error</strong> Sorry, you are not allowed to view FAQ sections. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/sections');
      exit;
    }

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
        if (isset($_POST['id'])) {

          // Try validating
          try {

            // Get ID
            $this->data['id'] = htmlspecialchars(stripslashes(trim($_POST['id'])));

            // Get item data
            $this->data['item'] = (!empty($_POST['item'])) ? $_POST['item'] : array();
          } catch (Exception $e) {

            // Log error if any and set flash message
            error_log($e->getMessage(), 0);
            flashMsg('sections_faqs', '<strong>Error</strong> There was an error editing the FAQ sections. Please try again', 'warning');
          }
        } else { // Required data not set. Set Errors.

          $this->data['err']['id'] = 'Please make sure the FAQ section ID is set';
          flashMsg('sections_faqs', '<strong>Error</strong> Please make sure the FAQ section ID is set. Please try again', 'warning');
        }

        // If valid, update
        if (empty($this->data['err'])) {
          // Validated

          // Set ID
          $dataID = $this->data['id'];

          // Get existing values
          if ($existingValues = $this->contentModel->listAssignedFAQSectionFAQs((int) $dataID)) {

            // Loop through values
            foreach ($existingValues as $eValue) {

              // Check if existing value is one of the posted values
              if (!in_array($eValue->faqs_id, array_column($this->data['item'], 'item_id'))) {

                // Not in array. Delete value
                $this->contentModel->deleteAssignedFAQSections((int) $eValue->faqs_id);
              }
            }
          }

          // Loop through posted items
          foreach ($this->data['item'] as $faqItem) {

            // Get faq item ID
            $itemID = (!empty($faqItem['item_id'])) ? htmlspecialchars(trim($faqItem['item_id'])) : '';

            // Get content ID
            $contentID = (!empty($faqItem['content_id'])) ? htmlspecialchars(trim($faqItem['content_id'])) : '';

            // Get Sort Order
            $sortOrder = (!empty($faqItem['sort'])) ? htmlspecialchars(trim($faqItem['sort'])) : 0;

            // Check if updating existing item
            if (!empty($itemID)) {

              // Value exists. Update
              $this->contentModel->editFAQLink(
                (int) $itemID,
                (int) $sortOrder
              );
            } else { // No such value. Add

              // Add FAQ Section Link
              $this->contentModel->addFAQLink(
                (int) $dataID,
                (int) $contentID,
                (int) $sortOrder
              );
            }
          }

          // Set success message
          flashMsg('admin_sections', '<strong>Success</strong> FAQ section updated successfully.');

          // Return to list view
          redirectTo('admin/sections');
          exit;
        }

        // If it's made it this far there were errors. Load edit view with data

        // Set menu data
        $this->setFAQData();

        // Load view
        $this->load->view('sections/faq', $this->data, 'admin');
        exit;
      } else { // Error with the ID. Redirect to list view with error.

        // Set Error
        flashMsg('admin_sections', '<strong>Error</strong> There was an error saving the FAQ section. Please try again', 'warning');
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

          // Set FAQ Data
          $this->setFAQData();

          // Load view
          $this->load->view('sections/faq', $this->data, 'admin');
          exit;
        } else { // Error getting the data. Redirect to list.
          flashMsg('admin_sections', '<strong>Error</strong> Sorry, there was an error getting that FAQ section. Please try again.', 'warning');
        }
      } // No ID present. Redirect to list.

      // Redirect user
      redirectTo('admin/sections');
      exit;
    }
  }
}
