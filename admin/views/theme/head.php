<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- Primary Meta Tags ~ https://metatags.io/ -->
  <title><?php echo $pageMetaTitle; ?></title>
  <meta name="description" content="<?php echo $pageMetaDescription; ?>">
  <link rel="canonical" href="<?php echo $pageMetaCanonical; ?>">
  <!-- Open Graph / Facebook ~ https://ogp.me/ -->
  <meta property="og:type" content="<?php echo $pageMetaType; ?>">
  <meta property="og:title" content="<?php echo $pageMetaTitle; ?>">
  <meta property="og:description" content="<?php echo $pageMetaDescription; ?>">
  <meta property="og:image" content="<?php echo $pageMetaImage; ?>">
  <meta property="og:url" content="<?php echo $pageMetaCanonical; ?>">
  <!-- Add Additional Open Graph / Facebook Info -->
  <meta property="og:site_name" content="<?php echo $data->site_name; ?>">
  <meta property="og:locale" content="en_NZ">
  <!-- Add Icons -->
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_site_url('admin-files/apple-touch-icon.png'); ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_site_url('admin-files/favicon-32x32.png'); ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_site_url('admin-files/favicon-16x16.png'); ?>">
  <link rel="manifest" href="<?php echo get_site_url('admin-files/site.webmanifest'); ?>">
  <link rel="mask-icon" href="<?php echo get_site_url('admin-files/safari-pinned-tab.svg'); ?>" color="#d13f2d">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="theme-color" content="#eeeeee">
  <!-- Remove Tap Highlight on Windows Phone IE -->
  <meta name="msapplication-tap-highlight" content="no" />
  <!--[if IE]>
    <script type="text/javascript" src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <!-- CSS -->
  <!-- Material Icons ~ https://material.io/tools/icons/ -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!-- Fontawesome ~ https://fontawesome.com/icons -->
  <?php
  $fontAwesomeURL = $option->get('font_awesome_kit_url');
  if (!empty($fontAwesomeURL)) { ?>
    <script src="<?= $fontAwesomeURL; ?>" crossorigin="anonymous"></script>
  <?php } ?>
  <!-- Toastify ~ https://apvarun.github.io/toastify-js/ -->
  <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <?php if (!empty($loadScripts) && is_array($loadScripts)) :
    if (in_array("chosen", $loadScripts)) { ?>
      <!-- Chosen ~ https://harvesthq.github.io/chosen/ -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
    <?php }
    if (in_array("trumbowyg", $loadScripts)) { ?>
      <!-- Trumbowyg ~ https://alex-d.github.io/Trumbowyg/ -->
      <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.19.1/ui/trumbowyg.min.css" integrity="sha256-iS3knajmo8cvwnS0yrVDpNnCboUEwZMJ6mVBEW1VcSA=" crossorigin="anonymous" />
  <?php }
  endif; ?>
  <!-- Perfect Scrollbar ~ https://github.com/mdbootstrap/perfect-scrollbar -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.4.0/css/perfect-scrollbar.min.css">
  <!-- Site Styling -->
  <link href="<?php echo get_site_url('css/cornerstone.css?v=' . str_replace(' ', '', trim(CS_VERSION))); ?>" rel="stylesheet">
  <link href="<?php echo get_site_url('admin-files/css/cs-admin.css?v=' . str_replace(' ', '', trim(CS_VERSION))); ?>" rel="stylesheet">
  <?php
  // Output custom admin.css file if it exists
  if (file_exists(get_public_path('admin-files/css/admin.css'))) : ?>
    <link href="<?php echo get_site_url('admin-files/css/admin.css?v=' . str_replace(' ', '', trim($option->get('site_version')))); ?>" rel="stylesheet" type="text/css">
  <?php endif; ?>
  <!-- SCRIPTS -->
  <!-- jQuery library -->
  <script src="<?php echo get_site_url('js/vendor/modernizr-3.6.0.min.js'); ?>" async></script>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script>
    window.jQuery || document.write('<script src="<?php echo get_site_url('js/vendor/jquery-3.3.1.min.js'); ?>"><\/script>')
  </script>
  <?php
  // Output any page specific extras if it exists
  if (!empty($pageHeadExtras)) {
    echo $pageHeadExtras;
  } ?>
</head>

<?php if (!isset($hideThemeHeader) || !$hideThemeHeader) : ?>

  <body <?= (!empty($pageID)) ? 'id="' . $pageID . '"' : ''; ?> class="cs-page cs-admin cs-components<?= (!empty($_COOKIE['csasbs']) && $_COOKIE['csasbs']) ? ' sidebar__collapsed' : ''; ?>">
    <!--[if lte IE 9]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->
  <?php endif; ?>