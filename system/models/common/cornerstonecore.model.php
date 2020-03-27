<?php

/**
 * Core Cornerstone Model
 *
 * @package Cornerstone
 */

class CornerstoneCore
{

  // Set the default properties
  private $conn;

  /**
   * Construct the User
   * No parameters required, nothing will be returned
   */
  public function __construct()
  {

    // Create a database connection
    $this->conn = new CornerstoneDBH;
  }

  /**
   * Get SEO data
   *
   * @param string $keyword Keyword of the URL
   *
   * @return object|bool Return object with results or FALSE if no results
   */
  public function getSEOData(string $keyword)
  {

    // Check keyword
    if (!empty($keyword) && is_string($keyword)) {

      // Run query to get results
      $results = $this->conn->dbh->selecting(
        DB_PREFIX . "seo_url",
        "*",
        where(
          eq('seo_keyword', $keyword)
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0) {

        // Return results
        return $results[0];
        exit;
      } // No results. Return FALSE.

    } // Keyword check failed. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Get Content data
   *
   * @param int $contentID ID of the content
   *
   * @return object|bool Return object with results or FALSE if no results
   */
  public function getContentData(int $contentID)
  {

    // Check values
    if (!empty($contentID) && is_numeric($contentID)) {

      // Run query to get results
      $results = $this->conn->dbh->selecting(
        DB_PREFIX . "content",
        "*",
        where(
          eq('content_id', $contentID, _AND),
          neq('content_status', '0', _AND),
          neq('content_status', '3')
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0) {

        // Return results
        return $results[0];
        exit;
      } // No results. Return FALSE.

    } // Value check failed. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Get Content meta data
   *
   * @param int $contentID ID of the content
   *
   * @return object|bool Return object with results or FALSE if no results
   */
  public function getContentMetaData(int $contentID)
  {

    // Check values
    if (!empty($contentID) && is_numeric($contentID)) {

      // Run query to get results
      $results = $this->conn->dbh->selecting(
        DB_PREFIX . "content_meta",
        "*",
        where(
          eq('cmeta_content_id', $contentID)
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0) {

        // Return results
        return $results;
        exit;
      } // No results. Return FALSE.

    } // Value check failed. Return FALSE.

    // Return FALSE
    return FALSE;
  }
}
