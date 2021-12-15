<?php

/**
 * Content Model
 *
 * @package Cornerstone
 * @subpackage Mission Equine
 */

use function ezsql\functions\{
  selecting,
  inserting,
  updating,
  deleting,
  leftJoin,
  where,
  eq,
  like,
  orderBy,
  limit
};

class Content extends Cornerstone\ModelBase
{

  // Set the default properties

  /**
   * Construct the User
   * No parameters required, nothing will be returned
   */
  public function __construct($cdbh, $option)
  {
    // Load the model base constructor
    parent::__construct($cdbh, $option);
    $this->conn->dbh->tableSetup('content_section', DB_PREFIX);
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
      // Get data
      $this->conn->dbh->tableSetup('content_section', DB_PREFIX);
      $sectionResults = selecting(
        "section_id,
        section_name,
        section_type,
        section_location_name",
        where(
          eq('section_id', $sectionID)
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($sectionResults)) {

        // Return results
        return $sectionResults[0];
      } // No results. Return FALSE.
    } // ID is not valid. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Get list of sections
   *
   * @param array $params Multiple parameters as required
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

    // Set section type not menu
    // $this->whereArray[] = neq("section_type", "5", _AND);

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

    // Setup table
    $this->conn->dbh->tableSetup('content_section', DB_PREFIX);

    if ($countResults) {

      // Run query to count data
      $results = selecting(
        "COUNT(section_id) AS total_results",
        ...$this->sql
      );
    } else {

      // Run query to find data
      $results = selecting(
        "section_id,
        section_name,
        section_type,
        section_location_name",
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
   * @param string $locationName `[optional]` Location of the section. Defaults to "null"
   *
   * @return bool|int Will return FALSE if failed or true if successful.
   */
  public function addSection(string $name, int $type, string $locationName = null)
  {

    // Add data
    $this->conn->dbh->tableSetup('content_section', DB_PREFIX);
    inserting(
      array(
        'section_name' => $name,
        'section_type' => $type,
        'section_location_name' => $locationName,
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
   * @param string $locationName `[optional]` Location of the section. Defaults to "null"
   *
   * @return int Will return FALSE if failed or TRUE if successful.
   */
  public function editSection(int $sectionID, string $name, int $type, string $locationName = null)
  {

    // Make sure the ID is a number
    if (!empty($sectionID) && is_numeric($sectionID)) {

      // Update data
      $this->conn->dbh->tableSetup('content_section', DB_PREFIX);
      updating(
        array(
          'section_name' => $name,
          'section_type' => $type,
          'section_location_name' => $locationName,
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
      // Get data
      $this->conn->dbh->tableSetup('content AS c', DB_PREFIX);
      $contentData = selecting(
        "c.content_id,
        c.content_title,
        c.content_content,
        c.content_status,
        c.content_show_updated,
        cs.section_id,
        cs.section_location_name,
        (SELECT seo_keyword FROM cs_seo_url WHERE seo_type_id = c.content_id AND seo_type = '0' ORDER BY seo_id DESC LIMIT 1) AS content_slug",
        leftJoin(
          "c",
          DB_PREFIX . "content_section",
          "content_section_id",
          "section_id",
          "cs"
        ),
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
   * @param array $params Multiple parameters as required
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

    // Set type of content
    $this->whereArray[] = eq("c.content_type", "0");

    // Check for search
    if (!empty($params['search'])) {
      $this->whereArray[] = like("UPPER(c.content_title)", "%" . strtoupper($params['search']) . "%");
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
        $this->whereArray[] = eq("c.content_status", $statusInt);
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

    // Set table
    $this->conn->dbh->tableSetup('content AS c', DB_PREFIX);

    if ($countResults) {

      // Run query to count data
      $pageResults = selecting(
        "COUNT(c.content_id) AS total_results",
        ...$this->sql
      );
    } else {

      // Run query to find data
      $pageResults = selecting(
        "c.content_id,
        c.content_title,
        c.content_status,
        c.content_added_dtm,
        c.content_edited_dtm,
        cs.section_id,
        cs.section_name,
        cs.section_location_name,
        CONCAT(ua.user_first_name, ' ', ua.user_last_name) AS added_by,
        CONCAT(ue.user_first_name, ' ', ue.user_last_name) AS edited_by,
        (SELECT seo_keyword FROM cs_seo_url WHERE seo_type_id = c.content_id AND seo_type = '0' ORDER BY seo_id DESC LIMIT 1) AS content_slug",
        leftJoin(
          "c",
          DB_PREFIX . "content_section",
          "content_section_id",
          "section_id",
          "cs"
        ),
        leftJoin(
          "c",
          DB_PREFIX . "users",
          "content_added_id",
          "user_id",
          "ua"
        ),
        leftJoin(
          "c",
          DB_PREFIX . "users",
          "content_edited_id",
          "user_id",
          "ue"
        ),
        ...$this->sql
      );
    }

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($pageResults)) {

      // Return results
      return json_decode(json_encode(array('count' => $this->conn->dbh->getNum_Rows(), 'results' => $pageResults)), FALSE);
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
    $this->conn->dbh->tableSetup('content_section', DB_PREFIX);
    $pageSectionResults = selecting(
      "section_id,
      section_name",
      where(
        eq("section_type", "0")
      )
    );

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($pageSectionResults)) {

      // Return results
      return $pageSectionResults;
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
   * @param int $showUpdated `[optional]` If page is mean to show updated message. Defaults to "0"
   *
   * @return bool|int Will return FALSE if failed or inserted ID if successful.
   */
  public function addPage(string $title, string $content, int $status, int $sectionID = 0, int $showUpdated = 0)
  {

    // Add data
    $this->conn->dbh->tableSetup('content', DB_PREFIX);
    inserting(
      array(
        'content_title' => $title,
        'content_content' => $content,
        'content_status' => $status,
        'content_type' => '0',
        'content_section_id' => $sectionID,
        'content_show_updated' => $showUpdated,
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

    // Setup table
    $this->conn->dbh->tableSetup('content_meta', DB_PREFIX);

    // Add data
    inserting(
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

    // Add data
    inserting(
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
   * @param int $showUpdated `[optional]` If page is mean to show updated message. Defaults to "0"
   *
   * @return int Will return FALSE if failed or TRUE if successful.
   */
  public function editPage(int $pageID, string $title, string $content, int $status, int $sectionID = 0, int $showUpdated = 0)
  {

    // Make sure the ID is a number
    if (!empty($pageID) && is_numeric($pageID)) {

      // Update data
      $this->conn->dbh->tableSetup('content', DB_PREFIX);
      updating(
        array(
          'content_title' => $title,
          'content_content' => $content,
          'content_status' => $status,
          'content_section_id' => $sectionID,
          'content_show_updated' => $showUpdated,
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

    // Update data
    $this->conn->dbh->tableSetup('content_meta', DB_PREFIX);
    updating(
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
    updating(
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

  ###################
  ####    FAQ    ####
  ###################

  /**
   * Get FAQ
   *
   * @param int $faqID ID of the faq to retrieve
   *
   * @return object Return object with page data
   */
  public function getFAQ(int $faqID)
  {

    // Check data is valid
    if (!empty($faqID) && is_numeric($faqID)) {
      $this->conn->dbh->tableSetup('content AS c', DB_PREFIX);
      $contentData = selecting(
        "c.content_id,
        c.content_title,
        c.content_content,
        c.content_status,
        c.content_show_updated,
        section_ids",
        leftJoin(
          "c",
          "(SELECT faqs_content_id, GROUP_CONCAT(DISTINCT faqs_section_id) AS section_ids FROM " . DB_PREFIX . "content_faq_section GROUP BY faqs_content_id)",
          "content_id",
          "faqs_content_id",
          "cs"
        ),
        where(
          eq('c.content_id', $faqID)
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($contentData)) {

        // Return results
        return $contentData[0];
      } // No results. Return FALSE.
    } // Data invalid. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Get list of FAQs
   *
   * @param array $params Multiple parameters as required
   *
   * @return object Return object with list of FAQs
   */
  public function listFAQs($params = array())
  {

    // Set if counting or not
    $countResults = (!empty($params['count']) && $params['count'] == TRUE) ? TRUE : FALSE;

    // Build query
    $this->sql = array();
    $this->whereArray = array();

    // Set type of content
    $this->whereArray[] = eq("c.content_type", "1");

    // Check for search
    if (!empty($params['search'])) {
      $this->whereArray[] = like("UPPER(c.content_title)", "%" . strtoupper($params['search']) . "%");
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
        $this->whereArray[] = eq("c.content_status", $statusInt);
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

    // Setup table
    $this->conn->dbh->tableSetup('content AS c', DB_PREFIX);

    if ($countResults) {

      // Run query to count data
      $results = selecting(
        "COUNT(c.content_id) AS total_results",
        ...$this->sql
      );
    } else {

      // Run query to find data
      $results = selecting(
        "c.content_id,
        c.content_title,
        c.content_status,
        c.content_added_dtm,
        c.content_edited_dtm,
        CONCAT(ua.user_first_name, ' ', ua.user_last_name) AS added_by,
        CONCAT(ue.user_first_name, ' ', ue.user_last_name) AS edited_by",
        leftJoin(
          "c",
          DB_PREFIX . "users",
          "content_added_id",
          "user_id",
          "ua"
        ),
        leftJoin(
          "c",
          DB_PREFIX . "users",
          "content_edited_id",
          "user_id",
          "ue"
        ),
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
   * Get list of assigned FAQ sections
   *
   * @param int $FAQID The ID of the FAQ
   *
   * @return object Return object with list of sections
   */
  public function listAssignedFAQSections(int $FAQID)
  {

    // Run query to find data
    $this->conn->dbh->tableSetup('content_faq_section AS f', DB_PREFIX);
    $results = selecting(
      "fs.faqs_id,
      s.section_id,
      s.section_name",
      leftJoin(
        "fs",
        DB_PREFIX . "content_section",
        "faqs_section_id",
        "section_id",
        "s"
      ),
      where(
        eq("faqs_content_id", $FAQID)
      ),
      orderBy("s.section_name", "ASC")
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
   * Get list of assigned FAQ sections faqs
   *
   * @param int $FAQsID The ID of the FAQ Section
   *
   * @return object Return object with list of faqs
   */
  public function listAssignedFAQSectionFAQs(int $FAQsID)
  {

    // Run query to find data
    $this->conn->dbh->tableSetup('content_faq_section AS fs', DB_PREFIX);
    $results = selecting(
      "fs.faqs_id,
      c.content_id,
      c.content_title,
      fs.faqs_sort_order",
      leftJoin(
        "fs",
        DB_PREFIX . "content",
        "faqs_content_id",
        "content_id",
        "c"
      ),
      where(
        eq("faqs_section_id", $FAQsID)
      ),
      orderBy("c.content_title", "ASC")
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
   * Delete assigned FAQ sections
   *
   * @param int $faqSectionID ID of the assignment to delete
   *
   * @return bool Will return FALSE if failed or TRUE if successful.
   */
  public function deleteAssignedFAQSections(int $faqSectionID)
  {

    // Check data is valid
    if (!empty($faqSectionID) && is_numeric($faqSectionID)) {

      // Run query to delete
      $this->conn->dbh->tableSetup('content_faq_section', DB_PREFIX);
      deleting(
        where(
          eq("faqs_id", $faqSectionID)
        )
      );

      // Check if any rows affected
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // No rows affected. Return FALSE.

    } // Data invalid. Return FALSE

    // Return FALSE
    return FALSE;
  }

  /**
   * Get list of faq sections
   *
   * @return object Return object with list of sections
   */
  public function listFAQSections()
  {

    // Run query to find data
    $this->conn->dbh->tableSetup('content_section', DB_PREFIX);
    $sectionResults = selecting(
      "section_id,
      section_name",
      where(
        eq("section_type", "1")
      )
    );

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($sectionResults)) {

      // Return results
      return $sectionResults;
    } // No results. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Count faq links
   *
   * @param int $faqSectionID ID of the section to count
   *
   * @return int Return number of links
   */
  public function countFAQLinks(int $faqSectionID)
  {

    // Check data is valid
    if (!empty($faqSectionID) && is_numeric($faqSectionID)) {

      // Run query to find products
      $this->conn->dbh->tableSetup('content_faq_section', DB_PREFIX);
      selecting(
        "faqs_id",
        where(
          eq("faqs_section_id", $faqSectionID)
        )
      );

      // Return total
      return $this->conn->dbh->getNum_Rows();
    } // Data invalid. Return "0"
    return 0;
  }

  /**
   * Add FAQ
   *
   * @param string $title Title of the FAQ
   * @param string $content Content of the FAQ
   * @param int $status Status of the FAQ
   * @param int $showUpdated `[optional]` If FAQ is mean to show updated message. Defaults to "0"
   *
   * @return bool|int Will return FALSE if failed or inserted ID if successful.
   */
  public function addFAQ(string $title, string $content, int $status, int $showUpdated = 0)
  {

    // Add data
    $this->conn->dbh->tableSetup('content', DB_PREFIX);
    inserting(
      array(
        'content_title' => $title,
        'content_content' => $content,
        'content_status' => $status,
        'content_type' => '1',
        'content_show_updated' => $showUpdated,
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
   * Add FAQ Link
   *
   * @param int $sectionID The ID of the section
   * @param int $contentID The ID of the content for the section
   * @param int $sortOrder `[optional]` The sort order of the menu item in the menu. Defaults to "0"
   *
   * @return bool|int Will return FALSE if failed or true if successful.
   */
  public function addFAQLink(int $sectionID, int $contentID, int $sortOrder = 0)
  {

    // Check data is valid
    if (!empty($sectionID) && is_numeric($sectionID)) {

      // Insert data
      $this->conn->dbh->tableSetup('content_faq_section', DB_PREFIX);
      inserting(
        array(
          'faqs_content_id' => $contentID,
          'faqs_section_id' => $sectionID,
          'faqs_sort_order' => $sortOrder
        )
      );

      // Check if added successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Data was added. Return TRUE
        return TRUE;
      } // Data failed to be added. Return FALSE
    } // Data invalid. Return FALSE

    // Return FALSE
    return FALSE;
  }

  /**
   * Edit FAQ
   *
   * @param int $faqID ID of the FAQ
   * @param string $title Title of the FAQ
   * @param string $content Content of the FAQ
   * @param int $status Status of the FAQ
   * @param int $showUpdated `[optional]` If FAQ is mean to show updated message. Defaults to "0"
   *
   * @return int Will return FALSE if failed or TRUE if successful.
   */
  public function editFAQ(int $faqID, string $title, string $content, int $status, int $showUpdated = 0)
  {

    // Check data is valid
    if (!empty($faqID) && is_numeric($faqID)) {

      // Update data
      $this->conn->dbh->tableSetup('content', DB_PREFIX);
      updating(
        array(
          'content_title' => $title,
          'content_content' => $content,
          'content_status' => $status,
          'content_show_updated' => $showUpdated,
          'content_edited_id' => $_SESSION['_cs']['user']['uid'],
          'content_edited_dtm' => date('Y-m-d H:i:s')
        ),
        eq("content_id", $faqID)
      );

      // Check if updated successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // Unable to edit. Return FALSE.

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Edit FAQ Link
   *
   * @param int $faqLinkID ID of the faq link
   * @param int $sortOrder `[optional]` The sort order of the menu item in the menu. Defaults to "0"
   *
   * @return bool|int Will return FALSE if failed or true if successful.
   */
  public function editFAQLink(int $faqLinkID, int $sortOrder = 0)
  {

    // Check data is valid
    if (!empty($faqLinkID) && is_numeric($faqLinkID)) {

      // Set fallback data
      $sortOrder = (!empty($sortOrder)) ? $sortOrder : 0;

      // Update data
      $this->conn->dbh->tableSetup('content_faq_section', DB_PREFIX);
      updating(
        array(
          'faqs_sort_order' => $sortOrder
        ),
        eq("faqs_id", $faqLinkID)
      );

      // Check if edited successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Data was edited. Return TRUE
        return TRUE;
      } // Data failed to be edited. Return FALSE
    } // Data invalid. Return FALSE

    // Return FALSE
    return FALSE;
  }

  ####################
  ####    MENU    ####
  ####################

  /**
   * Count menu items
   *
   * @param int $menuID ID of the menu to count
   *
   * @return int Return number of links
   */
  public function countMenuItems(int $menuID)
  {

    // Check data is valid
    if (!empty($menuID) && is_numeric($menuID)) {

      // Get data
      $this->conn->dbh->tableSetup('content_menu', DB_PREFIX);
      selecting(
        "menui_id",
        where(
          eq("menui_menu_id", $menuID)
        )
      );

      // Return total
      return $this->conn->dbh->getNum_Rows();
    } // Data invalid. Return "0"
    return 0;
  }

  /**
   * Get list of menus
   *
   * @return object Return object with list of menus
   */
  public function listMenus()
  {

    // Run query to find data
    $this->conn->dbh->tableSetup('content_section', DB_PREFIX);
    $menuResults = selecting(
      "section_id,
      section_name",
      where(
        eq("section_type", "5")
      ),
      orderBy("section_name", "ASC")
    );

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($menuResults)) {

      // Return results
      return $menuResults;
    } // No results. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Get list of menu items
   *
   * @param int $menuID The ID of the menu to retrieve data for
   *
   * @return object Return object with list of menu items
   */
  public function listMenuItems(int $menuID)
  {

    // Check data is valid
    if (!empty($menuID) && is_numeric($menuID)) {

      // Run query to find data
      $this->conn->dbh->tableSetup('content_menu AS cm', DB_PREFIX);
      $menuItemResults = selecting(
        "cm.menui_id,
        COALESCE(c.content_type, 0) as content_type,
        cm.menui_content_id,
        c.content_title,
        cm.menui_custom_url,
        cm.menui_custom_title,
        cm.menui_sort_order",
        leftJoin(
          "cm",
          DB_PREFIX . "content",
          "menui_content_id",
          "content_id",
          "c"
        ),
        where(
          eq("menui_menu_id", $menuID)
        ),
        orderBy("cm.menui_sort_order ASC, c.content_title", "ASC")
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($menuItemResults)) {

        // Return results
        return $menuItemResults;
      } // No results. Return FALSE.
    } // Invalid data. Return FALSE

    // Return FALSE
    return false;
  }

  /**
   * Add Menu Item
   *
   * @param int $menuID ID of the menu
   * @param int $contentID `[optional]` The ID of the content for the menu item. Defaults to "null"
   * @param string $customURL `[optional]` The custom URL of the menu item. Defaults to "null"
   * @param string $customTitle `[optional]` The custom title of the menu item. Defaults to "null"
   * @param int $sortOrder `[optional]` The sort order of the menu item in the menu. Defaults to "0"
   *
   * @return bool|int Will return FALSE if failed or true if successful.
   */
  public function addMenuItem(int $menuID, int $contentID = null, string $customURL = null, string $customTitle = null, int $sortOrder = 0)
  {

    // Check data is valid
    if (!empty($menuID) && is_numeric($menuID)) {

      // Set fallback data
      $contentID = (!empty($contentID)) ? $contentID : null;
      $customURL = (!empty($customURL)) ? $customURL : null;
      $customTitle = (!empty($customTitle)) ? $customTitle : null;
      $sortOrder = (!empty($sortOrder)) ? $sortOrder : 0;

      // Insert data
      $this->conn->dbh->tableSetup('content_menu', DB_PREFIX);
      inserting(
        array(
          'menui_content_id' => $contentID,
          'menui_menu_id' => $menuID,
          'menui_custom_url' => $customURL,
          'menui_custom_title' => $customTitle,
          'menui_sort_order' => $sortOrder,
          'menui_added_id' => $_SESSION['_cs']['user']['uid'],
          'menui_added_dtm' => date('Y-m-d H:i:s')
        )
      );

      // Check if added successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Data was added. Return TRUE
        return TRUE;
      } // Data failed to be added. Return FALSE
    } // Data invalid. Return FALSE

    // Return FALSE
    return FALSE;
  }

  /**
   * Edit Menu Item
   *
   * @param int $menuItemID ID of the menu item
   * @param int $contentID `[optional]` The ID of the content for the menu item. Defaults to "null"
   * @param string $customURL `[optional]` The custom URL of the menu item. Defaults to "null"
   * @param string $customTitle `[optional]` The custom title of the menu item. Defaults to "null"
   * @param int $sortOrder `[optional]` The sort order of the menu item in the menu. Defaults to "0"
   *
   * @return bool|int Will return FALSE if failed or true if successful.
   */
  public function editMenuItem(int $menuItemID, int $contentID = null, string $customURL = null, string $customTitle = null, int $sortOrder = 0)
  {

    // Check data is valid
    if (!empty($menuItemID) && is_numeric($menuItemID)) {

      // Set fallback data
      $contentID = (!empty($contentID)) ? $contentID : null;
      $customURL = (!empty($customURL)) ? $customURL : null;
      $customTitle = (!empty($customTitle)) ? $customTitle : null;
      $sortOrder = (!empty($sortOrder)) ? $sortOrder : 0;

      // Update data
      $this->conn->dbh->tableSetup('content_menu', DB_PREFIX);
      updating(
        array(
          'menui_content_id' => $contentID,
          'menui_custom_url' => $customURL,
          'menui_custom_title' => $customTitle,
          'menui_sort_order' => $sortOrder,
          'menui_edited_id' => $_SESSION['_cs']['user']['uid'],
          'menui_edited_dtm' => date('Y-m-d H:i:s')
        ),
        eq("menui_id", $menuItemID)
      );

      // Check if edited successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Data was edited. Return TRUE
        return TRUE;
      } // Data failed to be edited. Return FALSE
    } // Data invalid. Return FALSE

    // Return FALSE
    return FALSE;
  }

  /**
   * Delete menu item
   *
   * @param int $menuItemID ID of the menu item to delete
   *
   * @return bool Will return FALSE if failed or TRUE if successful.
   */
  public function deleteMenuItem(int $menuItemID)
  {

    // Check data is valid
    if (!empty($menuItemID) && is_numeric($menuItemID)) {

      // Run query to delete
      $this->conn->dbh->tableSetup('content_menu', DB_PREFIX);
      deleting(
        where(
          eq("menui_id", $menuItemID)
        )
      );

      // Check if any rows affected
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // No rows affected. Return FALSE.

    } // Data invalid. Return FALSE

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
      $this->conn->dbh->tableSetup('content_meta', DB_PREFIX);
      $results = selecting(
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

  /**
   * Get list of content items
   *
   * @return object Return object with list of content items
   */
  public function listContentItems()
  {

    // Run query to find data
    $this->conn->dbh->tableSetup('content', DB_PREFIX);
    $contentItemResults = selecting(
      "content_id,
      content_title",
      orderBy("content_title", "ASC")
    );

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($contentItemResults)) {

      // Return results
      return $contentItemResults;
    } // No results. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Get list of content menus
   *
   * @param int $contentID ID of the content to check for
   *
   * @return object Return object with list of menus
   */
  public function listContentMenus(int $contentID)
  {

    // Check data is valid
    if (!empty($contentID) && is_numeric($contentID)) {

      // Run query to find data
      $this->conn->dbh->tableSetup('content_menu AS m', DB_PREFIX);
      $menuResults = selecting(
        "m.menui_id,
        s.section_name,
        m.menui_menu_id",
        leftJoin(
          "m",
          DB_PREFIX . "content_section",
          "menui_menu_id",
          "section_id",
          "s"
        ),
        where(
          eq("m.menui_content_id", $contentID)
        ),
        orderBy("s.section_name", "ASC")
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($menuResults)) {

        // Return results
        return $menuResults;
      } // No results. Return FALSE.
    } // Data invalid. Return FALSE

    // Return FALSE
    return false;
  }
}
