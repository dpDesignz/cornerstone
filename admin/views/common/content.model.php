<?php

/**
 * Content Model
 *
 * @package Cornerstone
 * @subpackage Mission Equine
 */

class Content
{

  // Set the default properties
  private $conn;
  private $optn;

  /**
   * Construct the class
   * No parameters required, nothing will be returned
   */
  public function __construct($option)
  {

    // Create a database connection
    $this->conn = new CornerstoneDBH;
    // Set the options
    $this->optn = $option;
  }

  /**
   * Get page
   *
   * @param int $pageID ID of the page to retrieve
   *
   * @return object Return object with page data
   */
  public function getPage(int $productID)
  {

    // Check the ID is valid
    if (!empty($productID) && is_numeric($productID)) {
      $results = $this->conn->dbh->selecting(
        "me_product AS p",
        "p.product_id,
        p.product_type,
        p.product_image,
        p.product_name,
        p.product_article_no,
        s.supplier_name,
        c.category_name,
        b.brand_name,
        p.product_quantity,
        p.product_viewed,
        p.product_status,
        (SELECT seo_keyword FROM cs_seo_url WHERE seo_type_id = p.product_id AND seo_type = '1' ORDER BY seo_id DESC LIMIT 1) AS product_slug,
        p.product_edited_dtm,
        CONCAT(ue.user_first_name, ' ', ue.user_last_name) AS edited_by",
        leftJoin("p", "me_supplier", "product_supplier_id", "supplier_id", "s"),
        leftJoin("p", "me_product_category", "product_category_id", "category_id", "c"),
        leftJoin("p", "me_brand", "product_brand_id", "brand_id", "b"),
        leftJoin("p", "cs_users", "product_edited_id", "user_id", "ue"),
        where(
          eq('p.product_id', $productID)
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0) {

        // Return results
        return $results[0];
      } // No results. Return FALSE.
    } // ID is not valid. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Get list of pages
   *
   * @param array $params Multiple paramters as required
   *
   * @return object Return object with list of page
   */
  public function listPages($params = array())
  {

    // Set if counting or not
    $countResults = (!empty($params['count']) && $params['count'] == TRUE) ? TRUE : FALSE;

    // Build query
    $this->sql = array();
    $this->whereArray = array();

    // Set product type
    $this->whereArray[] = eq("p.product_type", "0", _AND);

    // Check for search
    if (!empty($params['search'])) {
      $this->whereArray[] = like("UPPER(p.product_sounds_like)", "%" . strtoupper(metaphone($params['search'])) . "%", _AND);
    }

    // Check for category ID
    if (!empty($params['filter_category_id'])) {
      $this->whereArray[] = eq("c.category_id", $params['filter_category_id'], _AND);
    }

    // Check for supplier ID
    if (!empty($params['filter_supplier_id'])) {
      $this->whereArray[] = eq("s.supplier_id", $params['filter_supplier_id'], _AND);
    }

    // Check for brand ID
    if (!empty($params['filter_brand_id'])) {
      $this->whereArray[] = eq("b.brand_id", $params['filter_brand_id'], _AND);
    }

    // Check for status
    if (!empty($params['filter_status'])) {
      // Get status
      switch (trim($params['filter_status'])) {
        case 'archived':
          $statusInt = 0;
          break;
        case 'active':
          $statusInt = 1;
          break;
        case 'hidden':
          $statusInt = 2;
          break;

        default:
          $statusInt = null;
          break;
      }

      // Filter by status if not null
      if ($statusInt !== null) {
        $this->whereArray[] = eq("p.product_status", $statusInt, _AND);
      }
    }

    // Combine where
    if (!empty($this->whereArray)) {
      $this->sql[] = where(...$this->whereArray);
    }

    // Check for sort
    if (!$countResults && !empty($params['sort']) && !empty($params['order'])) {
      $this->sql[] = orderBy($params['sort'], $params['order']);
    }

    // Check for page number/limit
    if (!$countResults && !empty($params['limit'])) {
      // Check for page number
      if (!empty($params['page'])) {
        $offset = ($params['page'] - 1) * $params['limit'];
      } else {
        $offset = 0;
      }
      $this->sql[] = limit($params['limit'], $offset);
    }

    if ($countResults) {

      // Run query to count data
      $results = $this->conn->dbh->selecting(
        "me_product AS p",
        "COUNT(p.product_id) AS total_results",
        leftJoin("p", "me_supplier", "product_supplier_id", "supplier_id", "s"),
        leftJoin("p", "me_product_category", "product_category_id", "category_id", "c"),
        leftJoin("p", "me_brand", "product_brand_id", "brand_id", "b"),
        ...$this->sql
      );
    } else {

      // Run query to find data
      $results = $this->conn->dbh->selecting(
        "me_product AS p",
        "p.product_id,
        c.category_id",
        leftJoin("p", "me_supplier", "product_supplier_id", "supplier_id", "s"),
        leftJoin("p", "me_product_category", "product_category_id", "category_id", "c"),
        leftJoin("p", "me_brand", "product_brand_id", "brand_id", "b"),
        leftJoin("p", "cs_users", "product_edited_id", "user_id", "ue"),
        ...$this->sql
      );
    }

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0) {

      // Check if wanting the count returned
      if ($countResults) {

        // Return the total count
        return $results[0]->total_results;
        exit;
      } else { // Wanting to return product data

        // Init the product data
        $productData = array();

        // Loop through results
        foreach ($results as $result) {
          // Get/Set the product data
          $productData[$result->product_id] = $this->getProduct((int) $result->product_id);
        }

        // Return results
        return json_decode(json_encode(array('count' => $this->conn->dbh->getNum_Rows(), 'results' => json_decode(json_encode($productData)), FALSE)), FALSE);
      }
    } // No results. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Add Page
   *
   * @param int $pageID ID of the page
   * @param int $tagID ID of the category
   *
   * @return bool|int Will return FALSE if failed or true if successful.
   */
  public function addPage(int $productID, int $tagID)
  {

    // Add link into `me_product_to_tag`
    $this->conn->dbh->insert("me_product_to_tag", array('ptt_product_id' => $productID, 'ptt_tag_id' => $tagID));

    // Check if added successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Return TRUE
      return TRUE;
    } // Unable to add. Return FALSE.

    // Return FALSE
    return FALSE;
  }
}
