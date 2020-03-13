<?php

/**
 * The User Authorization template file
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = "Authorize | " . SITE_NAME . " Admin";
$pageMetaDescription = SITE_NAME . " user authorization page.";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('admin/authorize');
$pageMetaType = "website";

// Set any page injected values
$pageHasForm = TRUE;
$pageHeadExtras = '<link href="' . get_site_url('admin-files/css/user-forms.css?v=' . str_replace(' ', '', trim($option->get('site_version')))) . '" rel="stylesheet" type="text/css">';
$pageFooterExtras = '<script>
    $().ready(function() {
      $("#admin-auth-form").validate({
        rules: {
          token: {
            required: true,
            minlength: 6
          }
        },
        messages: {
          token: {
            required: "Please enter your password",
            minlength: "Please enter at least 6 characters"
          }
        }
      })';
// Output errors if they exist
if (!empty($data->err)) {
  // Call the formatting function
  $pageFooterExtras .= showValidationErrors($data->err);
}
$pageFooterExtras .= '});
  </script>';

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
    <form action="<?php echo get_site_url('admin/authorize'); ?>" method="POST" id="admin-auth-form" class="csc-form">
      <input type="hidden" name="redirect-to" id="redirect-to" value="<?php if (!empty($_GET['redirect'])) echo htmlspecialchars(filter_var($_GET['redirect'], FILTER_SANITIZE_URL)); ?>">
      <input type="hidden" name="selector" id="selector" value="<?php echo $data->selector; ?>">
      <h3 class="cs-h3">Authorization</h3>
      <p class="cs-body1">Authorize your sign in to continue.</p>
      <?php flashMsg('admin_auth'); ?>
      <div class="csc-input-field">
        <input type="text" name="token" id="token" autocapitalize="off" <?php if (!empty($data->token)) echo ' value="' . $data->token . '"'; ?> required>
        <label for="token">Authorization Code</label>
      </div>
      <div class="csc-row csc-row--no-gap">
        <p class="csc-col csc-col12 csc-col--md6 cs-text-left-md cs-my-1 cs-mb-3"><button type="submit" name="action" value="authorize" class="csc-btn green">Authorize <i class="material-icons csc-bi-right">exit_to_app</i></button></p>
        <p class="csc-col csc-col12 csc-col--md6 cs-text-right-md cs-my-1 cs-mb-3 cs-caption"><a href="<?php echo get_site_url('admin/login'); ?>" class="csc-btn--flat-small">Back to login</a></p>
      </div>
    </form>
  </section>
</main>
<!-- End Main ~#~ Start Footer -->
<footer>
  <p><span><a href="https://github.com/dpDesignz/cornerstone" target="_blank" title="Cornerstone PHP Framework Website">Cornerstone</a></span><span>Version <?php echo CS_VERSION; ?></span>&copy; <?php echo date('Y') . ' ' . SITE_NAME; ?></p>
</footer>

<?php
// Load html footer
$hideThemeFooter = TRUE;
require(get_theme_path('footer.php', 'admin')); ?>