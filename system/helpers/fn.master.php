<?php

/**
 * The "Master" functions file, which also references all the rest
 *
 * @package Cornerstone
 */

/**
 * Simple redirect
 *
 * @param string $path path to be redirected to
 */
function redirectTo($path)
{
	header('location: ' . get_site_url() . ltrim(str_replace(get_site_subfolder(), '', $path), '/'));
}

/**
 * Get an option from the `cs_options` table
 *
 * @param string|array $optionData Name of option(s) to retrieve from the database, either a string or array (not case sensitive)
 * @return string|object Value of the requested options as either a string or object (with $key as strtolower())
 */
function get_option($optionData)
{

	// Access global option
	global $option;

	// return results
	return $option->get($optionData);
	exit;
}

/**
 * Get the complete site URL
 *
 * @param string $filePath: File path to add to url (optional)
 * @return string The complete site URL
 */
function get_site_url($filePath = '')
{

	/**
	 * Check if site is SSL or not
	 *
	 * @var string $siteSSL returns as SSL prefix if SITESSL option set true
	 */
	$siteSSL = (SITE_HTTPS) ? 'https://' : 'http://';

	return (isset($filePath) && !empty($filePath) && is_string($filePath)) ? $siteSSL . SITE_URL . '/' . ltrim($filePath, '/') : $siteSSL . SITE_URL . '/';
}

/**
 * Get the site subfolder
 *
 * @return string The site subfolder if it exists
 */
function get_site_subfolder()
{
	// Parse the URL
	return trim(parse_url(get_site_url())['path'], '/');
}

/**
 * Get the absolute path to the system folder
 *
 * @param string $filePath Optional file name to add to the path
 * @return string The absolute path to the system folder
 */
function get_sys_path($filePath = '')
{
	return (isset($filePath) && !empty($filePath) && is_string($filePath)) ? DIR_SYSTEM . ltrim($filePath, '/') : DIR_SYSTEM;
}

/**
 * Get the absolute path to the library folder
 *
 * @param string $filePath Optional file name to add to the path
 * @return string The absolute path to the library folder
 */
function get_lib_path($filePath = '')
{
	return (isset($filePath) && !empty($filePath) && is_string($filePath)) ? DIR_CS .  ltrim($filePath, '/') : DIR_CS;
}

/**
 * Get the absolute path to the theme folder
 *
 * @param string $filePath Optional file name to add to the path
 * @param string $themeType Optional theme type to add to the path
 * @return string The absolute path to the system folder
 */
function get_theme_path($filePath = '', $themeType = 'system')
{

	// Check for allowed sub-folders
	if (!array_key_exists($themeType, ALLOWED_SUBFOLDERS)) $themeType = 'system';

	// Set theme directory
	$directory = DIR_ROOT . _DS . trim($themeType, '/') . _DS . 'views' . _DS . 'theme' . _DS;

	// Return the path
	return (isset($filePath) && !empty($filePath) && is_string($filePath)) ? $directory . ltrim($filePath, '/') : $directory;
}

/**
 * Get the absolute path to the public folder
 *
 * @param string $filePath Optional file name to add to the path
 * @return string The absolute path to the public folder
 */
function get_public_path($filePath = '')
{

	// Set public directory
	$directory = DIR_PUBLIC;

	// Replace directory separator in filePath
	$filePath = str_replace('/', _DS, $filePath);

	// Return the path
	return (isset($filePath) && !empty($filePath) && is_string($filePath)) ? $directory . ltrim($filePath, '/') : $directory;
}

// Reference the extra function files
require_once(DIR_HELPERS . 'fn.filters.php'); // Filters for sorting data
