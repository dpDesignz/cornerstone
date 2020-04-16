<?php

/**
 * Admin Core Settings File
 *
 * @package Cornerstone
 */

// Set the meta/og information for the page
$pageMetaTitle = "Core Settings | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " core settings page.";
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = get_site_url('admin/settings/core');
$pageMetaType = "website";

// Set any page injected values
$pageHasForm = TRUE;
$pageHeadExtras = '';
$pageFooterExtras = '<script>
  // Bind values chosen
  $("#site_timezone").chosen({
    no_results_text: "Oops, no timezones matched",
    width: "100%",
    search_contains: "true"
  });
  </script>';
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
</style>
<div class="csc-wrapper">
  <h1 class="cs-h2 cs-my-2">Core Settings</h1>
  <?php flashMsg('settings_core'); ?>
</div>
<form action="<?php echo get_site_url('admin/settings/save'); ?>" method="POST" id="setting-form" class="csc-wrapper csc-form cs-mt-4">
  <input type="hidden" name="set_type" value="core">
  <section class="csc-container cs-mb-3 cs-p-3">
    <fieldset>
      <legend>Site Information</legend>
      <input type="hidden" name="setting[site_name][current]" <?php if (!empty($data->curr_site_name)) echo ' value="' . $data->curr_site_name . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="text" name="setting[site_name][set]" id="site_name" tabindex="1" <?php if (!empty($data->set_site_name)) echo ' value="' . $data->set_site_name . '"'; ?> autocapitalize="on" required>
          <label for="site_name">Site Name*</label>
        </div>
      </div>
      <input type="hidden" name="setting[site_https][current]" <?php if (!empty($data->curr_site_https)) echo ' value="' . $data->curr_site_https . '"'; ?>>
      <input type="hidden" name="setting[site_https][bool]" value="TRUE">
      <input type="hidden" name="setting[site_url][current]" <?php if (!empty($data->curr_site_url)) echo ' value="' . $data->curr_site_url . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col4 csc-input-field">
          <div class="csc-switch">
            <label>
              http://
              <input type="checkbox" name="setting[site_https][set]" tabindex="2" <?php if (!empty($data->set_site_https) && $data->set_site_https) echo 'checked'; ?>>
              <span class="csc-lever"></span>
              https://
            </label>
          </div>
        </div>
        <div class="csc-col csc-col8 csc-input-field">
          <input type="text" name="setting[site_url][set]" id="site_url" tabindex="3" <?php if (!empty($data->set_site_url)) echo ' value="' . $data->set_site_url . '"'; ?> autocapitalize="off" required>
          <label for="site_url" data-tippy-content="Don't include the 'https://' or 'http://' at the beginning or the trailing '/' at the end.">Site URL* <i class="far fa-question-circle"></i></label>
        </div>
      </div>
      <input type="hidden" name="setting[site_offline][current]" <?php if (!empty($data->curr_site_offline)) echo ' value="' . $data->curr_site_offline . '"'; ?>>
      <input type="hidden" name="setting[site_offline][bool]" value="TRUE">
      <div class="csc-row">
        <div class="csc-col csc-col8">
          <p class="cs-body1 cs-mt-0">Maintenance Mode</p>
        </div>
        <div class="csc-col csc-col4 cs-text-right">
          <div class="csc-switch">
            <label>
              Off
              <input type="checkbox" name="setting[site_offline][set]" tabindex="4" <?php if (!empty($data->set_site_offline) && $data->set_site_offline) echo 'checked'; ?>>
              <span class="csc-lever"></span>
              On
            </label>
          </div>
        </div>
      </div>
    </fieldset>
  </section>
  <section class="csc-container cs-mb-3 cs-p-3">
    <fieldset>
      <legend>Localization</legend>
      <input type="hidden" name="setting[site_timezone][current]" <?php if (!empty($data->curr_site_timezone)) echo ' value="' . $data->curr_site_timezone . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <select name="setting[site_timezone][set]" id="site_timezone" tabindex="5" data-placeholder="Select a timezone" required>
            <?php echo $data->timezone_options; ?>
          </select>
          <label>Timezone*</label>
        </div>
      </div>
      <input type="hidden" name="setting[phone_locale][current]" <?php if (!empty($data->curr_phone_locale)) echo ' value="' . $data->curr_phone_locale . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="text" name="setting[phone_locale][set]" id="phone_locale" tabindex="6" <?php if (!empty($data->set_phone_locale)) echo ' value="' . $data->set_phone_locale . '"'; ?> autocapitalize="off">
          <label for="phone_locale">Phone Locale</label>
        </div>
      </div>
    </fieldset>
  </section>
  <section class="csc-container cs-p-3" style="min-height: 0;">
    <div class="csc-row csc-row--no-gap">
      <div class="csc-col csc-col6 cs-my-1 cs-mb-3">
        <a href="<?php echo get_site_url('admin/settings'); ?>" class="csc-btn--flat"><span>Cancel</span></a>
      </div>
      <div class="csc-col csc-col6 cs-text-right cs-my-1 cs-mb-3">
        <button type="submit" name="action" tabindex="11" value="save" class="csc-btn csc-btn--success">Save <i class="fas fa-save csc-bi-right"></i></button>
      </div>
    </div>
    <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
  </section>
</form>

<script>
  // Init validation on document ready
  $(document).ready(function() {
    let validator = $("#setting-form").validate({
      rules: {
        site_name: {
          required: true,
          minlength: 3
        },
        site_url: {
          required: true,
          url: true
        }
      },
      messages: {
        site_name: {
          required: "Please enter a site name",
          minlength: "Please enter at least 3 characters"
        },
        site_url: {
          required: "Please enter a site URL",
          minlength: "Please enter a valid URL"
        }
      }
    });
    <?php
    // Output errors if they exist
    if (!empty($data->err)) {
      // Call the formatting function
      echo 'validator' . showValidationErrors($data->err);
    } ?>
  });
</script>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>