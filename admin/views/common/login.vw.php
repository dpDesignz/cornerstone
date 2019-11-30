<?php
  /**
   * The User Login template file
   *
   * @package Cornerstone
   * @subpackage Core Cornerstone Theme
   */

  // Set the meta/og information for the page
  $pageMetaTitle = "Login | " . SITE_NAME . " Admin";
  $pageMetaDescription = SITE_NAME . " Admin login page.";
  $pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
  $pageMetaCanonical = get_site_url('/admin/login');
  $pageMetaType = "website";

  // Set any page injected values
  $pageHasForm = TRUE;
  $pageHeadExtras = '<link href="' . get_site_url('admin-files/css/user-forms.css?v=' . str_replace(' ', '', trim(get_option('site_version')))) . '" rel="stylesheet" type="text/css">';
  $pageFooterExtras = '<script>
    $().ready(function() {
      $("#admin-login-form").validate({
        rules: {
          udata: {
            required: true,
            minlength: 3
          },
          password: {
            required: true,
            minlength: 6
          }
        },
        messages: {
          udata: {
            required: "Please enter your username or email address",
            minlength: "Please enter at least 3 characters"
          },
          password: {
            required: "Please enter your password",
            minlength: "Please enter at least 6 characters"
          }
        }
      })';
  // Output errors if they exist
  if(!empty($data->err)) {
    // Call the formatting function
    $pageFooterExtras .= showValidationErrors($data->err);
  }
  $pageFooterExtras .= '});
  </script>';

  // Set redirect URL
  // Check if redirect set in query
  if(!empty($_GET['redirect'])) {

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
  <header>
    <section id="cs--header__logo">
        <a href="https://github.com/dpDesignz/cornerstone" target="_blank" title="Cornerstone PHP Framework Website"><img src="<?php echo get_site_url('img/cornerstone/cornerstone_framework_logo_white.svg'); ?>" alt="Cornerstone PHP Framework"></a>
    </section>
  </header>
  <!-- End Header ~#~ Start Main -->
  <main>
    <section id="cs--user-form" class="csc-card center animated animatedFadeInUp fadeInUp">
      <form action="<?php echo get_site_url('admin/login'); ?>" method="POST" id="admin-login-form" class="csc-form">
        <input type="hidden" name="redirect-to" id="redirect-to" value="<?php echo $redirectURL; ?>">
        <h3 class="cs-h3">Admin</h3>
        <p class="cs-body1">Sign in to your account.</p>
        <?php flashMsg('admin_login'); ?>
        <div class="csc-input-field">
          <i class="material-icons csc-prefix">account_circle</i>
          <input type="text" name="udata" id="udata" autocapitalize="off"<?php if(!empty($data->udata)) echo ' value="' . $data->udata . '"'; ?> required>
          <label for="udata">Email/Username</label>
        </div>
        <div class="csc-input-field cs-mb-1">
          <i class="material-icons csc-prefix">vpn_key</i>
          <input type="password" name="password" id="password" required>
          <label for="password">Password</label>
        </div>
        <div class="csc-row csc-row--no-gap">
          <p class="csc-col csc-col12 csc-col--md7 cs-text-left-md cs-my-1 cs-mb-3"><label><input type="checkbox" name="remember" id="remember-me"<?php if(!empty($data->remember) && !$data->remember) echo ' checked'; ?>><span>Remember me</span></label></p>
          <p class="csc-col csc-col12 csc-col--md5 cs-text-right-md cs-my-1 cs-mb-3 cs-caption"><a href="<?php echo get_site_url('admin/users/forgot-password'); ?>" class="csc-text-grey text-darken-2">Forgot Password?</a></p>
        </div>
        <button type="submit" name="action" value="log-in" class="csc-btn csc-btn--wide green">Sign In <i class="material-icons csc-bi-right">exit_to_app</i></button>
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