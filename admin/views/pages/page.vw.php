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
  'chosen',
  'trumbowyg'
);
$pageHeadExtras = '';
$pageFooterExtras = '<script>
  $("#status, #section_id, #menu").chosen({
    disable_search_threshold: 10,
    no_results_text: "Oops, nothing found!",
    width: "100%",
    search_contains: "true",
    allow_single_deselect: true
  });
  </script>
  <!-- Load Trumbowyg plugins -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.20.0/plugins/cleanpaste/trumbowyg.cleanpaste.min.js" integrity="sha256-GGXtZ0tz4DfEMvShclGiegXJZt9r49+KqwWUvZ6+nlY=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.20.0/plugins/fontsize/trumbowyg.fontsize.min.js" integrity="sha256-zYP+CK+pN1VGM8OrJx3Z2DYZWDMZvfMFTXjVQRx2Gbw=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.20.0/plugins/colors/trumbowyg.colors.min.js" integrity="sha256-3DiuDRovUwLrD1TJs3VElRTLQvh3F4qMU58Gfpsmpog=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.20.0/plugins/history/trumbowyg.history.min.js" integrity="sha256-Y2sxDzGz3ssKKi6XjYh8A/6L3y4rtRpP7QMGyZrrwiA=" crossorigin="anonymous"></script>
  <script src="' . get_site_url('js/plugins/addid/trumbowyg.addid.js') . '"></script>';
$currentNav = 'cms';
$currentSubNav = $currentNav . '/pages';

// Load html head
require(get_theme_path('head.php', 'admin'));
// Load html layout
require(get_theme_path('layout.php', 'admin')); ?>

<?= (!empty($data->breadcrumbs)) ? outputBreadcrumbs((object) $data->breadcrumbs) : ''; // Output breadcrumbs
?>
<div class="csc-wrapper csc-row cs-my-2" style="max-width: 90%;">
  <div class="csc-col csc-col12">
    <h1 class="cs-h2 cs-my-2"><?php echo $data->page_title; ?></h1>
  </div>
