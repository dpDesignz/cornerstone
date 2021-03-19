<?php

/**
 * The login template file
 *
 * @package Cornerstone
 * @subpackage Mission Equine
 */

// Set the meta/og information for the page
$pageMetaTitle = "Sign In | " . $data->site_name;
$pageMetaDescription = "Sign in to " . $data->site_name . " to access your order history, wishlist, and account information.";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('account/login');
$pageMetaType = "website";

// Set any page injected values
$pageHasForm = TRUE;
$pageBodyClassID = 'class="cs-page cs-components cs-account"';
$pageHeadExtras = '';
$pageFooterExtras = '';

// Init redirect URL
$redirectURL = '';
// Check if redirect set in query
if (!empty($_GET['redirect'])) {
  // If in url, set to this
  $redirectURL = filter_var($_GET['redirect'], FILTER_SANITIZE_URL);
} else if ($_SERVER['REQUEST_URI'] != "/account/login") { // Else, see if request URI is set

  // Set to request URI
  $redirectURL = htmlspecialchars(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL));
}

// Load html head
require(get_theme_path('head.php'));
// Load html layout
require(get_theme_path('layout.php')); ?>

<!-- End Header ~#~ Start Content -->
<style>
  aside {
    text-align: center;
  }

  aside i.fas {
    color: var(--primary-color);
    font-size: 5em;
  }

  aside h3 {
    margin-bottom: 0;
  }
</style>
<div id="content">
  <nav class="csc-breadcrumbs" aria-label="Breadcrumb">
    <?php
    // Check for and output breadcrumbs
    if (!empty($data->breadcrumbs)) {
      // Output breadcrumbs
      echo outputBreadcrumbs((object) $data->breadcrumbs);
    } ?>
  </nav>
  <div class="csc-wrapper">
    <?php flashMsg('account_login'); ?>
    <div class="csc-row">
      <main class="csc-col csc-col12 csc-col--md8">
        <h1>Sign In</h1>
        <form action="<?php echo get_site_url('account/login'); ?>" method="POST" id="user-login-form" class="csc-form paper">
          <input type="hidden" name="redirect-to" id="redirect-to" value="<?php echo $redirectURL; ?>">
          <div class="csc-input-field cs-mt-4">
            <input type="text" name="user" id="user" autocapitalize="off" <?php if (!empty($data->user)) echo ' value="' . $data->user . '"'; ?> required>
            <label for="user">Email</label>
          </div>
          <div class="csc-input-field cs-mb-1">
            <input type="password" name="password" id="password" required>
            <label for="password">Password</label>
          </div>
          <div class="csc-row csc-row--no-gap">
            <p class="csc-col csc-col12 csc-col--md7 cs-text-left-md cs-my-1 cs-mb-3"><label><input type="checkbox" name="remember" id="remember-me" <?php if (!empty($data->remember) && !$data->remember) echo ' checked'; ?>><span>Remember me</span></label></p>
            <p class="csc-col csc-col12 csc-col--md5 cs-text-right-md cs-my-1 cs-mb-3 cs-caption"><a href="<?php echo get_site_url('/account/password/forgot'); ?>" class="csc-text-grey text-darken-2">Forgot Password?</a></p>
          </div>
          <button type="submit" name="action" value="log-in" class="csc-btn csc-btn--large csc-btn--success">Sign In</button>
        </form>
      </main>
      <aside class="csc-col csc-col12 csc-col--md4 csc-grey lighten-4 csc-col--align-center cs-p-4 cs-mb-3">
        <?php
        // Only show registration if active
        if ($option->get('registration_active')) { ?>
          <i class="fas fa-user-plus"></i>
          <h3>Create Account</h3>
          <p class="cs-body2">Having an account with <?php echo $data->site_name; ?> enables you to save multiple addresses, add products to wish lists, check the status of your recent orders and more.</p>
          <p><a href="<?php echo get_site_url('account/register'); ?>" class="csc-btn csc-btn--dark">Create Account</a></p>
        <?php } else { ?>
          <i class="fas fa-door-open"></i>
          <h3>Signing In</h3>
          <p class="cs-body1">Sign in to your <?php echo $data->site_name; ?> account to save multiple addresses, add products to wish lists, check the status of your recent orders and more.</p>
          <p class="cs-caption">Sorry, site registration is currently closed.</p>
        <?php } ?>
      </aside>
    </div>
  </div>
</div>
<!-- End Content ~#~ Start Footer -->

<script>
  // Init validation on document ready
  $(document).ready(function() {
    let validator = $("#user-login-form").validate({
      rules: {
        email: {
          required: true,
          minlength: 3
        },
        password: {
          required: true,
          minlength: 6
        }
      },
      messages: {
        email: {
          required: "Please enter your email address",
          minlength: "Please enter at least 3 characters"
        },
        password: {
          required: "Please enter your password",
          minlength: "Please enter at least 6 characters"
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