<?php

/**
 * ME Account Extended Model
 *
 * @package Cornerstone
 * @subpackage Mission Equine
 */

class AccountExt extends ModelBase
{

  // Set the default properties
  private $customerID;
  private $customerGroup;

  /**
   * Construct the model
   */
  public function __construct($cdbh, $option)
  {
    // Load the model base constructor
    parent::__construct($cdbh, $option);

    // Set the customer ID if available
    if (!empty($_SESSION['_me']['customer']['id']) && is_numeric($_SESSION['_me']['customer']['id'])) {
      $this->setCustomerID((int) $_SESSION['_me']['customer']['id']);
    }
  }

  /**
   * Set customer ID
   *
   * @param int $id ID of customer you want to set
   */
  public function setCustomerID(int $id)
  {
    $this->customerID = $id;
  }

  /**
   * Set customer group
   *
   * @param int $id ID of customer group you want to set
   */
  public function setCustomerGroup(int $id)
  {
    $this->customerGroup = $id;
  }

  ########################
  ####    CUSTOMER    ####
  ########################

  /**
   * Get customer details
   *
   * @return bool Will return data if found, or FALSE if not found
   */
  public function getCustomerDetails()
  {

    // Check if the ID is set
    if (!empty($this->customerID) && is_numeric($this->customerID)) {

      // Run query to find data
      $detailsResults = $this->conn->dbh->selecting(
        "me_customer AS cus",
        "cus.customer_id,
        cus.customer_title,
        CONCAT(cus.customer_firstname, ' ', cus.customer_lastname) AS customer_name,
        cus.customer_email,
        con.contact_name,
        con.contact_email,
        cus.customer_payment_term,
        au.user_display_name AS assigned_user_name,
        loc.location_name AS assigned_location_name,
        cus.customer_default_address_id,
        cus.customer_default_payment_id",
        leftJoin(
          "cus",
          "me_contact",
          "customer_contact_id",
          "contact_id",
          "con"
        ),
        leftJoin(
          "cus",
          "cs_users",
          "customer_assigned_user_id",
          "user_id",
          "au"
        ),
        leftJoin(
          "cus",
          "me_locations",
          "customer_assigned_location_id",
          "location_id",
          "loc"
        ),
        where(
          eq("customer_id", $this->customerID)
        )
      );

      // Check if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($detailsResults)) {

        // Return True
        return $detailsResults[0];
      } // No results. Return FALSE
    } // Data invalid. Return FALSE

    // Return False
    return FALSE;
  }

  /**
   * Edit Customer
   *
   * @param int $defaultDeliveryAddressID `[optional]` The default delivery address ID for the customer
   * @param int $defaultPaymentID`[optional]` The default payment ID for the customer
   *
   * @return int Will return FALSE if failed or TRUE if successful.
   */
  public function editCustomer(
    int $defaultDeliveryAddressID = 0,
    int $defaultPaymentID = 0
  ) {

    // Check data is valid
    if (!empty($this->customerID) && is_numeric($this->customerID)) {

      // Set fallbacks
      $defaultDeliveryAddressID = (!empty($defaultDeliveryAddressID)) ? $defaultDeliveryAddressID : null;
      $defaultPaymentID = (!empty($defaultPaymentID)) ? $defaultPaymentID : null;

      // Update info in `me_customer`
      $this->conn->dbh->update(
        "me_customer",
        array(
          'customer_default_address_id' => $defaultDeliveryAddressID,
          'customer_default_payment_id' => $defaultPaymentID,
          'customer_edited_id' => 1, // Cornerstone account
          'customer_edited_dtm' => date('Y-m-d H:i:s')
        ),
        eq(
          "customer_id",
          $this->customerID
        )
      );

      // Check if updated successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // Unable to edit address. Return FALSE.

    } // Data invalid. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  ######################
  ####    ORDERS    ####
  ######################

  /**
   * Get list of recent orders
   *
   * @param int $totalOrders `[optional]` How many recent orders to return. Defaults to "5"
   *
   * @return object Return object with list of orders
   */
  public function listRecentOrders(int $totalOrders = 6)
  {

    // Check data is valid
    if (!empty($this->customerID) && is_numeric($this->customerID)) {
      // Run query to find data
      $recentOrderResults = $this->conn->dbh->selecting(
        "me_customer_order",
        "order_id,
        order_order_date,
        order_number,
        order_subtotal,
        order_tax_total,
        order_rounding_total,
        order_freight,
        order_status,
        (SELECT COUNT(*) FROM me_customer_order_lines WHERE orline_order_id = order_id) AS order_lines",
        where(
          eq("order_customer_id", $this->customerID),
          neq("order_status", "0")
        ),
        orderBy('order_order_date DESC, order_added_dtm', "DESC"),
        limit($totalOrders)
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($recentOrderResults)) {

        // Return results
        return $recentOrderResults;
      } // No results. Return FALSE
    } // Data invalid. Return FALSE

    // Return FALSE
    return false;
  }
}
