<?php

/**
 * The Admin File Copy File
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Admin Theme
 */ ?>

<div class="modal csc-modal--flowable" style="display: block; width: 100%;">
  <form action="<?= get_site_url('admin/files/copy'); ?>" method="POST" id="file-copy-form" class="csc-form">
    <div class="csc-modal__header">
      <i class="fas fa-copy"></i> Copying "<em><?= $data->item; ?></em>"
    </div>
    <div class="csc-modal__content">
      <?php flashMsg('filemanager_copy'); ?>
      <input type="hidden" name="path" value="<?= $data->path; ?>">
      <input type="hidden" name="item" value="<?= $data->item; ?>">
      <p class="cs-body2 cs-pb-4"><strong>Source path:</strong> <em><?= $data->path; ?></em></p>
      <fieldset>
        <div class="csc-input-field">
          <select name="destination" id="destination" data-placeholder="Select a destination folder" tabindex="1" required>
            <?php echo $data->folder_options; ?>
          </select>
          <label>Destination Folder</label>
        </div>
      </fieldset>
    </div>
    <div class="csc-modal__actions">
      <a href="#" class="csc-btn--flat" rel="modal:close"><span>Cancel</span></a>
      <button type="submit" name="action" tabindex="2" value="copy" class="csc-btn csc-btn--success">Copy <i class="fas fa-copy csc-bi-right"></i></button>
      <button type="submit" name="action" tabindex="3" value="move" class="csc-btn csc-btn--success">Move <i class="fas fa-cut csc-bi-right"></i></button>
    </div>
  </form>

  <script>
    // Add input change listeners
    addLabelListeners(document.querySelector('#brand_name'));

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
</div>