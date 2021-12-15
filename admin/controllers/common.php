<?php
class Common extends Cornerstone\Controller
{

  /**
   * Index Page
   */
  public function index()
  {
    $this->dashboard();
  }

  /**
   * Dashboard Page
   */
  public function dashboard()
  {

    // Check if user is allowed admin access
    checkAdminAccess();

    // Load Dashboard
    $this->load->view('common/dashboard', $this->data, 'admin');
  }

  /**
   * Progress Page
   */
  public function progress()
  {
    // Check user is allowed to view this
    if (!$this->role->canDo('view_progress')) {
      // Redirect user with error
      flashMsg('admin_dashboard', '<strong>Error</strong> Sorry, you are not allowed to view the site progress. Please contact your site administrator for access to this.', 'warning');
      redirectTo('admin');
      exit;
    }

    // Check if user is logged in
    if (!userPageProtect()) {

      // If user is not logged in, show the login page
      // Only show error if page isn't direct access
      if ($_SERVER['REQUEST_URI'] != "/admin/") {
        flashMsg('admin_login', 'You need to log in first.', 'warning');
      }
      $this->load->view('common/login', '', 'admin');
      exit;
    } else { // Output dashboard

      // Set Breadcrumbs
      $this->data['breadcrumbs'] = array(
        array(
          'text' => 'Dashboard',
          'href' => get_site_url('admin')
        ),
        array(
          'text' => 'Progress',
          'href' => get_site_url('admin/progress')
        )
      );

      // Load view
      $this->load->view('pages/progress', $this->data, 'admin');
    }
  }

  /**
   * Login Page
   */
  public function login()
  {
    // Load the view
    $this->load->view('common/login', '', 'admin');
  }
}
