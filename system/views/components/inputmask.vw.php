<?php

/**
 * Input Mask template file
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
$pageFooterExtras = '
    $( document ).ready(function() {
      $("#inputmask-form").validate({
        rules: {
          rate: {
            required: true,
            number: true
          }
        },
        messages: {
          rate: {
            required: "Please enter a tax rate value",
            number: "Please enter a valid number with decimals"
          }
        }
      })});';

// Load html head
require(get_theme_path('head.php')); ?>

<!-- End Header ~#~ Start Main -->
<div class="csc-wrapper cs-mt-3">
  <form action="" method="POST" id="inputmask-form" class="csc-form cs-p-3">
    <div class="csc-row csc-row--no-pad">
      <div class="csc-col csc-col12 csc-col--md6 csc-input-field">
        <input type="text" name="default_markup" id="default_markup" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'suffix': '%', 'min': '0.00'" tabindex="4" required>
        <label for="default_markup">Default Markup %*</label>
      </div>
      <div class="csc-col csc-col12 csc-col--md6 csc-input-field">
        <input type="text" name="rate" id="rate" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 4, 'digitsOptional': false, 'suffix': '%', 'min': '0.0000'" tabindex="3" value="15.0000" required>
        <label for="rate">Tax Rate*</label>
      </div>
    </div>
  </form>
</div>
<!-- End Main ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>