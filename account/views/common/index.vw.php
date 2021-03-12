<?php

/**
 * The main account index template file
 *
 * @package Cornerstone
 * @subpackage Mission Equine
 */

// Set the meta/og information for the page
$pageMetaTitle = "Account | " . $data->site_name;
$pageMetaDescription = "";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('account');
$pageMetaType = "website";

// Set any page injected values
$pageBodyClassID = 'class="cs-grid cs-components me-account"';
$pageHeadExtras = '';
$pageFooterExtras = '';

// Load html head
require(get_theme_path('head.php'));
// Load html layout
require(get_theme_path('layout.php')); ?>

<!-- End Header ~#~ Start Content -->
<div id="content">
  <nav class="csc-breadcrumbs" aria-label="Breadcrumb">
    <?php
    // Check for and output breadcrumbs
    if (!empty($data->breadcrumbs)) {
      // Output breadcrumbs
      echo outputBreadcrumbs((object) $data->breadcrumbs);
    } ?>
  </nav>
  <div class="wrapper">
    <?php flashMsg('account_index'); ?>
    <header id="account__header">
      <section>
        <h1>My Account</h1>
      </section>
      <section>
        <a class="csc-btn csc-btn--outlined csc-btn--danger" href="<?php echo get_site_url('account/logout'); ?>">Sign out <i class="fas fa-sign-out-alt csc-bi-right"></i></a>
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
              <h3><?php echo (!empty($data->name)) ? $data->name : $data->contact_name; ?></h3>
              <p><?php echo (!empty($data->email)) ? $data->email : $data->contact_email; ?></p>
              <p class="cs-body2 cs-muted"><strong>Account Name:</strong> <?php echo (!empty($data->contact_name)) ? $data->contact_name : 'n/a'; ?></p>
            </div>
            <div>
              <a href="<?php echo get_site_url('account/settings'); ?>" data-tippy-content="Edit your account"><i class="fas fa-cog"></i></a>
            </div>
          </header>
          <div class="cs-p-3">
            <p class="cs-body2"><strong>Default Location:</strong> <?php echo (!empty($data->assigned_location_name)) ? $data->assigned_location_name : 'n/a'; ?></p>
            <p class="cs-body2"><strong>Assigned Staff:</strong> <?php echo (!empty($data->assigned_user_name)) ? $data->assigned_user_name : 'n/a'; ?></p>
            <p class="cs-body2"><strong>Payment Terms:</strong> <?php echo (!empty($data->payment_term_output)) ? $data->payment_term_output : 'n/a'; ?></p>
          </div>
        </section>
        <section id="account__overview__recent-orders">
          <header>
            <div>
              <i class="fas fa-bags-shopping"></i>
            </div>
            <div>
              <h3>Recent Orders</h3>
            </div>
            <div>
              <a class="cs-body2" href="<?php echo get_site_url('account/orders'); ?>" data-tippy-content="View your order history"><strong>View all</strong></a>
            </div>
          </header>
          <div id="account__overview__recent-orders__list">
            <?php echo $data->recent_orders; ?>
          </div>
        </section>
        <?php if (1 === 2) { ?>
          <section id="account__overview__lists">
            <header>
              <div>
                <i class="fas fa-clipboard-list"></i>
              </div>
              <div>
                <h3>Lists</h3>
              </div>
              <div>
                <a class="cs-body2" href="<?php echo get_site_url('account/lists'); ?>" data-tippy-content="View your lists"><strong>View all</strong></a>
              </div>
            </header>
            <div class="cs-p-3">
              <p class="cs-body2"><em>You currently don't have any lists available</em></p>
            </div>
          </section>
        <?php } ?>
      </div>
    </main>
  </div>
</div>
<!-- End Content ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>