<?php

/**
 * The User Login template file
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = "Login | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " Admin login page.";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('/admin/login');
$pageMetaType = "website";

// Set any page injected values
$pageID = "cs-admin-login";
$loadScripts = array(
  'validate'
);
$pageHeadExtras = '<link href="' . get_site_url('admin-files/css/user-forms.css?v=' . str_replace(' ', '', trim($option->get('site_version')))) . '" rel="stylesheet" type="text/css">';
$pageFooterExtras = '';

// Set redirect URL
// Check if redirect set in query
if (!empty($_GET['redirect'])) {

  // If in url, set to this
  $redirectURL = filter_var($_GET['redirect'], FILTER_SANITIZE_URL);
} else if ($_SERVER['REQUEST_URI'] != "/admin/login") { // Else, see if request URI is set

  // Set to request URI
  $redirectURL = htmlspecialchars(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL));
} else { // Else set empty

  $redirectURL = '';
}

// Load html head
require(get_theme_path('head.php', 'admin')); ?>

<!-- Start Header -->
<header id="cs-header">
  <section id="cs--header__logo">
    <?php
    // Output site_logo.svg file if it exists, else output the Cornerstone logo
    if (file_exists(get_public_path('admin-files/img/site_logo.svg'))) { ?>
      <a href="<?= get_site_url('admin/'); ?>"><img class="site-logo" src="<?= get_site_url('admin-files/img/site_logo.svg'); ?>" alt="<?= $data->site_name; ?>" /></a>
    <?php } else { ?>
      <a href="https://github.com/dpDesignz/cornerstone" target="_blank" title="Cornerstone PHP Framework Website"><img src="<?= get_site_url('img/cornerstone/cornerstone_framework_logo_white.svg'); ?>" alt="Cornerstone PHP Framework"></a>
    <?php } ?>
  </section>
</header>
<!-- End Header ~#~ Start Main -->
<main id="cs-main">
  <section id="cs--user-form" class="csc-card center animated animatedFadeInUp fadeInUp">
    <form action="<?= get_site_url('account/login'); ?>" method="POST" id="admin-login-form" class="csc-form">
      <input type="hidden" name="redirect-to" id="redirect-to" value="<?= $redirectURL; ?>">
      <h1>Sign in to <?= (!empty($data->site_name)) ? $data->site_name : 'your account'; ?></h1>
      <?php flashMsg('admin_login'); ?>
      <section id="cs--user-form__fields">
        <div class="csc-input-field">
          <input type="text" name="user" id="user" autocapitalize="off" value="<?= (!empty($data->user)) ? $data->user : ''; ?>" placeholder="Email or Username" required>
        </div>
        <div id="cs--user-form__password" class="csc-input-field">
          <span id="cs--user-form__password__wrapper">
            <input type="password" name="password" id="password" placeholder="Password" data-shown="false" data-lpignore="true" required>
            <i id="toggle-password" class="fa-solid fa-eye-slash"></i>
          </span>
          <p><a href="<?= get_site_url('account/password/forgot'); ?>" class="csc-text-grey text-darken-2">Forgot your password?</a></p>
        </div>
      </section>
      <section id="cs--user-form__actions">
        <p>
          <label><input type="checkbox" name="remember" id="remember-me" <?php if (!empty($data->remember) && !$data->remember) echo ' checked'; ?>><span>Remember me</span></label>
        </p>
        <button type="submit" name="action" value="log-in" class="csc-btn csc-btn--wide csc-btn--success ">Sign In <i class="fa-solid fa-arrow-right-to-bracket csc-bi-right"></i></button>
      </section>
    </form>
  </section>
</main>
<!-- End Main ~#~ Start Footer -->
<footer id="cs-footer">
  <p><span><a href="https://github.com/dpDesignz/cornerstone" target="_blank" title="Cornerstone PHP Framework Website">Cornerstone</a></span><span>Version <?= CS_VERSION; ?></span>&copy; <?= date('Y') . ' ' . $data->site_name; ?></p>
</footer>

<script>
  // Toggle view password
  const passwordInput = document.querySelector("#password");
  const togglePwd = document.querySelector("#toggle-password");
  if (passwordInput && togglePwd) {
    togglePwd.addEventListener("click", () => {
      if (passwordInput.dataset.shown === "true") {
        // Hide password
        passwordInput.type = "password";
        passwordInput.dataset.shown = "false";
        togglePwd.classList.remove('fa-eye');
        togglePwd.classList.add('fa-eye-slash');
      } else {
        // Show password
        passwordInput.type = "text";
        passwordInput.dataset.shown = "true";
        togglePwd.classList.remove('fa-eye-slash');
        togglePwd.classList.add('fa-eye');
      }
    });
  }
  // Init validation on document ready
  $(document).ready(function() {
    let validator = $("#admin-login-form").validate({
      rules: {
        user: {
          required: true,
          minlength: 3
        },
        password: {
          required: true,
          minlength: 6
        }
      },
      messages: {
        user: {
          required: "Please enter your username or email address",
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
$hideThemeFooter = TRUE;
require(get_theme_path('footer.php', 'admin')); ?>