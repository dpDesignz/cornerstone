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
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_site_url('apple-touch-icon.png'); ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_site_url('favicon-32x32.png'); ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_site_url('favicon-16x16.png'); ?>">
  <link rel="manifest" href="<?php echo get_site_url('site.webmanifest'); ?>">
  <link rel="mask-icon" href="<?php echo get_site_url('safari-pinned-tab.svg'); ?>" color="#d13f2d">
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
    <script src="<?php echo $fontAwesomeURL; ?>" crossorigin="anonymous"></script>
  <?php } ?>
  <!-- Chosen ~ https://harvesthq.github.io/chosen/ -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
  <!-- Site Styling -->
  <link href="<?php echo get_site_url('css/cornerstone.css?' . trim(CS_VERSION)); ?>" rel="stylesheet" type="text/css">
  <?php
  // Output custom main.css file if it exists
  if (file_exists(get_public_path('css/main.css'))) : ?>
    <link href="<?php echo get_site_url('css/main.css?v=' . str_replace(' ', '', trim($option->get('site_version')))); ?>" rel="stylesheet" type="text/css">
  <?php endif; ?>
  <!-- SCRIPTS -->
  <!-- jQuery library -->
  <script src="<?php echo get_site_url('js/vendor/modernizr-3.6.0.min.js'); ?>" async></script>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous" async></script>
  <script>
    window.jQuery || document.write('<script src="<?php echo get_site_url('js/vendor/jquery-3.3.1.min.js'); ?>"><\/script>')
  </script>
  <?php
  // Output any page specific extras if it exists
  if (!empty($pageHeadExtras)) {
    echo $pageHeadExtras;
  } ?>
</head>

<body <?php if (!empty($pageBodyClassID)) {
        echo $pageBodyClassID;
      } ?>>
  <!--[if lte IE 9]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->