</div>
<form action="<?php echo $data->action_url; ?>" method="POST" id="page-form" class="csc-form">
  <div class="csc-wrapper csc-row csc-container">
    <section class="csc-col csc-col12 csc-col--md9 cs-p-3">
      <?php flashMsg('pages_page'); ?>
      <?php
      // Output ID if set
      if (!empty($data->id)) { ?>
        <input type="hidden" name="id" value="<?php echo $data->id; ?>">
      <?php } ?>
      <fieldset>
        <legend>Page</legend>
        <div class="csc-row csc-row--no-pad">
          <div class="csc-col csc-col12 csc-input-field">
            <input type="text" name="title" id="title" tabindex="1" autocapitalize="on" <?php if (!empty($data->title)) echo ' value="' . $data->title . '"'; ?> required>
            <label for="title">Page Title*</label>
          </div>
        </div>
        <div class="csc-row csc-row--no-pad">
          <div class="csc-col csc-col12 csc-input-field">
            <textarea name="content" id="content" class="csc-textarea" tabindex="2"><?php if (!empty($data->content)) echo $data->content; ?></textarea>
            <label for="content">Page Content*</label>
          </div>
        </div>
      </fieldset>
      <fieldset>
        <legend>Meta Information <small>for Google search results</small></legend>
        <div class="csc-row csc-row--no-pad">
          <div class="csc-col csc-col12 csc-input-field">
            <input type="text" name="meta_title" id="meta_title" tabindex="3" autocapitalize="off" data-counter maxlength="60" <?php if (!empty($data->meta_title)) echo ' value="' . $data->meta_title . '"'; ?>>
            <label for="meta_title">Meta Title (optional)</label>
          </div>
        </div>
        <div class="csc-row csc-row--no-pad">
          <div class="csc-col csc-col12 csc-input-field">
            <textarea data-autoresize data-counter name="meta_description" id="meta_description" class="csc-textarea" rows="1" maxlength="160" tabindex="4"><?php if (!empty($data->meta_description)) echo $data->meta_description; ?></textarea>
            <label for="meta_description">Meta Description <span class="label-hide">(optional)</span></label>
          </div>
        </div>
      </fieldset>
      <div class="csc-row csc-row--no-gap cs-hide-md-up">
        <div class="csc-col csc-col12">
          <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
        </div>
      </div>
    </section>
    <section class="csc-col csc-col12 csc-col--md3 csc-form-details">
      <h4 class="cs-h4">Page Options</h4>
      <div class="csc-row csc-row--no-pad">
        <div class="csc-col csc-col12 csc-input-field">
          <select name="status" id="status" data-placeholder="Select a page status" tabindex="5" required>
            <?php echo $data->status_options; ?>
          </select>
          <label>Status*</label>
        </div>
      </div>
      <div class="csc-row csc-row--no-pad">
        <div class="csc-col csc-col12 csc-input-field">
          <select name="section_id" id="section_id" data-placeholder="Select a page section" tabindex="6">
            <?php echo $data->section_options; ?>
          </select>
          <label>Section (optional)</label>
        </div>
      </div>
      <div class="csc-row csc-row--no-pad">
        <div class="csc-col csc-col12 csc-input-field">
          <select name="menu[]" id="menu" multiple data-placeholder="Select a menu to assign the page to" tabindex="7">
            <?php echo $data->menu_options; ?>
          </select>
          <label>Menus (optional)</label>
        </div>
      </div>
      <div class="csc-row csc-row--no-pad">
        <div class="csc-col csc-col12 cs-text-center csc-input-field">
          <p class="cs-my-0">
            <label>
              <input type="checkbox" name="show_updated" id="show_updated" tabindex="8" <?php if (!empty($data->show_updated) && $data->show_updated) echo ' checked'; ?>>
              <span>Show "updated" on page <i class="far fa-question-circle" data-tippy-content="Tick this if you want to display a 'Page update...' message on the bottom of the page."></i></span>
            </label>
          </p>
        </div>
      </div>
      <?php
      // Output ID if set
      if (!empty($data->viewLink)) { ?>
        <p class="cs-body2 cs-pl-2">View page: <a href="<?php echo $data->viewLink; ?>" target="_blank">/<?php echo str_replace(get_site_url(), '', $data->viewLink); ?> <i class="fas fa-link"></i></a></p>
      <?php } ?>
      <div class="csc-row csc-row--no-gap">
        <div class="csc-col csc-col12 csc-col--md6 cs-my-1 cs-mb-3"><a href="<?php echo get_site_url('admin/pages/'); ?>" class="csc-btn--flat"><span>Cancel</span></a></div>
        <div class="csc-col csc-col12 cs-text-right csc-col--md6 cs-my-1 cs-mb-3"><button type="submit" name="action" tabindex="8" value="save" class="csc-btn csc-btn--success">Save <i class="fas fa-save csc-bi-right"></i></button></div>
      </div>
      <hr>
      <h4 class="cs-h4">Form Details</h4>
      <p class="cs-body1"><?php echo $data->instructions; ?></p>
      <h5 class="cs-h5">Form Fields</h5>
      <p class="cs-body2"><strong>Page Title: </strong> The title of the page.</p>
      <p class="cs-body2"><strong>Page Content: </strong> The HTML/Formatted content of the page.</p>
      <p class="cs-body2"><strong>Meta Title: </strong> <em>(optional)</em> Over-write the default content that's shown in the page title and also on search engines.</p>
      <p class="cs-body2"><strong>Meta Description: </strong> <em>(optional)</em> Over-write the default content that's shown in the description on search engines.</p>
      <p class="cs-body2"><strong>Status: </strong> The status of the page.</p>
      <p class="cs-body2"><strong>Section: </strong> <em>(optional)</em> The section the page belongs to.</p>
      <p class="cs-body2"><strong>Menus: </strong> <em>(optional)</em> The menu(s) the page is assigned to.</p>
      <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
    </section>
  </div>
</form>

<script>
  $(document).ready(function() {
    // Auto Load Trumbowyg Editor ~ https://alex-d.github.io/Trumbowyg/documentation/
    if ($.trumbowyg !== undefined) {
      $('#content').trumbowyg({
        btns: [
          ['formatting'],
          ['fontsize'],
          ['strong', 'em', 'del'],
          ['foreColor', 'backColor'],
          ['superscript', 'subscript'],
          ['link', 'addID'],
          ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
          ['unorderedList', 'orderedList'],
          ['horizontalRule'],
          ['removeformat'],
          ['historyUndo', 'historyRedo'],
          ['viewHTML'],
          ['fullscreen']
        ],
        autogrow: true,
        resetCss: true,
      });
    }
  });
</script>
<script>
  // Init validation on document ready
  $(document).ready(function() {
    let validator = $("#page-form").validate({
      rules: {
        title: {
          required: true,
          minlength: 3
        },
        meta_title: {
          maxlength: 70
        },
        meta_description: {
          maxlength: 168
        }
      },
      messages: {
        title: {
          required: "Please enter a page name",
          minlength: "Please enter at least 3 characters"
        },
        meta_title: {
          maxlength: "This is limited to 70 characters"
        },
        meta_description: {
          maxlength: "This is limited to 168 characters"
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