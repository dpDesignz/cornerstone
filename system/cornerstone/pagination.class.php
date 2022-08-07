<?php

namespace Cornerstone;

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) 2019-2020, dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 */

/**
 * Pagination Class
 */

class Pagination
{
  // Define public properties
  protected $total_records = 0;
  protected $current_page = 1;
  protected $items_per_page = 20;
  protected $pages_per_direction = 3;
  protected $request_uri = '';

  /**
   * Set the options for the pagination
   *
   * @param int $set_total_records `[optional]` The total amount of records available
   * @param int $set_current_page `[optional]` The current page (offset)
   * @param int $set_items_per_page `[optional]` The total amount of items to display (limit)
   * @param int $set_pages_per_direction `[optional]` The total amount of pages to display either side of the current page
   *
   */
  public function set_props(int $set_total_records = null, int $set_current_page = null, $set_items_per_page = null, $set_pages_per_direction = null)
  {
    // Set the property
    $this->total_records = (!empty($set_total_records)) ? $set_total_records : $this->total_records;
    $this->current_page = (!empty($set_current_page)) ? $set_current_page : $this->current_page;
    $this->items_per_page = (!empty($set_items_per_page)) ? $set_items_per_page : $this->items_per_page;
    $this->pages_per_direction = (!empty($set_pages_per_direction)) ? $set_pages_per_direction : $this->pages_per_direction;
  }

