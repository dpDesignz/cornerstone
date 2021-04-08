<?php

/**
 * User Role Permission Add File
 *
 * @package Cornerstone
 */

// Set the meta/og information for the page
$pageMetaTitle = "Add Permission | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " admin add permission.";
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = $data->action_url;
$pageMetaType = "website";

// Set any page injected values
$loadScripts = array(
  'validate',
  'chosen'
);
$pageHeadExtras = '';
$pageFooterExtras = '<script>
  $("#roles").chosen({
    no_results_text: "Oops, no roles matched",
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
  <div class="csc-col csc-col12">
    <h1 class="cs-h2 cs-my-2"><?php echo $data->page_title; ?></h1>
  </div>
</div>
<div class="csc-wrapper csc-row csc-container">
  <section class="csc-col csc-col12 csc-col--md8">
    <?php flashMsg('roles_permission'); ?>
    <form action="<?php echo $data->action_url; ?>" method="POST" id="permision-form" class="csc-form cs-p-3 me-form">
      <fieldset>
        <legend>Details</legend>
        <div class="csc-row">
          <div class="csc-col csc-col12 csc-input-field">
            <input type="text" name="key" id="key" tabindex="1" autocapitalize="off" <?php if (!empty($data->key)) echo ' value="' . $data->key . '"'; ?> required>
            <label for="key">Key*</label>
          </div>
        </div>
        <div class="csc-row">
          <div class="csc-col csc-col12 csc-input-field">
            <select name="roles[]" id="roles" data-placeholder="Select the roles to assign to" tabindex="2" multiple required>
              <?php echo $data->role_options; ?>
            </select>
            <label>Roles*</label>
          </div>
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
    <p class="cs-body2"><strong>Key*:</strong> The key of the user role permission.</p>
    <p class="cs-body2"><strong>Roles*:</strong> The roles to assign this permission to.</p>
    <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
  </section>
</div>


<script>
  // Init validation on document ready
  $(document).ready(function() {
    let validator = $("#permision-form").validate({
      rules: {
        key: {
          required: true,
          minlength: 3
        }
      },
      messages: {
        key: {
          required: "Please enter a permission key",
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