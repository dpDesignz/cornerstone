<?php

/**
 * Defines constants and global variables
 * based on autoload options from the `cs_options` table
 *
 * @package Cornerstone
 */

// Load options from `cs_options` table where `autoload` is `true`
$csOptions = new CornerstoneDBH; // Init db connection
foreach ($csOptions->dbh->selecting(DB_PREFIX . 'options', array('option_name', 'option_value'), eq('autoload', '1')) as $row) {
	define(strtoupper($row->option_name), $row->option_value);
}

/**#@+
 * Cornerstone constants
 */
\defined('CS_VERSION') or \define('CS_VERSION', '0.2.24'); // Last updated ~ 2020-04-16
\defined('_DS') or \define('_DS', \DIRECTORY_SEPARATOR);
/**#@-*/

/**#@+
 * Constants for expressing human-readable data sizes in their respective number of bytes.
 */
define('KB_IN_BYTES', 1024);
define('MB_IN_BYTES', 1024 * KB_IN_BYTES);
define('GB_IN_BYTES', 1024 * MB_IN_BYTES);
define('TB_IN_BYTES', 1024 * GB_IN_BYTES);
/**#@-*/

/**#@+
 * Constants for expressing human-readable intervals
 * in their respective number of seconds.
 *
 * Please note that these values are approximate and are provided for convenience.
 * For example, MONTH_IN_SECONDS wrongly assumes every month has 30 days and
 * YEAR_IN_SECONDS does not take leap years into account.
 *
 * If you need more accuracy please consider using the DateTime class (https://secure.php.net/manual/en/class.datetime.php).
 *
 */
define('MINUTE_IN_SECONDS', 60);
define('HOUR_IN_SECONDS',   60 * MINUTE_IN_SECONDS);
define('DAY_IN_SECONDS',    24 * HOUR_IN_SECONDS);
define('WEEK_IN_SECONDS',    7 * DAY_IN_SECONDS);
define('MONTH_IN_SECONDS',  30 * DAY_IN_SECONDS);
define('YEAR_IN_SECONDS',  365 * DAY_IN_SECONDS);
/**#@-*/
