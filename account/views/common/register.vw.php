<?php

/**
 * The register template file
 *
 * @package Cornerstone
 * @subpackage Mission Equine
 */

// Set the meta/og information for the page
$pageMetaTitle = "Create a new account | " . $data->site_name;
$pageMetaDescription = "Create a new " . $data->site_name . " account to access your order history, wishlist, and account information.";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('account/register');
$pageMetaType = "website";

// Set any page injected values
$loadScripts = array(
  'validate'
);
$pageBodyClassID = 'class="cs-page cs-components cs-account"';
$pageHeadExtras = '';
$pageFooterExtras = '';
if (!empty($data->recaptcha_site_key)) {
  $pageFooterExtras .= '<script src="https://www.google.com/recaptcha/api.js?render=' . $data->recaptcha_site_key . '"></script>';
}

// Load html head
require(get_theme_path('head.php'));
// Load html layout
require(get_theme_path('layout.php')); ?>

<!-- End Header ~#~ Start Content -->
<style>
  aside h3 {
    margin-bottom: 0;
  }
</style>
<div id="cs-main">
  <?= (!empty($data->breadcrumbs)) ? outputBreadcrumbs((object) $data->breadcrumbs) : ''; // Output breadcrumbs
  ?>
  <div class="csc-wrapper">
    <?php flashMsg('account_register'); ?>
    <div class="csc-row cs-pb-5">
      <main class="csc-col csc-col12 csc-col--md8">
        <h1>Create an account</h1>
        <form action="<?php echo get_site_url('account/register'); ?>" method="POST" id="registration-form" class="csc-form paper">
          <input type="hidden" name="action" value="register" />
          <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
          <div class="csc-input-field cs-mt-4">
            <input type="text" name="first_name" id="first_name" tabindex="1" autocapitalize="on" <?php if (!empty($data->first_name)) echo ' value="' . $data->first_name . '"'; ?> required>
            <label for="first_name">First Name*</label>
          </div>
          <div class="csc-input-field cs-mt-4">
            <input type="text" name="last_name" id="last_name" tabindex="2" autocapitalize="on" <?php if (!empty($data->last_name)) echo ' value="' . $data->last_name . '"'; ?> required>
            <label for="last_name">Last Name*</label>
          </div>
          <div class="csc-input-field cs-mt-4">
            <input type="email" name="email" id="email" tabindex="3" autocapitalize="off" <?php if (!empty($data->email)) echo ' value="' . $data->email . '"'; ?> required>
            <label for="email">Email Address*</label>
          </div>
          <div class="csc-input-field cs-mb-1">
            <input type="password" name="password" id="password" autocomplete="new-password" tabindex="4" data-lpignore="true" required>
            <label for="password">Choose a Password*</label>
          </div>
          <div class="csc-input-field cs-mb-1">
            <input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password" tabindex="5" data-lpignore="true" required>
            <label for="confirm_password">Confirm Your Password*</label>
          </div>
          <div class="csc-input-field cs-mb-1">
            <p>
              <label><input type="checkbox" name="accepted_conditions" id="accepted_conditions" <?php echo (!empty($data->accepted_conditions) && $data->accepted_conditions) ? ' checked' : ''; ?> required><span>I've read and accept the <a href="<?php echo get_site_url('terms-conditions'); ?>" target="_blank">Terms &amp; Conditions</a> and <a href="<?php echo get_site_url('privacy-policy'); ?>" target="_blank">Privacy Policy</a> for the <?php echo $data->site_name; ?> website</span></label>
            </p>
          </div>
          <button type="submit" name="action" tabindex="6" value="register" class="csc-btn csc-btn--large csc-btn--success">Create Account</button>
        </form>
      </main>
      <aside class="csc-col csc-col12 csc-col--md4">
        <section class="csc-grey lighten-4 cs-p-4 cs-mb-4">
          <h3>Creating an account</h3>
          <p class="cs-body1">Create a <?php echo $data->site_name; ?> account to save multiple addresses, add products to wish lists, check the status of your recent orders and more.</p>
        </section>
        <section class="csc-grey lighten-4 cs-p-4 cs-mb-4 cs-text-center">
          <h3>Already have an account?</h3>
          <p><a href="<?php echo get_site_url('account/login'); ?>" class="csc-btn csc-btn--dark">Sign in now</a></p>
        </section>
      </aside>
    </div>
  </div>
</div>
<!-- End Content ~#~ Start Footer -->

<script>
  // Init validation on document ready
  $(document).ready(function() {
    let validator = $("#registration-form").validate({
      rules: {
        first_name: {
          required: true,
          minlength: 3
        },
        last_name: {
          required: true,
          minlength: 3
        },
        email: {
          required: true,
          email: true
        },
        password: {
          required: true,
          minlength: 6,
          maxlength: 64,
          pattern: "((?=.*\\d)(?=.*[a-z])(?=.*[A-Z]).{6,128})"
        },
        "confirm_password": {
          required: true,
          equalTo: "#password"
        },
        "accepted_conditions": {
          required: true
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
        email: {
          required: "Please enter your email address",
          email: "Please enter a valid email address"
        },
        password: {
          required: "Please enter your new password",
          minlength: "Please enter at least 6 characters",
          maxlength: "Please enter no more than 128 characters",
          pattern: "Please enter no more than 128 characters",
          pattern: "Your password must be at least six characters long and contain at least one upper case letter and one number."
        },
        "confirm_password": {
          required: "Please confirm your new password",
          equalTo: "Both passwords must match"
        },
        "accepted_conditions": {
          required: "Please accept the terms &amp; conditions"
        }
      }
      <?php if ($data->recaptcha_site_key) { ?>,
        submitHandler(form) {
          if (typeof grecaptcha == 'object') {
            // Init captcha
            grecaptcha.execute('<?php echo $data->recaptcha_site_key; ?>', {
              action: 'register'
            }).then(function(token) {
              const recaptchaResponse = document.getElementById('recaptchaResponse');
              recaptchaResponse.value = token;
              // Submit the form
              form.submit();
            });
          } else {
            document.getElementById('recaptchaResponse').value = -1;
            form.submit();
          }
        }
      <?php } ?>
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