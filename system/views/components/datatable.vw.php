<?php

/**
 * Input Mask template file
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = ucfirst($data->component) . " Component | " . SITE_NAME;
$pageMetaDescription = "Cornerstone example " . $data->component . " component page.";
$pageMetaKeywords = "cornerstone, php, framework";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('component/' . $data->component);
$pageMetaType = "website";

// Set any page injected values
$pageHasForm = TRUE;
$pageBodyClassID = 'class="cs-page cs-components"';
$pageHeadExtras = '<style>
</style>';
$pageFooterExtras = '';

// Load html head
require(get_theme_path('head.php')); ?>

<!-- End Header ~#~ Start Main -->
<div class="csc-wrapper cs-mt-3">
  <div class="csc-row">
    <div class="csc-col csc-col12">
      <div class="csc-data-table">
        <section class="csc-data-table__header--with-actions">
          <div class="csc-data-table__header__title">
            <h6>Table Title</h6>
          </div>
          <div class="csc-data-table__header__actions">
            <a class="csc-btn csc-btn--flat-small" href="#!"><i class="fas fa-ellipsis-v"></i></a>
          </div>
        </section>
        <section class="csc-data-table__table">
          <table>
            <thead class="csc-table-header">
              <tr>
                <th><span class="csc-table-header__title csc-table-header__title--active" aria-sort="desc">Code <i class="fas fa-long-arrow-alt-down direction-icon"></i></span></th>
                <th><span class="csc-table-header__title" aria-sort="asc">Name <i class="fas fa-long-arrow-alt-up direction-icon"></i></span></th>
                <th class="csc-table--right-content"><span class="csc-table-header__title" aria-sort="asc">Cost <i class="fas fa-long-arrow-alt-down direction-icon"></i></span></th>
                <th class="csc-table--right-content"><span class="csc-table-header__title" aria-sort="asc">Retail <i class="fas fa-long-arrow-alt-down direction-icon"></i></span></th>
                <th class="csc-table--right-content"><span class="csc-table-header__title" aria-sort="asc">MAC <i class="fas fa-long-arrow-alt-down direction-icon"></i></span></th>
                <th class="csc-table--right-content"><span class="csc-table-header__title" aria-sort="asc">Available <i class="fas fa-long-arrow-alt-down direction-icon"></i></span></th>
                <th><span class="csc-table-header__title" aria-sort="asc">Status <i class="fas fa-long-arrow-alt-up direction-icon"></i></span></th>
              </tr>
            </thead>
            <tbody class="csc-table-body csc-table-body--zebra">
              <tr>
                <td>14700</td>
                <td>Item Name</td>
                <td class="csc-table--right-content">$400.00</td>
                <td class="csc-table--right-content">$750.00</td>
                <td class="csc-table--right-content">$401.60</td>
                <td class="csc-table--right-content">6</td>
                <td>Active</td>
              </tr>
            </tbody>
          </table>
        </section>
        <section class="csc-data-table__footer">
          <p>This is the footer</p>
          <span>
            <?php echo outputPagination(90); ?>
          </span>
        </section>
      </div>
    </div>
  </div>
  <div class="csc-row cs-mt-3">
    <div class="csc-col csc-col12">
      <div class="csc-data-table">
        <section class="csc-data-table__header--with-actions">
          <form class="csc-data-table__search" action="">
            <label for="csdt-search"><i class="fas fa-search"></i></label>
            <input type="text" name="search" id="csdt-search" tabindex="1" <?php if (!empty($data->search)) echo ' value="' . $data->search . '"'; ?>>
            <button type="submit" tabindex="2" value="search" class="csc-btn csc-btn--outlined csc-btn--tiny">Search</button>
            <a class="csc-btn csc-btn--warning csc-btn--tiny" href="' . get_site_url('admin/products') . '">Clear Search</a>
          </form>
          <div class="csc-data-table__header__actions">
            <a class="csc-btn csc-btn--flat-small" href="#!"><i class="fas fa-ellipsis-v"></i></a>
          </div>
        </section>
        <section class="csc-data-table__table">
          <table>
            <thead class="csc-table-header">
              <tr>
                <th><span class="csc-table-header__title csc-table-header__title--active" aria-sort="desc">Code <i class="fas fa-long-arrow-alt-down direction-icon"></i></span></th>
                <th><span class="csc-table-header__title" aria-sort="asc">Name <i class="fas fa-long-arrow-alt-up direction-icon"></i></span></th>
                <th class="csc-table--right-content"><span class="csc-table-header__title" aria-sort="asc">Cost <i class="fas fa-long-arrow-alt-down direction-icon"></i></span></th>
                <th class="csc-table--right-content"><span class="csc-table-header__title" aria-sort="asc">Retail <i class="fas fa-long-arrow-alt-down direction-icon"></i></span></th>
                <th class="csc-table--right-content"><span class="csc-table-header__title" aria-sort="asc">MAC <i class="fas fa-long-arrow-alt-down direction-icon"></i></span></th>
                <th class="csc-table--right-content"><span class="csc-table-header__title" aria-sort="asc">Available <i class="fas fa-long-arrow-alt-down direction-icon"></i></span></th>
                <th><span class="csc-table-header__title" aria-sort="asc">Status <i class="fas fa-long-arrow-alt-up direction-icon"></i></span></th>
              </tr>
            </thead>
            <tbody class="csc-table-body csc-table-body--zebra">
              <tr>
                <td>14700</td>
                <td>Item Name</td>
                <td class="csc-table--right-content">$400.00</td>
                <td class="csc-table--right-content">$750.00</td>
                <td class="csc-table--right-content">$401.60</td>
                <td class="csc-table--right-content">6</td>
                <td>Active</td>
              </tr>
            </tbody>
          </table>
        </section>
        <section class="csc-data-table__footer">
          <p>This is the footer</p>
          <span>
            <?php echo outputPagination(90); ?>
          </span>
        </section>
      </div>
    </div>
  </div>
</div>
<!-- End Main ~#~ Start Footer -->

<?php
// Load html footer
require(get_theme_path('footer.php')); ?>