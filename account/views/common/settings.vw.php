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
$pageHasForm = TRUE;
$pageBodyClassID = 'class="cs-grid cs-components me-account"';
$pageHeadExtras = '<!-- Chosen ~ https://harvesthq.github.io/chosen/ -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">';
$pageFooterExtras = '<!-- Chosen ~ https://harvesthq.github.io/chosen/ -->
<script src="//cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js" integrity="sha256-c4gVE6fn+JRKMRvqjoDp+tlG4laudNYrXI1GncbfAYY=" crossorigin="anonymous"></script>
<script>
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
<div id="content">
  <nav class="csc-breadcrumbs" aria-label="Breadcrumb">
    <?php
    // Check for and output breadcrumbs
    if (!empty($data->breadcrumbs)) {
      // Output breadcrumbs
      echo outputBreadcrumbs((object) $data->breadcrumbs);
    } ?>
  </nav>
  <div class="wrapper">
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
      $pgMenu = 10;
      require_once(DIR_ROOT . _DS . 'account' . _DS . 'views' . _DS . 'common' . _DS . 'account-menu.php'); ?>
      <form action="<?php echo get_site_url('account/settings'); ?>" method="POST" id="account__settings" class="csc-form paper" autocomplete="off">
        <fieldset id="account__settings__details">
          <legend>Customer Details</legend>
          <div id="account__settings__customer">
            <div class="csc-input-field">
              <select name="title" id="title" data-placeholder="Select a title" tabindex="1" required>
                <?php echo $data->title_options; ?>
              </select>
              <label>Title*</label>
            </div>
            <div class="csc-input-field">
              <input type="text" name="firstname" id="firstname" tabindex="2" <?php if (!empty($data->firstname)) echo ' value="' . $data->firstname . '"'; ?>>
              <label for="firstname">First Name*</label>
            </div>
            <div class="csc-input-field">
              <input type="text" name="lastname" id="lastname" tabindex="3" <?php if (!empty($data->lastname)) echo ' value="' . $data->lastname . '"'; ?>>
              <label for="lastname">Last Name*</label>
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
        <fieldset id="account__settings__details">
          <legend>Default Details</legend>
          <div class="csc-input-field">
            <select name="default_address_id" id="default_address_id" data-placeholder="Select a default address" tabindex="20">
              <?php echo $data->address_options; ?>
            </select>
            <label>Default Delivery Address (optional)</label>
          </div>
          <div class="csc-input-field">
            <select name="default_payment_id" id="default_payment_id" data-placeholder="Select a default payment card" tabindex="21">
              <?php echo $data->card_options; ?>
            </select>
            <label>Default Payment Card (optional)</label>
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
        firstname: {
          required: true,
          minlength: 3
        },
        lastname: {
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
        firstname: {
          required: "Please enter your first name",
          minlength: "Please enter at least 3 characters"
        },
        lastname: {
          required: "Please enter your last name",
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