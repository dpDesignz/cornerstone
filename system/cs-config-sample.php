<?php

/**
 * The base configurations of the Cornerstone Framework.
 *
 * This file has the following configurations:
 * - Directories
 * - Database settings
 *
 * @package Cornerstone
 */

// Start buffering (this is for custom error handling)
ob_start();

// DIRECTORIES
define('DIR_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR); // Absolute path to base directory for Cornerstone.
define('DIR_PUBLIC', DIR_ROOT . 'public' . DIRECTORY_SEPARATOR); // Absolute path to public directory for Cornerstone.
define('DIR_SYSTEM', DIR_ROOT . 'system' . DIRECTORY_SEPARATOR); // Path to the system folder.
define('DIR_ADMIN', DIR_ROOT . 'admin' . DIRECTORY_SEPARATOR); // Path to the admin folder.
define('DIR_CS', DIR_SYSTEM . 'cornerstone' . DIRECTORY_SEPARATOR); // Path to the Cornerstone folder.
define('DIR_HELPERS', DIR_SYSTEM . 'helpers' . DIRECTORY_SEPARATOR); // Path to the Cornerstone "helpers" folder.
define('ALLOWED_SUBFOLDERS', ['admin' => 'common/index', 'account' => 'common/index', 'api' => 'connect/interface']); // List of allowed sub-folders and their default controllers/methods for the core class

// ** Database settings - You can obtain this information from your web-host ** //
defined('DB_HOSTNAME') or define('DB_HOSTNAME', 'localhost'); // Database Host Name
defined('DB_NAME') or define('DB_NAME', 'database_name_here'); // Database Name
defined('DB_USER') or define('DB_USER', 'database_user_here'); // Database Username
defined('DB_PASSWORD') or define('DB_PASSWORD', 'database_password_here'); // Database Password
defined('DB_CHARSET') or define('DB_CHARSET', 'utf8mb4'); // Database Charset
defined('DB_PREFIX') or define('DB_PREFIX', 'cs_'); // Database Prefix
defined('EZSQL_TYPE') or define('EZSQL_TYPE', 'pdo'); // ezSQL Engine Type (mysqli, pgsql, sqlsrv, sqlite3, or pdo)

// Uncomment if wanting to use the filp/whoops error logging over the cornerstone error logging
// NOTE: This requires the filp/whoops package to be installed to use
$useFilpWhoops = TRUE;

// Set up the Cornerstone Framework.
require_once(DIR_SYSTEM . 'bootstrap.php');
