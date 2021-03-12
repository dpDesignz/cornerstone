<?php

class FAQ extends Controller
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

    // Define the page type
    $this->pageType = 'faq';

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
        'text' => 'FAQ',
        'href' => get_site_url('admin/faq')
      )
    );
  }

  /**
   * Load the index table
   *
   * @param mixed $params Mixed values of extra parameters
   */
  public function loadIndexTable(...$params)
  {

    // Init list page
    $this->init_list_page('admin/faq', ...$params);

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

    // Check for sort
    $this->get_sort_order(
      array('title' => 'content_title', 'creator' => 'content_added_id', 'section' => 'section_name', 'updated' => 'content_edited_dtm'), // Allowed sort fields
      array('sort' => 'content_title', 'order' => 'ASC'), // Fallback
      ...$params
    );

    // Output list

    // Get pages
    if ($dataList = $this->contentModel->listFAQs($this->params)) {

      // Count how many items match the results
      $this->params['count'] = TRUE;
      $this->data['totalResults'] = ($this->contentModel->listFAQs($this->params))->results[0]->total_results;

      // Set data list output
      $dataListOut = '';

      // Set the pagination
      $pagination = new Pagination;
      $pagination->set_props((int) $this->data['totalResults'], (int) $this->params['page'], (int) $this->params['limit']);
      $this->data['pagination'] = $pagination->render();

      // Loop through data
      foreach ($dataList->results as $data) {

        // Set edit link
        $showTitle = ($this->role->canDo('edit_faq')) ? '<a href="' . get_site_url('admin/faq/edit/' . $data->content_id) . '" data-tippy-content="Edit ' . htmlspecialchars_decode($data->content_title) . '">' . htmlspecialchars_decode($data->content_title) . '</a>' : htmlspecialchars_decode($data->content_title);
        $editLink = ($this->role->canDo('edit_faq')) ? '<a href="' . get_site_url('admin/faq/edit/' . $data->content_id) . '">Edit</a>' : '';

        // Get FAQ assigned sections
        $sectionName = '';
        if ($faqSections = $this->contentModel->listAssignedFAQSections((int) $data->content_id)) {
          // Loop through assigned sections
          foreach ($faqSections as $sectionData) {
            $sectionName .= $sectionData->section_name . ', ';
          }

          // Remove trailing comma
          $sectionName = rtrim($sectionName, ', ');
        }

        // Set fallback section name
        $sectionName = (empty($sectionName)) ? '<span class="cs-muted">none set</span>' : $sectionName;

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
              <strong class="item--title">' . $showTitle . $statusOutput . '</strong>
              <span class="hover-item cs-caption cs-muted">' . $editLink . '</span>
            </td>
            <td>' . $data->added_by . '</td>
            <td>' . $sectionName . '</td>
            <td><span class="cs-muted" data-tippy-content="' . $lastUpdatedBy . '"><strong>' . $dateType . '</strong><br>' . $lastUpdatedShort . '</span></td>
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
    // Check user is allowed to view this
    if (!$this->role->canDo('view_faq')) {
      // Redirect user with error
      flashMsg('admin_dashboard', '<strong>Error</strong> Sorry, you are not allowed to view FAQs. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin');
      exit;
    }

    // Output pages list
    $this->loadIndexTable(...$params);

    // Load view
    $this->load->view('faq/index', $this->data, 'admin');
    exit;
  }

  /**
   * Set Add Data
   *
   * @param int $setStatus `[optional]` Status of faq. Defaults to published.
   * @param array $setSections `[optional]` Sections of faq. Defaults to empty.
   */
  protected function setAddData(int $setStatus = 1, array $setSections = array())
  {

    // Page Type
    $this->data['page_type'] = 'add';
    // Action URL
    $this->data['action_url'] = get_site_url('admin/faq/add/');
    // H1
    $this->data['page_title'] = 'Add FAQ';
    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => $this->data['page_title'],
      'href' => $this->data['action_url']
    );
    // Instructions
    $this->data['instructions'] = 'Enter the details for the new FAQ to add it.';

    ###########################
    ####    STATUS LIST    ####
    ###########################

    // Set status list options
    $statusOptionsData = array(0 => 'Draft', 1 => 'Published', 3 => 'Archived');
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
    if ($sectionsData = $this->contentModel->listFAQSections()) {
      // Sections data exists

      // Loop through sections data
      foreach ($sectionsData as $section) {

        // Set selected if chosen type
        $selected = (!empty($setSection) && in_array($section->section_id, $setSection)) ? ' selected' : '';

        // Set to output
        $sectionOptions .= '<option value="' . $section->section_id . '"' . $selected . '>' . $section->section_name . '</option>';
      }
    }
    // Set blank fallback
    $sectionOptions = ($sectionOptions !== '<option></option>') ? $sectionOptions : $sectionOptions . '<option disabled>There are currently no sections available to assign this faq to.</option>';
    // Set section list options to data
    $this->data['section_options'] = $sectionOptions;

    #####################
    ####    MENUS    ####
    #####################

    // Get menus
    $this->data['menu_options'] = '<option></option>';
    if ($menuOptsData = $this->contentModel->listMenus()) {

      // Loop through data
      foreach ($menuOptsData as $menuData) {

        // Set to output
        $this->data['menu_options'] .= '<option value="' . $menuData->section_id . '">' . htmlspecialchars_decode($menuData->section_name) . '</option>';
      }
    }
    // Set blank fallback
    $this->data['menu_options'] = ($this->data['menu_options'] !== '<option></option>') ? $this->data['menu_options'] : $this->data['menu_options'] . '<option disabled>There are currently no menus available to assign this page to.</option>';
  }

  /**
   * Add Page
   */
  public function add()
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('add_faq')) {
      // Redirect user with error
      flashMsg('admin_faq', '<strong>Error</strong> Sorry, you are not allowed to add FAQs. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/faq');
      exit;
    }

    // Process "add"

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "save") {

      // Get content data
      // This is needing to be done before the sanitization to keep the markup
      $this->data['content'] = trim($_POST['content']);
      if (empty($this->data['content'])) {
        // Data not set. Return error.
        $this->data['err']['content'] = 'Please enter some content';
        flashMsg('faq_faq', '<strong>Error</strong> There was an error adding the FAQ. Please enter some content.', 'warning');
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
            $this->data['err']['title'] = 'Please enter an FAQ title';
          } else if (strlen($this->data['title']) < 3) {
            // Data is less than 3 characters. Return error.
            $this->data['err']['title'] = 'Please enter at least 3 characters';
          }

          // Get status data
          $this->data['status'] = htmlspecialchars(stripslashes(trim($_POST['status'])));
          if (!is_numeric($this->data['status'])) {
            // Data is not a valid type. Return error.
            $this->data['err']['status'] = 'Please select a valid FAQ status';
          }

          // Get section data
          $this->data['section_id'] = (!empty($_POST['section_id']) && is_array($_POST['section_id'])) ? $_POST['section_id'] : array();

          // Get menu data
          $this->data['menu'] = (!empty($_POST['menu']) && is_array($_POST['menu'])) ? $_POST['menu'] : array();

          // Get show_updated data
          $this->data['show_updated'] = (isset($_POST['show_updated']) && !empty($_POST['show_updated'])) ? TRUE : FALSE;
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          flashMsg('faq_faq', '<strong>Error</strong> There was an error adding the FAQ. Please try again', 'warning');
        }
      } else { // Required data not set. Set Errors.

        $this->data['err']['title'] = 'Please enter an FAQ title';
      }

      // If valid, add new
      if (empty($this->data['err'])) {
        // Validated

        // Add new FAQ
        if ($contentID = $this->contentModel->addFAQ(
          $this->data['title'],
          $this->data['content'],
          (int) $this->data['status'],
          (int) $this->data['show_updated']
        )) {
          // Added

          // Check for assigned sections
          if (!empty($this->data['section_id'])) {
            // Loop through assigned sections
            foreach ($this->data['section_id'] as $optNo => $sectionLink) {
              // Add section link
              $this->contentModel->addFAQLink(
                (int) trim($sectionLink),
                (int) $contentID
              );
            }
          }

          // Check for assigned menus
          if (!empty($this->data['menu'])) {
            // Loop through assigned menus
            foreach ($this->data['menu'] as $optNo => $menuLink) {
              // Add menu link
              $this->contentModel->addMenuItem(
                (int) trim($menuLink),
                (int) $contentID
              );
            }
          }

          // Load the Cornerstone Core model
          $this->cornerstoneCoreModel = $this->load->model('common/cornerstonecore');

          // Set SEO URL
          $this->cornerstoneCoreModel->checkSEOURL(
            (int) 1,
            (int) $contentID,
            $this->data['title']
          );

          // Set success message
          flashMsg('admin_faq', '<strong>Success</strong> The "' . $this->data['title'] . '" FAQ was added successfully');

          // Return to list
          redirectTo('admin/faq');
          exit;
        } // Unable to add. Return error.

        // Set error message
        flashMsg('faq_faq', '<strong>Error</strong> There was an error adding the FAQ. Please try again.', 'warning');
      }

      // If it's made it this far there were errors. Load add view with submitted data

      // Set Add Data
      $this->setAddData((int) $this->data['status'], $this->data['section_id']);
    } else { // FAQ wasn't posted. Load view.

      // Set Add Data
      $this->setAddData();
    }

    // Load add view
    $this->load->view('faq/faq', $this->data, 'admin');
    exit;
  }

  /**
   * Set Edit Page Data
   *
   * @param int $setStatus Status of faq.
   * @param array $setSections `[optional]` Sections of faq. Defaults to empty.
   */
  protected function setEditData(int $setStatus, array $setSections = array())
  {

    // Page Type
    $this->data['page_type'] = 'edit';
    // Action URL
    $this->data['action_url'] = get_site_url('admin/faq/edit/' . $this->data['id']);
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
    $statusOptionsData = array(0 => 'Draft', 1 => 'Published', 3 => 'Archived');
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
    if ($sectionsData = $this->contentModel->listFAQSections()) {
      // Sections data exists

      // Loop through sections data
      foreach ($sectionsData as $section) {

        // Set selected if chosen type
        $selected = (!empty($setSections) && in_array($section->section_id, $setSections)) ? ' selected' : '';

        // Set to output
        $sectionOptions .= '<option value="' . $section->section_id . '"' . $selected . '>' . $section->section_name . '</option>';
      }
    }
    // Set blank fallback
    $sectionOptions = ($sectionOptions !== '<option></option>') ? $sectionOptions : $sectionOptions . '<option disabled>There are currently no sections available to assign this faq to.</option>';
    // Set section list options to data
    $this->data['section_options'] = $sectionOptions;

    #####################
    ####    MENUS    ####
    #####################

    // Get existing assigned menus
    $existingMenus = array();
    if ($menusData = $this->contentModel->listContentMenus((int) $this->data['id'])) {

      // Loop through data
      foreach ($menusData as $menu) {
        // Add to existing menus
        $existingMenus[] = $menu->menui_menu_id;
      }
    }

    // Get menus
    $this->data['menu_options'] = '<option></option>';
    if ($menuOptsData = $this->contentModel->listMenus()) {

      // Loop through data
      foreach ($menuOptsData as $menuData) {

        // Set if selected
        $selected = (!empty($existingMenus) && in_array($menuData->section_id, $existingMenus)) ? ' selected' : '';

        // Set to output
        $this->data['menu_options'] .= '<option value="' . $menuData->section_id . '"' . $selected . '>' . htmlspecialchars_decode($menuData->section_name) . '</option>';
      }
    }
    // Set blank fallback
    $this->data['menu_options'] = ($this->data['menu_options'] !== '<option></option>') ? $this->data['menu_options'] : $this->data['menu_options'] . '<option disabled>There are currently no menus available to assign this faq to.</option>';
  }

  /**
   * Edit Page
   */
  public function edit(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('edit_page')) {
      // Redirect user with error
      flashMsg('admin_dashboard', '<strong>Error</strong> Sorry, you are not allowed to edit pages. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/pages');
      exit;
    }

    // Process "edit"

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "save") {

      // Get content data
      // This is needing to be done before the sanitization to keep the markup
      $this->data['content'] = trim($_POST['content']);
      if (empty($this->data['content'])) {
        // Data not set. Return error.
        $this->data['err']['content'] = 'Please enter some content';
        flashMsg('faq_faq', '<strong>Error</strong> There was an error updating the FAQ. Please enter some content.', 'warning');
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
              $this->data['err']['title'] = 'Please enter an FAQ title';
            } else if (strlen($this->data['title']) < 3) {
              // Data is less than 3 characters. Return error.
              $this->data['err']['title'] = 'Please enter at least 3 characters';
            }

            // Get status data
            $this->data['status'] = htmlspecialchars(stripslashes(trim($_POST['status'])));
            if (!is_numeric($this->data['status'])) {
              // Data is not a valid type. Return error.
              $this->data['err']['status'] = 'Please select a valid FAQ status';
            }

            // Get section data
            $this->data['section_id'] = (!empty($_POST['section_id']) && is_array($_POST['section_id'])) ? $_POST['section_id'] : array();

            // Get menu data
            $this->data['menu'] = (!empty($_POST['menu']) && is_array($_POST['menu'])) ? $_POST['menu'] : array();

            // Get show_updated data
            $this->data['show_updated'] = (isset($_POST['show_updated']) && !empty($_POST['show_updated'])) ? TRUE : FALSE;
          } catch (Exception $e) {

            // Log error if any and set flash message
            error_log($e->getMessage(), 0);
            flashMsg('faq_faq', '<strong>Error</strong> There was an error updating the FAQ. Please try again', 'warning');
          }
        } else { // Required data not set. Set Errors.

          $this->data['err']['title'] = 'Please enter an FAQ title';
        }

        // If valid, update
        if (empty($this->data['err'])) {
          // Validated

          // Set ID
          $dataID = $this->data['id'];

          // Update
          if ($this->contentModel->editFAQ(
            (int) $dataID,
            $this->data['title'],
            $this->data['content'],
            (int) $this->data['status'],
            (int) $this->data['show_updated']
          )) {
            // Updated

            // Get existing sections
            $existingSections = array();
            if ($existingSectionValues = $this->contentModel->listAssignedFAQSections((int) $dataID)) {

              // Loop through values
              foreach ($existingSectionValues as $sectionValue) {

                // Check if existing value is one of the posted values
                if (!in_array($sectionValue->section_id, $this->data['section_id'])) {

                  // Not in array. Delete assigned section
                  $this->contentModel->deleteAssignedFAQSections((int) $sectionValue->faqs_id);
                } else {
                  // Add to existing array
                  $existingSections[] = $sectionValue->section_id;
                }
              }
            }

            // Loop through posted sections
            foreach ($this->data['section_id'] as $optNo => $sectionID) {

              // Check if adding item
              if (!in_array($sectionID, $existingSections)) {

                // Add assigned section
                $this->contentModel->addFAQLink(
                  (int) trim($sectionID),
                  (int) $dataID
                );
              }
            }

            // Get existing menu items
            $existingMenus = array();
            if ($existingValues = $this->contentModel->listContentMenus((int) $dataID)) {

              // Loop through values
              foreach ($existingValues as $eValue) {

                // Check if existing value is one of the posted values
                if (!in_array($eValue->menui_menu_id, $this->data['menu'])) {

                  // Not in array. Delete menu
                  $this->contentModel->deleteMenuItem((int) $eValue->menui_id);
                } else {
                  // Add to existing array
                  $existingMenus[] = $eValue->menui_menu_id;
                }
              }
            }

            // Loop through posted menu items
            foreach ($this->data['menu'] as $optNo => $menuID) {

              // Check if adding item
              if (!in_array($menuID, $existingMenus)) {

                // Add menu item
                $this->contentModel->addMenuItem(
                  (int) trim($menuID),
                  (int) $dataID
                );
              }
            }

            // Set success message
            flashMsg('admin_faq', '<strong>Success</strong> The "' . $this->data['title'] . '" FAQ was updated successfully.');

            // Return to list view
            redirectTo('admin/faq');
            exit;
          } else { // Unable to update. Return error and redirect to edit view.

            // Set error message
            flashMsg('faq_faq', '<strong>Error</strong> There was an error updating the FAQ. Please contact your admin to get this fixed.', 'danger');
          }
        }

        // If it's made it this far there were errors. Load edit view with data

        // Set edit data
        $this->setEditData((int) $this->data['status'], $this->data['section_id']);

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
        if ($contentData = $this->contentModel->getFAQ((int) $params[0])) {

          // Set data
          foreach ($contentData as $key => $data) {
            $this->data[str_replace(array('content_'), '', $key)] = $data;
          }

          // Get section IDs as array
          $sectionIDs = (!empty($this->data['section_ids'])) ? explode(',', $this->data['section_ids']) : array();

          // Set Edit Data
          $this->setEditData((int) $this->data['status'], $sectionIDs);

          // Load view
          $this->load->view('faq/faq', $this->data, 'admin');
          exit;
        } // Error getting the data. Redirect to list.

      } // No ID present. Redirect to list.

      // Redirect user
      redirectTo('admin/faq');
      exit;
    }
  }
}
