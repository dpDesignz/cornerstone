  <!-- Start Sidebar -->
  <aside id="sidebar__main">
    <!-- Mobile Navigation -->
    <section id="csa-sidebar__mobile-nav">
      <button id="csa-sidebar__mobile-nav__btn" aria-controls="menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </section>
    <section id="csa-sidebar__identity">
      <?php
      // Output site_logo.svg file if it exists, else output the site name
      if (file_exists(get_public_path('admin-files/img/site_logo.svg'))) { ?>
        <a href="<?php echo get_site_url('admin/'); ?>"><img src="<?php echo get_site_url('admin-files/img/site_logo.svg'); ?>" alt="<?php echo $data->site_name; ?> Logo" /></a>
      <?php } else { ?>
        <h3><a href="<?php echo get_site_url('admin/'); ?>"><?php echo $data->site_name; ?></a></h3>
      <?php } ?>
    </section>
    <nav id="sidebar__nav">
      <ol id="sidebar__nav-links">
        <li id="sidebar__collapse"><button id="collapse-btn" data-tippy-content="Menu Collapse"><i class="fas fa-play-circle"></i> <span>Collapse Menu</span></button></li>
        <li class="sidebar__nav-separator">
          <span>Dashboard</span>
        </li>
        <li <?php echo (!empty($currentNav) && $currentNav === 'dashboard') ? 'class="active"' : '' ?>><a href="<?php echo get_site_url('admin/dashboard'); ?>" data-tippy-content="Dashboard"><i class="material-icons">dashboard</i> <span>Dashboard</span></a></li>
        <?php
        // Set fallbacks
        $currentNav = (!empty($currentNav)) ? $currentNav : '';
        $currentSubNav = (!empty($currentSubNav)) ? $currentSubNav : '';
        // Require any extra menu items
        require(get_theme_path('ext.navbar.php', 'admin'));

        // Users Menu
        $adminSidebarUsersMenu = array(array(
          'type' => 'parent',
          'permission' => 'view_user',
          'identifier' => 'users',
          'text' => 'Users',
          'icon' => 'fas fa-users',
          'children' => array(
            array(
              'identifier' => 'users',
              'text' => 'All Users',
              'href' => get_site_url('admin/users')
            ),
            array(
              'permission' => 'view_user_role',
              'identifier' => 'roles',
              'text' => 'Roles',
              'href' => get_site_url('admin/roles')
            )
          )
        ));
        echo outputAdminMenu($adminSidebarUsersMenu, $currentNav, $currentSubNav);

        // Settings Menu
        $adminSidebarSettingsMenu = array(array(
          'type' => 'parent',
          'permission' => 'view_settings',
          'identifier' => 'settings',
          'text' => 'Settings',
          'icon' => 'fas fa-cogs',
          'children' => array(
            array(
              'identifier' => 'index',
              'text' => 'Settings',
              'href' => get_site_url('admin/settings')
            )
          )
        ));
        echo outputAdminMenu($adminSidebarSettingsMenu, $currentNav, $currentSubNav); ?>
      </ol>
    </nav>
  </aside>
  <!-- End Sidebar ~#~ Start Header -->
  <!-- Start Header -->
  <header>
    <section id="csa-header__search"></section>
    <section id="csa-header__nav">
      <nav>
        <ol>
          <li><a href="<?php echo get_site_url(); ?>" data-tippy-content="View Website Front End" target="_blank"><i class="fas fa-home"></i></a></li>
          <li><a href="javascript:alert('Notification system coming soon');" data-tippy-content="Notifications (Coming soon)"><i class="fas fa-bell"></i></a></li>
          <li><a href="javascript:alert('Help desk coming soon');" data-tippy-content="Need help? (Coming soon)"><i class="far fa-question-circle"></i></a></li>
          <li><a href="<?php echo get_site_url('admin/logout'); ?>" data-tippy-content="Log out" class="logout"><i class="fas fa-power-off"></i></a></li>
        </ol>
      </nav>
    </section>
    <section id="csa-header__welcome">
      <p><span>Welcome</span> <strong><?php echo $_SESSION['_cs']['user']['name']; ?></strong></p>
    </section>
  </header>
  <!-- End Header ~#~ Start Content -->
  <div id="main__content">
    <!-- Start Main -->
    <main>