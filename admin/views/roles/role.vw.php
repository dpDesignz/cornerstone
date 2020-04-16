<?php

/**
 * User Role Add/Edit File
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
$pageHasForm = TRUE;
$pageHeadExtras = '';
$pageFooterExtras = '<script>
  $("#permissions").chosen({
    no_results_text: "Oops, no permissions matched",
    width: "100%",
    search_contains: "true"
  });
  </script>';
$currentNav = 'users';
$currentSubNav = $currentNav . '/roles';

// Load html head
require(get_theme_path('head.php', 'admin'));
// Load html layout
require(get_theme_path('layout.php', 'admin')); ?>

<div class="csc-row csc-row--no-pad cs-mt-3">
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
<div class="csc-wrapper csc-row cs-my-2">
  <section class="csc-col csc-col12 csc-col--md6">
    <h1 class="cs-h2 cs-my-2"><?php echo $data->page_title; ?></h1>
  </section>
  <section class="csc-col csc-col12 csc-col--md6 cs-text-center cs-text-right-md csc-col--ga-middle">
    <?php if (!empty($data->id) && $role->isMasterUser()) { ?>
      <p class="cs-mt-0 cs-mt-md-3">
        <a class="csc-btn--small" href="<?php echo get_site_url('admin/roles/allpermissions/' . $data->id); ?>" title="Assign all permissions"><i class="fas fa-sync csc-bi-left"></i> Assign All</a>
      </p>
    <?php } ?>
  </section>
</div>
<div class="csc-wrapper csc-row csc-container">
  <section class="csc-col csc-col12 csc-col--md8">
    <?php flashMsg('roles_role'); ?>
    <form action="<?php echo $data->action_url; ?>" method="POST" id="role-form" class="csc-form cs-p-3 me-form">
      <?php
      // Output ID if set
      if (!empty($data->id)) { ?>
        <input type="hidden" name="id" value="<?php echo $data->id; ?>">
      <?php } ?>
      <fieldset>
        <legend>Details</legend>
        <div class="csc-row">
          <div class="csc-col csc-col12 csc-input-field">
            <input type="text" name="name" id="name" tabindex="1" autocapitalize="on" <?php if (!empty($data->name)) echo ' value="' . $data->name . '"'; ?> required>
            <label for="name">Name*</label>
          </div>
        </div>
        <div class="csc-row">
          <div class="csc-col csc-col12 csc-input-field">
            <input type="text" name="key" id="key" tabindex="2" autocapitalize="off" <?php if (!empty($data->key)) echo ' value="' . $data->key . '"'; ?> required>
            <label for="key">Key*</label>
          </div>
        </div>
        <div class="csc-row">
          <?php if (!empty($data->no_perm_options)) { ?>
            <div class="csc-col csc-col12">
              <?php echo $data->no_perm_options; ?>
            </div>
          <?php } else { ?>
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
          <?php } ?>
        </div>
      </fieldset>
      <div class="csc-row csc-row--no-gap">
        <div class="csc-col csc-col12 csc-col--md6 cs-my-1 cs-mb-3"><a href="<?php echo get_site_url('admin/roles'); ?>" class="csc-btn--flat"><span>Cancel</span></a></div>
        <div class="csc-col csc-col12 cs-text-right csc-col--md6 cs-my-1 cs-mb-3"><button type="submit" name="action" tabindex="4" value="save" class="csc-btn csc-btn--success">Save <i class="fas fa-save csc-bi-right"></i></button></div>
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
    <p class="cs-body1"><?php echo $data->instructions; ?></p>
    <h5 class="cs-h5">Form Fields</h5>
    <p class="cs-body2"><strong>Name*:</strong> The name of the user role.</p>
    <p class="cs-body2"><strong>Key*:</strong> The key of the user role. This is used by your developer for identifying a role in the code.</p>
    <p class="cs-body2"><strong>Permissions*:</strong> The permissions this user role is allowed.</p>
    <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
  </section>
</div>


<script>
  // Init validation on document ready
  $(document).ready(function() {
    let validator = $("#role-form").validate({
      rules: {
        name: {
          required: true,
          minlength: 3
        },
        key: {
          required: true,
          minlength: 3
        }
      },
      messages: {
        name: {
          required: "Please enter a user role name",
          minlength: "Please enter at least 3 characters"
        },
        key: {
          required: "Please enter a user role key",
          minlength: "Please enter at least 3 characters"
        }
      }
    });
    <?php
    // Output errors if they exist
    if (!empty($data->err)) {
      // Call the formatting function
      echo 'validator' . showValidationErrors($data->err);
    } ?>
  });
</script>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>