<?php

/**
 * The main page template file
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = $data->page_meta_title . " | " . $data->site_name;
$pageMetaDescription = $data->page_meta_description;
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url($data->seo_keyword);
$pageMetaType = "website";

// Set any page injected values
$pageBodyClassID = 'class="cs-page cs-components"';
$pageHeadExtras = $data->page_head_extras;
$pageFooterExtras = $data->page_footer_extras;

// Load html head
require(get_theme_path('head.php')); ?>

<!-- End Header ~#~ Start Main -->
<div class="csc-wrapper">
  <?php echo $data->content_content; ?>
</div>
<!-- End Main ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>