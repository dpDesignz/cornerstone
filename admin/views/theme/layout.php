  <!-- Start Sidebar -->
  <aside id="sidebar__main">
    <section id="csa-sidebar_identity">
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
        <li id="sidebar__collapse"><button id="collapse-btn" title="Menu Collapse" class="tooltip"><i class="fas fa-play-circle"></i> <span>Collapse Menu</span></button></li>
        <li class="sidebar__nav-separator">
          <span>Dashboard</span>
        </li>
        <li <?php echo (!empty($currentNav) && $currentNav === 'dashboard') ? 'class="active"' : '' ?>><a class="tooltip" href="<?php echo get_site_url('admin/dashboard'); ?>" title="Dashboard"><i class="material-icons">dashboard</i> <span>Dashboard</span></a></li>
        <?php
        // Set fallbacks
        $currentNav = (!empty($currentNav)) ? $currentNav : '';
        $currentSubNav = (!empty($currentSubNav)) ? $currentSubNav : '';
        // Require any extra menu items
        require(get_theme_path('ext.navbar.php', 'admin')); ?>
        <li class="has-subnav<?php echo (!empty($currentNav) && $currentNav === 'users') ? ' active' : '' ?>">
          <a class="tooltip" data-toggle="collapse" title="Users" aria-expanded="<?php echo (!empty($currentNav) && $currentNav === 'users') ? 'true' : 'false'; ?>"><i class="fas fa-users"></i> <span>Users</span><b class="caret"></b></a>
          <ol class="sidebar__sub-nav" aria-hidden="<?php echo (!empty($currentNav) && $currentNav === 'users') ? 'false' : 'true'; ?>">
            <li <?php echo (!empty($currentSubNav) && $currentSubNav === 'users/index') ? 'class="active"' : ''; ?>><a href="<?php echo get_site_url('admin/users'); ?>">All Users</a></li>
            <li <?php echo (!empty($currentSubNav) && $currentSubNav === 'users/add') ? 'class="active"' : ''; ?>><a href="<?php echo get_site_url('admin/users/add'); ?>">Add New</a></li>
            <li <?php echo (!empty($currentSubNav) && $currentSubNav === 'users/groups') ? 'class="active"' : ''; ?>><a href="<?php echo get_site_url('admin/users/groups'); ?>">User Groups</a></li>
          </ol>
        </li>
        <li class="has-subnav<?php echo (!empty($currentNav) && $currentNav === 'settings') ? ' active' : '' ?>">
          <a class="tooltip" data-toggle="collapse" title="Settings" aria-expanded="<?php echo (!empty($currentNav) && $currentNav === 'settings') ? 'true' : 'false'; ?>"><i class="fas fa-cogs"></i> <span>Settings</span><b class="caret"></b></a>
          <ol class="sidebar__sub-nav" aria-hidden="<?php echo (!empty($currentNav) && $currentNav === 'settings') ? 'false' : 'true'; ?>">
            <li <?php echo (!empty($currentSubNav) && $currentSubNav === 'settings/index') ? 'class="active"' : ''; ?>><a href="<?php echo get_site_url('admin/settings'); ?>">Site Settings</a></li>
          </ol>
        </li>
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
          <li><a class="tooltip" href="<?php echo get_site_url(); ?>" title="View Website Front End" target="_blank"><i class="fas fa-home"></i></a></li>
          <li><a href="javascript:alert('Notification system coming soon');" title="Notifications (Coming soon)" class="tooltip"><i class="fas fa-bell"></i></a></li>
          <li><a href="javascript:alert('Help desk coming soon');" title="Need help? (Coming soon)" class="tooltip"><i class="far fa-question-circle"></i></a></li>
          <li><a href="<?php echo get_site_url('admin/logout'); ?>" title="Log out" class="logout tooltip"><i class="fas fa-power-off"></i></a></li>
        </ol>
      </nav>
    </section>
    <section id="csa-header__welcome">
      <p><span>Welcome</span><strong><?php echo $_SESSION['_cs']['user']['name']; ?></strong></p>
    </section>
  </header>
  <!-- End Header ~#~ Start Main -->
  <main id="main__content">
    <main>