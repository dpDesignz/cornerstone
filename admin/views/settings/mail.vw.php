<?php

/**
 * Admin Mail Settings File
 *
 * @package Cornerstone
 */

// Set the meta/og information for the page
$pageMetaTitle = "Mail Settings | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " mail settings page.";
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = get_site_url('admin/settings/mail');
$pageMetaType = "website";

// Set any page injected values
$pageHasForm = TRUE;
$pageHeadExtras = '';
$pageFooterExtras = '<script>
  // Bind values chosen
  $("#smtp_secure, #smtp_auth").chosen({
    width: "100%",
    disable_search: "true"
  });
  </script>';
$currentNav = 'settings';
$currentSubNav = $currentNav . '/index';

// Load html head
require(get_theme_path('head.php', 'admin'));
// Load html layout
require(get_theme_path('layout.php', 'admin')); ?>

<div class="csc-row csc-row--no-pad cs-mt-3">
  <section class="csc-col csc-col12">
    <nav class="csc-breadcrumbs">
      <?php
      // Check for and output breadcrumbs
      if (!empty($data->breadcrumbs)) {
        // Output breadcrumbs
        echo outputBreadcrumbs((object) $data->breadcrumbs);
      } ?>
    </nav>
  </section>
</div>
<style>
  body.cs-page .csc-wrapper {
    max-width: 750px;
  }
</style>
<div class="csc-wrapper">
  <h1 class="cs-h2 cs-my-2">Mail Settings</h1>
  <?php flashMsg('settings_mail'); ?>
