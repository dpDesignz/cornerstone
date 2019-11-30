<?php
/**
 * The "Generate" related functions file
 *
 * @package Cornerstone
 */

/**
 * Generate a cryptographically secure key to a defined length if set, else default length defined by `crypto_hex_length` in `cs_options` table
 *
 * @param int $length Length of key to return
 * @return string Generated key to defined length
*/
function get_crypto_key($length = '') {

  // Check if token length defined and number, else set to `crypto_hex_length`
  if(empty( trim( $length ) ) || !is_numeric( $length )) {
    $length = get_option('crypto_hex_length');
  }

  // Create cryptographically secure random string
  try {
    $string = random_bytes($length / 2);
  } catch (TypeError $e) {
      // Well, it's an integer, so this IS unexpected.
      die("An unexpected error has occurred");
  } catch (Error $e) {
      // This is also unexpected because 32 is a reasonable integer.
      die("An unexpected error has occurred");
  } catch (Exception $e) {
      // If you get this message, the CSPRNG failed hard.
      die("Could not generate a random string. Is our OS secure?");
  }

  // Return generated cryptographically secure key
  return bin2hex($string);
}

/**
 * Generate a cryptographically secure token to a defined length if set, else default length defined by `crypto_hex_length` in `cs_options` table
 *
 * @param int $length Length of token to return
 * @return string Generated token to defined length
*/
function get_crypto_token($length = '') {

  // Check if token length defined and number, else set to `crypto_hex_length`
  if(empty( trim( $length ) ) || !is_numeric( $length )) {
    $length = get_option('crypto_hex_length');
  }

  // Create cryptographically secure random string
  try {
    $string = random_bytes($length);
  } catch (TypeError $e) {
      // Well, it's an integer, so this IS unexpected.
      die("An unexpected error has occurred");
  } catch (Error $e) {
      // This is also unexpected because 32 is a reasonable integer.
      die("An unexpected error has occurred");
  } catch (Exception $e) {
      // If you get this message, the CSPRNG failed hard.
      die("Could not generate a random string. Is our OS secure?");
  }

  // Return generated cryptographically secure key
  return $string;
}

/**
 * Generate a pin to a defined length if set
 *
 * @param int $length Length of pin to return
 * @return string Generated pin to defined length
*/
function get_pin($length = 6) {

  // Set minimim number
  $minNumber = (int)str_pad('1',$length,'0',STR_PAD_RIGHT);

  // Set maximum number
  $maxNumber = (int)str_pad('9',$length,'9',STR_PAD_RIGHT);

  // Return generated pin
  return mt_rand ( $minNumber , $maxNumber );
} ?>