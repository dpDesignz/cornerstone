<?php
/**
 * Class used for authorisation with login pages
 *
 * @package Cornerstone
 */
namespace Cornerstone\User;

// ** Direct Access Check ** //
if(!isset($fear) || $fear!='hidden') {header('Refresh:2; url=/', true, 303);echo 'Sorry, you don\'t have access to this file.';exit();}

class User {

	// Set Properties
	public $uid;

	/**
	 * Construct the User class
	 *
	 * @param string $userID User ID to check against in the database
	*/
	public function __construct($userID = "") {
		// If $userID is empty, set it to the user ID stored in the session
		if(empty( $userID ) && isset( $_SESSION['_cs-uid'] ) && !empty( $_SESSION['_cs-uid'] ) ) { $userID = $_SESSION['_cs-uid']; }
		$this->uid = $userID;
	}

	/**
	 * Get the core user information from the database by the users ID
	 *
	 * @param string $userID User ID to check against in the database
	 * @return object|bool Returns the user information as a stdClass Object or false if not found
	*/
	public function getUserByID(){

		// Make the $csdb global accessible
		global $csdb;

		// Set $userID
		$userID = $this->uid;

		// Check if $userID is not a string or is not a number or is empty
		if( !is_string( $userID ) || !is_numeric( $userID ) || empty( trim( $userID ) ) ) {
			return false;
		} else {
			// Get the user information
			$result = $csdb->selecting('cs_users', '*', eq('user_id', trim( $userID ) ) );
			// Check if the user is available
			if( $result != false ) {
				// If it is, return user
				foreach( $result as $row ) {
					return $row;
					break; // Makes sure only 1 option is returned
				}
			} else {
				// Else return false
				return $result;
			}
		}
	}

	/**
	 * Get the core user information from the database by the users Login or Email
	 *
	 * @param string $userData User Login or Email to check against in the database
	 * @return object|bool Returns the user information as a stdClass Object or false if not found
	*/
	protected function getUserByLogin($userData){

		// Make the $csdb global accessible
		global $csdb;

		// Check if $userData is not a string or is empty
		if( !is_string( $userData ) || empty( trim( $userData ) ) ) {
			return false;
		} else {
			// Get the user information
			$result = $csdb->selecting('cs_users', '*', eq('LOWER(user_login)', trim( strtolower( $userData ) ), _OR), eq('LOWER(user_email)',trim( strtolower( $userData ) ) ) );
			// Check if the user is available
			if( $result != false ) {
				// If it is, return user
				foreach( $result as $row ) {
					return $row;
					break; // Makes sure only 1 option is returned
				}
			} else {
				// Else return false
				return $result;
			}
		}
	}

	/**
	* Get user field(s) from the `cs_users` or `cs_usermeta` table
	*
	* @param array $fieldKey Name of field(s) to retrieve from the database (not case sensitive), and if from `cs_usermeta` table or not.
	*             - array($column => 0) = Name of column to call from `cs_users`.
	*             - array($key => 1) = Key of row to call from `cs_usermeta`.
	* @param bool $returnString Will return the value as a string (will only return first requested field if multiple requested).
	* @param bool|string $default Default value to return if the option does not exist (only works for fields from `cs_usermeta` table or if `cs_users` value is empty) (optional).
	* @return array|string Value of the requested field(s) (with $key as strtolower()).
	*/
	public function getUserField($fieldKey, $returnString = 0, $default = false) {
		// Allow function to access $csdb
		global $csdb;

		// If $this->uid is empty, set it to the user ID stored in the session
		(!empty( $this->uid ) ) ? $userID = $this->uid : $userID = $_SESSION['user_id'] ;

		// Check if $fieldKey is an array
		if( is_array( $fieldKey ) ) {

			// Check if the array is empty
			if ( count( $fieldKey ) < 1 ) {
				return $default;
			} else {

				// Create array to return
				$returnArray = array();

				// Get the field(s) from the databse
				foreach($fieldKey as $columnKey => $metaField) {

					// Check if column key isn't set and assume it's meant to be a user field
					if(is_int($columnKey)) {
						$columnKey = $metaField;
					}
					if($metaField === 1) {
						// Get the field from the `cs_usermeta` table
						$result = $csdb->selecting('cs_user_meta', 'umeta_value', eq('UPPER(umeta_key)',strtoupper($columnKey), _AND), eq('umeta_user_iD',$userID));
					} else {
						// Get the field from the `cs_users` table
						$result = $csdb->selecting('cs_users', $columnKey, eq('user_id',$userID));
					}

					// Check if the option is available
					if( $result != false ) {

						// If it is, return option
						foreach( $result as $row ) {

							if($metaField === 1) {
								// If option is empty, return $default value, else return $columnKey=>$value from table
								$returnArray[strtolower($columnKey)] = ( empty( trim( $row->umeta_value ) ) ) ? $default : $row->umeta_value;
								break; // Makes sure only 1 option is returned
							} else {
								// If option is empty, return $default value, else return $key=>$value from table
								$returnArray[strtolower($columnKey)] = ( empty( trim( $row->$columnKey ) ) ) ? $default : $row->$columnKey;
								break; // Makes sure only 1 option is returned
							}
						}
					} else {
						// If option is not available, return $default value
						$returnArray[strtolower($columnKey)] = $default;
					}
				}
				// Return first value as string if requested, else return as an object
				return ($returnString) ? reset($returnArray) : (object)$returnArray;
			}
		} else { // Return $default if not array
			return $default;
		}
	}
} ?>