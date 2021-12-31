<?php

/**
 * The Admin File Bulk Copy File
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Admin Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = "Bulk Copy | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " bulk copy files.";
$pageMetaImage = get_site_url('admin-files/img/site_logo.png');
$pageMetaCanonical = get_site_url('admin/files/bulk-copy/');
$pageMetaType = "website";

// Set any page injected values
$loadScripts = array(
  'chosen'
);
$pageID = 'admin-file-manager';
$pageHeadExtras = '';
$pageFooterExtras = '';
$currentNav = 'filemanager';

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
    <h1 class="cs-mb-0">Bulk Copy Files</h1>
  </section>
  <section class="csc-col csc-col12 csc-col--md4 cs-text-center cs-text-right-md csc-col--ga-middle">
    <p class="cs-mt-0 cs-mt-md-3">
      <a href="<?= get_site_url('admin/files/?p=' . $data->current_path); ?>" data-tippy-content="Back to file manager"><i class="fas fa-arrow-circle-left"></i> Back</a>
    </p>
  </section>
</div>
<?php flashMsg('files_bulkcopy'); ?>
<section class="csc-container cs-p-3">
  <form action="<?= $data->action_link; ?>" method="POST" id="file-bulk-copy-form" class="csc-form">
    <input type="hidden" name="path" value="<?= $data->path; ?>">
    <?= $data->file_inputs; ?>
    <p class="cs-body2">Files: <em><strong><?= implode('</strong>, <strong>', $data->copy_files); ?></strong></em></p>
    <p class="cs-body2 cs-pb-4"><strong>Source path:</strong> <em>/<?= $data->current_path; ?></em></p>
    <fieldset>
      <div class="csc-input-field">
        <select name="destination" id="destination" data-placeholder="Select a destination folder" tabindex="1" required>
          <?php echo $data->folder_options; ?>
        </select>
        <label>Destination Folder</label>
      </div>
    </fieldset>
    <div class="csc-row csc-row--no-gap">
      <div class="csc-col csc-col12 csc-col--md6 cs-my-1 cs-mb-3"><a href="<?= get_site_url('admin/files/?p=' . $data->current_path); ?>" class="csc-btn--flat"><span>Cancel</span></a></div>
      <div class="csc-col csc-col12 cs-text-right csc-col--md6 cs-my-1 cs-mb-3">
        <button type="submit" name="action" tabindex="10" value="copy" class="csc-btn csc-btn--success">Copy <i class="fas fa-copy csc-bi-right"></i></button>
        <button type="submit" name="action" tabindex="11" value="move" class="csc-btn csc-btn--info">Move <i class="fas fa-cut csc-bi-right"></i></button>
      </div>
    </div>
  </form>
</section>

<script>
  // Document Ready set
  $(document).ready(function() {
    $("#destination").chosen({
      disable_search_threshold: 5,
      no_results_text: "Sorry, nothing was found matching",
      width: "100%",
      search_contains: "true"
    });
  });
</script>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>