<?php
/**
 * The "Output/Page" related functions file
 *
 * @package Cornerstone
 */

/**
 * Output a length between times in friendly format
 *
 * @param string $date1 Date/Time from in Y-m-d H:i:s format.
 * @param string $date2 Date/Time to in Y-m-d H:i:s format.
 * @param string $length Length of output. Set to "l" for detailed down to the second. Defaults to short if nothing entered.
 * @param string $format Format to return the output in. Based on {@link https://www.php.net/manual/en/dateinterval.format.php PHP Date Interval Format}.
 * If no format is set, the function will try assume the best output
 *
 * @return string Will return the requested output.
 *
 * Thanks to {@link https://stackoverflow.com/a/57552804/1248664 Nick} for his help on this
*/
function friendlyDtmDiff($date1, $date2, $length = '', $format = '') {
  // Create DateTime for diff()
  $dt1 = new \DateTime($date1);
  $dt2 = new \DateTime($date2);

  // Create intervals
  if ($dt1 < $dt2) {
      $ago = '';
      $interval = $dt1->diff($dt2);
  }
  else { // If $dt2 is older than $dt1, reverse the roles
      $ago = '-';
      $interval = $dt2->diff($dt1);
  }

  // Assume best output options
  if(empty($format) || $format == '') {
    $formatCheck = $interval->days*86400 + $interval->h*3600 + $interval->i*60 + $interval->s;
    if($formatCheck > YEAR_IN_SECONDS) { // Assume Years
      $format = 'y';
    } else if($formatCheck > MONTH_IN_SECONDS) { // Assume Months
      $format = 'm';
    } else if($formatCheck > DAY_IN_SECONDS) { // Assume Days
      $format = 'd';
    } else if($formatCheck > HOUR_IN_SECONDS) { // Assume Hours
      $format = 'h';
    } else if($formatCheck > MINUTE_IN_SECONDS) { // Assume Minutes
      $format = 'i';
    } else {// Assume seconds
      $format = 's';
    }
  }

  // Output format (minimum 2 digits for upper case formats)
  $of = ($format < 'a') ? '%02d' : '%d';

  // generate output using an array of terms to be imploded
  $output = array();
  // create time components
  switch ($format) {
      case 'Y':
      case 'y':
          $years = $interval->y;
          $plural = ($years == 1) ? '' : 's';
          if ($years) $output[] = sprintf("$of year" . $plural, $years);
          if ($length != 'l') break;
          $interval->y = 0;
      case 'M':
      case 'm':
          $months = $interval->y * 12 + $interval->m;
          $plural = ($months == 1) ? '' : 's';
          if ($months) $output[] = sprintf("$of month" . $plural, $months);
          if ($length != 'l') break;
          $interval->m = $interval->y = 0;
      case 'D':
      case 'd':
          $days = ($interval->y * 12 + $interval->m) * 30 + $interval->d;
          $plural = ($days == 1) ? '' : 's';
          if ($days) $output[] = sprintf("$of day" . $plural, $days);
          if ($length != 'l') break;
          $interval->d = $interval->m = $interval->y = 0;
      case 'H':
      case 'h':
          $hours = (($interval->y * 12 + $interval->m) * 30 + $interval->d) * 24 + $interval->h;
          $plural = ($hours == 1) ? '' : 's';
          if ($hours) $output[] = sprintf("$of hour" . $plural, $hours);
          if ($length != 'l') break;
          $interval->h = $interval->d = $interval->m = $interval->y = 0;
      case 'I':
      case 'i':
          $minutes = ((($interval->y * 12 + $interval->m) * 30 + $interval->d) * 24 + $interval->h) * 60 + $interval->i;
          $plural = ($minutes == 1) ? '' : 's';
          if ($minutes) $output[] = sprintf("$of minute" . $plural, $minutes);
          if ($length != 'l') break;
          $interval->i = $interval->h = $interval->d = $interval->m = $interval->y = 0;
      case 'S':
      case 's':
          $seconds = (((($interval->y * 12 + $interval->m) * 30 + $interval->d) * 24 + $interval->h) * 60 + $interval->i) * 60 + $interval->s;
          $plural = ($seconds == 1) ? '' : 's';
          if ($seconds) $output[] = sprintf("$of second" . $plural, $seconds);
          break;
      default:
          return 'Invalid format';
          break;
  }
  // Output Oxford Comma
  (count($output) > 2) ? $oxford = ',' : $oxford = '';

  // put the output string together
  $last = array_pop($output);
  return (count($output) ? implode(', ', $output) . $oxford . ' and ' : '') . $last . $ago;
}

/**
 * Output errors loop in validation
 *
 * @param object $errors: An object with the error fields and messages defined
 *
 * @return string Will return the errors as a string for appending to the javascript.
 *
*/
function showValidationErrors($errors) {

    // Start the show errors output
    $output = '.showErrors({ ';

        // Loop through all the errors
    foreach($errors as $field => $error) {

        // Add the error to the string
        $output .= '"' . $field . '" : "' . $error . '", ';

    }

    // Remove the last comma
    $output = rtrim(trim($output), ',');

    // Close the errors output
    $output .= ' });';

    // Return the string to be output
    return $output;
}