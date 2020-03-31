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
        'identifier' => 'sections',
        'text' => 'Sections',
        'href' => get_site_url('admin/sections')
      ),
      array(
        'identifier' => 'pages',
        'text' => 'Pages',
        'href' => get_site_url('admin/pages')
      ),
      array(
        'identifier' => 'media',
        'text' => 'Media Manager',
        'href' => 'javascript:alert(\'Coming Soon\');'
      ),
      array(
        'identifier' => 'faq',
        'text' => 'FAQs',
        'href' => 'javascript:alert(\'Coming Soon\');'
      )
    )
  ) // Site Content Section
);

echo outputAdminMenu($adminSidebarCustomMenu, $currentNav, $currentSubNav | '');
