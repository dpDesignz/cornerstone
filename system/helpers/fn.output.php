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
  // Get the current user role permissions
  $userRoles = '';
  if (isLoggedInUser()) {
    global $role;
    $userRoles = $role;
    $userRoles->setUserPermissions((int) $_SESSION['_cs']['user']['uid']);
  }

  // Init output
  $returnOutput = '<nav class="csc-breadcrumbs" aria-label="breadcrumbs">';
  $itemsFound = false;

  // Output breadcrumbs
  foreach ($breadcrumbs as $breadcrumb) {
    $itemsFound = true;
    // Check for href
    $href = (!empty($breadcrumb->href)) ? ' href="' . $breadcrumb->href . '"' : '';
    // Check for permission
    $href = (empty($breadcrumb->permission) || (!empty($userRoles) && $userRoles->canDo($breadcrumb->permission))) ? $href : '';
    // Check for title
    $title = (!empty($breadcrumb->title)) ? $breadcrumb->title : $breadcrumb->text;
    // Check for last item
    $ariaCurrent =  ($breadcrumb === end($breadcrumbs)) ? ' aria-current="page"' : '';
    $returnOutput .= '<a' . $href . ' class="csc-breadcrumb" title="' . $title . '"' . $ariaCurrent . '>' . $breadcrumb->text . '</a>';
  }

  $returnOutput .= (!$itemsFound) ? '<a class="csc-breadcrumb" title="No breadcrumb available">n/a</a>' : '';

  // Return output
  return $returnOutput  . '</nav>';
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
  $returnParentOutput = '';
  $returnChildrenWrapper = '';
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
  $returnParentOutput .= '<li class="has-subnav' . $activeNav . '"><a data-toggle="collapse" data-tippy-content="' . $fallbackTitle . '" aria-expanded="' . $ariaExpanded . '"><i class="' . $fallbackIcon . '"></i> <span>' . $item->text . '</span><b class="caret"></b></a>';

  // Check children isn't empty
  if (!empty($item->children)) {
    // Children isn't empty

    // Loop through children
    foreach ($item->children as $childItem) {

      // Make sure the child item is an object
      $childItem = (object) $childItem;
      $hasChildren = FALSE;

      // Check if a user permission is required
      if (empty($childItem->permission) || (!empty($userRoles) && $userRoles->canDo($childItem->permission))) {

        // Set has children
        $hasChildren = TRUE;

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
    if ($hasChildren) {
      $returnChildrenWrapper = '<ol class="sidebar__sub-nav" aria-hidden="' . $ariaHidden . '">' . $returnOutput . '</ol>';
    }
  }

  // Add to return output
  $returnParentOutput .= $returnChildrenWrapper . '</li>';

  // Return output
  return (!empty($returnChildrenWrapper)) ? $returnParentOutput : '';
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
 * Output menu
 *
 * @param object $menuItems
 * @param string $pathMatch
 *
 * @return string Will return the menu as a string
 */
function base_outputMenu(object $menuItems, string $pathMatch = null)
{
  // Init output
  $returnOutput = '';

  // Init last parent
  $topParent = 0;
  $previousLayers = 0;

  // Check if path match is set
  if ($pathMatch !== null) {
    // Create path match array
    $pathMatchArray = explode(",", $pathMatch);
    // Count how many layers deep the match is
    $pathMatchCount = count($pathMatchArray);
  }

  // Output menu
  foreach ($menuItems as $menuItem) {
    // Check for href
    $href = (!empty($menuItem->href)) ? ' href="' . $menuItem->href . '"' : '';
    // Check for title
    $title = (!empty($menuItem->title)) ? $menuItem->title : $menuItem->text;

    // Get path info
    $path = explode(",", $menuItem->path);

    // Get Layers
    $currentLayers = count($path);

    // Check if has child items or not
    if (!empty($previousLayers) && $currentLayers > $previousLayers) {
      // Add chevron and close the link
      $returnOutput .= ' <i class="fal fa-chevron-down"></i></a><ol>';
    } else if (!empty($previousLayers)) {

      // Close link
      $returnOutput .= '</a></li>';
    }

    // Set top parent
    $topParent = $path[0];

    // Check if needing to close the layers
    if (!empty($previousLayers) && $currentLayers == 1 && $previousLayers > 1) {
      $exitLayers = $previousLayers;
      while ($exitLayers > 1) {
        // Close layer
        $returnOutput .= '</ol></li>';
        $exitLayers--;
      }
    } else if (!empty($previousLayers) && $currentLayers < $previousLayers) {
      // Close layer
      $returnOutput .= '</ol></li>';
    }

    // Define active path as empty
    $activePath = '';
    // Check if path match array is set
    if (isset($pathMatchArray) && is_array($pathMatchArray) && count($pathMatchArray) > 0) {
      // Check if path match is eligible
      if ($currentLayers <= $pathMatchCount) {
        // Check if item is in current path match
        if (implode(',', array_slice($pathMatchArray, 0, $currentLayers)) == $menuItem->path) {
          // Set active path
          $activePath = ' class="nav-active"';
        }
      }
    }

    // Set to output
    $returnOutput .= '<li><a' . $activePath . $href . ' title="' . $title . '" aria-role="menuItem">' . $menuItem->text;

    // Set the previous layers
    $previousLayers = $currentLayers;
  }

  // Close last item
  $returnOutput .= (empty($returnOutput)) ? '' : '</a></li>';

  // Check if needing to close any layers
  if (!empty($previousLayers) && $previousLayers > 0) {
    $exitLayers = $previousLayers;
    while ($exitLayers > 1) {
      // Close layer
      $returnOutput .= '</ol></li>';
      $exitLayers--;
    }
  }

  // Return output
  return (!empty($returnOutput)) ? $returnOutput : '<li><a>No menu items available</a></li>';
}

/**
 * Output Admin Index Filters
 *
 * @param array $menuItems An array of the menu items to output
 * @param string $currentNav `[optional]` The current navigation identifier. Defaults to empty
 * @param string $currentSubNav `[optional]` The current sub-navigation identifier. Defaults to empty
 *
 * @return string Will return the menu as a string
 */
function outputAdminIndexFilters($search = '', $placeholder = '', $sortBy = [], $perPage = [])
{
  // Check for any extra sorting
  $sortByOP = '';
  if (!empty($sortBy) && is_array($sortBy)) {
    foreach ($sortBy as $value => $label) {
      $sortByOP .= '<option value="' . $value . '">' . $label . '</option>';
    }
  }
  // Check for over-riding per page
  $perPageOP = '';
  if (!empty($perPage) && is_array($perPage)) {
    foreach ($perPage as $value => $isSelected) {
      $selected = ($isSelected) ? ' selected' : '';
      $perPageOP .= '<option value="' . $value . '"' . $selected . '>' . $value . ' per page</option>';
    }
  } else {
    $perPageOP = '<option value="10">10 per page</option>
      <option value="25" selected="">25 per page</option>
      <option value="50">50 per page</option>
      <option value="100">100 per page</option>
      <option value="150">150 per page</option>
      <option value="200">200 per page</option>';
  }
  // Set search placeholder
  $searchPlaceholder = (!empty($placeholder)) ? $placeholder : 'Search data...';
  // Check for search value
  $searchOP = (!empty($search)) ? ' value="' . $search . '"' : '';
  // Return output
  return '<section id="index-filters__container" class="csc-container">
    <div id="index-filters--search">
      <label for="index-search"><i class="fa-solid fa-search"></i></label>
      <input type="text" name="search" id="index-search" tabindex="1"' . $searchOP . ' placeholder="' . $searchPlaceholder . '">
    </div>
    <div id="index-filters--actions">
      <div id="index-filters__sort-by__container">
        <strong>Sort by:</strong>
        <select id="index-filters--sort-by">
          <option value="az" selected="">A - Z</option>
          <option value="za">Z - A</option>
          <option value="newest">Newest</option>
          <option value="oldest">Oldest</option>
          ' . $sortByOP . '
        </select>
      </div>
      <nav>
        <ol>
          <li><button type="button" class="csc-btn--tiny csc-btn--outlined index-pagination-btn" data-btn-type="first" data-page-num="1" data-tippy-content="First Page"><i class="fa-solid fa-backward-fast"></i></button></li>
          <li><button type="button" class="csc-btn--tiny csc-btn--outlined index-pagination-btn" data-btn-type="prev" data-page-num="" data-tippy-content="Previous Page"><i class="fa-solid fa-backward-step"></i></button></li>
          <li><button type="button" class="csc-btn--tiny csc-btn--outlined index-pagination-btn" data-btn-type="next" data-page-num="" data-tippy-content="Next Page"><i class="fa-solid fa-forward-step"></i></button></li>
          <li><button type="button" class="csc-btn--tiny csc-btn--outlined index-pagination-btn" data-btn-type="last" data-page-num="" data-tippy-content="Last Page"><i class="fa-solid fa-forward-fast"></i></button></li>
        </ol>
      </nav>
      <div id="index-filters__per-page__container">
        <strong>View:</strong>
        <select id="index-filters--pagination-per-page">
          ' . $perPageOP . '
        </select>
      </div>
    </div>
  </section>';
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
