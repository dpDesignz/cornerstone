<?php

/**
 * Admin Security Settings File
 *
 * @package Cornerstone
 */

// Set the meta/og information for the page
$pageMetaTitle = "Security Settings | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " security settings page.";
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = get_site_url('admin/settings/security');
$pageMetaType = "website";

// Set any page injected values
$loadScripts = array(
  'validate',
  'chosen'
);
$pageHeadExtras = '';
$pageFooterExtras = '<script>
  // Bind values chosen
  $("#cookie_expire_type").chosen({
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

<?= (!empty($data->breadcrumbs)) ? outputBreadcrumbs((object) $data->breadcrumbs) : ''; // Output breadcrumbs
?>
<style>
  body.cs-page .csc-wrapper {
    max-width: 750px;
  }
</style>
<div class="csc-wrapper">
  <h1 class="cs-h2 cs-my-2">Security Settings</h1>
  <?php flashMsg('settings_security'); ?>
</div>
<form action="<?php echo get_site_url('admin/settings/save'); ?>" method="POST" id="setting-form" class="csc-wrapper csc-form cs-mt-4">
  <input type="hidden" name="set_type" value="security">
  <section class="csc-container cs-mb-3 cs-p-3">
    <fieldset>
      <legend>User Settings</legend>
      <input type="hidden" name="setting[registration_active][current]" <?php if (!empty($data->curr_registration_active)) echo ' value="' . $data->curr_registration_active . '"'; ?>>
      <input type="hidden" name="setting[registration_active][bool]" value="TRUE">
      <div class="csc-row cs-mb-2">
        <div class="csc-col csc-col8">
          <p class="cs-body1 cs-mt-0">Enable Registrations</p>
        </div>
        <div class="csc-col csc-col4 cs-text-right">
          <div class="csc-switch">
            <label>
              No
              <input type="checkbox" name="setting[registration_active][set]" tabindex="1" <?php if (!empty($data->set_registration_active) && $data->set_registration_active) echo 'checked'; ?>>
              <span class="csc-lever"></span>
              Yes
            </label>
          </div>
        </div>
      </div>
      <input type="hidden" name="setting[max_logins][current]" <?php if (!empty($data->curr_max_logins)) echo ' value="' . $data->curr_max_logins . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="number" name="setting[max_logins][set]" id="max_logins" tabindex="2" <?php if (!empty($data->set_max_logins)) echo ' value="' . $data->set_max_logins . '"'; ?> autocapitalize="off" required>
          <label for="max_logins" data-tippy-content="The maximum amount of login attempts a user can make before their account is locked for the length of time set for the password reset expire.">Maximum Login Attempts* <i class="far fa-question-circle"></i></label>
        </div>
      </div>
      <input type="hidden" name="setting[auth_required][current]" <?php if (!empty($data->curr_auth_required)) echo ' value="' . $data->curr_auth_required . '"'; ?>>
      <input type="hidden" name="setting[auth_required][bool]" value="TRUE">
      <div class="csc-row cs-mb-2">
        <div class="csc-col csc-col8">
          <p class="cs-body1 cs-mt-0">Authentication Required <i class="far fa-question-circle" data-tippy-content="Set if you want 2 factor authentication (2FA) enabled for admin users when logging in."></i></p>
        </div>
        <div class="csc-col csc-col4 cs-text-right">
          <div class="csc-switch">
            <label>
              No
              <input type="checkbox" name="setting[auth_required][set]" tabindex="3" <?php if (!empty($data->set_auth_required) && $data->set_auth_required) echo 'checked'; ?>>
              <span class="csc-lever"></span>
              Yes
            </label>
          </div>
        </div>
      </div>
      <input type="hidden" name="setting[auth_expire][current]" <?php if (!empty($data->curr_auth_expire)) echo ' value="' . $data->curr_auth_expire . '"'; ?>>
      <div class="csc-row cs-mb-2">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="number" name="setting[auth_expire][set]" id="auth_expire" tabindex="4" <?php if (!empty($data->set_auth_expire)) echo ' value="' . $data->set_auth_expire . '"'; ?> autocapitalize="off" required>
          <label for="auth_expire" data-tippy-content="How long an authentication code is valid for after issue. This value MUST be entered in seconds.">Authentication Expires* (in seconds) <i class="far fa-question-circle"></i></label>
        </div>
      </div>
      <input type="hidden" name="setting[password_reset_expire][current]" <?php if (!empty($data->curr_password_reset_expire)) echo ' value="' . $data->curr_password_reset_expire . '"'; ?>>
      <div class="csc-row cs-mb-2">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="number" name="setting[password_reset_expire][set]" id="password_reset_expire" tabindex="5" <?php if (!empty($data->set_password_reset_expire)) echo ' value="' . $data->set_password_reset_expire . '"'; ?> autocapitalize="off" required>
          <label for="password_reset_expire" data-tippy-content="How long a password reset link (a maximum login attempt lock) is valid for after issue. This value MUST be entered in seconds.">Password Reset Expires* (in seconds) <i class="far fa-question-circle"></i></label>
        </div>
      </div>
      <input type="hidden" name="setting[session_expire][current]" <?php if (!empty($data->curr_session_expire)) echo ' value="' . $data->curr_session_expire . '"'; ?>>
      <div class="csc-row cs-mb-2">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="number" name="setting[session_expire][set]" id="session_expire" tabindex="6" <?php if (!empty($data->set_session_expire)) echo ' value="' . $data->set_session_expire . '"'; ?> autocapitalize="off" required>
          <label for="session_expire" data-tippy-content="How long a users login session is valid for after set. This value MUST be entered in seconds.">Session Expires* (in seconds) <i class="far fa-question-circle"></i></label>
        </div>
      </div>
      <input type="hidden" name="setting[cookie_expire][current]" <?php if (!empty($data->curr_cookie_expire)) echo ' value="' . $data->curr_cookie_expire . '"'; ?>>
      <?php
      // Split cookie data
      $cookieData = explode(',', $data->curr_cookie_expire); ?>
      <div class="csc-row csc-row--no-gap">
        <div class="csc-col csc-col4 csc-input-field">
          <input type="number" name="setting[cookie_expire][set][1]" id="cookie_expire_length" tabindex="7" <?php if (!empty($cookieData[1])) echo ' value="' . $cookieData[1] . '"'; ?> autocapitalize="off" required>
          <label for="cookie_expire_length" data-tippy-content="How long a users 'remember me' cookie is valid for after set.">Cookie Expiry* <i class="far fa-question-circle"></i></label>
        </div>
        <div class="csc-col csc-col8 csc-input-field">
          <select name="setting[cookie_expire][set][0]" id="cookie_expire_type" tabindex="8" data-placeholder="Set Cookie Expire Type" required>
            <option value="0" <?php if (empty($cookieData[0]) || $cookieData[0] === "0") echo 'selected'; ?>>Days</option>
            <option value="1" <?php if (!empty($cookieData[0]) && $cookieData[0] === "1") echo 'selected'; ?>>Weeks</option>
            <option value="2" <?php if (!empty($cookieData[0]) && $cookieData[0] === "2") echo 'selected'; ?>>Months</option>
            <option value="3" <?php if (!empty($cookieData[0]) && $cookieData[0] === "3") echo 'selected'; ?>>Years</option>
          </select>
        </div>
    </fieldset>
  </section>
  <section class="csc-container cs-mb-3 cs-p-3">
    <fieldset>
      <legend>Browser Tracking</legend>
      <input type="hidden" name="setting[browser_tracking][current]" <?php if (!empty($data->curr_browser_tracking)) echo ' value="' . $data->curr_browser_tracking . '"'; ?>>
      <input type="hidden" name="setting[browser_tracking][bool]" value="TRUE">
      <div class="csc-row">
        <div class="csc-col csc-col8">
          <p class="cs-body1 cs-mt-0">Enable Browser Tracking <i class="far fa-question-circle" data-tippy-content="Enables browser tracking for password reset links, new passwords, logins, and other user interaction browser information.<br><strong>WARNING</strong>: Make sure you are aware of your local Privacy Policy requirements before enabling this."></i></p>
        </div>
        <div class="csc-col csc-col4 cs-text-right">
          <div class="csc-switch">
            <label>
              No
              <input type="checkbox" name="setting[browser_tracking][set]" tabindex="9" <?php if (!empty($data->set_browser_tracking) && $data->set_browser_tracking) echo 'checked'; ?>>
              <span class="csc-lever"></span>
              Yes
            </label>
          </div>
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
        <button type="submit" name="action" tabindex="10" value="save" class="csc-btn csc-btn--success">Save <i class="fas fa-save csc-bi-right"></i></button>
      </div>
    </div>
    <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
  </section>
</form>

<script>
  // Init validation on document ready
  $(document).ready(function() {
    let validator = $("#setting-form").validate({
      rules: {
        max_logins: {
          required: true,
          minlength: 1,
          digits: true
        },
        auth_expire: {
          required: true,
          minlength: 1,
          digits: true
        },
        password_reset_expire: {
          required: true,
          minlength: 1,
          digits: true
        },
        session_expire: {
          required: true,
          minlength: 1,
          digits: true
        }
      },
      messages: {
        max_logins: {
          required: "Please enter a maximum attempt number",
          minlength: "Please enter at least 1 number",
          digits: "Please enter a valid number"
        },
        auth_expire: {
          required: "Please enter an authentication period",
          minlength: "Please enter at least 1 number",
          digits: "Please enter a valid number"
        },
        password_reset_expire: {
          required: "Please enter a password reset period",
          minlength: "Please enter at least 1 number",
          digits: "Please enter a valid number"
        },
        session_expire: {
          required: "Please enter a session expiry period",
          minlength: "Please enter at least 1 number",
          digits: "Please enter a valid number"
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
require(get_theme_path('footer.php', 'admin')); ?>