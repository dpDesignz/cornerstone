<?php

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) 2019-2020, dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 */

use function ezsql\functions\{
  selecting,
  leftJoin,
  where,
  eq,
  orderBy
};

/**
 * Output Content Class
 */

class OPContent
{

  // Define Properties
  protected $conn;

  /**
   * Constructor
   */
  public function __construct()
  {
    // Create a database connection
    $this->conn = new CornerstoneDBH;
  }

  ####################
  ####    MENU    ####
  ####################

  /**
   * Get Menu Items data
   *
   * @param int $menuID ID of the Menu
   *
   * @return object|bool Return object with results or FALSE if no results
   */
  private function getMenuItemsData(int $menuID)
  {

    // Check data is valid
    if (!empty($menuID) && is_numeric($menuID)) {

      // Run query to get results
      $this->conn->dbh->tableSetup('content_menu AS mi', DB_PREFIX);
      $itemResults = selecting(
        "mi.menui_id,
        COALESCE(c.content_type, 0) as content_type,
        mi.menui_content_id,
        c.content_title,
        cs.section_id,
        cs.section_location_name,
        mi.menui_custom_url,
        mi.menui_custom_title,
        mi.menui_sort_order,
        (SELECT seo_keyword FROM " .
          DB_PREFIX . "seo_url WHERE seo_type_id = c.content_id AND seo_type = content_type ORDER BY seo_id DESC LIMIT 1) AS content_slug",
        leftJoin(
          "mi",
          DB_PREFIX . "content",
          "menui_content_id",
          "content_id",
          "c"
        ),
        leftJoin(
          "c",
          DB_PREFIX . "content_section",
          "content_section_id",
          "section_id",
          "cs"
        ),
        where(
          eq("mi.menui_menu_id", $menuID)
        ),
        orderBy("mi.menui_sort_order ASC, c.content_title", "ASC")
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($itemResults)) {

        // Return results
        return $itemResults;
        exit;
      } // No results. Return FALSE.

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Output Menu
   *
   * @param int $menuID The ID of the menu to output
   *
   * @return string Will return the menu as a string
   */
  public function outputMenu(int $menuID)
  {

    // Check data is valid
    if (!empty($menuID) && is_numeric($menuID)) {

      // Get Menu Items
      //-------------------------------------------------------------------
      $menuData = $this->getMenuItemsData((int) $menuID);

      // Menu Items
      //-------------------------------------------------------------------

      // Start output
      $returnOutput = '<ul id="menu-output-' . $menuID . '">';

      // Check for FAQ items
      if (!empty($menuData)) {

        // Loop through menu items
        foreach ($menuData as $menuItemContent) {
          // Check if URL has http or https in it
          $internalLink = (!empty($menuItemContent->menui_custom_url) && (strpos($menuItemContent->menui_custom_url, "http://") === TRUE || strpos($menuItemContent->menui_custom_url, "https://") === TRUE)) ? TRUE : FALSE;

          // Set fallback data
          $openBlank = '';
          if (!empty($menuItemContent->menui_custom_url) && $internalLink) {
            $urlOut = htmlspecialchars_decode($menuItemContent->menui_custom_url);
            $openBlank = ' target="_blank';
          } else if (!empty($menuItemContent->menui_custom_url)) {
            $urlOut = get_site_url($menuItemContent->menui_custom_url);
          } else {
            // Check for section location
            $sectionLocation = (!empty($menuItemContent->section_location_name)) ? $menuItemContent->section_location_name . '/' : '';
            $urlOut = get_site_url($sectionLocation . $menuItemContent->content_slug);
          }
          $linkTitle = (!empty($menuItemContent->menui_custom_title)) ? htmlspecialchars_decode($menuItemContent->menui_custom_title) : htmlspecialchars_decode($menuItemContent->content_title);
          $linkTitle = (empty($linkTitle)) ? 'not set' : $linkTitle;

          // Add to return output
          $returnOutput .= '<li><a href="' . $urlOut . '" title="' . $linkTitle . '"' . $openBlank . '>' . $linkTitle . '</a></li>';
        }
      } else { // No FAQ items. Output message
        // Output message
        $returnOutput .= '<li><em>There are no items in this menu</em></li>';
      }

      // End output
      $returnOutput .= '</ul>';
    } else { // Data invalid. Set error
      $returnOutput = '<em>There was an error retrieving this menu</em>';
    }

    return $returnOutput;
  }

  ####################
  ####    FAQs    ####
  ####################

  /* gets the FAQ section for outputting */
  private function getFAQSection(int $faqSectionID)
  {

    // Check data is valid
    if (!empty($faqSectionID) && is_numeric($faqSectionID)) {
      $this->conn->dbh->tableSetup('content_section', DB_PREFIX);
      $sectionData = selecting(
        "section_id,
        section_name",
        where(
          eq('section_id', $faqSectionID),
          eq('section_type', "1")
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($sectionData)) {

        // Init faqItems return
        $faqItemsReturn = array();

        // Check for FAQ Items
        if ($faqItemsData = $this->getFAQSItemsData((int) $sectionData[0]->section_id)) {
          // Meta data exists

          // Loop through data
          foreach ($faqItemsData as $itemOutput) {
            $faqItemsReturn[$itemOutput->content_id] = $itemOutput;
          }
        }

        // Return results
        return json_decode(json_encode(array('section' => $sectionData[0], 'faqs' => (object) $faqItemsReturn, FALSE)), FALSE);
      } // No results. Return FALSE.
    } // Data invalid. Return FALSE

    // Return FALSE
    return FALSE;
  }

  /**
   * Get FAQ Section Items data
   *
   * @param int $faqSectionID ID of the FAQ Section
   *
   * @return object|bool Return object with results or FALSE if no results
   */
  private function getFAQSItemsData(int $faqSectionID)
  {

    // Check data is valid
    if (!empty($faqSectionID) && is_numeric($faqSectionID)) {

      // Run query to get results
      $this->conn->dbh->tableSetup('content_faq_section AS fs', DB_PREFIX);
      $itemResults = selecting(
        "c.content_id,
        c.content_title,
        c.content_content,
        c.content_show_updated,
        c.content_edited_dtm",
        leftJoin(
          "fs",
          DB_PREFIX . "content",
          "faqs_content_id",
          "content_id",
          "c"
        ),
        where(
          eq('fs.faqs_section_id', $faqSectionID),
          eq('c.content_status', "1")
        ),
        orderBy("fs.faqs_sort_order ASC, c.content_title", "ASC")
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($itemResults)) {

        // Return results
        return $itemResults;
        exit;
      } // No results. Return FALSE.

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Output FAQ
   *
   * @param int $faqID ID of the FAQ
   *
   * @return string Will return the faq as a string
   */
  public function outputFAQ(int $faqID)
  {

    // Check data is valid
    if (!empty($faqID) && is_numeric($faqID)) {

      // Run query to get results
      $this->conn->dbh->tableSetup('content', DB_PREFIX);
      $itemResults = selecting(
        "content_id,
        content_title,
        content_content,
        content_show_updated,
        content_edited_dtm",
        where(
          eq('content_id', $faqID),
          eq('content_status', "1")
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($itemResults)) {

        // Get results
        $faqItem = $itemResults[0];

        // Set fallback data
        $faqTitle = (!empty($faqItem->content_title)) ? htmlspecialchars_decode($faqItem->content_title) : '<em>No title set</em>';
        $faqContent = (!empty($faqItem->content_content)) ? htmlspecialchars_decode($faqItem->content_content) : '<em>No details set</em>';

        // Check if needing to show update data
        if ($faqItem->content_show_updated) {
          // Get edited DTM
          $editedDtm = new DateTime($faqItem->content_edited_dtm);
          // Echo
          $editedDtm = '<p class="cs-body2"><em>This FAQ was last updated on ' . $editedDtm->format('F j<\s\up>S</\s\up>, Y') . '</em></p>';
        } else {
          // Set blank
          $editedDtm = '';
        }

        // Set output
        $returnOutput = '<section id="faq-item-' . $faqID . '" class="csc-collapsible csc-faq-section">
          <dl>
            <dt class="csc-collapsible__header faq-item__header" aria-expanded="false" aria-controls="faq_' . $faqItem->content_id . '_desc"><i class="fas fa-caret-right"></i> <strong>' . $faqTitle . '</strong></dt>
            <dd id="faq_' . $faqItem->content_id . '_desc" class="csc-collapsible__body">' . $faqContent . $editedDtm . '</dd>
          </dl>
        </section>
        <script>
          // Init collapsible
          document.addEventListener("DOMContentLoaded", function(DOMEvent) {
            // Add event listener to collapsible
            document.querySelector(`#faq-item-' . $faqID . ' .faq-item__header`).addEventListener(\'click\', toggleFAQCollapsible);
          });
        </script>';

        // Return results
        return $returnOutput;
        exit;
      } // No FAQ items. Output message
    } // Data invalid. Return error message.
    // Output message
    return '<em>There was an error retrieving this FAQ section</em>';
    exit;
  }

  /**
   * Output FAQ Section
   *
   * @param int $faqSectionID The ID of the FAQ Section to output
   *
   * @return string Will return the faq section as a string
   */
  public function outputFAQSection(int $faqSectionID)
  {

    // Check data is valid
    if (!empty($faqSectionID) && is_numeric($faqSectionID)) {

      // Get FAQ Section
      //-------------------------------------------------------------------
      $faqSectionData = $this->getFAQSection((int) $faqSectionID);

      // FAQ Sections
      //-------------------------------------------------------------------
      // Set fallbacks
      $faqSectionTitle = (!empty($faqSectionData->section->section_name)) ? '<h4>' . htmlspecialchars_decode($faqSectionData->section->section_name) . '</h4>' : '';

      // Start output
      $returnOutput = '<section id="faq-section-' . $faqSectionID . '" class="csc-collapsible csc-faq-section">' . $faqSectionTitle;

      // Check for FAQ items
      if (!empty($faqSectionData->faqs)) {
        // Start outputting items
        $returnOutput .= '<dl>';

        // Loop through FAQ items
        foreach ($faqSectionData->faqs as $faqID => $faqItemContent) {
          // Set fallback data
          $faqTitle = (!empty($faqItemContent->content_title)) ? htmlspecialchars_decode($faqItemContent->content_title) : '<em>No title set</em>';
          $faqContent = (!empty($faqItemContent->content_content)) ? htmlspecialchars_decode($faqItemContent->content_content) : '<em>No details set</em>';

          // Check if needing to show update data
          if ($faqItemContent->content_show_updated) {
            // Get edited DTM
            $editedDtm = new DateTime($faqItemContent->content_edited_dtm);
            // Echo
            $editedDtm = '<p class="cs-body2"><em>This FAQ was last updated on ' . $editedDtm->format('F j<\s\up>S</\s\up>, Y') . '</em></p>';
          } else {
            // Set blank
            $editedDtm = '';
          }

          // Add to return output
          $returnOutput .= '<dt class="csc-collapsible__header faq-item__header" aria-expanded="false" aria-controls="faq_' . $faqItemContent->content_id . '_desc"><i class="fas fa-caret-right"></i> <strong>' . $faqTitle . '</strong></dt>
          <dd id="faq_' . $faqItemContent->content_id . '_desc" class="csc-collapsible__body">' . $faqContent . $editedDtm . '</dd>';
        }

        // end outputting items
        $returnOutput .= '</dl>';
      } else { // No FAQ items. Output message
        // Output message
        $returnOutput .= '<p class="cs-body2 cs-text-center"><em>There are no items in this FAQ section</em></p>';
      }

      // End output
      $returnOutput .= '</section>
      <script>
        // Init collapsible
        document.addEventListener("DOMContentLoaded", function(DOMEvent) {
          // Add event listener to collapsible
          document.querySelectorAll(`#faq-section-' . $faqSectionID . ' .faq-item__header`).forEach(collapsible => collapsible.addEventListener(\'click\', toggleFAQCollapsible));
        });
      </script>';
    } else { // Data invalid. Set error
      $returnOutput = '<em>There was an error retrieving this FAQ section</em>';
    }

    return $returnOutput;
  }

  /**
   * Check for FAQ and FAQ section output
   *
   * @param string $content Content to check
   *
   * @return string Will return the string with any FAQ data replaced
   */
  public function checkStringFAQ(string $returnedContent)
  {
    // Check for FAQ section output
    preg_match_all('/\[faqs:(\d+)\]/i', $returnedContent, $faqSections);

    // Check if any FAQ section matches found
    if (!empty($faqSections[1])) {
      // Loop through faq sections
      foreach ($faqSections[1] as $faqSectionID) {
        // Get FAQ Section to output
        $faqSectionOutput = $this->outputFAQSection((int) $faqSectionID);

        // Get and replace content on page
        $returnedContent = str_replace(array('<p><code>[faqs:' . $faqSectionID . ']</code></p>', '<p>[faqs:' . $faqSectionID . ']</p>', '[faqs:' . $faqSectionID . ']'), $faqSectionOutput, $returnedContent);
      }
    }

    // Check for FAQ output
    preg_match_all('/\[faq:(\d+)\]/i', $returnedContent, $faqItems);

    // Check if any FAQ matches found
    if (!empty($faqItems[1])) {
      // Loop through faq sections
      foreach ($faqItems[1] as $faqItemID) {
        // Get FAQ to output
        $faqOutput = $this->outputFAQ((int) $faqItemID);

        // Get and replace content on page
        $returnedContent = str_replace(array('<p><code>[faq:' . $faqItemID . ']</code></p>', '<p>[faq:' . $faqItemID . ']</p>', '[faq:' . $faqItemID . ']'), $faqOutput, $returnedContent);
      }
    }

    // Output the results
    return $returnedContent;
  }
}
