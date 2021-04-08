<?php

/**
 * User Add/Edit File
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Admin
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
$pageHeadExtras = '
<style>
  #submit-disabled {display: none;}
</style>';
$pageFooterExtras = '<script>
  $("#role_id, #status").chosen({
    disable_search_threshold: 5,
    no_results_text: "Oops, nothing found!",
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

<div class="csc-row csc-row--no-pad cs-mt-3">
  <section class="csc-col csc-col12">
    <nav class="csc-breadcrumbs" aria-label="Breadcrumb">
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
    <?php flashMsg('users_user'); ?>
    <form action="<?php echo $data->action_url; ?>" method="POST" id="user-form" class="csc-form cs-p-3">
      <?php
      // Output ID if set
      if (!empty($data->id)) { ?>
        <input type="hidden" name="id" value="<?php echo $data->id; ?>">
      <?php } ?>
      <fieldset>
        <legend>User</legend>
        <div class=" csc-row csc-row--no-pad csc-row--no-gap">
          <div class="csc-col csc-col12 csc-col--md6 csc-input-field">
            <input type="text" name="login" id="login" tabindex="1" autocapitalize="on" value="<?php if (!empty($data->login)) echo $data->login; ?>" data-lpignore="true" required>
            <label for="login">Login (username)*</label>
          </div>
          <div class="csc-col csc-col12 csc-col--md6 csc-input-field">
            <input type="text" name="display_name" id="display_name" tabindex="2" autocapitalize="on" value="<?php if (!empty($data->display_name)) echo $data->display_name; ?>" data-lpignore="true" required>
            <label for="display_name">Display Name*</label>
          </div>
        </div>
        <div class="csc-row csc-row--no-pad csc-row--no-gap">
          <div class="csc-col csc-col12 csc-col--md6 csc-input-field">
            <input type="text" name="first_name" id="first_name" tabindex="3" autocapitalize="on" value="<?php if (!empty($data->first_name)) echo $data->first_name; ?>" data-lpignore="true" required>
            <label for="first_name">First Name*</label>
          </div>
          <div class="csc-col csc-col12 csc-col--md6 csc-input-field">
            <input type="text" name="last_name" id="last_name" tabindex="4" autocapitalize="on" value="<?php if (!empty($data->last_name)) echo $data->last_name; ?>" data-lpignore="true" required>
            <label for="last_name">Last Name*</label>
          </div>
        </div>
        <div class="csc-row csc-row--no-pad">
          <div class="csc-col csc-col12 csc-input-field">
            <input type="email" name="email" id="email" tabindex="5" autocapitalize="off" value="<?php if (!empty($data->email)) echo $data->email; ?>" data-lpignore="true" required>
            <label for="email">Email Address*</label>
          </div>
        </div>
      </fieldset>
      <fieldset>
        <legend>Account Details</legend>
        <div class="csc-row csc-row--no-pad">
          <div class="csc-col csc-col12 csc-input-field">
            <select name="role_id" id="role_id" data-placeholder="Choose the assigned role" tabindex="6" data-lpignore="true" required>
              <?php echo $data->role_options; ?>
            </select>
            <label>Assigned Role*</label>
          </div>
        </div>
        <div class="csc-row csc-row--no-pad">
          <div class="csc-col csc-col12 csc-input-field cs-my-0">
            <p class="cs-text-center">
              <label>
                <input type="checkbox" name="auth_rqd" id="auth_rqd" <?php if (!empty($data->auth_rqd) && $data->auth_rqd) echo ' checked'; ?> tabindex="7"><span>Auth required <em>(optional)</em></span>
              </label>
              <i class="far fa-question-circle" data-tippy-content="Ticking this will require the user to use 2 factor authentication via email to log in"></i>
            </p>
          </div>
        </div>
        <?php
        // Only show status if editing
        if ($data->page_type == "edit") {
        ?>
          <div class="csc-row csc-row--no-pad">
            <div class="csc-col csc-col12 csc-input-field cs-text-center">
              <p class="cs-body1">Account status</p>
              <div class="csc-switch">
                <label>
                  Inactive
                  <input type="checkbox" name="status" tabindex="8" <?php if (!empty($data->status) && $data->status) echo 'checked'; ?>>
                  <span class="csc-lever"></span>
                  Active
                </label>
              </div>
            </div>
          </div>
        <?php } ?>
      </fieldset>
      <div class="csc-row csc-row--no-gap">
        <div class="csc-col csc-col12 csc-col--md6 cs-my-1 cs-mb-3"><a href="<?php echo $data->cancel_btn; ?>" class="csc-btn--flat"><span>Cancel</span></a></div>
        <div class="csc-col csc-col12 cs-text-right csc-col--md6 cs-my-1 cs-mb-3">
          <button type="submit" name="action" tabindex="10" value="save" class="csc-btn csc-btn--success">Save <i class="fas fa-save csc-bi-right"></i></button>
        </div>
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
    <p class="cs-body1 cs-mb-0"><strong>Customer</strong></p>
    <p class="cs-body2"><strong>Login (username)*: </strong> The username used for logging in to the admin end of the website.</p>
    <p class="cs-body2"><strong>Display Name*: </strong> The display name shown on the site when the user does something. This is generally just the first name of the user.</p>
    <p class="cs-body2"><strong>First Name*: </strong> The first name of the user.</p>
    <p class="cs-body2"><strong>Last Name*: </strong> The last name of the user.</p>
    <p class="cs-body2"><strong>Email Address*: </strong> The email address of the user. This can also be used when logging in to the admin end of the website.</p>
    <p class="cs-body2"><strong>Assigned Role*: </strong> The assigned role of the user.</p>
    <p class="cs-body2"><strong>Auth Required: </strong> <em>(optional)</em> Set if the user requires 2 factor authentication to log in.</p>
    <?php
    // Only show status if editing
    if ($data->page_type == "edit") {
    ?>
      <p class="cs-body2"><strong>Status*: </strong> The status of the user.</p>
    <?php } ?>
    <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
  </section>
</div>

<script>
  // On document ready
  $(document).ready(function() {
    // Init validation
    let validator = $("#user-form").validate({
      rules: {
        login: {
          required: true,
          minlength: 3
        },
        display_name: {
          required: true,
          minlength: 2
        },
        first_name: {
          required: true,
          minlength: 2
        },
        last_name: {
          required: true,
          minlength: 2
        },
        email: {
          required: true,
          email: true
        }
      },
      messages: {
        login: {
          required: "Please enter a login (username) value",
          minlength: "Please enter at least 3 characters"
        },
        display_name: {
          required: "Please enter a display name",
          minlength: "Please enter at least 2 characters"
        },
        first_name: {
          required: "Please enter a first name",
          minlength: "Please enter at least 2 characters"
        },
        last_name: {
          required: "Please enter a last name",
          minlength: "Please enter at least 2 characters"
        },
        email: {
          email: "Please enter a valid email address"
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