<?php

/**
 * The Admin File Permissions File
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Admin Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = "Change {$data->item_type} Permissions | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " change {$data->item_type} permissions.";
$pageMetaImage = get_site_url('admin-files/img/site_logo.png');
$pageMetaCanonical = get_site_url('admin/files/chmod/');
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
    <h1 class="cs-mb-0">Change <?= $data->item_type; ?> Permissions</h1>
    <p class="cs-subtitle1"><?= $data->item_type; ?> path: <em><?= '/' . $data->file_path; ?></em></p>
  </section>
  <section class="csc-col csc-col12 csc-col--md6 cs-text-center cs-text-right-md csc-col--ga-middle">
    <p class="cs-mt-0 cs-mt-md-3">
      <a href="<?= get_site_url('admin/files/?p=' . $data->current_path); ?>" data-tippy-content="Back to file manager"><i class="fas fa-arrow-circle-left"></i> Back</a>
    </p>
  </section>
</div>
<?php flashMsg('files_chmod'); ?>
<section class="csc-container cs-p-3">
  <form action="" method="POST" id="file-chmod-form" class="csc-form">
    <input type="hidden" name="p" value="<?= $data->current_path_enc; ?>">
    <input type="hidden" name="item" value="<?= $data->file_enc; ?>">

    <table>
      <tr>
        <td></td>
        <td><strong>Owner</strong></td>
        <td><strong>Group</strong></td>
        <td><strong>Other</strong></td>
      </tr>
      <tr>
        <td><strong>Read</strong></td>
        <td><label><input type="checkbox" name="ur" value="1" <?php echo ($data->file_mode & 00400) ? ' checked' : '' ?>><span></span></label></td>
        <td><label><input type="checkbox" name="gr" value="1" <?php echo ($data->file_mode & 00040) ? ' checked' : '' ?>><span></span></label></td>
        <td><label><input type="checkbox" name="or" value="1" <?php echo ($data->file_mode & 00004) ? ' checked' : '' ?>><span></span></label></td>
      </tr>
      <tr>
        <td><strong>Write</strong></td>
        <td><label><input type="checkbox" name="uw" value="1" <?php echo ($data->file_mode & 00200) ? ' checked' : '' ?>><span></span></label></td>
        <td><label><input type="checkbox" name="gw" value="1" <?php echo ($data->file_mode & 00020) ? ' checked' : '' ?>><span></span></label></td>
        <td><label><input type="checkbox" name="ow" value="1" <?php echo ($data->file_mode & 00002) ? ' checked' : '' ?>><span></span></label></td>
      </tr>
      <tr>
        <td><strong>Execute</strong></td>
        <td><label><input type="checkbox" name="ux" value="1" <?php echo ($data->file_mode & 00100) ? ' checked' : '' ?>><span></span></label></td>
        <td><label><input type="checkbox" name="gx" value="1" <?php echo ($data->file_mode & 00010) ? ' checked' : '' ?>><span></span></label></td>
        <td><label><input type="checkbox" name="ox" value="1" <?php echo ($data->file_mode & 00001) ? ' checked' : '' ?>><span></span></label></td>
      </tr>
    </table>

    <div class="csc-row csc-row--no-gap">
      <div class="csc-col csc-col12 csc-col--md6 cs-my-1 cs-mb-3"><a href="<?= get_site_url('admin/files/?p=' . $data->current_path); ?>" class="csc-btn--flat"><span>Cancel</span></a></div>
      <div class="csc-col csc-col12 cs-text-right csc-col--md6 cs-my-1 cs-mb-3">
        <button type="submit" name="action" tabindex="10" value="change" class="csc-btn csc-btn--success">Change <i class="fas fa-save csc-bi-right"></i></button>
      </div>
    </div>
  </form>
</section>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>