<?php

/**
 * Set session data so that logins work properly over all browsers
 * This should fix login errors some users can face
 * More info can be found at {@link https://www.php.net/manual/en/session.security.ini.php the php user manual} and {@link https://www.phparch.com/2018/01/php-sessions-in-depth/}
 * @package Cornerstone
 */

# PREVENTING SESSION HIJACKING
# Prevents javascript XSS attacks aimed to steal the session ID
ini_set('session.cookie_httponly', 1);
# Make sure the cookie lifetime is set to '0'
ini_set('session.cookie_lifetime', 0);
# Adds entropy into the randomization of the session ID, as PHP's random number
# generator has some known flaws
ini_set('session.entropy_file', '/dev/urandom');
# Uses a strong hash
ini_set('session.hash_function', 'whirlpool');
# Set the session save location (best for shared servers)
# Uncomment out the next line if you would like to set a custom path and haven't already set the value in your `php.ini` file.
# ini_set('session.save_path',realpath(ABSPATH . 'tmp' . DIRECTORY_SEPARATOR));
# Note: The folder referenced above must exist for it to work.
# Set the session garbage collection lifetime to custom defined minutes (PHP default is 24 minutes)
ini_set('session.gc_maxlifetime', (int) $option->get("session_expire"));
# Enable session garbage collection with a 1% chance of
# running on each session_start()
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
# Uses a secure connection (HTTPS) if possible
# Allow cookies to be sent over insecure connections if not an HTTPS site
($option->get('site_https')) ? ini_set('session.cookie_secure', 1) : ini_set('session.cookie_secure', false);
# PREVENTING SESSION FIXATION
# Prevent the browser to send an uninitialized session
ini_set('session.use_strict_mode', 1);
# Session ID cannot be passed through URLs
# so only use cookies to store the session id on the client side
ini_set('session.use_only_cookies', 1);
# Force transparent sid support to 0 to prevent exposed ids
ini_set('session.use_trans_sid', 0);
# Set the cookie domain to the root so it can be accessed across sub-domains
ini_set('session.cookie_domain', str_replace('www', '', $option->get('site_url')));
# Set a custom session name
session_name('CSSESSID');
# Load the session class
new CornerstoneSessionHandler();
# Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
# Set the Cornerstone session variable
if (empty($_SESSION['_cs'])) $_SESSION['_cs'] = array();
# Implement manual session time out
if (isset($_SESSION['_cs']['LAST_ACTIVITY']) && (time() - $_SESSION['_cs']['LAST_ACTIVITY'] > (int) $option->get('session_expire'))) {
  // last request was more than the max allowed
  session_unset();     // unset $_SESSION variable for the run-time
  session_destroy();   // destroy session data in storage
  $_SESSION['_cs'] = array(); // Reset the Cornerstone session variable
}
$_SESSION['_cs']['LAST_ACTIVITY'] = time(); // update last activity time stamp
# Implement session re-generating to avoid attacks on sessions
if (!isset($_SESSION['_cs']['CREATED'])) {
  $_SESSION['_cs']['CREATED'] = time();
} else if ((time() - $_SESSION['_cs']['CREATED']) > (int) $option->get('session_expire')) {
  // session started more than the defined amount
  session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
  $_SESSION['_cs']['CREATED'] = time();  // update creation time
}

/**
 * Flash Message
 *
 * @param string $name: Name of flash message for targeting
 * @param string $message: Message content `(optional)`
 * @param string $class: Display class. Defaults to 'success' `(optional)`
 * @param int $icon: Display icon. Defaults to '1' (true) `(optional)`
 *
 * @return string Echos out message ONLY if called, not when setting
 */
