<?php

/**
 * Pagination template file
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
      <ul class="cs-pagination">
        <li class="cs-pagitem--info">1 - 1 of 1</li>
        <li class="cs-pagitem--disabled"><a href="#!"><i class="fas fa-chevron-left"></i></a></li>
        <li class="cs-pagitem--active"><a href="#!">1</a></li>
        <li class="waves-effect"><a href="#!">2</a></li>
        <li class="waves-effect"><a href="#!">3</a></li>
        <li class="waves-effect"><a href="#!"><i class="fas fa-chevron-right"></i></a></li>
      </ul>
    </div>
  </div>
  <div class="csc-row cs-mt-3">
    <div class="csc-col csc-col12 cs-mt-3">
      <?php echo outputPagination(958); ?>
    </div>
  </div>
</div>
<!-- End Main ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>