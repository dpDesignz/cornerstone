<?php

/**
 * Collapsible template file
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
  <section class="csc-collapsible">
    <article>
      <div class="csc-collapsible__header"><i class="fas fa-caret-right"></i> Shipment 1</div>
      <div class="csc-collapsible__body">
        <p>Shipment content</p>
      </div>
    </article>
  </section>
</div>
<!-- End Main ~#~ Start Footer -->

<script>
  document.addEventListener("DOMContentLoaded", function(DOMEvent) {
    // Add event listener to collapsible
    document.querySelectorAll(`.csc-collapsible__header`).forEach(collapsible => collapsible.addEventListener('click', toggleCollapsible));
  });
</script>

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>