function flashMsg($name, $message = '', $class = 'success', int $icon = 1)
{

  // Check if message needs to be set
  if (!empty($message) && empty($_SESSION['_cs']['msg_' . $name])) {

    // Empty the session message if it's already been set and wasn't cleared properly
    if (!empty($_SESSION['_cs']['msg_' . $name])) unset($_SESSION['cs']['msg_' . $name]);
    if (!empty($_SESSION['_cs']['msg_' . $name . '_class'])) unset($_SESSION['cs']['msg_' . $name . '_class']);

    // Set the new message value and class
    $_SESSION['_cs']['msg_' . $name] = $message;
    $_SESSION['_cs']['msg_' . $name . '_class'] = $class;
    $_SESSION['_cs']['msg_' . $name . '_icon'] = $icon;
  } else if (empty($message) && !empty($_SESSION['_cs']['msg_' . $name])) { // Echo out message only if it is set and has been called

    // Get the class if it's set, else return empty
    $class = !empty($_SESSION['_cs']['msg_' . $name . '_class']) ? $_SESSION['_cs']['msg_' . $name . '_class'] : '';

    // Get the icon class if it's set, else return empty
    $icon = (!empty($_SESSION['_cs']['msg_' . $name . '_icon']) && $_SESSION['_cs']['msg_' . $name . '_icon']) ? ' csc-alert--icon' : '';

    // Echo out the message
    echo '<div class="csc-alert csc-alert--' . $class . $icon . '">' . $_SESSION['_cs']['msg_' . $name] . '</div>';

    // Unset the session
    unset($_SESSION['_cs']['msg_' . $name]);
    unset($_SESSION['_cs']['msg_' . $name . '_class']);
    unset($_SESSION['_cs']['msg_' . $name . '_icon']);
  }
}

/**
 * Check Flash Message
 *
 * @param string $name: Name of flash message to check for
 *
 * @return bool Returns TRUE if set, or FALSE if not
 */
function checkFlashMsg($name)
{

  // Check if flash message is set
  if (!empty($name) && !empty($_SESSION['_cs']['msg_' . $name])) {

    // Not empty. Return TRUE
    return TRUE;
  } else { // Flash message not set. Return FALSE.

    // Return FALSE
    return FALSE;
  }
}

/**
 * Check user is logged in
 *
 * (no params)
 *
 * @return bool TRUE if logged in, FALSE if not
 */
function isLoggedInUser()
{
  return (!empty($_SESSION['_cs']['user']['uid'])) ? TRUE : FALSE;
}

/**
 * Page Protection Script - Users
 *
 * @param $logout [optional] Define if you want the script to log the user out instead of returning false. Defaults to FALSE.
 *
 * @return bool TRUE if logged in, FALSE if not (or logout if defined)
 */
function userPageProtect($logout = FALSE)
{

  /* Secure against Session Hijacking by checking user agent */
  if (!empty($_SESSION['_cs']['HTTP_USER_AGENT']) && !password_verify($_SERVER['HTTP_USER_AGENT'], $_SESSION['_cs']['HTTP_USER_AGENT'])) {
    // User Agent doesn't match

    if ($logout) { // Log out user

      redirectTo('admin/logout');
      exit;
    } else { // Return FALSE/

      // Return FALSE
      return FALSE;
    }
  } // User agent verified. Continue.
  // Before we allow sessions, we need to verify the auth token stored in database
  // Check if the user session is already set
  if (empty($_SESSION['_cs']['user'])) {
    // Session is not set

    // Check if cookie is set
    if (!empty($_COOKIE['_cs'])) {
      // Cookie is set

      // Get global $loader
      global $loader;

      // Load userauth model
      $userAuth = $loader->model('cornerstone/userauth', 'admin');

      // Check if the cookie is expired
      if ($userID = $userAuth->checkAuthCookie()) {
        // Cookie is valid

        // Set user ID
        $userAuth->setUID($userID);

        // Authenticate user
        $userAuth->authenticateUser();

        // Return TRUE
        return TRUE;
      } // Cookie is expired. Delete cookie and return FALSE.

      // Delete cookie
      $userAuth->deleteAuthCookie();
    } // Cookie is not set. Return FALSE.

    if ($logout) { // Log out user

      redirectTo('admin/logout');
      exit;
    } else { // Return FALSE/

      // Return FALSE
      return FALSE;
    }
  } else { // User session is set. Return TRUE.

    // Return TRUE
    return TRUE;
  }
}
