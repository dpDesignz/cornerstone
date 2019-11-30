<?php
  // Load Config File
  if (is_file('../system/cs-config.php')) {
    require_once('../system/cs-config.php');
  }

  // Redirect to Installer if config didn't load
  if (!defined('DIR_PUBLIC')) {
    header('Location: install/index.php');
    exit;
  }

  // Init Core Library
  $init = new Core;