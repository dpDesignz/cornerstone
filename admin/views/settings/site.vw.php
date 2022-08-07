<?php

/**
 * Admin Site Settings File
 *
 * @package Cornerstone
 */

// Set the meta/og information for the page
$pageMetaTitle = "Site Settings | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " site settings page.";
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = get_site_url('admin/settings/site');
$pageMetaType = "website";

// Set any page injected values
$loadScripts = array(
  'validate',
  'chosen'
);
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
<style>
  body.cs-page .csc-wrapper {
    max-width: 750px;
  }
</style>
<div class="csc-wrapper">
  <h1 class="cs-h2 cs-my-2">Site Settings</h1>
  <?php flashMsg('settings_site'); ?>
</div>
<form action="<?php echo get_site_url('admin/settings/save'); ?>" method="POST" id="setting-form" class="csc-wrapper csc-form cs-mt-4">
  <input type="hidden" name="set_type" value="site">
  <section class="csc-container cs-mb-3 cs-p-3">
    <fieldset>
      <legend>Style Settings</legend>
      <input type="hidden" name="setting[tooltip_settings][current]" <?php if (!empty($data->curr_tooltip_settings)) echo ' value="' . $data->curr_tooltip_settings . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="text" name="setting[tooltip_settings][set]" id="tooltip_settings" tabindex="1" <?php if (!empty($data->set_tooltip_settings)) echo ' value="' . $data->set_tooltip_settings . '"'; ?> autocapitalize="off">
          <label for="tooltip_settings" data-tippy-content="Any custom tooltip settings to set">Tooltip settings <i class="far fa-question-circle"></i></label>
        </div>
      </div>
    </fieldset>
  </section>
  <section class="csc-container cs-mb-3 cs-p-3">
    <fieldset>
      <legend>Site Docs</legend>
      <input type="hidden" name="setting[docs_private][current]" <?php if (!empty($data->curr_docs_private)) echo ' value="' . $data->curr_docs_private . '"'; ?>>
      <input type="hidden" name="setting[docs_private][bool]" value="TRUE">
      <div class="csc-row">
        <div class="csc-col csc-col8">
          <p class="cs-body1">Require login to view docs <i class="far fa-question-circle" data-tippy-content="Setting this requires a user to be logged in to be able to see the site docs"></i></p>
        </div>
        <div class="csc-col csc-col4 cs-text-right">
          <div class="csc-switch">
            <label>
              No
              <input type="checkbox" name="setting[docs_private][set]" tabindex="2" <?php if (!empty($data->set_docs_private) && $data->set_docs_private) echo 'checked'; ?>>
              <span class="csc-lever"></span>
              Yes
            </label>
          </div>
        </div>
      </div>
    </fieldset>
  </section>
  <section class="csc-container cs-mb-3 cs-p-3">
    <fieldset>
      <legend>Site Notice</legend>
      <p class="cs-body2">This setting is currently not available</p>
    </fieldset>
  </section>
  <section class="csc-container cs-p-3" style="min-height: 0;">
    <div class="csc-row csc-row--no-gap">
      <div class="csc-col csc-col6 cs-my-1 cs-mb-3">
        <a href="<?php echo get_site_url('admin/settings'); ?>" class="csc-btn--flat"><span>Cancel</span></a>
      </div>
      <div class="csc-col csc-col6 cs-text-right cs-my-1 cs-mb-3">
        <button type="submit" name="action" tabindex="3" value="save" class="csc-btn csc-btn--success">Save <i class="fas fa-save csc-bi-right"></i></button>
      </div>
    </div>
    <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
  </section>
</form>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>