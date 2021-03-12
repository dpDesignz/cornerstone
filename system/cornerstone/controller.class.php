<?php

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) 2019-2021, dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 */

/**
 * Base Controller Class
 * Loads the models and views
 */

abstract class Controller
{
  // Init $errors
  protected $errorsData = array();
  // Init $data
  protected $data = array();
  // Init $params
  protected $params = array();
  // Init $prop
  protected $prop = array();
  // Set the registry
  protected $registry;

  /**
   * Constructor
   */
  public function __construct($registry)
  {
    $this->registry = $registry;
  }


  /**
   * Get
   *
   * @param	string	$key
   *
   * @return	mixed
   */
  public function __get($key)
  {
    return $this->registry->get($key);
  }

  /**
   * Set
   *
   * @param	string	$key
   * @param	string	$value
   */
  public function __set($key, $value)
  {
    $this->registry->set($key, $value);
  }

  /**
   * Default - 404
   *
   * Used to load a 404 page if the requested method isn't valid
   * or an index method isn't defined in the loaded controller.
   *
   * (No params)
   */
  public function error(...$params)
  {
    $this->load->view('404');
  }

  #########################
  ####    FUNCTIONS    ####
  #########################

  /**
   * Load a child controller
   *
   * @param string $setDirectory The root directory of the controller
   * @param string $setController The child controller you want to load
   * @param string $setMethod `[optional]` The set method you want to load. Defaults to null
   * @param string $defaultMethod `[optional]` The default method you want to load if one isn't found. Defaults to "index"
   * @param array $params `[optional]` Mixed params that may or may not be passed
   */
  protected function load_child_controller(string $setDirectory, string $setController, string $setMethod = null, string $defaultMethod = 'index', ...$params)
  {

    // Set default method fallback
    $defaultMethod = (!empty($defaultMethod)) ? $defaultMethod : 'index';

    // Set $rootDir to defined directory
    $rootDir = DIR_ROOT . trim($setDirectory) . _DS;

    // Set controller
    $setController = trim($setController);

    // Require the controller
    require_once($rootDir . 'controllers' . _DS . strtolower($setController) . '.php');

    // Instantiate controller class
    $setController = new $setController($this->registry, ...$params);

    // Check to see if set method exists in controller
    if (!empty($setMethod) && method_exists($setController, $setMethod)) {
      // Unset the method param if found
      if (array_search($setMethod, $params) !== FALSE) {
        array_splice($params, array_search($setMethod, $params), 1);
      }
    } else {
      // Set method to the default method
      $setMethod = $defaultMethod;
    }

    // Call a callback with array of params
    call_user_func_array([$setController, $setMethod], $params);

    exit;
  }

  /**
   * Init the re-used values for a list page
   *
   * @param string $pageURI The URI of the current page
   * @param array $params Mixed params that may or may not be passed
   *
   * @return array Will set the initial values for a list page.
   */
  protected function init_list_page(string $pageURI, ...$params)
  {
    // Check for search and rebuild URL
    if (isset($this->request->get['search'])) {
      redirectTo(rtrim($pageURI, '/') . '/search/' . urlencode($this->request->get['search']));
      exit;
    }

    // Set parameters
    $this->request->set_params($params);
    $this->params = array();
    $this->data['showFilter'] = FALSE;
    $this->data['filterData'] = '';

    // Check for a search term
    if (isset($this->request->params['search']) && !empty($this->request->params['search'])) {
      $this->params['search'] = $this->request->params['search'];
      $this->data['search'] = $this->params['search'];
      if (empty($this->data['breadcrumbs']) || !is_array($this->data['breadcrumbs'])) {
        $this->data['breadcrumbs'] = array();
      }
      $this->data['breadcrumbs'][] = array(
        'text' => 'Search: ' . $this->params['search'],
        'href' => get_site_url(rtrim($pageURI, '/') . '/search/' . urlencode($this->params['search']))
      );
    }

    // Check for page number
    if (isset($this->request->params['page']) && !empty($this->request->params['page'])) {
      // Set page number
      $this->params['page'] = (int) $this->request->params['page'];
    } else { // No page number. Set page number
      $this->params['page'] = 1;
    }

    // Check for a page limit
    if (isset($this->request->params['limit']) && !empty($this->request->params['limit'])) {
      $this->params['limit'] = (int) $this->request->params['limit'];
    } else { // No page limit. Set page limit
      $this->params['limit'] = 25;
    }

    // Set no results message
    if (!empty($this->data['search'])) {
      $this->data['no_results_msg'] = '<p class="csc-body1">Sorry, there were no results that matched your search for <em>"' . $this->data['search'] . '"</em>.</p><p class="csc-body2"><a href="' . get_site_url(rtrim($pageURI, '/')) . '" title="Clear search results">Clear search results</a></p>';
    }

    // Set no filter results message
    $this->data['no_filter_results_msg'] = '<p class="csc-body1">Sorry, there were no results that matched your filter.</p>';
  }

  /**
   * Return a value of items to sort by
   *
   * @param array $canSortBy An associative array of columns that can be sorted by. Format: $key => $value = $shortValue => $columnName(s).
   * @param array $defaultSort An associative array for fall back if the requested sort isn't valid. Format: array('sort' => 'id', 'order' => 'ASC') (the array MUST have a valid `sort` and `order` key).
   * @param mixed $params The params fed from the method to find the sort and order values
   *
   * @return array Will set values to the params with a `sort` and `order` value, and `showFilter` if matched.
   */
  protected function get_sort_order(array $canSortBy, array $defaultSort, ...$params)
  {

    // Check for default sort item
    $this->data['defaultSort'] = (array_search($defaultSort['sort'], $canSortBy) !== FALSE) ? array_search($defaultSort['sort'], $canSortBy) : '';

    // Check for sort
    if (array_search('sort', $params) !== FALSE && !empty($params[array_search('sort', $params) + 1])) {
      // Get key of 'sort'
      $arrayKey = array_search('sort', $params);
      // Set what column to order by
      $sort = htmlspecialchars(urldecode(trim($params[$arrayKey + 1])));
      // Check if is a valid column to sort by
      if (array_key_exists($sort, $canSortBy)) {

        // Set column to sort by
        $this->params['sort'] = $canSortBy[$sort];

        // Check what direction to sort by
        $order = (!empty($params[$arrayKey + 2])) ? strtoupper(htmlspecialchars(urldecode(trim($params[$arrayKey + 2])))) : '';

        // Set what direction to sort by
        $this->params['order'] = (in_array($order, array("DESC", "ASC"))) ? $order : 'ASC';

        // Set `showFilter` to true
        $this->params['showFilter'] = TRUE;
        $this->params['sortFilter'] = ucwords($sort);

        // Set show filter
        if (!empty($this->params['showFilter'])) {
          $this->data['showFilter'] = $this->params['showFilter'];
          $this->data['filterData'] .= 'Sort by = ' . $this->params['sortFilter'] . ', ';
        }
        return;
      } // Requested sort was not a valid column. Define defaults

    } // No sort by set. Define defaults

    $this->params['sort'] = $defaultSort['sort'];
    $this->params['order'] = $defaultSort['order'];
    return;
  }
}
