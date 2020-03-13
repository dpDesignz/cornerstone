<?php

/**
 * The main index template file
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = $data->site_name;
$pageMetaDescription = "Cornerstone example index page.";
$pageMetaKeywords = "cornerstone, php, framework";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url();
$pageMetaType = "website";

// Set any page injected values
$pageBodyClassID = 'class="cs-page cs-components"';
$pageHeadExtras = '';
$pageFooterExtras = '<script>
  $(".my_select_box").chosen({
    disable_search_threshold: 10,
    no_results_text: "Oops, nothing found!",
    width: "100%",
    search_contains: "true",
    allow_single_deselect: true
  });
</script>';

// Load html head
require(get_theme_path('head.php')); ?>

<!-- End Header ~#~ Start Main -->
<div class="csc-wrapper">
  <div class="csc-alert csc-alert--danger">
    <strong>Error</strong> Hello World!
  </div>
</div>
<!-- End Main ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>