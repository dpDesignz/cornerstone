<?php

/**
 * Retrieves and creates the cs-config.php file.
 *
 * The permissions for the base directory must allow for writing files in order
 * for the cs-config.php to be created using this page.
 *
 * @package Cornerstone
 * @subpackage Installation
 */

/**
 * Disable error reporting
 *
 * Set this to error_reporting( -1 ) for debugging
 */
error_reporting(0);

if (!defined('DIR_ROOT')) {
  define('DIR_ROOT', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR); // Absolute path to base directory for Cornerstone.
  define('DIR_SYSTEM', DIR_ROOT . 'system' . DIRECTORY_SEPARATOR); // Path to the system folder.
}

// Get cs-config-sample.php
if (file_exists(DIR_SYSTEM . 'cs-config-sample.php')) {
  $config_file = file(DIR_SYSTEM . 'cs-config-sample.php');
} else {
  echo 'Sorry, I need a cs-config-sample.php file to work from. Please re-upload this file to your Cornerstone system folder.';
  exit;
}

// Get the install step
$installStep = isset($_GET['step']) ? (int) $_GET['step'] : 0;

// Check if cs-config.php is already created.
if ($installStep !== 3 && file_exists(DIR_SYSTEM . 'cs-config.php')) {
  echo 'The cs-config.php file already exists. If you need to reset any of the configuration items in this file, please delete it first or manually edit the file (advanced users).';
  exit;
}
// Get any errors
$errorNo = isset($_GET['error']) ? (int) $_GET['error'] : 0; ?>
<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="robots" content="noindex,nofollow" />
  <title>Cornerstone Install</title>
  <!-- Site Styling -->
  <link href="/install/normalize.css" rel="stylesheet" type="text/css">
  <link href="/install/install.css" rel="stylesheet" type="text/css">
</head>

