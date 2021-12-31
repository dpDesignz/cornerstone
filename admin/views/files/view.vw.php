<?php

/**
 * The Admin View File File
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Admin Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = "View File | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " view file.";
$pageMetaImage = get_site_url('admin-files/img/site_logo.png');
$pageMetaCanonical = get_site_url('admin/files/view/');
$pageMetaType = "website";

// Set any page injected values
$pageID = 'admin-file-manager';
$pageHeadExtras = '<!-- Highlight.js ~ https://highlightjs.org/ -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.3.1/styles/vs.min.css">';
$pageFooterExtras = '<!-- Highlight.js ~ https://highlightjs.org/ -->
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.3.1/highlight.min.js"></script>
<script>hljs.highlightAll(); var isHighlightingEnabled = true;</script>';
$currentNav = 'filemanager';

// Check if quick view
if (!$data->quickView) {

  // Load html head
  require(get_theme_path('head.php', 'admin'));
  // Load html layout
  require(get_theme_path('layout.php', 'admin')); ?>

  <div class="csc-row cs-mt-3">
    <section class="csc-col csc-col12">
      <nav class="csc-breadcrumbs">
        <?php
        // Check for and output breadcrumbs
        if (!empty($data->breadcrumbs)) {
          // Output breadcrumbs
          echo outputBreadcrumbs((object) $data->breadcrumbs);
        } ?>
      </nav>
    </section>
  </div>
  <div class="csc-row">
    <section class="csc-col csc-col12 csc-col--md8 cs-mb-md-3">
      <?= $data->headerOP; ?>
    </section>
    <section class="csc-col csc-col12 csc-col--md4 cs-text-center cs-text-right-md csc-col--ga-top">
      <p class="cs-mt-0 cs-mt-md-3">
        <a href="<?= get_site_url('admin/files/?p=' . $data->current_path); ?>" data-tippy-content="Back to file manager"><i class="fas fa-arrow-circle-left"></i> Back</a>
      </p>
    </section>
  </div>
<?php } else { ?>
  <div class="modal csc-modal--flowable" style="display: block; min-width: 50vw; width: 100%;">
    <div class="csc-modal__header">
      <i class="<?= $data->file_icon; ?>"></i> <?= $data->file; ?>
    </div>
    <div class="csc-modal__content" style="background-color: #f2f2f2;">
    <?php  } ?>
    <?php flashMsg('files_view'); ?>
    <section>
      <?= $data->contentOP; ?>
    </section>

    <?php
    // Check if quick view
    if (!$data->quickView) {
      // Load html footer
      require(get_theme_path('footer.php', 'admin'));
    } else { ?>
      <div class="csc-modal__actions">
        <a href="#" class="csc-btn--flat" rel="modal:close"><span>Close</span></a>
      </div>
    </div>
    <script>
      hljs.highlightAll();
      var isHighlightingEnabled = true;
    </script>
  </div>
<?php } ?>