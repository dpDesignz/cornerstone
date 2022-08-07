<?php

/**
 * Sections Add/Edit File
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
  'validate',
  'chosen'
);
$pageHeadExtras = '';
$pageFooterExtras = '<script>
  $("#type, #location_name").chosen({
    width: "100%",
    disable_search: true,
    allow_single_deselect: true
  });
  </script>';
$currentNav = 'cms';
$currentSubNav = $currentNav . '/sections';

// Load html head
require(get_theme_path('head.php', 'admin'));
// Load html layout
require(get_theme_path('layout.php', 'admin')); ?>

<?= (!empty($data->breadcrumbs)) ? outputBreadcrumbs((object) $data->breadcrumbs) : ''; // Output breadcrumbs
?>
<div class="csc-wrapper csc-row cs-my-2">
  <div class="csc-col csc-col12">
    <h1 class="cs-h2 cs-my-2"><?php echo $data->page_title; ?></h1>
  </div>
</div>
<div class="csc-wrapper csc-row csc-container">
  <section class="csc-col csc-col12 csc-col--md8">
    <?php flashMsg('sections_section'); ?>
    <form action="<?php echo $data->action_url; ?>" method="POST" id="section-form" class="csc-form cs-p-3 me-form">
      <?php
      // Output ID if set
      if (!empty($data->id)) { ?>
        <input type="hidden" name="id" value="<?php echo $data->id; ?>">
      <?php } ?>
      <fieldset>
        <legend>Details</legend>
        <div class="csc-row csc-row--no-pad csc-row--no-gap">
          <div class="csc-col csc-col12 csc-input-field">
            <input type="text" name="name" id="name" tabindex="1" autocapitalize="off" <?php if (!empty($data->name)) echo ' value="' . $data->name . '"'; ?> required>
            <label for="name">Section Name*</label>
          </div>
        </div>
        <div class="csc-row csc-row--no-pad">
          <div class="csc-col csc-col12 csc-input-field">
            <select name="type" id="type" data-placeholder="Select a section type" tabindex="2" required>
              <?php echo $data->type_options; ?>
            </select>
            <label>Type*</label>
          </div>
        </div>
        <div id="location-input" class="csc-row csc-row--no-pad">
          <div class="csc-col csc-col12 csc-input-field">
            <select name="location_name" id="location_name" data-placeholder="Select a section location" tabindex="3">
              <?php echo $data->location_options; ?>
            </select>
            <label>Location</label>
          </div>
        </div>
      </fieldset>
      <div class="csc-row csc-row--no-gap">
        <div class="csc-col csc-col12 csc-col--md6 cs-my-1 cs-mb-3"><a href="<?php echo get_site_url('admin/sections/'); ?>" class="csc-btn--flat"><span>Cancel</span></a></div>
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
    <p class="cs-body2"><strong>Section Name: </strong> The name of the section.</p>
    <p class="cs-body2"><strong>Type: </strong> The type of the section.</p>
    <p id="location-definition" class="cs-body2"><strong>Location: </strong> <em>(optional)</em> The location of the section. Leave blank to assign to the main site.</p>
    <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
  </section>
</div>

<script>
  // Hide "location" if type isn't "page"
  function toggleLocation() {
    // Get location type
    const type = document.querySelector('#type');

    // Check if type is page
    if (type) {
      // Toggle location visibility
      document.querySelector('#location-input').style.display = (parseInt(type.value) !== 0) ? 'none' : 'grid';
      document.querySelector('#location-definition').style.display = (parseInt(type.value) !== 0) ? 'none' : 'block';
    }
  }

  // Run toggle location on page load or change
  document.addEventListener("DOMContentLoaded", toggleLocation);
  $('#type').on('change', toggleLocation);
</script>
<script>
  // Init validation on document ready
  $(document).ready(function() {
    let validator = $("#section-form").validate({
      rules: {
        name: {
          required: true,
          minlength: 3
        }
      },
      messages: {
        name: {
          required: "Please enter a section name",
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