<?php

/**
 * Page Menu template file
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
$pageFooterExtras = '';

// Load html head
require(get_theme_path('head.php')); ?>

<!-- End Header ~#~ Start Main -->
<div class="csc-wrapper cs-mt-3">
  <div class="csc-row">
    <div class="csc-col csc-col12">
      <nav class="csc-pagemenu">
        <ol>
          <li><a href="#!" class="csc-pmitem csc-pmitem--active" title="Item 1">Item 1</a></li>
          <li><a href="#!" class="csc-pmitem" title="Item 2">Item 2</a></li>
          <li><a href="#!" class="csc-pmitem" title="Item 3">Item 3</a></li>
        </ol>
      </nav>
    </div>
  </div>
</div>
<!-- End Main ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>