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
 * @return string Will return an array with a `sort` and `order` value, and `showFilter` if matched.
*/
function get_sort_order(array $canSortBy, array $defaultSort, ...$params) {

  // Set data
  $return = array();

  // Check for sort
  if(array_search('sort', $params) !== FALSE && !empty($params[array_search('sort', $params) + 1])) {
    // Get key of 'sort'
    $arrayKey = array_search('sort', $params);
    // Set what column to order by
    $sort = htmlspecialchars(stripslashes(urldecode(trim($params[$arrayKey + 1]))));
    // Check if is a valid column to sort by
    if(array_key_exists($sort, $canSortBy)) {

      // Set column to sort by
      $return['sort'] = $canSortBy[$sort];

      // Check what direction to sort by
      $order = (!empty($params[$arrayKey + 2])) ? strtoupper(htmlspecialchars(stripslashes(urldecode(trim($params[$arrayKey + 2]))))) : '';

      // Set what direction to sort by
      $return['order'] = (in_array($order, array("DESC", "ASC"))) ? $order : 'ASC' ;

      // Set `showFilter` to true
      $return['showFilter'] = TRUE;

      return $return;
      exit;

    } // Requested sort was not a valid column. Define defaults

  } // No sort by set. Define defaults

  $return['sort'] = $defaultSort['sort'];
  $return['order'] = $defaultSort['order'];

  return $return;
  exit;

}