<?php

/**
 * The "Filter" related functions file
 *
 * @package Cornerstone
 */

/**
 * Return a value of items to sort by
 *
 * @param array $canSortBy An associative array of columns that can be sorted by. Format: $key => $value = $shortValue => $columnName(s).
 * @param array $defaultSort An associative array for fall back if the requested sort isn't valid. Format: array('sort' => 'id', 'order' => 'ASC') (the array MUST have a valid `sort` and `order` key).
 * @param mixed $params The params fed from the method to find the sort and order values
 *
 * @return array Will return an array with a `sort` and `order` value, and `showFilter` if matched.
 */
function get_sort_order(array $canSortBy, array $defaultSort, ...$params)
{

  // Set data
  $return = array();

  // Check for sort
  if (array_search('sort', $params) !== FALSE && !empty($params[array_search('sort', $params) + 1])) {
    // Get key of 'sort'
    $arrayKey = array_search('sort', $params);
    // Set what column to order by
    $sort = htmlspecialchars(urldecode(trim($params[$arrayKey + 1])));
    // Check if is a valid column to sort by
    if (array_key_exists($sort, $canSortBy)) {

      // Set column to sort by
      $return['sort'] = $canSortBy[$sort];

      // Check what direction to sort by
      $order = (!empty($params[$arrayKey + 2])) ? strtoupper(htmlspecialchars(stripslashes(urldecode(trim($params[$arrayKey + 2]))))) : '';

      // Set what direction to sort by
      $return['order'] = (in_array($order, array("DESC", "ASC"))) ? $order : 'ASC';

      // Set `showFilter` to true
      $return['showFilter'] = TRUE;
      $return['sortFilter'] = ucwords($sort);

      return $return;
      exit;
    } // Requested sort was not a valid column. Define defaults

  } // No sort by set. Define defaults

  $return['sort'] = $defaultSort['sort'];
  $return['order'] = $defaultSort['order'];

  return $return;
  exit;
}

/**
 * Return a link with the sort order attached
 *
 * @param string $sortItem The item to sort by
 * @param string $dir (optional) The direction to sort by. Defaults to "ASC"
 * @param string $uri (optional) The uri to change. Make sure to exclude the host (http(s)://(www).example.com). Will use the current URI if not supplied.
 *
 * @return string Will return the new URI string
 */
function get_sort_url(string $sortItem, string $dir = "", string $uri = "")
{
  // Variable assignments
  //-------------------------------------------------------------------
  // Check if URL is empty
  $uri = (empty($url)) ? $_SERVER['REQUEST_URI'] : $uri;
  // Get the request URI
  $request_uri = explode('?', $_SERVER['REQUEST_URI']);
  // Set the query string
  $query_string = (!empty($request_uri[1])) ? '?' . $request_uri[1] : '';
  // Set the request URI
  $request_uri = explode('/', trim($request_uri[0], '/'));

  // Check if the sort is set
  if (array_search('sort', $request_uri) === FALSE) {
    // Set sort if not set
    array_push($request_uri, 'sort');
  }

  // Set the sort key
  $sortKey = array_search('sort', $request_uri);

  // Check if $dir is empty
  $rDirSet = FALSE;
  if (empty($dir)) {
    // Check if request URI order by is set and one of allowed items
    if (!empty($request_uri[$sortKey + 2]) && in_array(strtoupper($request_uri[$sortKey + 2]), array("DESC", "ASC"))) {
      // Request URI order by is set
      $rDirSet = TRUE;
      // Check if the sort matches
      if (strtolower($sortItem) == strtolower($request_uri[$sortKey + 1])) {
        // Swap $dir
        $dir = (strtoupper($request_uri[$sortKey + 2]) == "ASC") ? "DESC" : "ASC";
      } else { // Sort doesn't match. Default to "ASC"
        $dir = "ASC";
      }
    } else { // Request URI order by is not set. Default to "ASC"
      $dir = "ASC";
    }
  }

  // Add sort item into the URL
  if (!empty($request_uri[$sortKey + 1]) && !is_string($request_uri[$sortKey + 1])) {
    // element after "sort" was not a string. Insert in
    array_splice($request_uri, $sortKey + 1, 0, $sortItem);
  } else {
    // element after "sort" was string. Update
    $request_uri[$sortKey + 1] = $sortItem;
  }

  // Check if order by is set and one of allowed items
  if ($rDirSet) {
    // Order by set. Update if new direction is valid
    if (!empty($dir) && in_array($dir, array("DESC", "ASC"))) {
      // Direction is valid. Update.
      $request_uri[$sortKey + 2] = strtolower($dir);
    }
  } else { // Order by is not set or valid. Insert in if valid
    // Insert in if valid
    if (!empty($dir) && in_array($dir, array("DESC", "ASC"))) {
      // Direction is valid. Update.
      array_splice($request_uri, $sortKey + 2, 0, strtolower($dir));
    }
  }

  // Rebuild the URI and return
  return implode('/', $request_uri) . $query_string;
}

/**
 * Check if the current column is being sorted
 *
 * @param string $sortItem The item to check
 * @param string $defaultSort (optional) The tables default sort item. Defaults to blank
 * @param string $uri (optional) The uri to check. Make sure to exclude the host (http(s)://(www).example.com). Will use the current URI if not supplied.
 *
 * @return bool|object Will return FALSE if no match or data if match
 */
function check_sort_item(string $sortItem, string $defaultSort = null, string $uri = null)
{
  // Variable assignments
  //-------------------------------------------------------------------
  // Check if URL is empty
  $uri = (empty($url)) ? $_SERVER['REQUEST_URI'] : $uri;
  // Get the request URI
  $request_uri = explode('?', $_SERVER['REQUEST_URI']);
  // Set the request URI
  $request_uri = explode('/', trim($request_uri[0], '/'));
  // Get the sort key
  $sortKey = array_search('sort', $request_uri);
  // Set the default direction
  $dir = "asc";

  // Check the sort direction
  if ($sortKey !== FALSE && !empty($request_uri[$sortKey + 2]) && in_array(strtoupper($request_uri[$sortKey + 2]), array("DESC", "ASC"))) {
    // Check if the sort matches
    if (strtolower($sortItem) == strtolower($request_uri[$sortKey + 1])) {
      // Swap $dir
      $dir = (strtoupper($request_uri[$sortKey + 2]) == "ASC") ? "asc" : "desc";
    }
  }

  // Check if the sort is set
  if ($sortKey !== FALSE) {

    // Check if sort item is set
    if (!empty($request_uri[$sortKey + 1]) && is_string($request_uri[$sortKey + 1])) {
      // Sort item is set. Check if matches $sortItem
      if ($request_uri[$sortKey + 1] == $sortItem) {
        // Match. Return TRUE and direction
        return (object) array('result' => TRUE, 'dir' => $dir);
        exit;
      } // No match. Check for default sort
    } // Sort item is not set. Check for default sort
  } else if (!empty($defaultSort)) { //  Check if $defaultSort set
    // Default sort set. Check if matches $sortItem
    if ($defaultSort == $sortItem) {
      // Match. Return TRUE and direction
      return (object) array('result' => TRUE, 'dir' => $dir);
      exit;
    } // No match. Return FALSE
  } // No successful checks. Return FALSE

  // Return false
  return FALSE;
  exit;
}
