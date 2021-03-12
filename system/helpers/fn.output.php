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
function friendlyDtmDiff($date1, $date2, $length = '', $format = '')
{
  // Create DateTime for diff()
  $dt1 = new \DateTime($date1);
  $dt2 = new \DateTime($date2);

  // Create intervals
  if ($dt1 < $dt2) {
    $ago = '';
    $interval = $dt1->diff($dt2);
  } else { // If $dt2 is older than $dt1, reverse the roles
    $ago = '-';
    $interval = $dt2->diff($dt1);
  }

  // Assume best output options
  if (empty($format) || $format == '') {
    $formatCheck = $interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s;
    if ($formatCheck > YEAR_IN_SECONDS) { // Assume Years
      $format = 'y';
    } else if ($formatCheck > MONTH_IN_SECONDS) { // Assume Months
      $format = 'm';
    } else if ($formatCheck > DAY_IN_SECONDS) { // Assume Days
      $format = 'd';
    } else if ($formatCheck > HOUR_IN_SECONDS) { // Assume Hours
      $format = 'h';
    } else if ($formatCheck > MINUTE_IN_SECONDS) { // Assume Minutes
      $format = 'i';
    } else { // Assume seconds
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
function showValidationErrors($errors)
{

  // Start the show errors output
  $output = '.showErrors({ ';

  // Loop through all the errors
  foreach ($errors as $field => $error) {

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

/**
 * Get closest in array
 * Based on https://stackoverflow.com/a/5464961/1248664
 *
 * @param int $search Item to search for
 * @param array $arr Array to search in
 *
 * @return string Will return the value of the closest match
 */
function getClosest(int $search, array $arr)
{
  $closest = null;
  foreach ($arr as $item) {
    if ($closest === null || abs($search - $closest) > abs($item - $search)) {
      $closest = $item;
    }
  }
  return $closest;
}

/**
 * Output breadcrumbs
 *
 * @param object $breadcrumbs
 *
 * @return string Will return the breadcrumbs as a string
 */
function outputBreadcrumbs(object $breadcrumbs)
{
  // Init output
  $returnOutput = '';

  // Output breadcrumbs
  foreach ($breadcrumbs as $breadcrumb) {
    // Check for href
    $href = (!empty($breadcrumb->href)) ? ' href="' . $breadcrumb->href . '"' : '';
    // Check for title
    $title = (!empty($breadcrumb->title)) ? $breadcrumb->title : $breadcrumb->text;
    // Check for last item
    $ariaCurrent =  ($breadcrumb === end($breadcrumbs)) ? ' aria-current="page"' : '';
    $returnOutput .= '<a' . $href . ' class="csc-breadcrumb" title="' . $title . '"' . $ariaCurrent . '>' . $breadcrumb->text . '</a>';
  }

  // Return output
  return (!empty($returnOutput)) ? $returnOutput : '<a class="csc-breadcrumb" title="No breadcrumb available">n/a</a>';
}

/**
 * Return parent admin menu item
 *
 * @param object $item An object array of the parent menu item to output
 * @param object $userRoles The user roles object for checking permissions
 * @param string $currentNav `[optional]` The current navigation identifier. Defaults to empty
 * @param string $currentSubNav `[optional]` The current sub-navigation identifier. Defaults to empty
 *
 * @return string Will return the menu as a string
 */
function returnParentAdminMenuItem(object $item, $userRoles, string $currentNav = '', string $currentSubNav = '')
{

  // Init output
  $returnOutput = '';

  // Check if currently active item
  $isActive = (!empty($currentNav) && strtolower($currentNav) == strtolower($item->identifier)) ? TRUE : FALSE;
  $activeNav = ($isActive) ? ' active' : '';
  $ariaExpanded = ($isActive) ? 'true' : 'false';
  $ariaHidden = ($isActive) ? 'false' : 'true';

  // Set title fallback
  $fallbackTitle = (!empty($item->title)) ? $item->title : $item->text;

  // Set icon fallback
  $fallbackIcon = (!empty($item->icon)) ? $item->icon : 'fas fa-bars';

  // Add to return output
  $returnOutput .= '<li class="has-subnav' . $activeNav . '"><a data-toggle="collapse" data-tippy-content="' . $fallbackTitle . '" aria-expanded="' . $ariaExpanded . '"><i class="' . $fallbackIcon . '"></i> <span>' . $item->text . '</span><b class="caret"></b></a>';

  // Check children isn't empty
  if (!empty($item->children)) {
    // Children isn't empty

    // Add to return output
    $returnOutput .= '<ol class="sidebar__sub-nav" aria-hidden="' . $ariaHidden . '">';

    // Loop through children
    foreach ($item->children as $childItem) {

      // Make sure the child item is an object
      $childItem = (object) $childItem;

      // Check if a user permission is required
      if (empty($childItem->permission) || (!empty($userRoles) && $userRoles->canDo($childItem->permission))) {

        // Check if currently active child item
        $isActiveChild = (!empty($currentSubNav) && strtolower($currentSubNav) == strtolower($item->identifier . '/' . $childItem->identifier)) ? 'class="active"' : '';

        // Set title fallback
        $fallbackTitle = (!empty($childItem->title)) ? $childItem->title : $childItem->text;

        // Set href fallback
        $fallbackHref = (!empty($childItem->href)) ? $childItem->href : 'javascript:alert(\'The ' . $fallbackTitle . ' section is coming soon\');';

        // Add to return output
        $returnOutput .= '<li ' . $isActiveChild . '><a href="' . $fallbackHref . '">' . $childItem->text . '</a></li>';
      }
    }

    // Add to return output
    $returnOutput .= '</ol>';
  }

  // Add to return output
  $returnOutput .= '</li>';

  // Return output
  return (!empty($returnOutput)) ? $returnOutput : '';
}

/**
 * Output Admin Menu
 *
 * @param array $menuItems An array of the menu items to output
 * @param string $currentNav `[optional]` The current navigation identifier. Defaults to empty
 * @param string $currentSubNav `[optional]` The current sub-navigation identifier. Defaults to empty
 *
 * @return string Will return the menu as a string
 */
function outputAdminMenu(array $menuItems, string $currentNav = '', string $currentSubNav = '')
{

  // Get the current user role permissions
  $userRoles = '';
  if (isLoggedInUser()) {
    global $role;
    $userRoles = $role;
    $userRoles->setUserPermissions((int) $_SESSION['_cs']['user']['uid']);
  }

  // Init output
  $returnOutput = '';

  // Output menuitem
  foreach ($menuItems as $item) {

    // Make sure the item is an object
    $item = (object) $item;

    // Check if a user permission is required
    if (empty($item->permission) || (!empty($userRoles) && $userRoles->canDo($item->permission))) {

      // Check the item type
      switch (trim(strtolower($item->type))) {
        case 'separator':
          // Add to return output
          $returnOutput .= '<li class="sidebar__nav-separator"><span>' . $item->text . '</span></li>';
          break;
        case 'link':
          // Check if currently active item
          $activeNav = (!empty($currentNav) && strtolower($currentNav) == strtolower($item->identifier)) ? 'class="active"' : '';
          // Set title fallback
          $fallbackTitle = (!empty($item->title)) ? $item->title : $item->text;
          // Set href fallback
          $fallbackHref = (!empty($item->href)) ? $item->href : 'javascript:alert(\'The ' . $fallbackTitle . ' section is coming soon\');';
          // Set icon fallback
          $fallbackIcon = (!empty($item->icon)) ? $item->icon : 'fas fa-bars';
          // Add to return output
          $returnOutput .= '<li ' . $activeNav . '><a href="' . $fallbackHref . '" data-tippy-content="' . $fallbackTitle . '"><i class="' . $fallbackIcon . '"></i> <span>' . $item->text . '</span></a></li>';
          break;
        case 'parent':
          // Get parent admin menu item
          $returnOutput .= returnParentAdminMenuItem((object) $item, $userRoles, $currentNav, $currentSubNav);
          break;

        default:
          break;
      }
    }
  }

  // Return output
  return (!empty($returnOutput)) ? $returnOutput : '';
}

/**
 * Create PDF using template
 *
 * @param string $templateFile File name of pdf template to be used
 * @param array $arrayReplace Array of items to replace in the template in Associative array format ("key"=>"value") (optional, dependant on template)
 *
 * @return bool|string Returns FALSE if there was an error, otherwise returns the created pdf template
 */
function createPDFTemplate($templateFile, $arrayReplace = array())
{

  // Set file path
  $filePath = DIR_SYSTEM . 'pdfs' . _DS . ltrim($templateFile, '/');
  // Check the file exists
  if (!empty($templateFile) && file_exists($filePath)) {

    // Get file contents
    $file = file_get_contents($filePath);

    // Check if the file has contents
    if (!empty($file)) {

      // Add generic options to array
      $arrayReplace['site_url'] = get_site_url();
      $arrayReplace['site_name'] = SITE_NAME;
      $arrayReplace['current_year'] = date('Y');

      // Loop through and replace items from array
      foreach ($arrayReplace as $key => $value) {
        $file = str_replace("{{" . $key . "}}", $value, $file);
      }

      // Return the created email
      return $file;
      exit;
    } else { // File was empty. Return FALSE

      return FALSE;
      exit;
    }
  } else { // Template doesn't exist. Return FALSE for error

    return FALSE;
    exit;
  }
}
