<?php

/**
 * Core Cornerstone Model
 *
 * @package Cornerstone
 */

class CornerstoneCore extends ModelBase
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
  }

  ###################
  ####    SEO    ####
  ###################

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
   * Check SEO URL
   *
   * @param int $type Type of item
   * @param int $typeID ID of the type of item
   * @param string $url SEO friendly URL
   *
   * @return object|bool Return object with zone or FALSE if no results
   */
  public function checkSEOURL(int $type, int $typeID, string $url)
  {

    // Check submitted info is valid
    if (is_numeric($type) && !empty($typeID) && is_numeric($typeID) && !empty($url) && is_string($url)) {

      // Return SEO friendly url
      $url = $this->generate_seo_link($url);

      // Trim URL to 96
      if (strlen($url) > 96) {
        $url = substr($url, 0, 96);
      }

      // Run query to find if already exists
      $this->conn->dbh->selecting(
        DB_PREFIX . "seo_url",
        "*",
        where(
          eq("seo_type", $type, _AND),
          eq("seo_type_id", $typeID, _AND),
          eq("seo_keyword", $url)
        )
      );

      // Check if any results
      if ($this->conn->dbh->getNum_Rows() > 0) {
        // Match found. Return TRUE

        // Return TRUE
        return TRUE;
        exit;
      } else { // Doesn't exist. Add it

        // Run query to find if keyword is already being used
        $newURL = $this->checkUniqueSEO($url, (int) $typeID);

        // Run query to find if already exists
        $this->conn->dbh->selecting(
          DB_PREFIX . "seo_url",
          "*",
          where(
            eq("seo_type", $type, _AND),
            eq("seo_type_id", $typeID, _AND),
            eq("seo_keyword", $newURL)
          )
        );

        // Check if any results
        if ($this->conn->dbh->getNum_Rows() > 0) {
          // Match found. Return TRUE

          // Return TRUE
          return TRUE;
          exit;
        } else { // Doesn't exist. Add it
          // Add data into `cs_seo_url`
          $this->conn->dbh->insert(
            DB_PREFIX . "seo_url",
            array(
              'seo_type' => $type,
              'seo_type_id' => $typeID,
              'seo_keyword' => $newURL
            )
          );

          // Check if added successfully
          if ($this->conn->dbh->affectedRows() > 0) {

            // Return TRUE
            return TRUE;
          } // Unable to add. Return FALSE.
        }
      }
    } // Submitted info isn't valid. Return FALSE

    // Return FALSE
    return FALSE;
  }

  /**
   * Check SEO URL is unique
   *
   * @param string $url SEO friendly URL
   * @param int $type `[optional]` Type of item. Defaults to "0"
   * @param int $typeID `[optional]` ID of the type of item. Defaults to "0"
   *
   * @return bool|int Returns string of available URL
   */
  private function checkUniqueSEO($url, int $typeID = 0)
  {
    // Run query to find if keyword is already being used
    $this->conn->dbh->selecting(
      DB_PREFIX . "seo_url",
      "*",
      where(
        eq("seo_keyword", $url, _AND),
        neq("seo_type_id", $typeID)
      )
    );

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0) {
      // URL is being used. Check how many times

      // Run query to find how many times it's being used
      $this->conn->dbh->selecting(
        DB_PREFIX . "seo_url",
        "*",
        where(
          like("seo_keyword", $url . "%", _AND),
          neq("seo_type_id", $typeID)
        )
      );

      // Check new string
      return $this->checkUniqueSEO($url . '-' . ($this->conn->dbh->getNum_Rows()), (int) $typeID);
    } // URL is available. Return $url

    // Return $url
    return $url;
  }

  /* takes the input, scrubs bad characters */
  private function generate_seo_link($input, $replace = '-', $remove_words = true, $words_array = array())
  {

    // Default fallback words array
    if (empty($words_array)) {
      $words_array = array('a', 'and', 'the', 'an', 'it', 'is', 'with', 'can', 'of', 'why', 'not');
    }

    //make it lowercase, remove punctuation, remove multiple/leading/ending spaces
    $return = str_replace("-", " ", htmlspecialchars_decode($input));
    $return = iconv('UTF-8', 'ASCII//TRANSLIT', $return);
    $return = html_entity_decode($return, ENT_QUOTES, 'utf-8');
    $return = preg_replace("`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i", "\\1", $return);
    $return = trim(str_replace(' +', ' ', preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($return))));
    $return = preg_replace('/\s+/', ' ', $return);

    //remove words, if not helpful to seo
    //i like my defaults list in remove_words(), so I wont pass that array
    if ($remove_words) {
      $return = $this->remove_words($return, $replace, $words_array);
    }

    //convert the spaces to whatever the user wants
    //usually a dash or underscore..
    //...then return the value.
    return str_replace(' ', $replace, $return);
  }

  /* takes an input, scrubs unnecessary words */
  private function remove_words($input, $replace, $words_array = array(), $unique_words = true)
  {
    //separate all words based on spaces
    $input_array = explode(' ', $input);

    //create the return array
    $return = array();

    //loops through words, remove bad words, keep good ones
    foreach ($input_array as $word) {
      //if it's a word we should add...
      if (!in_array($word, $words_array) && ($unique_words ? !in_array($word, $return) : true)) {
        $return[] = $word;
      }
    }

    //return good words separated by dashes
    return implode($replace, $return);
  }

  #######################
  ####    SECTION    ####
  #######################

  #######################
  ####    CONTENT    ####
  #######################

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
      $contentData = $this->conn->dbh->selecting(
        DB_PREFIX . "content AS c",
        "c.*,
        cs.section_name,
        cs.section_type,
        cs.section_location_name",
        leftJoin(
          "c",
          "cs_content_section",
          "content_section_id",
          "section_id",
          "cs"
        ),
        where(
          eq('c.content_id', $contentID, _AND),
          neq('c.content_status', '0', _AND),
          neq('c.content_status', '3')
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
