<?php

/**
 * The main account index template file
 *
 * @package Cornerstone
 */

// Set the meta/og information for the page
$pageMetaTitle = "Account | " . $data->site_name;
$pageMetaDescription = "";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('account');
$pageMetaType = "website";

// Set any page injected values
$pageBodyClassID = 'class="cs-page cs-components cs-account"';
$pageHeadExtras = '';
$pageFooterExtras = '';

// Load html head
require(get_theme_path('head.php'));
// Load html layout
require(get_theme_path('layout.php')); ?>

<!-- End Header ~#~ Start Content -->
<div id="cs-main">
  <?= (!empty($data->breadcrumbs)) ? outputBreadcrumbs((object) $data->breadcrumbs) : ''; // Output breadcrumbs
  ?>
  <div class="csc-wrapper">
    <?php flashMsg('account_index'); ?>
    <header id="account__header">
      <section>
        <h1>My Account</h1>
      </section>
      <section>
        <a class="csc-btn csc-btn--outlined csc-btn--danger" href="<?= get_site_url('account/logout'); ?>">Sign out <i class="fas fa-sign-out-alt csc-bi-right"></i></a>
      </section>
    </header>
    <main>
      <?php
      // Get account menu
      require_once(DIR_ROOT . _DS . 'account' . _DS . 'views' . _DS . 'common' . _DS . 'account-menu.php'); ?>
      <div id="account__overview" class="paper">
        <section id="account__overview__info">
          <header>
            <div>
              <i class="fas fa-user-tie"></i>
            </div>
            <div>
              <h3><?= (!empty($_SESSION['_cs']['user']['name'])) ? $_SESSION['_cs']['user']['name'] : '<em>Name not set</em>'; ?></h3>
              <p><?= (!empty($_SESSION['_cs']['user']['email'])) ? $_SESSION['_cs']['user']['email'] : '<em>Email not set</em>'; ?></p>
              <?php
              // Check if user can access the admin dashboard
              if ($data->isAdmin) { ?>
                <p><a class="csc-btn" href="<?= get_site_url('admin'); ?>" target="_blank"><i class="material-icons csc-bi-left">dashboard</i> View Admin Dashboard</a></p>
              <?php } ?>
            </div>
            <div>
              <a href="<?= get_site_url('account/settings'); ?>" data-tippy-content="Edit your account"><i class="fas fa-cog"></i></a>
            </div>
          </header>
        </section>
      </div>
    </main>
  </div>
</div>
<!-- End Content ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>