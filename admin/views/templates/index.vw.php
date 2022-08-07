<?php

/**
 * Admin Templates Index File
 *
 * @package Cornerstone
 */

// Set the meta/og information for the page
$pageMetaTitle = "Templates | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " templates page.";
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = get_site_url('admin/templates/');
$pageMetaType = "website";

// Set any page injected values
$pageHeadExtras = '';
$pageFooterExtras = '';
$currentNav = 'settings';
$currentSubNav = $currentNav . '/templates';

// Load html head
require(get_theme_path('head.php', 'admin'));
// Load html layout
require(get_theme_path('layout.php', 'admin')); ?>

<?= (!empty($data->breadcrumbs)) ? outputBreadcrumbs((object) $data->breadcrumbs) : ''; // Output breadcrumbs
?>
<style>
  #files-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
  }

  @media screen and (min-width: 992px) {
    #files-list {
      grid-template-columns: 1fr 1fr;
    }
  }

  #files-list section article {
    display: grid;
    grid-template-columns: 1fr auto;
  }

  #files-list section article p+p {
    text-align: right;
  }
</style>
<div class="csc-wrapper">
  <h1 class="cs-h2 cs-my-2">Templates</h1>
  <?php flashMsg('admin_templates'); ?>
  <div id="files-list">
    <section class="csc-container cs-p-3">
      <h2 class="cs-mt-0">Emails</h2>
      <?php echo $data->email_files_op; ?>
    </section>
    <section class="csc-container cs-p-3">
      <h2 class="cs-mt-0">PDFs</h2>
      <?php echo $data->pdf_files_op; ?>
    </section>
  </div>
</div>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>