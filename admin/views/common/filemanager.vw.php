<?php

/**
 * The Admin File Manager File
 *
 * @package Cornerstone
 * @subpackage Core Cornerstone Admin Theme
 */

// Set the meta/og information for the page
$pageMetaTitle = "File Manager | " . $data->site_name . " Admin";
$pageMetaDescription = $data->site_name . " file manager.";
$pageMetaImage = get_site_url('admin-files/img/site_logo.png');
$pageMetaCanonical = get_site_url('admin/files/');
$pageMetaType = "website";

// Set any page injected values
$pageHeadExtras = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css" />';
$pageFooterExtras = '';
$currentNav = 'filemanager';

// Load html head
require(get_theme_path('head.php', 'admin'));
// Load html layout
require(get_theme_path('layout.php', 'admin')); ?>

<h1>File Manager</h1>
<?php flashMsg('admin_filemanager'); ?>

<?php
// Load html footer
require(get_theme_path('footer.php', 'admin')); ?>