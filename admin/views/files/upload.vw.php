<?php

/**
 * The Admin File Upload File
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Admin Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = "File Upload | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " file manager.";
$pageMetaImage = get_site_url('admin-files/img/site_logo.png');
$pageMetaCanonical = get_site_url('admin/files/upload/');
$pageMetaType = "website";

// Set any page injected values
$pageID = 'admin-file-manager';
$pageHeadExtras = '<!-- Dropzone.js ~ https://www.dropzone.dev/js/ -->
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />';
$pageFooterExtras = '<!-- Dropzone.js ~ https://www.dropzone.dev/js/ -->
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>';
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
  <section class="csc-col csc-col12 csc-col--md6 cs-mb-md-3">
    <h1 class="cs-mb-0">File Upload</h1>
    <p class="cs-subtitle1"><strong>WARNING:</strong> If a file with the same name and extension already exists in this folder it will overwrite the current file.</p>
  </section>
  <section class="csc-col csc-col12 csc-col--md6 cs-text-center cs-text-right-md csc-col--ga-middle">
    <p class="cs-mt-0 cs-mt-md-3">
      <a href="<?= get_site_url('admin/files/?p=' . $data->current_path); ?>" data-tippy-content="Back to file manager"><i class="fas fa-arrow-circle-left"></i> Back</a>
    </p>
  </section>
</div>
<?php flashMsg('files_upload'); ?>
<section>
  <nav class="csc-tabs">
    <ol>
      <li><button class="csc-tab" title="Upload Files" data-ref="upload"><i class="far fa-arrow-alt-circle-up"></i> Upload Files</button></li>
      <li><button class="csc-tab" title="Upload from URL" data-ref="url"><i class="fas fa-link"></i> Upload from URL</button></li>
    </ol>
  </nav>
</section>
<section class="csc-container cs-p-3">
  <article id="tab__upload" class="csc-tab__content">
    <header>
      <p class="cs-body2">Destination Folder: <em><?= '/' . $data->destination_folder; ?></em></p>
    </header>
    <section>
      <form action="<?= get_site_url('admin/files/upload?p=' . $data->current_path); ?>" class="dropzone" id="fileUploader" enctype="multipart/form-data">
        <input type="hidden" name="p" value="<?= $data->current_path_enc; ?>">
        <input type="hidden" name="fullpath" id="fullpath" value="<?= $data->current_path_enc; ?>">
        <div class="fallback">
          <input name="file" type="file" multiple />
        </div>
      </form>
    </section>
  </article>
  <article id="tab__url" class="csc-tab__content">
    <header>
      <p class="cs-body2">Destination Folder: <em><?= '/' . $data->destination_folder; ?></em></p>
    </header>
    <section>
      <form action="" method="POST" id="file-upload-url-form" class="csc-form">
        <input type="hidden" name="path" value="<?= $data->current_path_enc; ?>">
        <fieldset>
          <div class="csc-input-field">
            <input type="text" name="uploadurl" id="uploadurl" tabindex="1" value="<?= !empty($data->uploadurl) ? $data->uploadurl : ''; ?>" data-lpignore="true" required>
            <label for="uploadurl">File URL*</label>
          </div>
        </fieldset>
        <div class="cs-text-right">
          <button type="submit" name="action" tabindex="2" value="upload-url" class="csc-btn csc-btn--success">Upload <i class="fas fa-upload csc-bi-right"></i></button>
        </div>
      </form>
    </section>
  </article>
</section>

<!-- Dropzone Config -->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    Dropzone.options.fileUploader = {
      timeout: 120000,
      maxFilesize: <?= $data->max_file_size; ?>,
      acceptedFiles: "<?= $data->accepted_files; ?>",
      init: function() {
        this.on("sending", function(file, xhr, formData) {
          let _path = (file.fullPath) ? file.fullPath : file.name;
          document.getElementById("fullpath").value = _path;
          xhr.ontimeout = (function() {
            toast('Error: Server Timeout');
          });
        }).on("success", function(res) {
          let _response = JSON.parse(res.xhr.response);
          if (_response.status == "error") {
            toast(_response.info);
          }
        }).on("error", function(file, response) {
          toast(response);
        });
      }
    }
  });
</script>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>