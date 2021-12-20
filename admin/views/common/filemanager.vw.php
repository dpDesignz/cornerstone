<?php

/**
 * The Admin File Manager File
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Admin Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = "File Manager | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " file manager.";
$pageMetaImage = get_site_url('admin-files/img/site_logo.png');
$pageMetaCanonical = get_site_url('admin/files/');
$pageMetaType = "website";

// Set any page injected values
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
  <section class="csc-col csc-col12 csc-col--md6 cs-mb-md-3">
    <h1 class="cs-mb-0">File Manager</h1>
  </section>
  <section class="csc-col csc-col12 csc-col--md6 cs-text-center cs-text-right-md csc-col--ga-middle">
    <p class="cs-mt-0 cs-mt-md-3">
      <!-- <a class="csc-btn--small" href="<?php echo get_site_url('admin/users/add'); ?>" title="Add a user"><i class="material-icons csc-bi-left">add</i> Add</a> -->
    </p>
  </section>
</div>
<?php flashMsg('admin_filemanager'); ?>
<section class="csc-container">
  <form id=" file-manager-form" action="" method="post">
    <input type="hidden" name="p" value="<?= $data->input_path; ?>">
    <input type="hidden" name="group" value="1">
    <div class="csc-data-table">
      <section class="csc-data-table__table">
        <table id="file-manager-table">
          <thead class="csc-table-header">
            <tr>
              <th style="width:3%" class="custom-checkbox-header">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" id="js-select-all-items" onclick="checkbox_toggle()">
                  <label class="custom-control-label" for="js-select-all-items"></label>
                </div>
              </th>
              <th>Name</th>
              <th>Size</th>
              <th>Modified</th>
              <?php if ($data->opPermissionColumns) : ?>
                <th>Perms</th>
                <th>Owner</th>
              <?php endif; ?>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="csc-table-body csc-table-body--zebra">
            <?php
            // link to parent folder
            if ($data->parent !== false) {
            ?>
              <tr>
                <td class=" nosort">
                </td>
                <td class="border-0"><a href="?p=<?php echo urlencode($data->parent) ?>"><i class="fa fa-chevron-circle-left go-back"></i> ..</a></td>
                <td class="border-0"></td>
                <td class="border-0"></td>
                <td class="border-0"></td>
                <?php if ($data->opPermissionColumns) { ?>
                  <td class="border-0"></td>
                  <td class="border-0"></td>
                <?php } ?>
              </tr>
            <?php }
            // Output Folders
            echo $data->opFolders;
            // Output files
            echo $data->opFiles;

            // Check if folders/files are present
            if (empty($data->folders) && empty($data->files)) { ?>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td colspan="<?php echo ($data->opPermissionColumns) ? '6' : '4' ?>"><em><?= 'Folder is empty'; ?></em></td>
            </tr>
          </tfoot>
        <?php } else { ?>
          <tfoot>
            <tr>
              <td class="gray" colspan="<?php echo ($data->opPermissionColumns) ? '7' : '5' ?>">
                <?= $data->opFullSize . $data->opFileCount . $data->opFolderCount . $data->opPartitionSize; ?>
              </td>
            </tr>
          </tfoot>
        <?php } ?>
        </table>
      </section>
    </div>

    <section class="cs-p-3">
      <ul class="list-inline footer-action">
        <li class="list-inline-item"> <a href="#/select-all" class="csc-btn csc-btn--tiny csc-btn--outlined csc-btn--info" onclick="select_all();return false;"><i class="fas fa-check-square csc-bi-left"></i> Select all </a></li>
        <li class="list-inline-item"><a href="#/unselect-all" class="csc-btn csc-btn--tiny csc-btn--outlined csc-btn--info" onclick="unselect_all();return false;"><i class="fas fa-window-close csc-bi-left"></i> Unselect all </a></li>
        <li class="list-inline-item"><a href="#/invert-all" class="csc-btn csc-btn--tiny csc-btn--outlined csc-btn--info" onclick="invert_all();return false;"><i class="fas fa-th-list csc-bi-left"></i> Invert Selection </a></li>
        <li class="list-inline-item"><input type="submit" class="hidden" name="delete" id="a-delete" value="Delete" onclick="return confirm('Delete selected files and folders?')">
          <a href="javascript:document.getElementById('a-delete').click();" class="csc-btn csc-btn--tiny csc-btn--outlined csc-btn--info"><i class="far fa-trash-alt csc-bi-left"></i> Delete </a>
        </li>
        <li class="list-inline-item"><input type="submit" class="hidden" name="zip" id="a-zip" value="zip" onclick="return confirm('Create archive?')">
          <a href="javascript:document.getElementById('a-zip').click();" class="csc-btn csc-btn--tiny csc-btn--outlined csc-btn--info"><i class="far fa-file-archive csc-bi-left"></i> Zip </a>
        </li>
        <li class="list-inline-item"><input type="submit" class="hidden" name="tar" id="a-tar" value="tar" onclick="return confirm('Create archive?')">
          <a href="javascript:document.getElementById('a-tar').click();" class="csc-btn csc-btn--tiny csc-btn--outlined csc-btn--info"><i class="far fa-file-archive csc-bi-left"></i> Tar </a>
        </li>
        <li class="list-inline-item"><input type="submit" class="hidden" name="copy" id="a-copy" value="Copy">
          <a href="javascript:document.getElementById('a-copy').click();" class="csc-btn csc-btn--tiny csc-btn--outlined csc-btn--info"><i class="far fa-copy csc-bi-left"></i> Copy </a>
        </li>
      </ul>
    </section>

  </form>
</section>
<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>