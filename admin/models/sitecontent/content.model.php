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

  /**
   * Construct the User
   * No parameters required, nothing will be returned
   */
  public function __construct()
  {

    // Create a database connection
    $this->conn = new CornerstoneDBH;
  }

  ########################
  ####    SECTIONS    ####
  ########################

  /**
   * Get section
   *
   * @param int $sectionID ID of the section to retrieve
   *
   * @return object Return object with section data
   */
  public function getSection(int $sectionID)
  {

    // Check the ID is valid
    if (!empty($sectionID) && is_numeric($sectionID)) {
      $results = $this->conn->dbh->selecting(
        DB_PREFIX . "content_section",
        "section_id,
        section_name,
        section_type,
        section_directory_name",
        where(
          eq('section_id', $sectionID)
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($results)) {

        // Return results
        return $results[0];
      } // No results. Return FALSE.
    } // ID is not valid. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Get list of sections
   *
   * @param array $params Multiple paramters as required
   *
   * @return object Return object with list of sections
   */
  public function listSections($params = array())
  {

    // Set if counting or not
    $countResults = (!empty($params['count']) && $params['count'] == TRUE) ? TRUE : FALSE;

    // Build query
    $this->sql = array();
    $this->whereArray = array();

    // Check for search
    if (!empty($params['search'])) {
      $this->whereArray[] = like("UPPER(section_name)", "%" . strtoupper($params['search']) . "%", _AND);
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
        DB_PREFIX . "content_section",
        "COUNT(section_id) AS total_results",
        ...$this->sql
      );
    } else {

      // Run query to find data
      $results = $this->conn->dbh->selecting(
        DB_PREFIX . "content_section",
        "section_id,
        section_name,
        section_type,
        section_directory_name",
        ...$this->sql
      );
    }

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($results)) {

      // Return results
      return json_decode(json_encode(array('count' => $this->conn->dbh->getNum_Rows(), 'results' => $results)), FALSE);
    } // No results. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Add Section
   *
   * @param string $name Name of the section
   * @param int $type Type of section
   * @param string $directoryName `[optional]` Directory of the section. Defaults to "null"
   *
   * @return bool|int Will return FALSE if failed or true if successful.
   */
  public function addSection(string $name, int $type, string $directoryName = null)
  {

    // Add data into `cs_content_section`
    $this->conn->dbh->insert(
      DB_PREFIX . "content_section",
      array(
        'section_name' => $name,
        'section_type' => $type,
        'section_directory_name' => $directoryName,
        'section_added_id' => $_SESSION['_cs']['user']['uid'],
        'section_added_dtm' => date('Y-m-d H:i:s')
      )
    );

    // Check if added successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Return TRUE
      return TRUE;
    } // Unable to add. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Edit Section
   *
   * @param int $sectionID ID of the section
   * @param string $name Name of the section
   * @param int $type Type of section
   * @param string $directoryName `[optional]` Directory of the section. Defaults to "null"
   *
   * @return int Will return FALSE if failed or TRUE if successful.
   */
  public function editSection(int $sectionID, string $name, int $type, string $directoryName = null)
  {

    // Make sure the ID is a number
    if (!empty($sectionID) && is_numeric($sectionID)) {

      // Update row in `cs_content_section`
      $result = $this->conn->dbh->update(
        DB_PREFIX . "content_section",
        array(
          'section_name' => $name,
          'section_type' => $type,
          'section_directory_name' => $directoryName,
          'section_edited_id' => $_SESSION['_cs']['user']['uid'],
          'section_edited_dtm' => date('Y-m-d H:i:s')
        ),
        eq("section_id", $sectionID)
      );

      // Check if updated successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // Unable to edit. Return FALSE.

    } // ID is not a number. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  #####################
  ####    PAGES    ####
  #####################

  /**
   * Get page
   *
   * @param int $pageID ID of the page to retrieve
   *
   * @return object Return object with page data
   */
  public function getPage(int $pageID)
  {

    // Check the ID is valid
    if (!empty($pageID) && is_numeric($pageID)) {
      $contentData = $this->conn->dbh->selecting(
        DB_PREFIX . "content AS c",
        "c.content_id,
        c.content_title,
        c.content_content,
        c.content_status,
        cs.section_id,
        (SELECT seo_keyword FROM cs_seo_url WHERE seo_type_id = c.content_id AND seo_type = '0' ORDER BY seo_id DESC LIMIT 1) AS content_slug",
        leftJoin("c", DB_PREFIX . "content_section", "content_section_id", "section_id", "cs"),
        where(
          eq('c.content_id', $pageID)
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0) {

        // Init content meta data return
        $contentMetaDataReturn = array();

        // Check for content meta
        if ($contentMetaData = $this->getContentMetaData((int) $contentData[0]->content_id)) {
          // Meta data exists

          // Loop through data
          foreach ($contentMetaData as $metaDataOutput) {
            $contentMetaDataReturn['content_' . $metaDataOutput->cmeta_key] = $metaDataOutput->cmeta_value;
          }
        }

        // Return results
        return json_decode(json_encode(array('content' => $contentData[0], 'content_meta' => (object) $contentMetaDataReturn, FALSE)), FALSE);
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
   * @return object Return object with list of pages
   */
  public function listPages($params = array())
  {

    // Set if counting or not
    $countResults = (!empty($params['count']) && $params['count'] == TRUE) ? TRUE : FALSE;

    // Build query
    $this->sql = array();
    $this->whereArray = array();

    // Check for search
    if (!empty($params['search'])) {
      $this->whereArray[] = like("UPPER(c.content_title)", "%" . strtoupper($params['search']) . "%", _AND);
    }

    // Check for status
    if (!empty($params['filter_status'])) {
      // Get status
      switch (trim($params['filter_status'])) {
        case 'draft':
          $statusInt = 0;
          break;
        case 'published':
          $statusInt = 1;
          break;
        case 'private':
          $statusInt = 2;
          break;
        case 'archived':
          $statusInt = 3;
          break;

        default:
          $statusInt = null;
          break;
      }

      // Filter by status if not null
      if ($statusInt !== null) {
        $this->whereArray[] = eq("c.content_status", $statusInt, _AND);
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
        DB_PREFIX . "content AS c",
        "COUNT(c.content_id) AS total_results",
        ...$this->sql
      );
    } else {

      // Run query to find data
      $results = $this->conn->dbh->selecting(
        DB_PREFIX . "content AS c",
        "c.content_id,
        c.content_title,
        c.content_status,
        c.content_added_dtm,
        c.content_edited_dtm,
        cs.section_id,
        cs.section_name,
        cs.section_directory_name,
        CONCAT(ua.user_first_name, ' ', ua.user_last_name) AS added_by,
        CONCAT(ue.user_first_name, ' ', ue.user_last_name) AS edited_by,
        (SELECT seo_keyword FROM cs_seo_url WHERE seo_type_id = c.content_id AND seo_type = '0' ORDER BY seo_id DESC LIMIT 1) AS content_slug",
        leftJoin("c", DB_PREFIX . "content_section", "content_section_id", "section_id", "cs"),
        leftJoin("c", DB_PREFIX . "users", "content_added_id", "user_id", "ua"),
        leftJoin("c", DB_PREFIX . "users", "content_edited_id", "user_id", "ue"),
        ...$this->sql
      );
    }

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0) {

      // Return results
      return json_decode(json_encode(array('count' => $this->conn->dbh->getNum_Rows(), 'results' => $results)), FALSE);
    } // No results. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Get list of page sections
   *
   * @return object Return object with list of sections
   */
  public function listPageSections()
  {

    // Run query to find data
    $results = $this->conn->dbh->selecting(
      DB_PREFIX . "content_section",
      "section_id,
      section_name",
      where(
        eq("section_type", "0")
      )
    );

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($results)) {

      // Return results
      return $results;
    } // No results. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Add Page
   *
   * @param string $title Title of the page
   * @param string $content Content of the page
   * @param int $status Status of the page
   * @param int $sectionID `[optional]` Section ID of the page. Defaults to "0"
   *
   * @return bool|int Will return FALSE if failed or inserted ID if successful.
   */
  public function addPage(string $title, string $content, int $status, int $sectionID = 0)
  {

    // Add data into `cs_content`
    $this->conn->dbh->insert(
      DB_PREFIX . "content",
      array(
        'content_title' => $title,
        'content_content' => $content,
        'content_status' => $status,
        'content_type' => '0',
        'content_section_id' => $sectionID,
        'content_added_id' => $_SESSION['_cs']['user']['uid'],
        'content_added_dtm' => date('Y-m-d H:i:s')
      )
    );

    // Check if added successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Return inserted ID
      return $this->conn->dbh->getInsert_Id();
    } // Unable to add. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Add Page Meta Data
   *
   * @param int $contentID ID of the page
   * @param string $metaTitle [optional] Meta Title of category
   * @param string $metaDescription [optional] Meta Description of
   *
   * @return bool|int Will return FALSE if failed or true if successful.
   */
  public function addPageMetaData(int $contentID, string $metaTitle = '', string $metaDescription = '')
  {

    // Init added success
    $addedSuccess = 0;

    // Add meta_title into `cs_content_meta`
    $this->conn->dbh->insert(
      DB_PREFIX . "content_meta",
      array(
        'cmeta_content_id' => $contentID,
        'cmeta_key' => 'meta_title',
        'cmeta_value' => $metaTitle,
        'cmeta_added_id' => $_SESSION['_cs']['user']['uid'],
        'cmeta_added_dtm' => date('Y-m-d H:i:s')
      )
    );

    // Check if added successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Increment added success
      $addedSuccess++;
    }

    // Add meta_description into `cs_content_meta`
    $this->conn->dbh->insert(
      DB_PREFIX . "content_meta",
      array(
        'cmeta_content_id' => $contentID,
        'cmeta_key' => 'meta_description',
        'cmeta_value' => $metaDescription,
        'cmeta_added_id' => $_SESSION['_cs']['user']['uid'],
        'cmeta_added_dtm' => date('Y-m-d H:i:s')
      )
    );

    // Check if added successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Increment added success
      $addedSuccess++;
    }

    // Check if data was added
    if ($addedSuccess > 0) {

      // Data was added. Return TRUE
      return TRUE;
    } // Data failed to be added. Return FALSE


    // Return FALSE
    return FALSE;
  }

  /**
   * Edit Page
   *
   * @param int $pageID ID of the page
   * @param string $title Title of the page
   * @param string $content Content of the page
   * @param int $status Status of the page
   * @param int $sectionID `[optional]` Section ID of the page. Defaults to "0"
   *
   * @return int Will return FALSE if failed or TRUE if successful.
   */
  public function editPage(int $pageID, string $title, string $content, int $status, int $sectionID = 0)
  {

    // Make sure the ID is a number
    if (!empty($pageID) && is_numeric($pageID)) {

      // Update row in `cs_content`
      $result = $this->conn->dbh->update(
        DB_PREFIX . "content",
        array(
          'content_title' => $title,
          'content_content' => $content,
          'content_status' => $status,
          'content_section_id' => $sectionID,
          'content_edited_id' => $_SESSION['_cs']['user']['uid'],
          'content_edited_dtm' => date('Y-m-d H:i:s')
        ),
        eq("content_id", $pageID)
      );

      // Check if updated successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // Unable to edit. Return FALSE.

    } // ID is not a number. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Add Page Meta Data
   *
   * @param int $contentID ID of the page
   * @param string $metaTitle [optional] Meta Title of category
   * @param string $metaDescription [optional] Meta Description of
   *
   * @return bool|int Will return FALSE if failed or true if successful.
   */
  public function editPageMetaData(int $contentID, string $metaTitle = '', string $metaDescription = '')
  {

    // Init edited success
    $editedSuccess = 0;

    // Update meta_title in `cs_content_meta`
    $this->conn->dbh->update(
      DB_PREFIX . "content_meta",
      array(
        'cmeta_value' => $metaTitle,
        'cmeta_edited_id' => $_SESSION['_cs']['user']['uid'],
        'cmeta_edited_dtm' => date('Y-m-d H:i:s')
      ),
      eq("cmeta_content_id", $contentID, _AND),
      eq("cmeta_key", 'meta_title')
    );

    // Check if edited successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Increment edited success
      $editedSuccess++;
    }

    // Update meta_description in `cs_content_meta`
    $this->conn->dbh->update(
      DB_PREFIX . "content_meta",
      array(
        'cmeta_value' => $metaDescription,
        'cmeta_edited_id' => $_SESSION['_cs']['user']['uid'],
        'cmeta_edited_dtm' => date('Y-m-d H:i:s')
      ),
      eq("cmeta_content_id", $contentID, _AND),
      eq("cmeta_key", 'meta_description')
    );

    // Check if edited successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Increment edited success
      $editedSuccess++;
    }

    // Check if data was edited
    if ($editedSuccess > 0) {

      // Data was edited. Return TRUE
      return TRUE;
    } // Data failed to be edited. Return FALSE


    // Return FALSE
    return FALSE;
  }

  #######################
  ####    CONTENT    ####
  #######################

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
