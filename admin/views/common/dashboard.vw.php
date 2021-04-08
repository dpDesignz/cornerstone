<?php

/**
 * The Admin Dashboard File
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Admin Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = "Dashboard | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " admin dashboard.";
$pageMetaImage = get_site_url('admin-files/img/site_logo.png');
$pageMetaCanonical = get_site_url('admin/dashboard');
$pageMetaType = "website";

// Set any page injected values
$pageHeadExtras = '';
$pageFooterExtras = '';
$currentNav = 'dashboard';

// Load html head
require(get_theme_path('head.php', 'admin'));
// Load html layout
require(get_theme_path('layout.php', 'admin')); ?>

<h1>Dashboard</h1>
<p>This is the admin dashboard. Sorry, it's not quite ready.</p>
<p>Please come back soon to see your awesome new dashboard finished!</p>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>