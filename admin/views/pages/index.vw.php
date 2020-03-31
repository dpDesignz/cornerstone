<?php

/**
 * Pages Index File
 *
 * @package Cornerstone
 * @subpackage Mission Equine
 */

// Set the meta/og information for the page
$pageMetaTitle = "Pages | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " page list.";
$pageMetaImage = get_site_url('img/site_ogp_image.png');
$pageMetaCanonical = get_site_url('admin/pages');
$pageMetaType = "website";

// Set any page injected values
$pageHeadExtras = '<style>
  #filter__status a.active {font-weight: bold; color: var(--font-color);}
</style>';
$pageFooterExtras = '';
$currentNav = 'cms';
$currentSubNav = $currentNav . '/pages';

// Load html head
require(get_theme_path('head.php', 'admin'));
// Load html layout
require(get_theme_path('layout.php', 'admin')); ?>

<div class="csc-row cs-mt-3">
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
<?php if (!empty($data->noData) && $data->noData) { ?>
  <div id="no-index-data" class="csc-row">
    <div class="csc-col csc-col12">
      <h1>Add a page</h1>
      <p id="no-index-data__tag">Add a page to display on your site</p>
      <p id="no-index-data__btn"><a class="csc-btn tooltip" href="<?php echo get_site_url('admin/pages/add'); ?>" title="Add a new page"><i class="material-icons csc-bi-left">add</i> Add page</a></p>
      <span id="no-index-data__icon"><i class="far fa-file"></i></span>
    </div>
  </div>
<?php } else { ?>
  <div class="csc-row">
    <section class="csc-col csc-col12 csc-col--md6 cs-mb-md-3">
      <h1 class="cs-mb-0">Pages<?php if (!empty($data->search)) { ?> <small><em>Search for: <?php echo $data->search; ?></em></small><?php } ?></h1>
      <p class="cs-caption cs-my-0">
        <span id="filter__status">
          <a <?php echo (empty($data->filterStatus)) ? 'class="active" ' : ''; ?> href="<?php echo get_site_url('admin/pages'); ?>">All</a<> | <a <?php echo (!empty($data->filterStatus) && strtolower($data->filterStatus) === 'published') ? 'class="active" ' : ''; ?>href="<?php echo get_site_url('admin/pages/status/published'); ?>">Published</a> | <a <?php echo (!empty($data->filterStatus) && strtolower($data->filterStatus) === 'draft') ? 'class="active" ' : ''; ?>href="<?php echo get_site_url('admin/pages/status/draft'); ?>">Draft</a>
        </span>
        <?php if ($data->showFilter) { ?> | Filtered Results: <?php echo (!empty($data->filterData)) ? $data->filterData : ''; ?><?php } ?></p>
    </section>
    <section class="csc-col csc-col12 csc-col--md6 cs-text-center cs-text-right-md csc-col--ga-middle">
      <p class="cs-mt-0 cs-mt-md-3">
        <a class="csc-btn--small" href="<?php echo get_site_url('admin/pages/add'); ?>" title="Add a new page"><i class="material-icons csc-bi">add</i> Add</a>
      </p>
    </section>
  </div>
  <?php flashMsg('admin_pages'); ?>
  <div class="csc-row csc-container">
    <div class="csc-col csc-col12 csc-data-table">
      <section class="csc-data-table__header cs-pb-0">
        <form class="csc-data-table__search" action="">
          <label for="csdt-search"><i class="fas fa-search"></i></label>
          <input type="text" name="search" id="csdt-search" tabindex="1" <?php if (!empty($data->search)) echo ' value="' . $data->search . '"'; ?>>
          <button type="submit" tabindex="2" value="search" class="csc-btn csc-btn--outlined csc-btn--tiny">Search</button>
          <?php echo (!empty($data->search)) ? '<a class="csc-btn csc-btn--outlined csc-btn--orange csc-btn--tiny" href="' . get_site_url('admin/pages') . '">Clear Search</a>' : ''; ?>
        </form>
      </section>
      <section id="pages-list" class="csc-data-table__table">
        <table>
          <thead class="csc-table-header">
            <tr>
              <th><span class="csc-table-header__title<?php echo ($sort_response = check_sort_item('title', $data->defaultSort)) ? ' csc-table-header__title--active  csc-table-header__title--' . $sort_response->dir . '" aria-sort="' . $sort_response->dir : ''; ?>"><a class="tooltip" href="<?php echo get_site_url(get_sort_url('title')); ?>" title="Sort by Title">Title <i class="fas fa-long-arrow-alt-down direction-icon"></i></a></span></th>
              <th><span class="csc-table-header__title<?php echo ($sort_response = check_sort_item('creator', $data->defaultSort)) ? ' csc-table-header__title--active  csc-table-header__title--' . $sort_response->dir . '" aria-sort="' . $sort_response->dir : ''; ?>"><a class="tooltip" href="<?php echo get_site_url(get_sort_url('creator')); ?>" title="Sort by Creator">Creator <i class="fas fa-long-arrow-alt-down direction-icon"></i></a></span></th>
              <th><span class="csc-table-header__title<?php echo ($sort_response = check_sort_item('section', $data->defaultSort)) ? ' csc-table-header__title--active  csc-table-header__title--' . $sort_response->dir . '" aria-sort="' . $sort_response->dir : ''; ?>"><a class="tooltip" href="<?php echo get_site_url(get_sort_url('section')); ?>" title="Sort by Section">Section <i class="fas fa-long-arrow-alt-down direction-icon"></i></a></span></th>
              <th><span class="csc-table-header__title<?php echo ($sort_response = check_sort_item('updated', $data->defaultSort)) ? ' csc-table-header__title--active  csc-table-header__title--' . $sort_response->dir . '" aria-sort="' . $sort_response->dir : ''; ?>"><a class="tooltip" href="<?php echo get_site_url(get_sort_url('updated')); ?>" title="Sort by Last Updated">Date <i class="fas fa-long-arrow-alt-down direction-icon"></i></a></span></th>
            </tr>
          </thead>
          <tbody class="csc-table-body csc-table-body--zebra">
            <?php echo $data->dataListOut; ?>
          </tbody>
        </table>
      </section>
      <section class="csc-data-table__footer">
        <p></p>
        <span class="csc-data-table__footer--end">
          <?php echo $data->pagination; ?>
        </span>
      </section>
    </div>
  </div>
<?php } ?>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>