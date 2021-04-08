<?php
// Load Config File
if (file_exists('../system/cs-config.php')) {
  require_once('../system/cs-config.php');
}

// Redirect to Installer if config didn't load
if (!file_exists('../system/cs-config.php') || !defined('DIR_PUBLIC')) {
  header('Location: ./install/');
  exit;
}

// Init Core Library
$init = new Core($registry);
