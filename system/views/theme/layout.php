<header id="cs-header">
  <!-- Mobile Navigation -->
  <section id="header__mobile-nav">
    <button id="header__mobile-nav__btn" aria-controls="menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </section>
  <!-- Logo -->
  <section id="header__logo">
    <?php
    // Output site_logo.svg file if it exists, else output the site name
    if (file_exists(get_public_path('admin-files/img/site_logo.svg'))) { ?>
      <a href="<?php echo get_site_url(); ?>"><?php echo file_get_contents(get_public_path('admin-files/img/site_logo.svg')); ?></a>
    <?php } else { ?>
      <h3><a href="<?php echo get_site_url(); ?>"><?php echo $data->site_name; ?></a></h3>
    <?php } ?>
  </section>
  <!-- Navigation -->
  <section id="header__nav" aria-role="menu">
    <nav aria-label="primary">
      <ol>
        <?php
        // Check for and output `menuitems`
        if (!empty($data->menuitems)) {
          // Check for pathMatch
          $pathMatch = (isset($data->menupathmatch)) ? $data->menupathmatch : null;
          // Output `menuitems`
          echo outputMenu((object) $data->menuitems, $pathMatch);
        } ?>
      </ol>
    </nav>
  </section>
  <!-- Account Menu -->
  <section id="header__account-menu">
    <a class="am-btn" href="<?php echo get_site_url('account'); ?>" data-tippy-content="My Account"><i class="fas fa-user-circle"></i></a>
  </section>
</header>