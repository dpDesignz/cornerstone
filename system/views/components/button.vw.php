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
  <p><button class="csc-btn">Save</button></p>
  <p><button class="csc-btn csc-btn--large">Save</button></p>
  <p><button class="csc-btn csc-btn--small">Save</button></p>
  <p><button class="csc-btn csc-btn--tiny">Save</button></p>
  <p><button class="csc-btn csc-btn--flat">Save</button></p>
  <p><button class="csc-btn csc-btn--outlined">Save</button></p>
  <p><button class="csc-btn csc-btn--floating">+</button></p>
  <p><button class="csc-btn csc-btn--inlineunder">Save</button></p>
</div>
<!-- End Main ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>