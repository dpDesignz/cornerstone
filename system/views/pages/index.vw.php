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
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = get_site_url();
$pageMetaType = "website";

// Set any page injected values
$pageBodyClassID = 'class="cs-page cs-components"';
$pageHeadExtras = '';
$pageFooterExtras = '<script src="https://unpkg.com/@popperjs/core@2"></script><script src="https://unpkg.com/tippy.js@6"></script>';

// Load html head
require(get_theme_path('head.php')); ?>

<!-- End Header ~#~ Start Content -->
<div class="csc-wrapper">
  <div class="csc-alert csc-alert--danger">
    <strong>Error</strong> Hello World!
  </div>
</div>

<div style="text-align: center;">
  <p>Hello <span class="tooltip" title="This is tooltipster">World</span>!</p>
  <p>Hello <span data-tippy-content="This is tippy">World</span>!</p>
</div>
<!-- End Main ~#~ Start Content -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>