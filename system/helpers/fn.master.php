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
function redirectTo($path){
	header('location: ' . get_site_url() . ltrim($path, '/'));
}

/**
 * Get an option from the `cs_options` table
 *
 * @param string|array $optionData Name of option(s) to retrieve from the database, either a string or array (not case sensitive)
 * @param bool|string $default Default value to return if the option does not exist.
 * @return string|object Value of the requested options as either a string or object (with $key as strtolower())
 */
function get_option($optionData, $default = false) {

	// Load database
	$csOption = new \CornerstoneDBH();

	if( is_string( $optionData ) ) { // Check if only 1 option

		// Check if the option is empty
		if ( empty( trim( $optionData ) ) ) {

			return $default;

		} else {

			// Get the option from the databse
			$result = $csOption->dbh->selecting(DB_PREFIX . 'options', 'option_value', eq('UPPER(option_name)',strtoupper($optionData)));

			// Check if the option is available
			if( $result != false ) {

				// If it is, return option
				foreach( $result as $row ) {

					// If option is empty, return $default value, else return the option
					return ( empty( trim( $row->option_value ) ) ) ? $default : $row->option_value;
					break; // Makes sure only 1 option is returned

				}
			} else {

				// If option is not available, return $default value
				return $default;

			}
		}
	} else if( is_array( $optionData ) ) { // Check if array of options

		// Check if the option array is empty
		if ( count( $optionData ) < 1 ) {
			return $optionData;
		} else {

			// Create array to return
			$returnArray = array();

			// Get the options from the databse
			foreach($optionData as $key) {

				// Get the option from the databse
				$result = $csOption->dbh->selecting(DB_PREFIX . 'options', 'option_value', eq('UPPER(option_name)',strtoupper($key)));

				// Check if the option is available
				if( $result != false ) {

					// If it is, return option
					foreach( $result as $row ) {

						// If option is empty, return $default value, else return $key=>$value from table
						$returnArray[strtolower($key)] = ( empty( trim( $row->option_value ) ) ) ? $default : $row->option_value;
						break; // Makes sure only 1 option is returned
					}
				} else {
					// If option is not available, return $default value
					$returnArray[strtolower($key)] = $default;
				}
			}
			// Return object
			return (object)$returnArray;
		}
	} else { // Return $default if not string or array
		return $default;
	}
}

/**
 * Get the complete site URL
 *
 * @param string $filePath: File path to add to url (optional)
 * @return string The complete site URL
 */
function get_site_url($filePath = '') {

	/**
	 * Check if site is SSL or not
	 *
	 * @var string $siteSSL returns as SSL prefix if SITESSL option set true
	*/
	$siteSSL = (SITE_HTTPS) ? 'https://' : 'http://';

	return ( isset( $filePath ) && !empty( $filePath ) && is_string( $filePath ) ) ? $siteSSL . SITE_URL . '/' . ltrim( $filePath, '/' ) : $siteSSL . SITE_URL . '/' ;
}

/**
 * Get the absolute path to the system folder
 *
 * @param string $filePath Optional file name to add to the path
 * @return string The absolute path to the system folder
 */
function get_sys_path($filePath = ''){
	return ( isset( $filePath ) && !empty( $filePath ) && is_string( $filePath ) ) ? DIR_SYSTEM . ltrim( $filePath, '/' ) : DIR_SYSTEM;
}

/**
 * Get the absolute path to the library folder
 *
 * @param string $filePath Optional file name to add to the path
 * @return string The absolute path to the library folder
 */
function get_lib_path($filePath = ''){
	return ( isset( $filePath ) && !empty( $filePath ) && is_string( $filePath ) ) ? DIR_CS .  ltrim( $filePath, '/' ) : DIR_CS ;
}

/**
 * Get the absolute path to the theme folder
 *
 * @param string $filePath Optional file name to add to the path
 * @param string $themeType Optional theme type to add to the path
 * @return string The absolute path to the system folder
 */
function get_theme_path($filePath = '', $themeType = 'system'){

	// Check for allowed sub-folders
	if(!array_key_exists($themeType, ALLOWED_SUBFOLDERS)) $themeType = 'system';

	// Set theme directory
	$directory = DIR_ROOT . _DS . trim( $themeType, '/' ) . _DS . 'views' . _DS . 'theme' . _DS;

	// Return the path
	return ( isset( $filePath ) && !empty( $filePath ) && is_string( $filePath ) ) ? $directory . ltrim( $filePath, '/' ) : $directory;
}

/**
 * Get the absolute path to the public folder
 *
 * @param string $filePath Optional file name to add to the path
 * @return string The absolute path to the public folder
 */
function get_public_path($filePath = ''){

	// Set public directory
	$directory = DIR_PUBLIC . _DS;

	// Return the path
	return ( isset( $filePath ) && !empty( $filePath ) && is_string( $filePath ) ) ? $directory . ltrim( $filePath, '/' ) : $directory;
}

// Reference the extra function files
require_once(DIR_HELPERS . 'fn.filters.php'); // Filters for sorting data