<?php

/**
 * User Permissions File
 *
 * @package Cornerstone
 */

// Set the meta/og information for the page
$pageMetaTitle = $data->page_title . " | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " " . $data->page_title;
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = $data->action_url;
$pageMetaType = "website";

// Set any page injected values
$loadScripts = array(
  'chosen'
);
$pageHeadExtras = '';
$pageFooterExtras = '
<script>
  // bind chosen
  $("#allowed, #disallowed").chosen({
    disable_search_threshold: 3,
    no_results_text: "Nothing found matching",
    width: "100%",
    search_contains: "true"
  });
</script>';
$currentNav = 'users';
$currentSubNav = $currentNav . '/users';

// Load html head
require(get_theme_path('head.php', 'admin'));
// Load html layout
require(get_theme_path('layout.php', 'admin')); ?>

<?= (!empty($data->breadcrumbs)) ? outputBreadcrumbs((object) $data->breadcrumbs) : ''; // Output breadcrumbs
?>
<div class="csc-wrapper cs-my-2">
  <h1 class="cs-h2 cs-my-2"><?= $data->page_title; ?></h1>
</div>
<div class="csc-wrapper csc-row csc-container">
  <section class="csc-col csc-col12 csc-col--md8">
    <?php flashMsg('users_permissions'); ?>
    <form action="<?= $data->action_url; ?>" method="POST" id="user-perms-form" class="csc-form cs-p-3">
      <?php
      // Output ID if set
      if (!empty($data->id)) { ?>
        <input type="hidden" name="id" value="<?= $data->id; ?>">
      <?php } ?>
      <fieldset>
        <legend>User</legend>
      </fieldset>
      <p class="cs-body1 cs-mt-0"><?= $data->display_name . " <em>({$data->login})</em>"; ?></p>
      <fieldset>
        <legend>Permissions</legend>
        <div class="csc-row csc-row--no-pad">
          <?php if (!empty($data->no_perm_options)) { ?>
            <div class="csc-col csc-col12">
              <?php echo $data->no_perm_options; ?>
            </div>
          <?php } else { ?>
            <div class="csc-col csc-col12 csc-input-field">
              <select name="allowed[]" id="allowed" tabindex="1" data-placeholder="Select allowed permissions" multiple="true">
                <?php echo $data->permissionsAllowedList; ?>
              </select>
              <label>Allowed</label>
            </div>
            <div class="csc-col csc-col12 csc-input-field">
              <select name="disallowed[]" id="disallowed" tabindex="2" data-placeholder="Select disallowed permissions" multiple="true">
                <?php echo $data->permissionsDisallowedList; ?>
              </select>
              <label>Disallowed</label>
            </div>
          <?php } ?>
        </div>
        <?php if (empty($data->no_perm_options)) { ?>
          <h3>Default Role Permissions</h3>
          <div class="csc-row">
            <div class="csc-col csc-col12 csc-col--md6 csc-col--lg4 csc-input-field">
              <h4>View Options</h4>
              <?php echo $data->viewOptions; ?>
            </div>
            <div class="csc-col csc-col12 csc-col--md6 csc-col--lg4 csc-input-field">
              <h4>Add Options</h4>
              <?php echo $data->addOptions; ?>
            </div>
            <div class="csc-col csc-col12 csc-col--md6 csc-col--lg4 csc-input-field">
              <h4>Edit Options</h4>
              <?php echo $data->editOptions; ?>
            </div>
            <div class="csc-col csc-col12 csc-col--md6 csc-col--lg4 csc-input-field">
              <h4>Archive/Delete Options</h4>
              <?php echo $data->deleteOptions; ?>
            </div>
            <div class="csc-col csc-col12 csc-col--md6 csc-col--lg4 csc-input-field">
              <h4>Other Options</h4>
              <?php echo $data->otherOptions; ?>
            </div>
          </div>
        <?php } ?>
      </fieldset>
      <div class="csc-row csc-row--no-gap">
        <div class="csc-col csc-col12 csc-col--md6 cs-my-1 cs-mb-3"><a href="<?= get_site_url('admin/users/'); ?>" class="csc-btn--flat"><span>Cancel</span></a></div>
        <div class="csc-col csc-col12 cs-text-right csc-col--md6 cs-my-1 cs-mb-3"><button type="submit" name="action" tabindex="5" value="save" class="csc-btn csc-btn--success">Save <i class="fas fa-save csc-bi-right"></i></button></div>
      </div>
      <div class="csc-row csc-row--no-gap cs-hide-md-up">
        <div class="csc-col csc-col12">
          <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
        </div>
      </div>
    </form>
  </section>
  <section class="csc-col csc-col12 csc-col--md4 cs-hide-md-down csc-form-details">
    <h4 class="cs-h4">Form Details</h4>
    <p class="cs-body1"><?= $data->instructions; ?></p>
    <h5 class="cs-h5">Form Fields</h5>
    <p class="cs-body2"><strong>Allowed:</strong> (optional) The permissions this user role is allowed over their assigned role.</p>
    <p class="cs-body2"><strong>Disallowed:</strong> (optional) The permissions this user role is not allowed over their assigned role.</p>
    <p class="cs-body2"><strong>N.B. If a permission is in both selections, the disallow will override the allow.</strong></p>
    <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
  </section>
</div>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>