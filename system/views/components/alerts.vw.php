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
$pageBodyClassID = 'class="cs-page cs-components"';
$pageHeadExtras = '<style>
  .csc-wrapper {min-height: 100vh;}
</style>';
$pageFooterExtras = '';

// Load html head
require(get_theme_path('head.php')); ?>

<!-- End Header ~#~ Start Main -->
<div class="csc-wrapper">
  <?php
  // Set alert data (type (string),has icon (int|bool))
  $alertArray = array('primary,0', 'secondary,0', 'success,1', 'warning,1', 'danger,1', 'info,1', 'light,0', 'dark,0');
  // Loop through options
  foreach ($alertArray as $alertType) {
    $alertType = explode(',', $alertType);
    echo '<div class="csc-alert csc-alert--' . $alertType[0] . '"><strong>' . ucfirst($alertType[0]) . '</strong> This is a ' . $alertType[0] . ' alert</div>';
    // Check if this alert type has an icon
    if ($alertType[1]) {
      echo '<div class="csc-alert csc-alert--' . $alertType[0] . ' csc-alert--icon"><strong>' . ucfirst($alertType[0]) . '</strong> This is a ' . $alertType[0] . ' alert with an icon</div>';
    }
  } ?>
</div>
<!-- End Main ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>