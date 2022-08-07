<?php

/**
 * Admin Add-on Settings File
 *
 * @package Cornerstone
 */

// Set the meta/og information for the page
$pageMetaTitle = "Add-on Settings | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " site add-on page.";
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = get_site_url('admin/settings/add-on');
$pageMetaType = "website";

// Set any page injected values
$loadScripts = array(
  'validate',
  'chosen'
);
$pageHeadExtras = '';
$pageFooterExtras = '';
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
  <h1 class="cs-h2 cs-my-2">Add-on Settings</h1>
  <?php flashMsg('settings_site'); ?>
</div>
<form action="<?php echo get_site_url('admin/settings/save'); ?>" method="POST" id="setting-form" class="csc-wrapper csc-form cs-mt-4">
  <input type="hidden" name="set_type" value="addon">
  <section class="csc-container cs-mb-3 cs-p-3">
    <fieldset>
      <legend>reCAPTCHA Settings <small><i class="fas fa-info-circle"></i> <a href="https://www.google.com/recaptcha/" target="_blank">reCAPTCHA docs</a></small></legend>
      <input type="hidden" name="setting[recaptcha_site_key][current]" <?php if (!empty($data->curr_recaptcha_site_key)) echo ' value="' . $data->curr_recaptcha_site_key . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="text" name="setting[recaptcha_site_key][set]" id="recaptcha_site_key" tabindex="1" <?php if (!empty($data->set_recaptcha_site_key)) echo ' value="' . $data->set_recaptcha_site_key . '"'; ?> autocapitalize="off">
          <label for="recaptcha_site_key" data-tippy-content="The reCAPTCHA site key can be obtained from your reCAPTCHA admin console site settings.">reCAPTCHA site key <i class="far fa-question-circle"></i></label>
        </div>
      </div>
      <input type="hidden" name="setting[recaptcha_secret_key][current]" <?php if (!empty($data->curr_recaptcha_secret_key)) echo ' value="' . $data->curr_recaptcha_secret_key . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="text" name="setting[recaptcha_secret_key][set]" id="recaptcha_secret_key" tabindex="2" <?php if (!empty($data->set_recaptcha_secret_key)) echo ' value="' . $data->set_recaptcha_secret_key . '"'; ?> autocapitalize="off">
          <label for="recaptcha_secret_key" data-tippy-content="The reCAPTCHA secret key can be obtained from your reCAPTCHA admin console site settings.">reCAPTCHA secret key <i class="far fa-question-circle"></i></label>
        </div>
      </div>
    </fieldset>
  </section>
  <section class="csc-container cs-mb-3 cs-p-3">
    <fieldset>
      <legend>Facebook Settings <small><i class="fas fa-info-circle"></i> <a href="https://developers.facebook.com/docs/apps/" target="_blank">Facebook developer docs</a></small></legend>
      <input type="hidden" name="setting[facebook_secret][current]" <?php if (!empty($data->curr_facebook_secret)) echo ' value="' . $data->curr_facebook_secret . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="text" name="setting[facebook_secret][set]" id="facebook_secret" tabindex="3" <?php if (!empty($data->set_facebook_secret)) echo ' value="' . $data->set_facebook_secret . '"'; ?> autocapitalize="off">
          <label for="recaptcha_site_key" data-tippy-content="Your Facebook secret be obtained from your Facebook Developer app basic settings.<br>Once this has a value and your developer has setup the integration you will be able to enable Facebook login on your site.">Facebook Secret <i class="far fa-question-circle"></i></label>
        </div>
      </div>
      <?php if (!empty($data->curr_facebook_secret)) { ?>
        <input type="hidden" name="setting[facebook_login_active][current]" <?php if (!empty($data->curr_facebook_login_active)) echo ' value="' . $data->curr_facebook_login_active . '"'; ?>>
        <input type="hidden" name="setting[facebook_login_active][bool]" value="TRUE">
        <div class="csc-row">
          <div class="csc-col csc-col8">
            <p class="cs-body1 cs-mt-0">Enable Facebook Login <i class="far fa-question-circle" data-tippy-content="Setting this will allow users to login with their Facebook account"></i></p>
          </div>
          <div class="csc-col csc-col4 cs-text-right">
            <div class="csc-switch">
              <label>
                No
                <input type="checkbox" name="setting[facebook_login_active][set]" tabindex="4" <?php if (!empty($data->set_facebook_login_active) && $data->set_facebook_login_active) echo 'checked'; ?>>
                <span class="csc-lever"></span>
                Yes
              </label>
            </div>
          </div>
        </div>
      <?php } ?>
    </fieldset>
  </section>
  <section class="csc-container cs-mb-3 cs-p-3">
    <fieldset>
      <legend>TextaHQ Settings <small><i class="fas fa-info-circle"></i> <a href="https://www.textahq.com/developers/" target="_blank">TextaHQ developer docs</a></small></legend>
      <input type="hidden" name="setting[texta_hq_key][current]" <?php if (!empty($data->curr_texta_hq_key)) echo ' value="' . $data->curr_texta_hq_key . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="text" name="setting[texta_hq_key][set]" id="texta_hq_key" tabindex="5" <?php if (!empty($data->set_texta_hq_key)) echo ' value="' . $data->set_texta_hq_key . '"'; ?> autocapitalize="off">
          <label for="texta_hq_key" data-tippy-content="Your TextaHQ key be obtained from your TextaHQ API Credentials page.<br>Once this has a value and your developer has setup the integration you will be able to enable TextaHQ on your site.">TextaHQ Key <i class="far fa-question-circle"></i></label>
        </div>
      </div>
      <?php if (!empty($data->curr_texta_hq_key)) { ?>
        <input type="hidden" name="setting[texta_hq_active][current]" <?php if (!empty($data->curr_texta_hq_active)) echo ' value="' . $data->curr_texta_hq_active . '"'; ?>>
        <input type="hidden" name="setting[texta_hq_active][bool]" value="TRUE">
        <div class="csc-row">
          <div class="csc-col csc-col8">
            <p class="cs-body1 cs-mt-0">Enable TextaHQ <i class="far fa-question-circle" data-tippy-content="Setting this will allow you to use TextaHQ with your site"></i></p>
          </div>
          <div class="csc-col csc-col4 cs-text-right">
            <div class="csc-switch">
              <label>
                No
                <input type="checkbox" name="setting[texta_hq_active][set]" tabindex="6" <?php if (!empty($data->set_texta_hq_active) && $data->set_texta_hq_active) echo 'checked'; ?>>
                <span class="csc-lever"></span>
                Yes
              </label>
            </div>
          </div>
        </div>
      <?php } ?>
    </fieldset>
  </section>
  <section class="csc-container cs-mb-3 cs-p-3">
    <fieldset>
      <legend>Font Awesome Settings <small><i class="fas fa-info-circle"></i> <a href="https://fontawesome.com/kits" target="_blank">Font Awesome Kits</a></small></legend>
      <input type="hidden" name="setting[font_awesome_kit_url][current]" <?php if (!empty($data->curr_font_awesome_kit_url)) echo ' value="' . $data->curr_font_awesome_kit_url . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="url" name="setting[font_awesome_kit_url][set]" id="font_awesome_kit_url" tabindex="8" <?php if (!empty($data->set_font_awesome_kit_url)) echo ' value="' . $data->set_font_awesome_kit_url . '"'; ?> autocapitalize="off">
          <label for="font_awesome_kit_url" data-tippy-content="Your Font Awesome kit url can be obtained from your sites kit dashboard">Font Awesome Kit URL <i class="far fa-question-circle"></i></label>
        </div>
      </div>
    </fieldset>
  </section>
  <section class="csc-container cs-mb-3 cs-p-3">
    <fieldset>
      <legend>Analytics Settings</legend>
      <input type="hidden" name="setting[analytics_code][current]" <?php if (!empty($data->curr_analytics_code)) echo ' value="' . $data->curr_analytics_code . '"'; ?>>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <textarea data-autoresize name="setting[analytics_code][set]" id="analytics_code" class="csc-textarea" rows="1" tabindex="8"><?php if (!empty($data->set_analytics_code)) echo $data->set_analytics_code; ?></textarea>
          <label for="analytics_code" data-tippy-content="Paste your analytics code from Google or Bing here">Analytics Code <i class="far fa-question-circle"></i></label>
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
        <button type="submit" name="action" tabindex="9" value="save" class="csc-btn csc-btn--success">Save <i class="fas fa-save csc-bi-right"></i></button>
      </div>
    </div>
    <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
  </section>
</form>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>