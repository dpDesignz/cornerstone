<?php

/**
 * This code decides what to do when an error occurs on the site
 * based on the `errorLogType` option in the `cs_options` table
 *
 * @package Cornerstone
 */

/**
 * Split `errorLogType` option
 */
$errorLogType = explode(',', ERROR_LOG_TYPE);

/**
 * Error handler, passes flow over the exception logger with new ErrorException.
 */
function log_error($num, $str, $file, $line, $context = null)
{
	log_exception(new ErrorException($str, 0, $num, $file, $line));
}

/**
 * Uncaught exception handler.
 */
function log_exception($e)
{
	global $errorLogType; // Allow function to access $errorLogType
	switch ($errorLogType[1]) {
		case 1: // Email error to `errorsToEmail` and redirect the user
			$errorEmailData = '<p>An error (' . get_class($e) . ') occurred on line <strong>' . $e->getLine() . '</strong> in the <strong>file: ' . $e->getFile() . '.</strong></p><p> ' . $e->getMessage() . ' </p>';
			$errorEmailHeaders = 'From: ' . SITE_FROM_EMAIL . '\r\n';
			$errorEmailHeaders .= 'Subject: Errortype ' . get_class($e) . ' from ' . SITE_NAME . '\r\n';
			$errorEmailHeaders .= 'MIME-Version: 1.0\r\n';
			$errorEmailHeaders .= 'Content-type: text/html; charset=iso-8859-1\r\n';
			error_log($errorEmailData, 1, ERRORS_TO_EMAIL, $errorEmailHeaders);
			header('Refresh:0; url=/', true, 303);
			break;
		case 2: // Print error to screen (Debugging Mode)
			ob_end_clean(); // Erase the buffer
			print '<!doctype html>' . PHP_EOL;
			print '<html lang="en">' . PHP_EOL;
			print '<head>' . PHP_EOL;
			print '<meta charset="utf-8">' . PHP_EOL;
			print '<title>Exception Occured on ' . SITE_NAME . '</title>' . PHP_EOL;
			print '</head><body style="margin: 0; padding: 0; font-family: Verdana, Geneva, sans-serif;">' . PHP_EOL;
			print '<div style="padding: 15px; background-color:#d3dcde; border-left: 5px solid #E81123;">' . PHP_EOL;
			print '<p style="color: #e67e22;">' . get_class($e) . ': ' . $e->getCode() . '</p>' . PHP_EOL;
			print '<p style="color: #4A5459; font-size: 1.8em; margin: 15px 0;">' . $e->getMessage() . '</p>' . PHP_EOL;
			print '</div>' . PHP_EOL;
			print '<div style="padding: 20px; border-left: 5px solid #e67e22; background-color: #ecf0f1;">' . PHP_EOL;
			print '<div style="background-color: #2c3e50;">' . PHP_EOL;
			print '<div style="padding: 8px; background-color: #d35400; color: #FFFFFF;">' . PHP_EOL;
			print '<p style="font-size: 0.9em; margin: 0;"><span style="display: inline-block; padding: 5px; background-color: #c0392b; font-size: 0.9em; color: #ecf0f1; border-radius: 5px;">File:</span> ' . $e->getFile() . ':' . $e->getLine() . '</p>' . PHP_EOL;
			print '</div>' . PHP_EOL;
			print '<div style="padding: 8px;">' . PHP_EOL;
			print '<table style="width: 100%;border: none; border-collapse: collapse;">' . PHP_EOL;
			$f = fopen($e->getFile(), 'r');
			$lineNo = 0;
			$startLine = $e->getLine() - 5;
			$endLine = $e->getLine() + 5;
			while ($line = fgets($f)) {
				$lineNo++;
				if ($lineNo >= $startLine) {
					$highlightLine = ($lineNo == $e->getLine()) ? 'background-color: #364d63;' : '';
					print '<tr><td style="width: 35px; padding: 0 3px 0 0; font-size: 0.9em; color: #95a5a6; vertical-align: top;">' . $lineNo . '</td><td style="background-color: #243342;  padding: 2px 5px; color: #ecf0f1;' . $highlightLine . '"><pre style="margin: 0; font-size: 1.2em; white-space: pre-wrap;">' . htmlspecialchars($line) . '</pre></td></tr>' . PHP_EOL;
				}
				if ($lineNo == $endLine) {
					break;
				}
			}
			fclose($f);
			print '</table>' . PHP_EOL;
			print '</div>' . PHP_EOL;
			print '<div style="padding: 8px; background-color: #34495e;">' . PHP_EOL;
			print '<p style="margin: 0; font-size: 0.9em; color: #bdccdb;">Error on line ' . $e->getLine() . '</p>' . PHP_EOL;
			print '</div>' . PHP_EOL;
			print '</div>' . PHP_EOL;
			print '<p style="margin-bottom: 0; font-size: 0.8em;"><em>If you are not a developer for this website, please notify the webmaster you saw this error at <a href="mailto:' . ERRORS_TO_EMAIL . '?Subject=' . urlencode('I saw an error on the ' . SITE_NAME . ' website') . '&body=File: ' . urlencode($e->getFile()) . '">' . ERRORS_TO_EMAIL . '</a></em></p>' . PHP_EOL;
			print '</div>' . PHP_EOL;
			print '<div style="padding: 20px; border-left: 5px solid #7f8c8d;">' . PHP_EOL;
			print '<h2 style="padding-bottom: 5px; color: #d35400; border-bottom: 1px solid #95a5a6;">Server/Request Data</h2>' . PHP_EOL;
			print '<table style="width: 100%;border: none; border-collapse: collapse; font-size: 0.8em;">' . PHP_EOL;
			foreach ($_SERVER as $key => $value) {
				print '<tr><td style="padding-right: 10px;">' . $key . '</td><td>' . $value . '</td></tr>' . PHP_EOL;
			}
			foreach ($_REQUEST as $key => $value) {
				// Skip line if data contains the phrase "password" or "pwd"
				if (strpos($key, "password") === FALSE && strpos($key, "pwd") === FALSE) {
					print '<tr><td style="padding-right: 10px;">' . $key . '</td><td>' . $value . '</td></tr>' . PHP_EOL;
				}
			}
			print '</table>' . PHP_EOL;
			print '<p style="text-align: center; color: #95a5a6; font-size: 0.7em; font-style: italic;">&copy; ' . date('Y') . ' Cornerstone</p>' . PHP_EOL;
			print '</div>' . PHP_EOL;
			print '</body></html>';
			break;
		default: // Log error in file and die with error
			$dateTimeNow = new DateTime();
			// Check if user information is available
			$userDetails = (!empty($_SESSION['_cs']['user']['uid']) && !empty($_SESSION['_cs']['user']['name'])) ? ' User: ' . $_SESSION['_cs']['user']['uid'] . '::' . $_SESSION['_cs']['user']['name'] . ';' : '';
			$message = $dateTimeNow->format("U") . " [" . $dateTimeNow->format("d-M-Y H:i:s e") . "] Type: " . get_class($e) . "; Message: {$e->getMessage()}; File: {$e->getFile()}; Line: {$e->getLine()};" . $userDetails;
			file_put_contents(DIR_SYSTEM . "storage" . _DS . "logs" . _DS . "error.log", $message . PHP_EOL, FILE_APPEND);
			http_response_code(500);
			die('Whoops, sorry, it looks like there was an error.<br><br><strong>Please let your developer know the code "' . $dateTimeNow->format("U") . '" for debugging.</strong><br><br>Press the back button to go back to where you were.');
			// header('Refresh:0; url=/', true, 303);
			break;
	}
	exit();
}