</div>
<form action="<?php echo get_site_url('admin/settings/save'); ?>" method="POST" id="setting-form" class="csc-wrapper csc-form cs-mt-4">
  <input type="hidden" name="set_type" value="mail">
  <section class="csc-container cs-mb-3 cs-p-3">
    <fieldset>
      <legend>PHP Mailer</legend>
      <input type="hidden" name="setting[enable_phpmailer][current]" <?php if (!empty($data->curr_enable_phpmailer)) echo ' value="' . $data->curr_enable_phpmailer . '"'; ?>>
      <input type="hidden" name="setting[enable_phpmailer][bool]" value="TRUE">
      <div class="csc-row">
        <div class="csc-col csc-col8">
          <p class="cs-body1 cs-mt-0">Enable <a href="https://github.com/PHPMailer/PHPMailer" target="_blank" data-tippy-content="View PHP Mailer Docs">PHP Mailer</a></p>
        </div>
        <div class="csc-col csc-col4 cs-text-right">
          <div class="csc-switch">
            <label>
              No
              <input type="checkbox" name="setting[enable_phpmailer][set]" tabindex="1" <?php if (!empty($data->set_enable_phpmailer) && $data->set_enable_phpmailer) echo 'checked'; ?>>
              <span class="csc-lever"></span>
              Yes
            </label>
          </div>
        </div>
      </div>
    </fieldset>
  </section>
  <section class="csc-container cs-mb-3 cs-p-3">
    <fieldset>
      <legend>SMTP Details <small>(for sending emails using your mail server)</small></legend>
      <p class="cs-body2 cs-mt-0 csc-text-deep-orange cs-text-center"><strong><i class="fas fa-exclamation-triangle"></i> DO NOT CHANGE THESE SETTINGS UNLESS YOU KNOW WHAT YOU ARE DOING! <i class="fas fa-exclamation-triangle"></i></strong></p>
      <input type="hidden" name="setting[smtp_host][current]" <?php if (!empty($data->curr_smtp_host)) echo ' value="' . $data->curr_smtp_host . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="text" name="setting[smtp_host][set]" id="smtp_host" tabindex="2" <?php if (!empty($data->set_smtp_host)) echo ' value="' . $data->set_smtp_host . '"'; ?> autocapitalize="off">
          <label for="smtp_host">SMTP Host</label>
        </div>
      </div>
      <input type="hidden" name="setting[smtp_username][current]" <?php if (!empty($data->curr_smtp_username)) echo ' value="' . $data->curr_smtp_username . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="text" name="setting[smtp_username][set]" id="smtp_username" tabindex="3" <?php if (!empty($data->set_smtp_username)) echo ' value="' . $data->set_smtp_username . '"'; ?> autocapitalize="off">
          <label for="smtp_username">SMTP Username</label>
        </div>
      </div>
      <input type="hidden" name="setting[smtp_password][current]" <?php if (!empty($data->curr_smtp_password)) echo ' value="' . $data->curr_smtp_password . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col10 csc-input-field">
          <input type="password" name="setting[smtp_password][set]" id="smtp_password" tabindex="4" <?php if (!empty($data->set_smtp_password)) echo ' value="' . $data->set_smtp_password . '"'; ?> autocapitalize="off">
          <label for="smtp_password">SMTP Password</label>
        </div>
        <div class="csc-col csc-col2 csc-input-field">
          <button type="button" class="csc-btn" id="view_password" data-tippy-content="Toggle view password"><i class="far fa-eye"></i></button>
        </div>
      </div>
      <input type="hidden" name="setting[smtp_port][current]" <?php if (!empty($data->curr_smtp_port)) echo ' value="' . $data->curr_smtp_port . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="number" name="setting[smtp_port][set]" id="smtp_port" tabindex="5" <?php if (!empty($data->set_smtp_port)) echo ' value="' . $data->set_smtp_port . '"'; ?> autocapitalize="off">
          <label for="smtp_port">SMTP Port</label>
        </div>
      </div>
      <input type="hidden" name="setting[smtp_secure][current]" <?php if (!empty($data->curr_smtp_secure)) echo ' value="' . $data->curr_smtp_secure . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <select name="setting[smtp_secure][set]" id="smtp_secure" tabindex="6" data-placeholder="Set SMTP Secure">
            <option value="TRUE" <?php if (!empty($data->set_smtp_secure) && $data->set_smtp_secure == "TRUE") echo 'selected'; ?>>True</option>
            <option value="FALSE" <?php if (empty($data->set_smtp_secure) || $data->set_smtp_secure == "FALSE") echo 'selected'; ?>>False</option>
          </select>
          <label>SMTP Secure</label>
        </div>
      </div>
      <input type="hidden" name="setting[smtp_auth][current]" <?php if (!empty($data->curr_smtp_auth)) echo ' value="' . $data->curr_smtp_auth . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <select name="setting[smtp_auth][set]" id="smtp_auth" tabindex="7" data-placeholder="Set SMTP Authentication">
            <option value="TRUE" <?php if (!empty($data->set_smtp_auth) && $data->set_smtp_auth == "TRUE") echo 'selected'; ?>>True</option>
            <option value="FALSE" <?php if (empty($data->set_smtp_auth) || $data->set_smtp_auth == "FALSE") echo 'selected'; ?>>False</option>
          </select>
          <label>SMTP Authentication</label>
        </div>
      </div>
    </fieldset>
  </section>
  <section class="csc-container cs-p-3" style="min-height: 0;">
    <div class="csc-row csc-row--no-gap">
      <div class="csc-col csc-col6 cs-my-1 cs-mb-3">
        <a href="<?php echo get_site_url('admin/settings'); ?>" class="csc-btn--flat"><span>Cancel</span></a>
      </div>
      <div class="csc-col csc-col6 cs-text-right cs-my-1 cs-mb-3">
        <button type="submit" name="action" tabindex="8" value="save" class="csc-btn csc-btn--success">Save <i class="fas fa-save csc-bi-right"></i></button>
      </div>
    </div>
    <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
  </section>
</form>

<script>
  // Toggle password view
  document.querySelector('#view_password').addEventListener('click', (elm) => {
    // Get button
    const viewBtn =
      elm.target.nodeName === 'BUTTON' ? elm.target : elm.target.parentNode;
    // Get icon
    const icon = viewBtn.querySelector('i');
    // Get input
    const input = document.querySelector('#smtp_password');
    // Check input type
    if (input.type === "password") {
      // Change to text
      input.type = 'text';
      // Change icon
      if (icon.classList.contains('fa-eye')) {
        icon.classList.remove('fa-eye');
      }
      icon.classList.add('fa-eye-slash');
    } else { // Change to password
      input.type = 'password';
      // Change icon
      if (icon.classList.contains('fa-eye-slash')) {
        icon.classList.remove('fa-eye-slash');
      }
      icon.classList.add('fa-eye');
    }
  });
</script>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>