<?php
class Roles extends Controller
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

      // Load the role model
      $this->roleModel = $this->load->model('cornerstone/userrole', 'admin');

      // Set Breadcrumbs
      $this->data['breadcrumbs'] = array(
        array(
          'text' => 'Dashboard',
          'href' => get_site_url('admin')
        ),
        array(
          'text' => 'Users',
          'href' => get_site_url('admin/users')
        ),
        array(
          'text' => 'Roles',
          'href' => get_site_url('admin/roles')
        )
      );
    }
  }

  /**
   * All Users Page
   */
  public function index(...$params)
  {
    // Check if user is allowed access to this
    if (!$this->role->canDo('view_user_role')) {
      // Redirect user with error
      flashMsg('admin_users', '<strong>Error</strong> Sorry, you are not allowed to view the user roles. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/users');
      exit;
    }

    // Output user roles index page

    // Check for search and rebuild URL
    if (isset($this->request->get['search'])) {
      redirectTo('admin/roles/search/' . urlencode($this->request->get['search']));
      exit;
    }

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
        'href' => get_site_url('admin/roles/search/' . urlencode($this->params['search']))
      );
    }

    // Allowed sort fields
    $this->canSortBy = array('name' => 'role_name');

    // Check for sort
    $sortOrder = get_sort_order($this->canSortBy, array('sort' => 'role_name', 'order' => 'ASC'), ...$params);

    // Set sort to params
    foreach ($sortOrder as $key => $value) {
      $this->params[$key] = $value;
    }

    // Set the default sort item
    $this->data['defaultSort'] = 'name';

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

    // Output roles list

    // Get data
    if ($dataList = $this->roleModel->listRoles($this->params)) {

      // Count how many items match the results
      $this->params['count'] = TRUE;
      $this->data['totalResults'] = ($this->roleModel->listRoles($this->params))->results[0]->total_results;

      // Set data list output
      $dataListOut = '';

      // Set the pagination
      $pagination = new Pagination;
      $pagination->total_records = (int) $this->data['totalResults'];
      $pagination->current_page = (int) $this->params['page'];
      $pagination->items_per_page = (int) $this->params['limit'];
      $this->data['pagination'] = $pagination->render();

      // Count total permissions
      $totalPermissions = $this->roleModel->countTotalPermissions();

      // Loop through data
      foreach ($dataList->results as $data) {

        // Decode meta data
        $metaData = (!empty($data->role_meta)) ? json_decode($data->role_meta) : '';

        // Set role locked
        $setLocked = (!empty($metaData->locked) && $metaData->locked) ? TRUE : FALSE;

        // Set role color
        $outputColor = (!empty($metaData->color)) ? $metaData->color : '<span class="cs-caption">not set</span>';

        // Set total permissions
        $rolePermissions = (strtolower($data->role_name) !== "master") ? $data->total_permissions : $totalPermissions;

        // Set edit options
        $nameOutput = ($data->role_key !== "master" && ($this->role->isMasterUser() || (!$setLocked && $this->role->canDo('edit_user_role')))) ? '<a href="' . get_site_url('admin/roles/edit/' . $data->role_id) . '" data-tippy-content="Edit ' . htmlspecialchars_decode($data->role_name) . '">' . htmlspecialchars_decode($data->role_name) . ' <span class="hover-item"><i class="fas fa-edit"></i></span></a>' : htmlspecialchars_decode($data->role_name);

        // Set row output
        $dataListOut .= '<tr class="has-hover-item">
            <td>
              <strong class="item--title">' . $nameOutput . '</strong>
            </td>
            <td style="background: ' . $outputColor . ';"><span>' . $outputColor . '</span></td>
            <td>' . $rolePermissions . '/' . $totalPermissions . '</td>
          </tr>';
      }

      // Output User List
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
        $outputMessage = '<p class="csc-body1">Sorry, there were no results that matched your search for <em>"' . $this->data['search'] . '"</em>.</p><p class="csc-body2"><a href="' . get_site_url('admin/roles') . '" title="Clear search results">Clear search results</a></p>';
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

    // Load view
    $this->load->view('roles/index', $this->data, 'admin');
    exit;
  }

  /**
   * Set Add Page Data
   *
   * @param array $rolePermissions `[optional]` Permissions for the role
   */
  protected function setAddData(array $rolePermissions = null)
  {

    // Page Type
    $this->data['page_type'] = 'add';
    // Action URL
    $this->data['action_url'] = get_site_url('admin/roles/add/');
    // H1
    $this->data['page_title'] = 'Add Role';
    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => $this->data['page_title'],
      'href' => $this->data['action_url']
    );
    // Instructions
    $this->data['instructions'] = 'Enter the details for the new role to add it.';

    ################################
    ####    PERMISSIONS LIST    ####
    ################################

    // Init permission options
    $this->data['viewOptions'] = '';
    $this->data['addOptions'] = '';
    $this->data['editOptions'] = '';
    $this->data['deleteOptions'] = '';
    $this->data['otherOptions'] = '';
    // Get list of permissions for assigning
    if ($permissionsData = $this->roleModel->listPermissions()) {

      // Loop through data and create options
      foreach ($permissionsData as $permission) {

        // Set checked if chosen
        $checked = (!empty($rolePermissions) && array_key_exists($permission->rp_id, $rolePermissions)) ? ' checked' : '';

        // Get type
        $permissionType = explode('_', $permission->rp_key);
        // Check which option to add to
        switch ($permissionType[0]) {
          case 'view':
            $this->data['viewOptions'] .= '<p><label><input type="checkbox" name="permissions[' . $permission->rp_id . ']" id="permission_' . $permission->rp_id . '"' . $checked . '><span>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</span></label></p>';
            break;
          case 'add':
            $this->data['addOptions'] .= '<p><label><input type="checkbox" name="permissions[' . $permission->rp_id . ']" id="permission_' . $permission->rp_id . '"' . $checked . '><span>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</span></label></p>';
            break;
          case 'edit':
            $this->data['editOptions'] .= '<p><label><input type="checkbox" name="permissions[' . $permission->rp_id . ']" id="permission_' . $permission->rp_id . '"' . $checked . '><span>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</span></label></p>';
            break;
          case 'delete':
          case 'archive':
            $this->data['deleteOptions'] .= '<p><label><input type="checkbox" name="permissions[' . $permission->rp_id . ']" id="permission_' . $permission->rp_id . '"' . $checked . '><span>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</span></label></p>';
            break;

          default:
            $this->data['otherOptions'] .= '<p><label><input type="checkbox" name="permissions[' . $permission->rp_id . ']" id="permission_' . $permission->rp_id . '"' . $checked . '><span>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</span></label></p>';
            break;
        }
      }
    } else {
      // Set blank options
      $this->data['no_perm_options'] = '<p class="cs-body2">There are currently no permissions available to assign to this role.</p>';
    }

    // Set fallbacks
    $this->data['viewOptions'] = (!empty($this->data['viewOptions'])) ? $this->data['viewOptions'] : '<p class="cs-caption">No view options available</p>';
    $this->data['addOptions'] = (!empty($this->data['addOptions'])) ? $this->data['addOptions'] : '<p class="cs-caption">No add options available</p>';
    $this->data['editOptions'] = (!empty($this->data['editOptions'])) ? $this->data['editOptions'] : '<p class="cs-caption">No edit options available</p>';
    $this->data['deleteOptions'] = (!empty($this->data['deleteOptions'])) ? $this->data['deleteOptions'] : '<p class="cs-caption">No delete options available</p>';
    $this->data['otherOptions'] = (!empty($this->data['otherOptions'])) ? $this->data['otherOptions'] : '<p class="cs-caption">No other options available</p>';
  }

  /**
   * Add Page
   */
  public function add()
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('add_user_role')) {
      // Redirect user with error
      flashMsg('admin_roles', '<strong>Error</strong> Sorry, you are not allowed to add user roles. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/roles');
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
          $this->data['name'] = htmlspecialchars(trim($_POST['name']));
          if (empty($this->data['name'])) {
            // Name not set. Return error.
            $this->data['err']['name'] = 'Please enter a user role name';
          } else if (strlen($this->data['name']) < 3) {
            // Name is less than 3 characters. Return error.
            $this->data['err']['name'] = 'Please enter at least 3 characters';
          }

          // Get key data
          $this->data['key'] = htmlspecialchars(strtolower(str_replace(' ', '_', trim($_POST['key']))));
          if (empty($this->data['key'])) {
            // Data not set. Return error.
            $this->data['err']['key'] = 'Please enter a user role key';
          } else if (strlen($this->data['key']) < 3) {
            // Data is less than 3 characters. Return error.
            $this->data['err']['key'] = 'Please enter at least 3 characters';
          }

          // Get permissions data
          $this->data['permissions'] = (isset($_POST['permissions'])) ? $_POST['permissions'] : array();
          if (empty($this->data['permissions'])) {
            // Data not set. Return error.
            $this->data['err']['permissions'] = 'Please select at least 1 permission';
            flashMsg('roles_role', '<strong>Error</strong> Please select at least 1 permission', 'warning');
          }
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          flashMsg('roles_role', '<strong>Error</strong> There was an error adding the role. Please try again', 'warning');
        }
      } else { // Required data not set. Set Errors.

        $this->data['err']['name'] = 'Please enter a user role name';
      }

      // If valid, add new
      if (empty($this->data['err'])) {
        // Validated

        // Add new role
        if ($roleID = $this->roleModel->addRole(
          $this->data['name'],
          $this->data['key']
        )) {
          // Added

          // Loop through submitted data
          foreach ($this->data['permissions'] as $permissionID => $true) {
            // Add permission
            $this->roleModel->addRolePermission(
              (int) $roleID,
              (int) $permissionID
            );
          }

          // Set success message
          flashMsg('admin_roles', '<strong>Success</strong> ' . $this->data['name'] . ' was added successfully');

          // Return to list
          redirectTo('admin/roles');
          exit;
        } // Unable to add. Return error.

        // Set error message
        flashMsg('roles_role', '<strong>Error</strong> There was an error adding the role. Please try again.', 'warning');
      }

      // If it's made it this far there were errors. Load add view with submitted data

      // Set Add Data
      $this->setAddData((array) $this->data['permissions']);
    } else { // Page wasn't posted. Load view.

      // Set Add Data
      $this->setAddData();
    }

    // Load add view
    $this->load->view('roles/role', $this->data, 'admin');
    exit;
  }

  /**
   * Set Edit Page Data
   *
   * @param array $rolePermissions `[optional]` Permissions for the role
   */
  protected function setEditData(array $rolePermissions = null)
  {

    // Page Type
    $this->data['page_type'] = 'edit';
    // Action URL
    $this->data['action_url'] = get_site_url('admin/roles/edit/' . $this->data['id']);
    // H1
    $this->data['page_title'] = 'Edit ' . $this->data['name'];
    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => $this->data['page_title'],
      'href' => $this->data['action_url']
    );
    // Instructions
    $this->data['instructions'] = 'Enter the new details for ' . $this->data['name'] . ' to update it.';

    ################################
    ####    PERMISSIONS LIST    ####
    ################################

    // Init permission options
    $this->data['viewOptions'] = '';
    $this->data['addOptions'] = '';
    $this->data['editOptions'] = '';
    $this->data['deleteOptions'] = '';
    $this->data['otherOptions'] = '';
    // Get list of permissions for assigning
    if ($permissionsData = $this->roleModel->listPermissions()) {

      // Loop through data and create options
      foreach ($permissionsData as $permission) {

        // Set checked if chosen
        $checked = (!empty($rolePermissions) && array_key_exists($permission->rp_id, $rolePermissions)) ? ' checked' : '';

        // Get type
        $permissionType = explode('_', $permission->rp_key);
        // Check which option to add to
        switch ($permissionType[0]) {
          case 'view':
            $this->data['viewOptions'] .= '<p><label><input type="checkbox" name="permissions[' . $permission->rp_id . ']" id="permission_' . $permission->rp_id . '"' . $checked . '><span>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</span></label></p>';
            break;
          case 'add':
            $this->data['addOptions'] .= '<p><label><input type="checkbox" name="permissions[' . $permission->rp_id . ']" id="permission_' . $permission->rp_id . '"' . $checked . '><span>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</span></label></p>';
            break;
          case 'edit':
            $this->data['editOptions'] .= '<p><label><input type="checkbox" name="permissions[' . $permission->rp_id . ']" id="permission_' . $permission->rp_id . '"' . $checked . '><span>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</span></label></p>';
            break;
          case 'delete':
          case 'archive':
            $this->data['deleteOptions'] .= '<p><label><input type="checkbox" name="permissions[' . $permission->rp_id . ']" id="permission_' . $permission->rp_id . '"' . $checked . '><span>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</span></label></p>';
            break;

          default:
            $this->data['otherOptions'] .= '<p><label><input type="checkbox" name="permissions[' . $permission->rp_id . ']" id="permission_' . $permission->rp_id . '"' . $checked . '><span>' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '</span></label></p>';
            break;
        }
      }
    } else {
      // Set blank options
      $this->data['no_perm_options'] = '<p class="cs-body2">There are currently no permissions available to assign to this role.</p>';
    }

    // Set fallbacks
    $this->data['viewOptions'] = (!empty($this->data['viewOptions'])) ? $this->data['viewOptions'] : '<p class="cs-caption">No view options available</p>';
    $this->data['addOptions'] = (!empty($this->data['addOptions'])) ? $this->data['addOptions'] : '<p class="cs-caption">No add options available</p>';
    $this->data['editOptions'] = (!empty($this->data['editOptions'])) ? $this->data['editOptions'] : '<p class="cs-caption">No edit options available</p>';
    $this->data['deleteOptions'] = (!empty($this->data['deleteOptions'])) ? $this->data['deleteOptions'] : '<p class="cs-caption">No delete options available</p>';
    $this->data['otherOptions'] = (!empty($this->data['otherOptions'])) ? $this->data['otherOptions'] : '<p class="cs-caption">No other options available</p>';
  }

  /**
   * Edit Page
   */
  public function edit(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('edit_user_role')) {
      // Redirect user with error
      flashMsg('admin_roles', '<strong>Error</strong> Sorry, you are not allowed to edit user roles. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/roles');
      exit;
    }

    // Process "edit"

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "save") {

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Check ID
      if (!empty($params) && is_numeric($params[0]) && $params[0] == $_POST['id']) {

        // Get information submitted and validate
        if (isset($_POST['name'])) {

          // Try validating
          try {

            // Get ID
            $this->data['id'] = htmlspecialchars(trim($_POST['id']));

            // Get name data
            $this->data['name'] = htmlspecialchars(trim($_POST['name']));
            if (empty($this->data['name'])) {
              // Name not set. Return error.
              $this->data['err']['name'] = 'Please enter a user role name';
            } else if (strlen($this->data['name']) < 3) {
              // Name is less than 3 characters. Return error.
              $this->data['err']['name'] = 'Please enter at least 3 characters';
            }

            // Get key data
            $this->data['key'] = htmlspecialchars(strtolower(str_replace(' ', '_', trim($_POST['key']))));
            if (empty($this->data['key'])) {
              // Data not set. Return error.
              $this->data['err']['key'] = 'Please enter a user role key';
            } else if (strlen($this->data['key']) < 3) {
              // Data is less than 3 characters. Return error.
              $this->data['err']['key'] = 'Please enter at least 3 characters';
            }

            // Get permissions data
            $this->data['permissions'] = (isset($_POST['permissions'])) ? $_POST['permissions'] : array();
            if (empty($this->data['permissions'])) {
              // Data not set. Return error.
              $this->data['err']['permissions'] = 'Please select at least 1 permission';
              flashMsg('roles_role', '<strong>Error</strong> Please select at least 1 permission', 'warning');
            }
          } catch (Exception $e) {

            // Log error if any and set flash message
            error_log($e->getMessage(), 0);
            flashMsg('roles_role', '<strong>Error</strong> There was an error updating the role. Please try again', 'warning');
          }
        } else { // Required data not set. Set Errors.

          $this->data['err']['name'] = 'Please enter a user role name';
        }

        // If valid, update
        if (empty($this->data['err'])) {
          // Validated

          // Set ID
          $dataID = $this->data['id'];

          // Update
          if ($this->roleModel->editRole(
            (int) $dataID,
            $this->data['name'],
            $this->data['key']
          )) {
            // Updated

            // Get existing permissions
            $existingData = array();
            if ($rolePermissionList = $this->roleModel->listRolePermissions((int) $dataID)) {
              // Loop through already assigned categories
              foreach ($rolePermissionList as $rpData) {
                // Check if category exists in posted data
                if (!array_key_exists($rpData->rpl_rp_id, $this->data['permissions'])) {
                  // Permission doesn't exist. Delete link
                  $this->roleModel->deleteRPLink(
                    (int) $dataID,
                    (int) $rpData->rpl_rp_id
                  );
                } else {
                  // Add to existing data array
                  $existingData[] = $rpData->rpl_rp_id;
                }
              }
            }

            // Loop through submitted data
            foreach ($this->data['permissions'] as $permissionID => $true) {
              // Check if already assigned
              if (empty($existingData) || !in_array($permissionID, $existingData)) {
                // Permission isn't already assigned. Add it
                $this->roleModel->addRolePermission(
                  (int) $dataID,
                  (int) $permissionID
                );
              }
            }

            // Set success message
            flashMsg('admin_roles', '<strong>Success</strong> ' . $this->data['name'] . ' was updated successfully.');

            // Return to page
            redirectTo('admin/roles');
            exit;
          } else { // Unable to update. Return error and redirect to edit view.

            // Set error message
            flashMsg('roles_role', '<strong>Error</strong> There was an error editing the role. Please contact your admin to get this fixed.', 'danger');
          }
        }

        // If it's made it this far there were errors. Load edit view with data

        // Set edit data
        $this->setEditData((array) $this->data['permissions']);

        // Load view
        $this->load->view('roles/roles', $this->data, 'admin');
        exit;
      } else { // Error with the ID. Redirect to list view with error.

        // Set Error
        flashMsg('admin_roles', '<strong>Error</strong> There was an error saving the role. Please try again', 'warning');
        redirectTo('admin/roles');
        exit;
      }
    } else { // Page wasn't posted. Load view.

      // Check ID
      if (!empty($params) && is_numeric($params[0])) {

        // Get data
        if ($roleData = $this->roleModel->getRole((int) $params[0])) {

          // Set data
          foreach ($roleData as $key => $data) {
            $this->data[str_replace(array('role_'), '', $key)] = $data;
          }

          // Check if the role is the master role
          if ($this->data['key'] === "master") {
            // Can't edit master role
            flashMsg('admin_roles', '<strong>Error</strong> The master role cannot be edited', 'danger');
            redirectTo('admin/roles');
            exit;
          }

          // Get role permissions
          $permissionResults = $this->roleModel->listRolePermissions((int) $this->data['id']);

          // Init role permissions
          $rolePermissions = array();
          // Loop through permissions and create an array
          foreach ($permissionResults as $key => $value) {
            // Add permissions to array
            $rolePermissions[$value->rpl_rp_id] = true;
          }

          // Decode the meta information
          $this->data['metaData'] = json_decode($this->data['meta']);

          // Set Edit Data
          $this->setEditData((array) $rolePermissions);

          // Load view
          $this->load->view('roles/role', $this->data, 'admin');
          exit;
        } // Error getting the data. Redirect to list.

      } // No ID present. Redirect to list.

      // Redirect user
      redirectTo('admin/roles');
      exit;
    }
  }

  /**
   * Set Add Permission Page Data
   *
   * @param array $setRoles `[optional]` Roles to assign the permission to
   */
  protected function setAddPermData(array $setRoles = null)
  {

    // Page Type
    $this->data['page_type'] = 'add';
    // Action URL
    $this->data['action_url'] = get_site_url('admin/roles/addpermission');
    // H1
    $this->data['page_title'] = 'Add Permission';
    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => $this->data['page_title'],
      'href' => $this->data['action_url']
    );
    // Instructions
    $this->data['instructions'] = 'Enter the details for the new permission to add it.';

    ##########################
    ####    ROLES LIST    ####
    ##########################

    // Init role options
    $roleOptions = '';
    // Get list of permissions for assigning
    if ($roleData = $this->roleModel->listRoles()) {

      // Loop through data and create options
      foreach ($roleData->results as $role) {

        // Skip Master
        if ($role->role_key !== "master") {

          // Set selected if chosen
          $selected = (!empty($setRoles) && in_array($role->role_id, $setRoles)) ? ' selected' : '';

          // Set to output
          $roleOptions .= '<option value="' . $role->role_id . '"' . $selected . '>' . $role->role_name . '</option>';
        }
      }
    } else {
      // Set blank options
      $roleOptions = '<option disabled>There are currently no roles available to assign to.</option>';
    }
    // Set role list options to data
    $this->data['role_options'] = $roleOptions;
  }

  /**
   * Add Permission Page
   */
  public function addpermission()
  {
    // Check user is allowed to view this
    if (!$this->role->isMasterUser()) {
      // Redirect user with error
      flashMsg('admin_roles', '<strong>Error</strong> Sorry, you are not allowed to add permissions. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/roles');
      exit;
    }

    // Process "add"

    //Check if page posted and process form if it is
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "save") {

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Get information submitted and validate
      if (isset($_POST['key'])) {

        // Try validating
        try {

          // Get key data
          $this->data['key'] = htmlspecialchars(strtolower(str_replace(' ', '_', trim($_POST['key']))));
          if (empty($this->data['key'])) {
            // Data not set. Return error.
            $this->data['err']['key'] = 'Please enter a user role key';
          } else if (strlen($this->data['key']) < 3) {
            // Data is less than 3 characters. Return error.
            $this->data['err']['key'] = 'Please enter at least 3 characters';
          }

          // Get roles data
          $this->data['roles'] = (isset($_POST['roles'])) ? $_POST['roles'] : array();
        } catch (Exception $e) {

          // Log error if any and set flash message
          error_log($e->getMessage(), 0);
          flashMsg('roles_permission', '<strong>Error</strong> There was an error adding the permission. Please try again', 'warning');
        }
      } else { // Required data not set. Set Errors.

        $this->data['err']['key'] = 'Please enter a permission key';
      }

      // If valid, add new
      if (empty($this->data['err'])) {
        // Validated

        // Add new permission
        if ($permissionID = $this->roleModel->addPermission(
          $this->data['key']
        )) {
          // Added

          // Loop through submitted data
          foreach ($this->data['roles'] as $roleID) {
            // Add permission
            $this->roleModel->addRolePermission(
              (int) $roleID,
              (int) $permissionID
            );
          }

          // Set success message
          flashMsg('admin_roles', '<strong>Success</strong>The "' . ucwords(str_replace('_', ' ', $this->data['key'])) . '" permission was added successfully');

          // Return to list
          redirectTo('admin/roles');
          exit;
        } // Unable to add. Return error.

        // Set error message
        flashMsg('roles_permission', '<strong>Error</strong> There was an error adding the permission. Please try again.', 'warning');
      }

      // If it's made it this far there were errors. Load add view with submitted data

      // Set Add Data
      $this->setAddData((array) $this->data['roles']);
    } else { // Page wasn't posted. Load view.

      // Set Add Data
      $this->setAddPermData();
    }

    // Load add view
    $this->load->view('roles/permission', $this->data, 'admin');
    exit;
  }

  /**
   * View Permission Page
   */
  public function viewpermissions()
  {
    // Check user is allowed to view this
    if (!$this->role->isMasterUser()) {
      // Redirect user with error
      flashMsg('admin_roles', '<strong>Error</strong> Sorry, you are not allowed to view permissions. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/roles');
      exit;
    }

    // Action URL
    $this->data['action_url'] = get_site_url('admin/roles/viewpermissions');
    // H1
    $this->data['page_title'] = 'Permissions List';
    // Set Breadcrumbs
    $this->data['breadcrumbs'][] = array(
      'text' => $this->data['page_title'],
      'href' => $this->data['action_url']
    );

    ################################
    ####    PERMISSIONS LIST    ####
    ################################

    // Init permissions json
    $this->data['permissions'] = '[';
    // Get list of permissions for assigning
    if ($permissionsData = $this->roleModel->listPermissions()) {

      // Loop through data and create options
      foreach ($permissionsData as $permission) {

        // Output permission
        $this->data['permissions'] .= '{
          "key" : "' . $permission->rp_key . '",
          "name" : "' . ucwords(str_replace('_', ' ', $permission->rp_key)) . '"
        },';
      }

      // Remove trailing comma
      $this->data['permissions'] = rtrim($this->data['permissions'], ',');
    } else {
      // Set blank options
      $this->data['permissions'] .= '{"empty" : true}';
    }

    // Close the JSON
    $this->data['permissions'] .= ']';

    // Load view
    $this->load->view('roles/permissions', $this->data, 'admin');
    exit;
  }

  /**
   * Assign All Permissions
   */
  public function allpermissions(...$params)
  {
    // Check user is allowed to view this
    if (!$this->role->isMasterUser()) {
      // Redirect user with error
      flashMsg('admin_roles', '<strong>Error</strong> Sorry, you are not allowed to assign all permissions. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin/roles');
      exit;
    }

    // Process "assign"

    // Check ID
    if (!empty($params) && is_numeric($params[0])) {

      // Set role ID
      $roleID = trim($params[0]);

      // Get existing permissions
      $existingData = array();
      if ($rolePermissionList = $this->roleModel->listRolePermissions((int) $roleID)) {
        // Loop through already assigned categories
        foreach ($rolePermissionList as $rpData) {
          // Add to existing data array
          $existingData[] = $rpData->rpl_rp_id;
        }
      }

      // Get list of permissions
      $allPermissions = $this->roleModel->listPermissions();

      // Loop through submitted data
      foreach ($allPermissions as $permissionData) {
        // Check if already assigned
        if (empty($existingData) || !in_array($permissionData->rp_id, $existingData)) {
          // Permission isn't already assigned. Add it
          $this->roleModel->addRolePermission(
            (int) $roleID,
            (int) $permissionData->rp_id
          );
        }
      }

      // Set success message
      flashMsg('admin_roles', '<strong>Success</strong> All permissions have been added to the requested role.');
    } else {
      // Redirect user with error
      flashMsg('admin_roles', '<strong>Error</strong> Sorry, there was an error with the role provided. Please try again.', 'warning');
    }

    // Return to page
    redirectTo('admin/roles');
    exit;
  }
}
