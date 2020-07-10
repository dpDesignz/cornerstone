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
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = get_site_url($data->page_meta_canonical);
$pageMetaType = "website";

// Set any page injected values
$pageBodyClassID = 'class="cs-page cs-components"';
$pageHeadExtras = $data->page_head_extras;
$pageFooterExtras = $data->page_footer_extras;

// Load html head
require(get_theme_path('head.php'));
// Load html layout
// require(get_theme_path('layout.php'));
?>

<!-- End Header ~#~ Start Main -->
<div class="csc-wrapper cs-px-3">
  <h1><?php echo $data->content_title; ?></h1>
  <?php
  // Output content
  echo $data->content_content;

  // Check if needing to print updated
  if ($data->content_show_updated) {
    // Get edited DTM
    $editedDtm = new DateTime($data->content_edited_dtm);
    // Echo
    echo '<p class="cs-body2 cs-text-right">This page was last updated on ' . $editedDtm->format('F j<\s\up>S</\s\up>, Y') . '</p>';
  } ?>
</div>
<!-- End Main ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>