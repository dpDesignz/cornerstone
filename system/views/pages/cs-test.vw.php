<?php

// $csdb = new CornerstoneDBH;

// $csdb->dbh->debugOn();

// $userID = 1;

// $result = $csdb->dbh->selecting(DB_PREFIX . "login_log", "login_id", where(groupWhere(eq("login_user_type", "1", _AND), eq("login_user_id", $userID), _AND), eq("login_status", "3", _AND), ));

// echo $csdb->dbh->getNum_Rows();

// $csdb->dbh->debug();

// $csdb->dbh->varDump($result);

class Test {

  /**
   * Class Constructor
  */
  public function __construct() {

    $params = array('sort', 'rate');

    // Set parameters
    $this->params = array();
    $this->data['showFilter'] = FALSE;

    // Allowed sort fields
    $canSortBy = array('name' => 'tax_name', 'rate' => 'tax_rate_type, tax_rate');

    $sortData = $this->testMethod($canSortBy, array('sort' => 'tax_name', 'order' => 'ASC'), ...$params);

    echo '<pre>';
    print_r($sortData);
    echo '</pre>';
    exit;

  }

  public function testMethod ($canSortBy, $defaultSort, ...$params) {

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
        $return['showFilter'] = TRUE;

        return $return;
        exit;
      } // Reuqested sort was not a valid column. Define defaults
    } // No sort by set. Define defaults
    $return['sort'] = $defaultSort['sort'];
    $return['order'] = $defaultSort['order'];

    return $return;
    exit;

  }

}

new Test();

exit; ?>