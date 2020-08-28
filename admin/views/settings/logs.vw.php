<?php

/**
 * Admin Logs Settings File
 *
 * @package Cornerstone
 */

// Set the meta/og information for the page
$pageMetaTitle = "Logs | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " site add-on page.";
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = get_site_url('admin/settings/add-on');
$pageMetaType = "website";

// Set any page injected values
$pageHasForm = TRUE;
$pageHeadExtras = '';
$pageFooterExtras = '';
$currentNav = 'settings';
$currentSubNav = $currentNav . '/index';

// Load html head
require(get_theme_path('head.php', 'admin'));
// Load html layout
require(get_theme_path('layout.php', 'admin')); ?>

<div class="csc-row csc-row--no-pad cs-mt-3">
  <section class="csc-col csc-col12">
    <nav class="csc-breadcrumbs">
      <?php
      // Check for and output breadcrumbs
      if (!empty($data->breadcrumbs)) {
        // Output breadcrumbs
        echo outputBreadcrumbs((object) $data->breadcrumbs);
      } ?>
    </nav>
  </section>
</div>
<style>
  body.cs-page .csc-wrapper {
    max-width: 750px;
  }

  body.cs-page .csc-wrapper h2 {
    display: block;
    width: 100%;
    margin-top: 0;
    margin-bottom: 16px;
    padding-bottom: 12px;
    font-size: 1.25rem;
    font-weight: bolder;
    border-bottom: 1px dotted #bdbdbd;
  }

  body.cs-page .csc-wrapper textarea {
    width: 100%;
    min-height: 175px;
    max-height: 400px;
    overflow: auto;
  }
</style>
<div class="csc-wrapper">
  <h1 class="cs-h2 cs-my-2">Logs</h1>
  <?php flashMsg('settings_logs'); ?>
  <section class="csc-container cs-mb-3 cs-p-3" style="min-height: 0;">
    <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><strong>NOTE: Emptying a log will delete the contents permanently</strong></p>
  </section>
  <?php echo $data->log_output; ?>
  <section class="csc-container cs-p-3" style="min-height: 0;">
    <div class="csc-row csc-row--no-gap">
      <div class="csc-col csc-col12 cs-my-1 cs-mb-3 cs-text-center">
        <a href="<?php echo get_site_url('admin/settings'); ?>" class="csc-btn--flat"><span>Back to Settings</span></a>
      </div>
    </div>
  </section>
</div>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>