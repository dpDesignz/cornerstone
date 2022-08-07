<?php

/**
 * The main Forgot Password template file
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = "Forgot Password | " . $data->site_name;
$pageMetaDescription = $data->site_name . " user forgot password page.";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('account/password/forgot');
$pageMetaType = "website";

// Set any page injected values
$pageID = "cs-account-forgot-password";
$loadScripts = array(
  'validate'
);
$pageHeadExtras = '<link href="' . get_site_url('admin-files/css/user-forms.css?v=' . str_replace(' ', '', trim($option->get('site_version')))) . '" rel="stylesheet" type="text/css">';
$pageFooterExtras = '';

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
    <form action="<?= get_site_url('account/password/forgot'); ?>" method="POST" id="account-forgot-password-form" class="csc-form">
      <h1>Forgot Password</h1>
      <p class="cs-body1">Please enter your email address below to request a password reset.</p>
      <?php flashMsg('admin_forgot_pwd'); ?>
      <section id="cs--user-form__fields" style="padding-top: 0;">
        <div class="csc-input-field">
          <input type="email" name="email" id="email" placeholder="Email Address" required>
        </div>
      </section>
      <button type="submit" name="action" value="request-password" class="csc-btn green">Request New Password <i class="material-icons csc-bi-right">send</i></button>
      <p class="cs-my-2"><a href="<?= $data->login_link; ?>" class="csc-btn--flat-small">Back to login</a></p>
    </form>
  </section>
</main>
<!-- End Main ~#~ Start Footer -->
<footer id="cs-footer">
  <p><span><a href="https://github.com/dpDesignz/cornerstone" target="_blank" title="Cornerstone PHP Framework Website">Cornerstone</a></span><span>Version <?= CS_VERSION; ?></span>&copy; <?= date('Y') . ' ' . $data->site_name; ?></p>
</footer>

<script>
  // Init validation on document ready
  $(document).ready(function() {
    let validator = $("#account-forgot-password-form").validate({
      rules: {
        email: {
          required: true,
          email: true
        }
      },
      messages: {
        email: {
          required: "Please enter your email address",
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
$hideThemeFooter = TRUE;
require(get_theme_path('footer.php', 'admin')); ?>