<?php

/**
 * The main index template file
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = ucfirst($data->component) . " Component | " . SITE_NAME;
$pageMetaDescription = "Cornerstone example " . $data->component . " component page.";
$pageMetaKeywords = "cornerstone, php, framework";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('component/' . $data->component);
$pageMetaType = "website";

// Set any page injected values
$pageHasForm = TRUE;
$pageBodyClassID = 'class="cs-page cs-components"';
$pageHeadExtras = '<style>
  .csc-wrapper {min-height: 100vh;}
</style>';
$pageFooterExtras = '<script>
      $( document ).ready(function() {
        // bind chosen elements
        $(".chosen-data").chosen({
          disable_search_threshold: 2,
          no_results_text: "Nothing found matching",
          width: "100%",
          search_contains: "true",
          allow_single_deselect: true
        });
      });
    </script>';

// Load html head
require(get_theme_path('head.php')); ?>

<!-- End Header ~#~ Start Main -->
<div class="csc-wrapper">
  <form action="" method="POST" id="chosen-form" class="csc-form cs-py-3">
    <div class="csc-row csc-row--no-pad">
      <div class="csc-col csc-col12 csc-input-field">
        <select name="status" class="chosen-data" tabindex="1" data-placeholder="Select a status" multiple>
          <option></option>
          <optgroup label="Status">
            <option value="0">Archived</option>
            <option value="1">Active</option>
            <option value="2">Hidden</option>
          </optgroup>
          <optgroup label="Colour">
            <option value="4">Orange</option>
            <option value="5">Red</option>
            <option value="6">Green</option>
          </optgroup>
        </select>
        <label>Status*</label>
      </div>
    </div>
    <div class="csc-row csc-row--no-pad">
      <div class="csc-col csc-col12 csc-input-field">
        <select name="status" class="chosen-data" tabindex="2" data-placeholder="Select a status">
          <option></option>
          <option value="0">Archived</option>
          <option value="1">Active</option>
          <option value="2">Hidden</option>
        </select>
        <label>Status*</label>
      </div>
    </div>
  </form>
</div>
<!-- End Main ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>