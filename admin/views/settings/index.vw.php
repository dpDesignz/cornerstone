<?php

/**
 * Admin Settings Index File
 *
 * @package Cornerstone
 */

// Set the meta/og information for the page
$pageMetaTitle = "Settings | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " settings page.";
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = get_site_url('admin/settings/');
$pageMetaType = "website";

// Set any page injected values
$pageHeadExtras = '';
$pageFooterExtras = '';
$currentNav = 'settings';
$currentSubNav = $currentNav . '/index';

// Load html head
require(get_theme_path('head.php', 'admin'));
// Load html layout
require(get_theme_path('layout.php', 'admin')); ?>

<?= (!empty($data->breadcrumbs)) ? outputBreadcrumbs((object) $data->breadcrumbs) : ''; // Output breadcrumbs
?>
<h1 class="cs-h2 cs-my-2">Settings</h1>
<?php flashMsg('admin_settings'); ?>
<style>
  #setting-sections>article {
    padding: 25px 12px;
    border-top: 3px solid hsl(0, 0%, 74%);
  }

  #setting-sections>article.red {
    color: hsl(0, 100%, 42%) !important;
    border-top-color: hsl(0, 100%, 42%);
  }

  #setting-sections>article.red * {
    color: hsl(0, 100%, 42%) !important;
  }

  #setting-sections>article:hover {
    border-top-color: var(--secondary-color);
  }

  #setting-sections>article>a {
    display: grid;
    grid-template-columns: 1fr;
    justify-items: center;
    align-content: start;
    color: inherit;
  }

  #setting-sections>article>a>i {
    color: var(--primary-color);
    font-size: 100px;
    transition: all 0.3s ease-in-out;
  }

  #setting-sections>article>a>h3 {
    margin-bottom: 0;
  }

  #setting-sections>article>a:hover>h3 {
    text-decoration: underline;
  }

  #setting-sections>article>a>p {
    color: hsl(0, 0%, 46%);
    font-size: 0.85em;
    text-align: center;
  }
</style>
<div id="setting-sections" class="csc-row cs-mt-4">
  <?php if ($role->canDo('edit_core_settings')) { ?>
    <article class="csc-col csc-col12 csc-col--md6 csc-col--lg3 csc-container">
      <a href="<?php echo get_site_url('admin/settings/core'); ?>">
        <i class="fas fa-wrench"></i>
        <h3>Core</h3>
        <p>URL, Site name, Maintenance mode +</p>
      </a>
    </article>
  <?php }
  if ($role->canDo('edit_mail_settings')) { ?>
    <article class="csc-col csc-col12 csc-col--md6 csc-col--lg3 csc-container">
      <a href="<?php echo get_site_url('admin/settings/mail'); ?>">
        <i class="fas fa-mail-bulk"></i>
        <h3>Mail</h3>
        <p>PHPMailer &amp; SMTP Information</p>
      </a>
    </article>
  <?php }
  if ($role->canDo('edit_security_settings')) { ?>
    <article class="csc-col csc-col12 csc-col--md6 csc-col--lg3 csc-container">
      <a href="<?php echo get_site_url('admin/settings/security'); ?>">
        <i class="fas fa-shield-alt"></i>
        <h3>Security</h3>
        <p>Registrations, Password options, Browser tracking +</p>
      </a>
    </article>
  <?php }
  if ($role->canDo('edit_site_settings')) { ?>
    <article class="csc-col csc-col12 csc-col--md6 csc-col--lg3 csc-container">
      <a href="<?php echo get_site_url('admin/settings/site'); ?>">
        <i class="fas fa-sitemap"></i>
        <h3>Site</h3>
        <p>Tooltip, Docs, Site notice</p>
      </a>
    </article>
  <?php }
  if ($role->canDo('edit_addon_settings')) { ?>
    <article class="csc-col csc-col12 csc-col--md6 csc-col--lg3 csc-container">
      <a href="<?php echo get_site_url('admin/settings/add-ons'); ?>">
        <i class="fas fa-puzzle-piece"></i>
        <h3>Add-ons</h3>
        <p>Add-on keys &amp; Integrations +</p>
      </a>
    </article>
  <?php }
  if ($role->canDo('view_log_settings')) { ?>
    <article class="csc-col csc-col12 csc-col--md6 csc-col--lg3 csc-container">
      <a href="<?php echo get_site_url('admin/settings/logs'); ?>">
        <i class="fas fa-clipboard-list"></i>
        <h3>Logs</h3>
        <p>Error, Warning, and Danger logs</p>
      </a>
    </article>
  <?php }
  if ($role->canDo('view_php_info')) { ?>
    <article class="csc-col csc-col12 csc-col--md6 csc-col--lg3 csc-container red" data-tippy-content="DANGER: Only click this if you know what you're doing!">
      <a href="<?php echo get_site_url('admin/settings/php_info'); ?>">
        <i class="fab fa-php"></i>
        <h3><i class="fas fa-exclamation-triangle"></i> PHP Info <i class="fas fa-exclamation-triangle"></i></h3>
        <p>PHP Environment Information</p>
      </a>
    </article>
  <?php } ?>
</div>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>