<?php

/**
 * The account settings template file
 *
 * @package Cornerstone
 * @subpackage Mission Equine
 */

// Set the meta/og information for the page
$pageMetaTitle = "Account Settings | " . $data->site_name;
$pageMetaDescription = "Edit account settings on the " . $data->site_name . " site";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('account/settings');
$pageMetaType = "website";

// Set any page injected values
$loadScripts = array(
  'validate',
  'chosen'
);
$pageBodyClassID = 'class="cs-page cs-components cs-account"';
$pageHeadExtras = '';
$pageFooterExtras = '<script>
  $("#title, #default_address_id, #default_payment_id").chosen({
    disable_search_threshold: 10,
    no_results_text: "Sorry, nothing was found matching",
    width: "100%",
    search_contains: "true",
    allow_single_deselect: true
  });
</script>';

// Load html head
require(get_theme_path('head.php'));
// Load html layout
require(get_theme_path('layout.php')); ?>

<!-- End Header ~#~ Start Content -->
<div id="cs-main">
  <nav class="csc-breadcrumbs" aria-label="Breadcrumb">
    <?php
    // Check for and output breadcrumbs
    if (!empty($data->breadcrumbs)) {
      // Output breadcrumbs
      echo outputBreadcrumbs((object) $data->breadcrumbs);
    } ?>
  </nav>
  <div class="csc-wrapper">
    <?php flashMsg('account_settings'); ?>
    <header id="account__header">
      <section>
        <h1>Account Settings</h1>
      </section>
      <section>
        <a class="csc-btn csc-btn--outlined csc-btn--danger" href="<?php echo get_site_url('account/logout'); ?>">Sign out <i class="fas fa-sign-out-alt csc-bi-right"></i></a>
      </section>
    </header>
    <main>
      <?php
      // Get account menu
      $pgMenu = 100;
      require_once(DIR_ROOT . _DS . 'account' . _DS . 'views' . _DS . 'common' . _DS . 'account-menu.php'); ?>
      <form action="<?php echo get_site_url('account/settings'); ?>" method="POST" id="account__settings" class="csc-form paper" autocomplete="off">
        <fieldset id="account__settings__details">
          <legend>Customer Details</legend>
          <div id="account__settings__user">
            <div class="csc-input-field">
              <input type="text" name="first_name" id="first_name" tabindex="2" <?php if (!empty($data->first_name)) echo ' value="' . $data->first_name . '"'; ?>>
              <label for="first_name">First Name*</label>
            </div>
            <div class="csc-input-field">
              <input type="text" name="last_name" id="last_name" tabindex="3" <?php if (!empty($data->last_name)) echo ' value="' . $data->last_name . '"'; ?>>
              <label for="last_name">Last Name*</label>
            </div>
            <div class="csc-input-field">
              <input type="text" name="display_name" id="display_name" tabindex="4" <?php if (!empty($data->display_name)) echo ' value="' . $data->display_name . '"'; ?>>
              <label for="display_name" data-tippy-content="Your display name as it will be shown to other users around the website.">Display Name* <i class="fas fa-question-circle"></i></label>
            </div>
          </div>
        </fieldset>
        <fieldset id="account__settings__details">
          <legend>Login Details</legend>
          <div class="csc-input-field">
            <input type="email" name="email" id="email" autocapitalize="off" <?php if (!empty($data->email)) echo ' value="' . $data->email . '"'; ?> required>
            <label for="email">Email*</label>
          </div>
          <p class="cs-body2 cs-pb-3"><strong>N.B. Leave the password field blank unless you want to change it.</strong></p>
          <div class="csc-input-field">
            <input type="password" name="password" id="password" autocomplete="off" data-lpignore="true">
            <label for="password">Password (optional)</label>
          </div>
          <div class="csc-input-field" id="account__settings__confirm-pwd">
            <input type="password" name="confirm_password" id="confirm_password" autocomplete="off" data-lpignore="true">
            <label for="confirm_password">Confirm Password*</label>
          </div>
        </fieldset>
        <div class="csc-form__actions">
          <div>
            <a href="<?php echo get_site_url('account'); ?>" class="csc-btn--flat"><span>Cancel</span></a>
          </div>
          <div>
            <button type="submit" name="action" tabindex="99" value="update" class="csc-btn csc-btn--success">Update Details <i class="fas fa-save csc-bi-right"></i></button>
          </div>
        </div>
      </form>
    </main>
  </div>
</div>
<!-- End Content ~#~ Start Footer -->

<script>
  // Get the password input and wrapper
  const passwordInput = document.querySelector('#password');
  const confirmPasswordWrapper = document.querySelector('#account__settings__confirm-pwd');

  // Password confirm function
  function passwordConfirm() {
    // Check if the password input has a value
    if (passwordInput.value.length > 0) {
      // Show the password confirm input
      confirmPasswordWrapper.style.display = 'block';
    } else {
      // Hide the password confirm input
      confirmPasswordWrapper.style.display = 'none';
    }
  }

  // Check if password input exists
  if (passwordInput) {
    // Check for change
    passwordInput.addEventListener('change', passwordConfirm);
    passwordInput.addEventListener('keyup', passwordConfirm);
  }
</script>
<script>
  // Init validation on document ready
  $(document).ready(function() {
    let validator = $("#account__settings").validate({
      rules: {
        first_name: {
          required: true,
          minlength: 3
        },
        last_name: {
          required: true,
          minlength: 3
        },
        display_name: {
          required: true,
          minlength: 3
        },
        email: {
          required: true,
          email: true
        },
        password: {
          minlength: 6,
          maxlength: 64,
          pattern: "((?=.*\\d)(?=.*[a-z])(?=.*[A-Z]).{6,128})"
        },
        confirm_password: {
          equalTo: "#password"
        }
      },
      messages: {
        first_name: {
          required: "Please enter your first name",
          minlength: "Please enter at least 3 characters"
        },
        last_name: {
          required: "Please enter your last name",
          minlength: "Please enter at least 3 characters"
        },
        display_name: {
          required: "Please enter your display name",
          minlength: "Please enter at least 3 characters"
        },
        email: {
          required: "Please enter your email address",
          email: "Please enter a valid email address"
        },
        password: {
          minlength: "Please enter at least 6 characters",
          maxlength: "Please enter no more than 128 characters",
          pattern: "Your password must be at least six characters long and contain at least one upper case letter and one number."
        },
        confirm_password: {
          equalTo: "Your passwords must match"
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
require(get_theme_path('footer.php')); ?>