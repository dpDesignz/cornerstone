<?php

/**
 * The New User Password template file
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = "New Password | " . $data->site_name;
$pageMetaDescription = $data->site_name . " user new password page.";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('account/password/new');
$pageMetaType = "website";

// Set any page injected values
$pageHasForm = TRUE;
$pageHeadExtras = '<link href="' . get_site_url('admin-files/css/user-forms.css?v=' . str_replace(' ', '', trim($option->get('site_version')))) . '" rel="stylesheet" type="text/css">';
$pageFooterExtras = '';

// Load html head
require(get_theme_path('head.php', 'admin')); ?>

<!-- Start Header -->
<header>
  <section id="cs--header__logo">
    <a href="https://github.com/dpDesignz/cornerstone" target="_blank" title="Cornerstone PHP Framework Website"><img src="<?php echo get_site_url('img/cornerstone/cornerstone_framework_logo_white.svg'); ?>" alt="Cornerstone PHP Framework"></a>
  </section>
</header>
<!-- End Header ~#~ Start Main -->
<main>
  <section id="cs--user-form" class="csc-card center animated animatedFadeInUp fadeInUp">
    <form action="<?php echo get_site_url('account/password/new'); ?>" method="POST" id="user-new-password-form" class="csc-form">
      <input type="hidden" name="selector" id="selector" value="<?php echo $data->selector; ?>">
      <input type="hidden" name="token" id="token" value="<?php echo $data->token; ?>">
      <h3 class="cs-h3">New Password</h3>
      <p class="cs-body1">Please enter your new password below.</p>
      <p class="cs-caption csc-alert csc-alert--info">Your password must be at least six characters long and contain at least one upper case letter, one number, and one special character.</p>
      <?php flashMsg('account_new_pwd'); ?>
      <div class="csc-input-field cs-mb-1">
        <input type="password" name="password" id="password" required>
        <label for="password">Password</label>
      </div>
      <div class="csc-input-field cs-mb-1">
        <input type="password" name="confirm-password" id="confirm-password" required>
        <label for="confirm-password">Confirm Password</label>
      </div>
      <div class="csc-row csc-row--no-gap">
        <p class="csc-col csc-col12 csc-col--md6 cs-text-left-md cs-my-1 cs-mb-3"><button type="submit" name="action" value="save-password" class="csc-btn green">Save <i class="material-icons csc-bi-right">save</i></button></p>
        <p class="csc-col csc-col12 csc-col--md6 cs-text-right-md cs-my-1 cs-mb-3 cs-caption"><a href="<?php echo get_site_url('account/login'); ?>" class="csc-btn--flat-small">Back to login</a></p>
      </div>
    </form>
  </section>
</main>
<!-- End Main ~#~ Start Footer -->
<footer>
  <p><span><a href="https://github.com/dpDesignz/cornerstone" target="_blank" title="Cornerstone PHP Framework Website">Cornerstone</a></span><span>Version <?php echo CS_VERSION; ?></span>&copy; <?php echo date('Y') . ' ' . $data->site_name; ?></p>
</footer>

<script>
  // Init validation on document ready
  $(document).ready(function() {
    let validator = $("#user-new-password-form").validate({
      rules: {
        password: {
          required: true,
          minlength: 8,
          maxlength: 64,
          pattern: "((?=.*\\\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\\\W]).{8,128})"
        },
        "confirm-password": {
          required: true,
          equalTo: "#password"
        }
      },
      messages: {
        password: {
          required: "Please enter your new password",
          minlength: "Please enter at least 8 characters",
          maxlength: "Please enter no more than 128 characters",
          pattern: "Please enter a valid password"
        },
        "confirm-password": {
          required: "Please confirm your new password",
          equalTo: "Both passwords must match"
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