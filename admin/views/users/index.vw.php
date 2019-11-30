<?php
  /**
   * The Admin Users Index File
   *
   * @package Cornerstone
   * @subpackage Core Cornerstone Admin Theme
   */

  // Set the meta/og information for the page
  $pageMetaTitle = "Users | " . SITE_NAME . " Admin";
  $pageMetaDescription = SITE_NAME . " admin all users.";
  $pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
  $pageMetaCanonical = get_site_url('admin/users');
  $pageMetaType = "website";

  // Set any page injected values
  $pageHeadExtras = '';
  $pageFooterExtras = '';
  $currentNav = 'users';
  $currentSubNav = $currentNav.'/index';

  // Load html head
  require(get_theme_path('head.php', 'admin'));
    // Load html layout
    require(get_theme_path('layout.php', 'admin')); ?>

    <div class="csc-row cs-mt-3">
        <section class="csc-col csc-col12">
          <nav class="csc-breadcrumbs">
          <a href="<?php echo get_site_url('admin'); ?>" class="csc-breadcrumb" title="Dashboard">Dashboard</a>
          <a href="<?php echo get_site_url('admin/users'); ?>" class="csc-breadcrumb" title="Users">Users</a>
        </nav>
      </section>
    </div>
    <div class="csc-row">
      <h1 class="csc-col csc-col12 csc-col--md6 cs-mb-0 cs-mb-md-3">Users</h1>
    </div>
    <div class="csc-row">
      <?php echo $data->userListOut; ?>
    </div>

<?php
  // Load html footer
  require(get_theme_path('footer.php', 'admin')); ?>