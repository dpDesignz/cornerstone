<?php

/**
 * Page Menu template file
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = ucfirst($data->component) . " Component | " . $data->site_name;
$pageMetaDescription = "Cornerstone example " . $data->component . " component page.";
$pageMetaKeywords = "cornerstone, php, framework";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('component/' . $data->component);
$pageMetaType = "website";

// Set any page injected values
$pageHasForm = TRUE;
$pageBodyClassID = 'class="cs-page cs-components"';
$pageHeadExtras = '<style>
  .csc-wrapper {min-height: 100vh;}
</style>';
$pageFooterExtras = '';

// Load html head
require(get_theme_path('head.php')); ?>

<!-- End Header ~#~ Start Main -->
<div class="csc-wrapper cs-mt-3">
  <section>
    <nav class="csc-tabs">
      <ol>
        <li><button class="csc-tab" title="Item 1" data-ref="item-1">Item 1</button></li>
        <li><button class="csc-tab csc-tab--active" title="Item 2" data-ref="item-2">Item 2</button></li>
        <li><button class="csc-tab" title="Item 3" data-ref="item-3">Item 3</button></li>
      </ol>
    </nav>
  </section>
  <section class="csc-container cs-p-3">
    <article id="tab__item-1" class="csc-tab__content">
      <header class="csc-tab__content__header">
        <h3>Item 1</h3>
        <p><a class="csc-btn--tiny" href="#" data-tippy-content="Add new"><i class="fas fa-plus csc-bi-left"></i> Add New</a></p>
      </header>
      <section>
        <p>This is some content</p>
      </section>
    </article>
    <article id="tab__item-2" class="csc-tab__content">
      <header class="csc-tab__content__header">
        <h3>Item 2</h3>
        <p></p>
      </header>
      <section>
        <p>This is a second tab</p>
      </section>
    </article>
    <article id="tab__item-3" class="csc-tab__content">
      <header class="csc-tab__content__header">
        <h3>Item 3</h3>
        <p></p>
      </header>
      <section>
        <p>This is the final tab</p>
      </section>
    </article>
  </section>
</div>
<!-- End Main ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>