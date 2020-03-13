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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  echo '<pre>';
  print_r($_POST);
  echo '</pre>';
  exit;
}

// Set any page injected values
$pageHasForm = TRUE;
$pageBodyClassID = 'class="cs-page cs-components"';
$pageHeadExtras = '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css" integrity="sha256-FdatTf20PQr/rWg+cAKfl6j4/IY3oohFAJ7gVC3M34E=" crossorigin="anonymous" />
<style>
  .csc-wrapper {min-height: 100vh;}
</style>';
$pageFooterExtras = '<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/js/select2.min.js" integrity="sha256-wfVTTtJ2oeqlexBsfa3MmUoB77wDNRPqT1Q1WA2MMn4=" crossorigin="anonymous"></script>
<script>
  $( document ).ready(function() {
    // bind select2 elements
    $(".select2-data").select2({
      minimumResultsForSearch: 2,
      language: {
        noResults: function() {
          return "No match found. Click here to add.";
        },
      },
      allowClear: true
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
        <select name="status" class="select2-data" tabindex="1" data-placeholder="Select a status" style="width:100%;">
          <option></option>
          <option value="0">Archived</option>
          <option value="1">Active</option>
          <option value="2">Hidden</option>
        </select>
        <label>Status*</label>
      </div>
    </div>
    <button type="submit" name="action" tabindex="2" value="save" class="csc-btn csc-btn--wide csc-btn--success">Save <i class="fas fa-save csc-bi-right"></i></button>
  </form>
</div>
<!-- End Main ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>