<?php

/**
 * Form Components template file
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = ucfirst($data->component) . " Components | " . SITE_NAME;
$pageMetaDescription = "Cornerstone example " . $data->component . " components page.";
$pageMetaKeywords = "cornerstone, php, framework";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('component/' . $data->component);
$pageMetaType = "website";

// Set any page injected values
$pageHasForm = TRUE;
$pageBodyClassID = 'class="cs-page cs-components"';
$pageHeadExtras = '';
$pageFooterExtras = '';

// Load html head
require(get_theme_path('head.php')); ?>

<!-- End Header ~#~ Start Main -->
<div class="csc-wrapper cs-mt-3" style="max-width: 768px;">
  <form action="" method="GET" id="demo-form" class="csc-form cs-p-3">
    <fieldset>
      <legend>Standard Form Elements</legend>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="text" name="demo_text" id="demo_text" autocapitalize="on" tabindex="1" required>
          <label for="demo_text">Demo Text*</label>
        </div>
      </div>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="tel" name="demo_phone" id="demo_phone" tabindex="2" required>
          <label for="demo_phone">Demo Phone/Tel*</label>
        </div>
      </div>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <input type="email" name="demo_email" id="demo_email" autocapitalize="off" tabindex="3" required>
          <label for="demo_email">Demo Email*</label>
        </div>
      </div>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <p><label><input type="checkbox" name="demo_checkbox" id="demo_checkbox" tabindex="4"><span>Demo Checkbox</span></label></p>
        </div>
      </div>
      <div class="csc-row cs-mb-3">
        <div class="csc-col csc-col12">
          <p class="cs-body2">Head to <a href="<?php echo get_site_url('components/chosen'); ?>">Chosen</a> or <a href="<?php echo get_site_url('components/select2'); ?>">Select2</a> pages for dropdown examples</p>
        </div>
      </div>
    </fieldset>
    <fieldset>
      <legend>Special/Styled Form Elements</legend>
      <div class="csc-row">
        <div class="csc-col csc-col12 csc-input-field">
          <div class="csc-switch">
            <label>
              Off
              <input type="checkbox">
              <span class="csc-lever"></span>
              On
            </label>
          </div>
        </div>
      </div>
    </fieldset>
    <div class="csc-row csc-row--no-gap">
      <div class="csc-col csc-col12 csc-col--md6 cs-my-1 cs-mb-3"><a href="<?php echo get_site_url('components'); ?>" class="csc-btn--flat"><span>Cancel</span></a></div>
      <div class="csc-col csc-col12 cs-text-right csc-col--md6 cs-my-1 cs-mb-3">
        <button type="submit" name="action" tabindex="5" value="save" class="csc-btn csc-btn--success">Save <i class="fas fa-save csc-bi-right"></i></button>
      </div>
    </div>
    <div class="csc-row csc-row--no-gap">
      <div class="csc-col csc-col12">
        <p class="cs-caption cs-text-grey cs-text-center cs-my-0"><em>*notes a required field</em></p>
      </div>
    </div>
  </form>
</div>
<!-- End Main ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>