<body>
  <!-- Start Header -->
  <header>
    <section id="cs--header__logo">
      <a href="https://github.com/dpDesignz/cornerstone" target="_blank" title="Cornerstone PHP Framework Website"><img src="/img/cornerstone/cornerstone_framework_logo_white.svg" alt="Cornerstone PHP Framework"></a>
    </section>
  </header>
  <!-- End Header ~#~ Start Main -->
  <main>
    <section id="cs--install-container">
      <?php
      // Check current install step
      switch ($installStep) {
        case 0:
        case 1: // Config file
          if (!empty($errorNo)) {
            // Display error
            switch ($errorNo) {
              case 101:
                echo '<p class="error"><span>ERROR:</span> Step 1 hasn\'t been completed.</p>';
                break;
            }
          }
      ?>
          <form action="?step=2" method="POST">
            <p>Enter your database connection details below. If you're not sure what these are, please contact your web host.</p>
            <table class="install-table" role="presentation">
              <tr>
                <th scope="row"><label for="dbhost">Database Host</label></th>
                <td><input name="dbhost" id="dbhost" type="text" aria-describedby="dbhost-desc" size="25" value="localhost" /></td>
                <td id="dbhost-desc">You should be able to get this info from your web host, if <code>localhost</code> doesn't work.</td>
              </tr>
              <tr>
                <th scope="row"><label for="dbname">Database Name</label></th>
                <td><input name="dbname" id="dbname" type="text" aria-describedby="dbname-desc" size="25" value="cornerstone" /></td>
                <td id="dbname-desc">The name of the database you want to use with Cornerstone.</td>
              </tr>
              <tr>
                <th scope="row"><label for="uname">Username</label></th>
                <td><input name="uname" id="uname" type="text" aria-describedby="uname-desc" size="25" value="cornerstone_user" /></td>
                <td id="uname-desc">Your database username.</td>
              </tr>
              <tr>
                <th scope="row"><label for="pwd">Password</label></th>
                <td><input name="pwd" id="pwd" type="text" aria-describedby="pwd-desc" size="25" value="password" autocomplete="off" /></td>
                <td id="pwd-desc">Your database password.</td>
              </tr>
              <tr>
                <th scope="row"><label for="prefix">Table Prefix</label></th>
                <td><input name="prefix" id="prefix" type="text" aria-describedby="prefix-desc" value="cs_" size="25" /></td>
                <td id="prefix-desc">If you want to run multiple installations of Cornerstone in a single database, change this.</td>
              </tr>
              <tr>
                <th scope="row"><label for="connection">Connection Type</label></th>
                <td>
                  <select name="connection" id="connection" aria-describedby="connection-desc" style="width: 100%;">
                    <option value="mysqli">MySQLi</option>
                    <option value="pgsql">pgSQL</option>
                    <option value="sqlsrv">SQLSRV</option>
                    <option value="sqlite3">SQLite3</option>
                    <option value="pdo" selected>PDO</option>
                  </select>
                </td>
                <td id="connection-desc">The <a href="https://github.com/ezSQL/ezsql" target="_blank">ezSQL</a> connection type for your database.</td>
              </tr>
            </table>
            <p class="step"><button name="action" type="submit" value="Next" class="csc-btn">Next</button></p>
          </form>
          <?php
          break;
        case 2: // Database connection
          //Check if page posted and process form if it is
          if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "Next") {

            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $dbhost = trim($_POST['dbhost']);
            $dbname = trim($_POST['dbname']);
            $dbuname  = trim($_POST['uname']);
            $dbpwd    = trim($_POST['pwd']);
            $dbprefix = trim($_POST['prefix']);
            $contype = trim($_POST['connection']);

            // Check prefix isn't empty
            if (empty($dbprefix)) {
              echo '<p class="error"><span>ERROR:</span> "Table Prefix" must not be empty.</p><p><a href="?step=1" onclick="javascript:history.go(-1);return false;" class="csc-btn">Try Again</a></p>';
              exit;
            }
            // Check prefix is valid
            if (preg_match('|[^a-z0-9_]|i', $dbprefix)) {
              echo '<p class="error"><span>ERROR:</span> "Table Prefix" can only contain numbers, letters, and underscores.</p><p><a href="?step=1" onclick="javascript:history.go(-1);return false;" class="csc-btn">Try Again</a></p>';
              exit;
            }
            // Check connection isn't empty
            if (empty($contype)) {
              echo '<p class="error"><span>ERROR:</span> "Connection Type" must be selected.</p><p><a href="?step=1" onclick="javascript:history.go(-1);return false;" class="csc-btn">Try Again</a></p>';
              exit;
            }

            // Test the DB connection.
            define('DB_HOSTNAME', $dbhost); // Database Host Name
            define('DB_NAME', $dbname); // Database Name
            define('DB_USER', $dbuname); // Database Username
            define('DB_PASSWORD', $dbpwd); // Database Password
            define('DB_CHARSET', 'utf8mb4'); // Database Charset
            define('DB_PREFIX', $dbprefix); // Database Prefix
            define('EZSQL_TYPE', $contype); // ezSQL Engine Type (mysqli, pgsql, sqlsrv, sqlite3, or pdo)

            // Load Composer vendor autoload [https://getcomposer.org/] (This is required for ezSQL, PHPMailer, random_compat, and WhichBrowser)
            require_once(DIR_SYSTEM . 'storage' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
            require_once(DIR_SYSTEM . 'cornerstone' . DIRECTORY_SEPARATOR . 'cornerstonedbh.class.php');

            // Re-construct $csdb with these new values.
            unset($csdb);
            $csdb = new CornerstoneDBH;

            // Test connection
            try {
              $this->dbh->connect();
            } catch (\Throwable $th) {
              echo '<p class="error"><span>ERROR:</span> There was an issue with your database details.</p><p><a href="?step=1" onclick="javascript:history.go(-1);return false;" class="csc-btn">Try Again</a></p>';
              exit;
            }
            if (!$this->dbh->isConnected()) {
              echo '<p class="error"><span>ERROR:</span> There was an issue with your database details.</p><p><a href="?step=1" onclick="javascript:history.go(-1);return false;" class="csc-btn">Try Again</a></p>';
              exit;
            }

            // Build the cs-config file
            foreach ($config_file as $line_num => $line) {

              if (!preg_match('/\A^defined\(\s*\'([A-Z_]+)\'\)/', $line, $match)) {
                continue;
              }

              $constant = $match[1];
              $padding  = $match[2];

              switch ($constant) {
                case 'DB_HOSTNAME':
                case 'DB_NAME':
                case 'DB_USER':
                case 'DB_PASSWORD':
                case 'DB_PREFIX':
                case 'EZSQL_TYPE':
                  $config_file[$line_num] = "defined('" . $constant . "') or define('" . $constant . "','" . addcslashes(constant($constant), "\\'") . "');\r\n";
                  break;
              }
            }
            unset($line);

            // Check the system folder is writable
            if (!is_writable(DIR_SYSTEM)) { ?>
              <p>Unable to create the <code>cs-config.php</code> file.</p>
              <p>You can create this file manually and paste the following text into it.</p>
              <?php
              // Set text for copying
              $config_text = '';
              foreach ($config_file as $line) {
                $config_text .= htmlentities($line, ENT_COMPAT, 'UTF-8');
              }
              ?>
              <textarea id="cs-config" cols="98" rows="15" class="code" readonly="readonly"><?php echo $config_text; ?></textarea>
              <p>Once you've completed that, click "Run the install"</p>
              <p class="step"><a href="?step=3" class="csc-btn">Run the install</a></p>
          <?php } else {
              // Create the config file
              $handle = fopen(DIR_SYSTEM . 'cs-config.php', 'w');
              foreach ($config_file as $line) {
                fwrite($handle, $line);
              }
              fclose($handle);
              chmod($path_to_wp_config, 0666);
            }
          } else {
            // Redirect to the first step
            header('Location: /install/?error=101');
            exit;
          }
          ?>
          <h1>Successful connection!</h1>
          <p>Time to...</p>
          <p class="step"><a href="?step=3" class="csc-btn">Run the install</a></p>
        <?php
          break;
        case 3: // Site settings
          // Load Config File
          if (file_exists(DIR_SYSTEM . 'cs-config.php')) {
            require_once(DIR_SYSTEM . 'cs-config.php');
          } else {
            // Redirect to the first step
            header('Location: /install/?error=101');
            exit;
          }

          //Check if page posted and process form if it is
          if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == "Install") {

            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $siteName = trim($_POST['name']);
            $userLogin = trim($_POST['uname']);
            $userPwd   = trim($_POST['pwd']);
            $userEmail = trim($_POST['email']);
            $faKitURL = trim($_POST['fa_kit_url']);

            // Check name isn't empty
            if (empty($siteName)) {
              echo '<p class="error"><span>ERROR:</span> "Site Name" must not be empty.</p><p><a href="?step=1" onclick="javascript:history.go(-1);return false;" class="csc-btn">Try Again</a></p>';
              exit;
            }

            // Check uname isn't empty
            if (empty($userLogin)) {
              echo '<p class="error"><span>ERROR:</span> "Username" must not be empty.</p><p><a href="?step=1" onclick="javascript:history.go(-1);return false;" class="csc-btn">Try Again</a></p>';
              exit;
            }

            // Check uname is valid
            if (preg_match('|[^a-zA-Z0-9_\-.@]|i', $userLogin)) {
              echo '<p class="error"><span>ERROR:</span> "Username" can only have alphanumeric characters, underscores, hyphens, periods, and the @ symbol.</p><p><a href="?step=1" onclick="javascript:history.go(-1);return false;" class="csc-btn">Try Again</a></p>';
              exit;
            }

            // Check pwd isn't empty
            if (empty($userPwd)) {
              echo '<p class="error"><span>ERROR:</span> "Password" must not be empty.</p><p><a href="?step=1" onclick="javascript:history.go(-1);return false;" class="csc-btn">Try Again</a></p>';
              exit;
            }

            // Validate password strength
            $uppercase = preg_match('@[A-Z]@', $userPwd);
            $lowercase = preg_match('@[a-z]@', $userPwd);
            $number    = preg_match('@[0-9]@', $userPwd);

            // Check validation is all ok
            if (empty($userPwd) || !$uppercase || !$lowercase || !$number || strlen($userPwd) < 6 || strlen($userPwd) > 128) {

              // If password not set or doesn't match the requirements, return error
              echo '<p class="error"><span>ERROR:</span> Your password must be at least six characters long and contain at least one upper case letter and one number.</p><p><a href="?step=1" onclick="javascript:history.go(-1);return false;" class="csc-btn">Try Again</a></p>';
              exit;
            }

            // Check email isn't empty
            if (empty($userEmail)) {
              echo '<p class="error"><span>ERROR:</span> "Email Address" must not be empty.</p><p><a href="?step=1" onclick="javascript:history.go(-1);return false;" class="csc-btn">Try Again</a></p>';
              exit;
            }

            // Validate email
            if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
              echo '<p class="error"><span>ERROR:</span> "Email Address" must be valid.</p><p><a href="?step=1" onclick="javascript:history.go(-1);return false;" class="csc-btn">Try Again</a></p>';
              exit;
            }

            // Check fa_kit_url isn't empty
            if (empty($faKitURL)) {
              echo '<p class="error"><span>ERROR:</span> "Font Awesome Kit" must not be empty.</p><p><a href="?step=1" onclick="javascript:history.go(-1);return false;" class="csc-btn">Try Again</a></p>';
              exit;
            }

            // Site URL
            $siteURL = substr($_SERVER['HTTP_REFERER'], 0, strpos($_SERVER['HTTP_REFERER'], "install/"));
            $siteURLBase = preg_replace(
              '#^https?://#',
              '',
              rtrim($siteURL, '/')
            );

            // Install tables into database
            require_once('tables.php');

            // Rename the install folder
            // rename(DIR_SYSTEM . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "install", DIR_SYSTEM . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "install-" . date('Ymd'));

            // Redirect to the main page
            header('Location: ' . $siteURL);
            exit;
          }
        ?>
          <h1>Welcome</h1>
          <p>Welcome to the site settings for your Cornerstone website. Simply fill out the information below and you'll be ready to get started with your new site!</p>
          <h2>Site Information</h2>
          <form action="?step=3" method="POST">
            <table class="install-table" role="presentation">
              <tr>
                <th scope="row"><label for="name">Site Name</label></th>
                <td><input name="name" id="name" type="text" size="25" value="" required /></td>
                <td>The website name shown across your site</td>
              </tr>
              <tr>
                <th scope="row"><label for="uname">Username</label></th>
                <td><input name="uname" id="uname" type="text" size="25" value="admin" required /></td>
                <td>Your master admin username used to log in. Usernames can only have alphanumeric characters, underscores, hyphens, periods, and the @ symbol.</td>
              </tr>
              <tr>
                <th scope="row"><label for="pwd">Password</label></th>
                <td><input name="pwd" id="pwd" type="text" size="25" autocomplete="off" required /></td>
                <td><strong>Important:</strong> You will need this password to log in. Please store it in a secure location.</td>
              </tr>
              <tr>
                <th scope="row"><label for="email">Email Address</label></th>
                <td><input name="email" id="email" type="text" value="" size="25" required /></td>
                <td>Double-check this before continuing. Failing to type this correctly could result in issues when logging in.</td>
              </tr>
              <tr>
                <th scope="row"><label for="fa_kit_url">Font Awesome Kit</label></th>
                <td><input name="fa_kit_url" id="fa_kit_url" type="text" value="" size="25" required /></td>
                <td>You need a <a href="https://fontawesome.com/start" target="_blank">Font Awesome Kit URL</a> for icons to work correctly.</td>
              </tr>
            </table>
            <p>Pressing "Install" will start to finish the site install which may take a few seconds. <strong>Only press the button once.</strong></p>
            <p class="step"><button name="action" type="submit" value="Install" class="csc-btn">Install</button></p>
          </form>
      <?php
          break;
      }
      ?>
    </section>
  </main>
  <!-- End Main ~#~ Start Footer -->
  <footer>
    <p><span><a href="https://github.com/dpDesignz/cornerstone" target="_blank" title="Cornerstone PHP Framework Website">Cornerstone</a></span>&copy; <?php echo date('Y'); ?></p>
  </footer>
</body>