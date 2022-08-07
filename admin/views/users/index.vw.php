<?php

/**
 * The Admin Users Index File
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Admin
 */

// Set the meta/og information for the page
$pageMetaTitle = "Users | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " admin all users.";
$pageMetaImage = get_site_url('img/cornerstone_framework_logo_white.png');
$pageMetaCanonical = get_site_url('admin/users');
$pageMetaType = "website";

// Set any page injected values
$pageHeadExtras = '';
$pageFooterExtras = '';
$currentNav = 'users';
$currentSubNav = $currentNav . '/index';

// Load html head
require(get_theme_path('head.php', 'admin'));
// Load html layout
require(get_theme_path('layout.php', 'admin')); ?>

<?= (!empty($data->breadcrumbs)) ? outputBreadcrumbs((object) $data->breadcrumbs) : ''; // Output breadcrumbs
?>
<?php if (!empty($data->noData) && $data->noData) { ?>
  <div id="no-index-data" class="csc-row">
    <div class="csc-col csc-col12">
      <h1>Add a user</h1>
      <p id="no-index-data__tag">Add a user to display on your site</p>
      <p id="no-index-data__btn"><a class="csc-btn" href="<?php echo get_site_url('admin/users/add'); ?>" data-tippy-content="Add a user"><i class="material-icons csc-bi-left">add</i> Add user</a></p>
      <span id="no-index-data__icon"><i class="fas fa-user-plus"></i></span>
    </div>
  </div>
<?php } else { ?>
  <div class="csc-row">
    <section class="csc-col csc-col12 csc-col--md6 cs-mb-md-3">
      <h1 class="cs-mb-0">Users<?php if (!empty($data->search)) { ?> <small><em>Search for: <?php echo $data->search; ?></em></small><?php } ?></h1>
      <p class="cs-caption cs-my-0">
        <?php if ($data->showFilter) { ?>Filtered Results: <?php echo (!empty($data->filterData)) ? $data->filterData : ''; ?><?php } ?></p>
    </section>
    <section class="csc-col csc-col12 csc-col--md6 cs-text-center cs-text-right-md csc-col--ga-middle">
      <p class="cs-mt-0 cs-mt-md-3">
        <a class="csc-btn--small" href="<?php echo get_site_url('admin/users/add'); ?>" title="Add a user"><i class="material-icons csc-bi-left">add</i> Add</a>
      </p>
    </section>
  </div>
  <?php flashMsg('admin_users'); ?>
  <div class="csc-row csc-container">
    <div class="csc-col csc-col12 csc-data-table">
      <section class="csc-data-table__header cs-pb-0">
        <form class="csc-data-table__search" action="">
          <label for="csdt-search"><i class="fas fa-search"></i></label>
          <input type="text" name="search" id="csdt-search" tabindex="1" <?php if (!empty($data->search)) echo ' value="' . $data->search . '"'; ?>>
          <button type="submit" tabindex="2" value="search" class="csc-btn csc-btn--outlined csc-btn--tiny">Search</button>
          <?php echo (!empty($data->search)) ? '<a class="csc-btn csc-btn--outlined csc-btn--orange csc-btn--tiny" href="' . get_site_url('admin/users') . '">Clear Search</a>' : ''; ?>
        </form>
      </section>
      <section id="users-list" class="csc-data-table__table">
        <table>
          <thead class="csc-table-header">
            <tr>
              <th><span class="csc-table-header__title<?php echo ($sort_response = check_sort_item('username', $data->defaultSort)) ? ' csc-table-header__title--active  csc-table-header__title--' . $sort_response->dir . '" aria-sort="' . $sort_response->dir : ''; ?>"><a href="<?php echo get_site_url(get_sort_url('username')); ?>" data-tippy-content="Sort by Username">Username <i class="fas fa-long-arrow-alt-down direction-icon"></i></a></span></th>
              <th><span class="csc-table-header__title<?php echo ($sort_response = check_sort_item('name', $data->defaultSort)) ? ' csc-table-header__title--active  csc-table-header__title--' . $sort_response->dir . '" aria-sort="' . $sort_response->dir : ''; ?>"><a href="<?php echo get_site_url(get_sort_url('name')); ?>" data-tippy-content="Sort by Name">Name <i class="fas fa-long-arrow-alt-down direction-icon"></i></a></span></th>
              <th><span class="csc-table-header__title<?php echo ($sort_response = check_sort_item('email', $data->defaultSort)) ? ' csc-table-header__title--active  csc-table-header__title--' . $sort_response->dir . '" aria-sort="' . $sort_response->dir : ''; ?>"><a href="<?php echo get_site_url(get_sort_url('email')); ?>" data-tippy-content="Sort by Email">Email <i class="fas fa-long-arrow-alt-down direction-icon"></i></a></span></th>
              <th><span class="csc-table-header__title<?php echo ($sort_response = check_sort_item('role', $data->defaultSort)) ? ' csc-table-header__title--active  csc-table-header__title--' . $sort_response->dir . '" aria-sort="' . $sort_response->dir : ''; ?>"><a href="<?php echo get_site_url(get_sort_url('role')); ?>" data-tippy-content="Sort by User Role">Role <i class="fas fa-long-arrow-alt-down direction-icon"></i></a></span></th>
              <th><span class="csc-table-header__title<?php echo ($sort_response = check_sort_item('login', $data->defaultSort)) ? ' csc-table-header__title--active  csc-table-header__title--' . $sort_response->dir . '" aria-sort="' . $sort_response->dir : ''; ?>"><a href="<?php echo get_site_url(get_sort_url('login')); ?>" data-tippy-content="Sort by Last Login">Last Login <i class="fas fa-long-arrow-alt-down direction-icon"></i></a></span></th>
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