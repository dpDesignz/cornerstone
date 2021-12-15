<?php
// Menu array
$adminSidebarCustomMenu = array(
  array(
    'type' => 'separator',
    'text' => 'Cornerstone'
  ), // Cornerstone Separator
  array(
    'type' => 'parent',
    'title' => 'Site Content',
    'text' => 'Site Content',
    'icon' => 'fas fa-desktop',
    'identifier' => 'cms',
    'children' => array(
      array(
        'permission' => 'view_section',
        'identifier' => 'sections',
        'text' => 'Sections',
        'href' => get_site_url('admin/sections')
      ),
      array(
        'permission' => 'view_page',
        'identifier' => 'pages',
        'text' => 'Pages',
        'href' => get_site_url('admin/pages')
      ),
      array(
        'permission' => 'view_files',
        'identifier' => 'filemanager',
        'text' => 'File Manager',
        'href' => get_site_url('admin/files/')
      ),
      array(
        'permission' => 'view_faq',
        'identifier' => 'faq',
        'text' => 'FAQs',
        'href' => get_site_url('admin/faq')
      )
    )
  ) // Site Content Section
);

echo outputAdminMenu($adminSidebarCustomMenu, $currentNav, $currentSubNav | '');