  /**
   * Output pagination for datatable footers
   * Based on https://applite.com/pagination-php-mysql/
   *
   * @return string Will return the pagination as a string
   */
  public function render()
  {

    // Variable assignments
    //-------------------------------------------------------------------
    // Get the request URI
    $request_uri = explode('?', $_SERVER['REQUEST_URI']);
    // Set the query string
    $query_string = (!empty($request_uri[1])) ? '?' . $request_uri[1] : '';
    // Set the request URI
    $request_uri = explode('/', trim($request_uri[0], '/'));
    // Set the base URL
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/";
    // Selection options for $items_per_page.  The user will see links to select these options for "Items per page".
    $ITEMS_STEPS = array(10, 25, 50, 100, 250, 500);
    /* Items per page - check for the var "items_per_page" and check if it's a value listed in $ITEMS_STEPS, otherwise, assign a default.
    * The default value will be the closest value in $ITEMS_STEPS
    */
    $items_per_page = (!empty($items_per_page) && in_array($this->items_per_page, $ITEMS_STEPS)) ? intval($this->items_per_page) : getClosest($this->items_per_page, $ITEMS_STEPS);
    // Check items per page also isn't set in the URL
    $items_per_page = (array_search('limit', $request_uri) !== FALSE && !empty($request_uri[array_search('limit', $request_uri) + 1]) && in_array(intval(trim($request_uri[array_search('limit', $request_uri) + 1])), $ITEMS_STEPS)) ? getClosest(intval(trim($request_uri[array_search('limit', $request_uri) + 1])), $ITEMS_STEPS) : $items_per_page;
    // Check current page also isn't set in the URL
    $current_page = (array_search('page', $request_uri) !== FALSE && !empty($request_uri[array_search('page', $request_uri) + 1])) ? intval(trim($request_uri[array_search('page', $request_uri) + 1])) : $this->current_page;
    // Modulo is the division remainder of $a/$b
    $total_modulo = $this->total_records % $items_per_page;
    // Total pages/last page - If modulo has a remainder, add an additional page
    $total_pages = ($total_modulo == 0) ? $this->total_records / $items_per_page : ceil($this->total_records / $items_per_page);
    // Current start item
    $current_start_item = ($current_page > 1) ? (($current_page - 1) * $items_per_page) : 1;

    // Debugging data display
    //-------------------------------------------------------------------
    // print '<pre><div style="border: 1px dashed #000;"><strong>Debug Data</strong><br />';
    // printf('<div class="debug"><strong>Items Per Page:</strong> %s</div>', $items_per_page);
    // printf('<div class="debug"><strong>Current Page: </strong> %s</div>', $current_page);   // Front end/human readable page#
    // printf('<div class="debug"><strong>Current Start Item:</strong> %s</div>', $current_start_item);
    // printf('<div class="debug"><strong>Current End Item:</strong> %s</div>', ($current_start_item + $items_per_page));
    // printf('<div class="debug"><strong>Total SQL results:</strong> %s</div>', $total_records);
    // printf('<div class="debug"><strong>Total/Last Page:</strong> %s <em>(floor|ceil(total/per page))</em></div>', $total_pages);
    // print '</div></pre>';

    // Navigation links - Items Per Page & First/Previous/<steps>/Next/Last
    //-------------------------------------------------------------------
    // Start output
    $returnOutput = '<span class="cs-pagination">';
    // Items per page
    $LINKS = array();
    $LINKSURI = $request_uri;
    // Check if the limit is set
    if (array_search('limit', $LINKSURI) === FALSE) {
      // Set limit if not set
      array_push($LINKSURI, 'limit');
    }
    // Loop through the step options
    foreach ($ITEMS_STEPS as $step) {
      // Reset current page to show same data
      if (!empty($LINKSURI[array_search('page', $LINKSURI) + 1]) && is_numeric($LINKSURI[array_search('page', $LINKSURI) + 1])) {
        // element after "item" was too high. Update
        $LINKSURI[array_search('page', $LINKSURI) + 1] = ceil($current_start_item / $step);
      }
      // Check if current page is greater than total pages would be
      if (!empty($LINKSURI[array_search('page', $LINKSURI) + 1]) && is_numeric($LINKSURI[array_search('page', $LINKSURI) + 1]) && ceil($this->total_records / $step) < $LINKSURI[array_search('page', $LINKSURI) + 1]) {
        // element after "item" was too high. Update
        $LINKSURI[array_search('page', $LINKSURI) + 1] = ceil($this->total_records / $step);
      }
      // Add limit step back into the URL
      if (!empty($LINKSURI[array_search('limit', $LINKSURI) + 1]) && !is_numeric($LINKSURI[array_search('limit', $LINKSURI) + 1])) {
        // element after "item" was not numeric. Insert in
        array_splice($LINKSURI, array_search('limit', $LINKSURI) + 1, 0, $step);
      } else {
        // element after "item" was numeric. Update
        $LINKSURI[array_search('limit', $LINKSURI) + 1] = $step;
      }
      // Rebuild the url
      $linksURL = $base_url . implode('/', $LINKSURI) . $query_string;
      // Items per page navigation
      $LINKS[] = ($step != $items_per_page) ? sprintf('<option value="%s">%d</option>', $linksURL, $step)
        : sprintf('<option value="%s" selected>%d</option>', $linksURL, $step);
    }
    $returnOutput .= sprintf('<span class="cs-pagitem--items">Items Per Page: <select class="cs-pagitems">%s</select></span>', implode('', $LINKS));

    // Total items
    $pageFrom = ($current_page - 1) * $items_per_page;
    $pageTo = $pageFrom + $items_per_page;
    $pageTo = ($pageTo > $this->total_records) ? $this->total_records : $pageTo;
    $returnOutput .= '<span class="cs-pagitem--info">' . ($pageFrom + 1) . ' - ' . $pageTo . ' of ' . $this->total_records . '</span>';

    // Page URI
    $PAGESURI = $request_uri;

    // Start pages output
    $returnOutput .= '<ul role="navigation" aria-label="pagination">';

    // Check if the current page is set
    if (array_search('page', $PAGESURI) === FALSE) {
      // Set current page if not set
      array_push($PAGESURI, 'page');
    }

    // First page
    if ($total_pages > ($this->pages_per_direction * 2)) {
      // Add current page back into the URL
      if (!empty($PAGESURI[array_search('page', $PAGESURI) + 1]) && !is_numeric($PAGESURI[array_search('page', $PAGESURI) + 1])) {
        // element after "page" was not numeric. Insert in
        array_splice($PAGESURI, array_search('page', $PAGESURI) + 1, 0, '1');
      } else {
        // element after "item" was numeric or empty. Update/add
        $PAGESURI[array_search('page', $PAGESURI) + 1] = '1';
      }
      // Rebuild the url
      $pageURL = $base_url . implode('/', $PAGESURI) . $query_string;
      // Check if first page
      $first_page_class = ($current_page == 1) ? 'cs-pagitem--disabled' : 'waves-effect';
      $first_page_URL = ($current_page == 1) ? 'javascript:void();' : $pageURL;
      $returnOutput .= '<li class="' . $first_page_class . '"><a href="' . $first_page_URL . '" data-tippy-content="First page"><i class="fas fa-angle-double-left"></i></a></li>';
    }

    // Previous page
    if ($current_page > 1) {
      // Add previous page into the URL
      if (!empty($PAGESURI[array_search('page', $PAGESURI) + 1]) && !is_numeric($PAGESURI[array_search('page', $PAGESURI) + 1])) {
        // element after "page" was not numeric. Insert in
        array_splice($PAGESURI, array_search('page', $PAGESURI) + 1, 0, ($current_page - 1));
      } else {
        // element after "item" was numeric or empty. Update/add
        $PAGESURI[array_search('page', $PAGESURI) + 1] = ($current_page - 1);
      }
      // Rebuild the url
      $pageURL = $base_url . implode('/', $PAGESURI) . $query_string;
      $returnOutput .= '<li class="waves-effect"><a href="' . $pageURL . '" data-tippy-content="Previous page"><i class="fas fa-chevron-left"></i></a></li>';
    }

    // Display links to specific page numbers IF we have a sufficient number of pages.
    // Set the first output page number
    $start_page = (($current_page - $this->pages_per_direction) > 0) ? $current_page - $this->pages_per_direction : 1;
    // Check the last page number difference
    $end_page_difference = (($start_page + $current_page) - ($this->pages_per_direction * 2)) + 1;
    $end_page_difference = ($end_page_difference < 0) ? abs($end_page_difference) : 0;
    // Set the last output page number
    $end_page = (($current_page + $this->pages_per_direction + $end_page_difference) > $total_pages) ? $total_pages : $current_page + $this->pages_per_direction + $end_page_difference;
    // Check the first page number difference
    if ($total_pages > ($this->pages_per_direction * 2)) {
      $start_page_difference = (($end_page - $current_page) - ($this->pages_per_direction * 2)) + 3;
      $start_page_difference = ($start_page_difference < 0) ? abs($start_page_difference) : 0;
      // Re-init start page
      $start_page = $start_page - $start_page_difference;
    }
    $i = $start_page; // Starting number
    $page_no = $i; // Page number
    if ($total_pages > 1) {
      // Output page numbers
      for ($i; $i <= $end_page; $i++) {
        $page_no = $i; // Page number
        // Add page number into the URL
        if (!empty($PAGESURI[array_search('page', $PAGESURI) + 1]) && !is_numeric($PAGESURI[array_search('page', $PAGESURI) + 1])) {
          // element after "page" was not numeric. Insert in
          array_splice($PAGESURI, array_search('page', $PAGESURI) + 1, 0, $page_no);
        } else {
          // element after "item" was numeric or empty. Update/add
          $PAGESURI[array_search('page', $PAGESURI) + 1] = $page_no;
        }
        // Rebuild the url
        $pageURL = $base_url . implode('/', $PAGESURI) . $query_string;
        // Check if current page
        $current_page_class = ($page_no == $current_page) ? 'cs-pagitem--active' : 'waves-effect';
        $current_page_URL = ($page_no == $current_page) ? 'javascript:void();' : $pageURL;
        $returnOutput .= '<li class="' . $current_page_class . '"><a href="' . $current_page_URL . '">' . $page_no . '</a></li>';
      }

      // Don't show "Next Page" if there is no next page
      if ($current_page < $total_pages) {
        // Add next page into the URL
        if (!empty($PAGESURI[array_search('page', $PAGESURI) + 1]) && !is_numeric($PAGESURI[array_search('page', $PAGESURI) + 1])) {
          // element after "page" was not numeric. Insert in
          array_splice($PAGESURI, array_search('page', $PAGESURI) + 1, 0, ($current_page + 1));
        } else {
          // element after "item" was numeric or empty. Update/add
          $PAGESURI[array_search('page', $PAGESURI) + 1] = ($current_page + 1);
        }
        // Rebuild the url
        $pageURL = $base_url . implode('/', $PAGESURI) . $query_string;
        $returnOutput .= '<li class="waves-effect"><a href="' . $pageURL . '" data-tippy-content="Next page"><i class="fas fa-chevron-right"></i></a></li>';
      }

      // Last Page
      if ($total_pages > ($this->pages_per_direction * 2)) {
        // Add next page into the URL
        if (!empty($PAGESURI[array_search('page', $PAGESURI) + 1]) && !is_numeric($PAGESURI[array_search('page', $PAGESURI) + 1])) {
          // element after "page" was not numeric. Insert in
          array_splice($PAGESURI, array_search('page', $PAGESURI) + 1, 0, $total_pages);
        } else {
          // element after "item" was numeric or empty. Update/add
          $PAGESURI[array_search('page', $PAGESURI) + 1] = $total_pages;
        }
        // Rebuild the url
        $pageURL = $base_url . implode('/', $PAGESURI) . $query_string;
        // Check if last page
        $last_page_class = ($current_page == $total_pages) ? 'cs-pagitem--disabled' : 'waves-effect';
        $returnOutput .= '<li class="' . $last_page_class . '"><a href="' . $pageURL . '" data-tippy-content="Last page"><i class="fas fa-angle-double-right"></i></a></li>';
      }
    }

    // End output
    $returnOutput .= '</ul></span>';

    return $returnOutput;
  }
}