/**
 * Checks for a fatal error, work around for set_error_handler not working on fatal errors.
 */
function check_for_fatal()
{
	$error = error_get_last();
	if (!empty($error) && $error["type"] == E_ERROR)
		log_error($error["type"], $error["message"], $error["file"], $error["line"]);
}

/**
 * Checks how errors are to be handled
 */
if (isset($useFilpWhoops) && $useFilpWhoops) {

	// Use filp/whoops package for error handling if is set
	// http://filp.github.io/whoops/
	$whoops = new \Whoops\Run;
	$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
	$whoops->register();
} else {

	// Register a function for execution on shutdown
	// http://php.net/manual/en/function.register-shutdown-function.php
	register_shutdown_function("check_for_fatal");

	// Sets a user-defined error handler function
	// http://php.net/manual/en/function.set-error-handler.php
	set_error_handler("log_error");

	// Sets a user-defined exception handler function
	// http://php.net/manual/en/function.set-exception-handler.php
	set_exception_handler("log_exception");

	// This determines whether errors should be printed to the screen
	// as part of the output or if they should be hidden from the user.
	// http://php.net/manual/en/errorfunc.configuration.php#ini.display-errors
	ini_set("display_errors", "off");
}

// Set which PHP errors are reported
// http://php.net/manual/en/function.error-reporting.php
switch ($errorLogType[0]) {
	case 0: // Turn off all error reporting
		error_reporting(0);
		break;
	case 1: // Report all PHP errors
		error_reporting(E_ALL);
		break;
	case 2: // Report all errors except E_NOTICE
		error_reporting(E_ALL & ~E_NOTICE);
		break;
	case 3: // Report simple running errors
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
		break;
	case 4: // Reporting E_NOTICE can be good too (to report uninitialized variables or catch variable name misspellings ...)
		error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
		break;
	default: // Report all PHP errors
		error_reporting(E_ALL);
		break;
}
