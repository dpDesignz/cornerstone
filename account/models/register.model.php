<?php

/**
 * ME Account Register Model
 *
 * @package Cornerstone
 * @subpackage Mission Equine
 */

class Register extends ModelBase
{

  // Set the default properties
  private $customerID;

  /**
   * Construct the model
   */
  public function __construct($cdbh, $option)
  {
    // Load the model base constructor
    parent::__construct($cdbh, $option);
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

  #######################
  ####    CONTACT    ####
  #######################

  /**
   * Add Customer Contact
   *
   * @param string $name Name of the customer
   * @param string $email Email address of the customer
   *
   * @return bool|int Will return FALSE if failed or contact ID if successful.
   */
  public function addCustomerContact(string $name, string $email)
  {

    // Add customer contact into `me_contact`
    $this->conn->dbh->insert(
      "me_contact",
      array(
        'contact_group_id' => '1',
        'contact_name' => $name,
        'contact_email' => $email,
        'contact_is_customer' => '1',
        'contact_added_id' => '1',
        'contact_added_dtm' => date('Y-m-d H:i:s')
      )
    );

    // Check if added successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Return ID
      return $this->conn->dbh->getInsert_Id();
    } // Unable to add. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Update primary customer on contact
   *
   * @param int $contactID ID of the contact
   * @param int $customerID ID of the customer
   *
   * @return int Will return FALSE if failed or TRUE if successful.
   */
  public function updatePrimaryCustomer(int $contactID, int $customerID)
  {

    // Make sure the ID is a number
    if (!empty($contactID) && is_numeric($contactID) && !empty($customerID) && is_numeric($customerID)) {

      // Update row in `me_contact`
      $result = $this->conn->dbh->update(
        "me_contact",
        array(
          'contact_primary_customer_id' => $customerID
        ),
        eq("contact_id", $contactID)
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

  ########################
  ####    CUSTOMER    ####
  ########################

  /**
   * Check email is unique
   *
   * @param string $email Email address to check
   *
   * @return int Will return how many results found
   */
  public function checkEmailUnique(string $email)
  {

    // Run query to find data
    $customerResults = $this->conn->dbh->selecting(
      "me_customer",
      "customer_id",
      where(
        eq("customer_email", $email)
      )
    );

    // Return results
    return $this->conn->dbh->getNum_Rows();
  }

  /**
   * Add Customer
   *
   * @param int $contactID The ID of the customers contact
   * @param string $firstName The customers first name
   * @param string $lastName The customers last name
   * @param string $email The customers email address
   * @param string $password The customers encrypted password
   * @param string $passwordKey The customers password key
   *
   * @return bool|int Will return FALSE if failed or customer ID if successful.
   */
  public function addCustomer(
    int $contactID,
    string $firstName,
    string $lastName,
    string $email,
    string $password,
    string $passwordKey
  ) {

    // Add data into `me_customer`
    $this->conn->dbh->insert(
      "me_customer",
      array(
        'customer_contact_id' => $contactID,
        'customer_firstname' => $firstName,
        'customer_lastname' => $lastName,
        'customer_email' => $email,
        'customer_password' => $password,
        'customer_password_key' => $passwordKey,
        'customer_added_dtm' => date('Y-m-d H:i:s')
      )
    );

    // Check if added successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Return ID
      return $this->conn->dbh->getInsert_Id();
    } // Unable to add. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  ######################
  ####    PERSON    ####
  ######################

  /**
   * Add Customer Person
   *
   * @param int $contactID The ID of the customers contact
   * @param int $customerID The ID of the customer account
   * @param string $name The persons name
   * @param string $email The persons email address
   *
   * @return bool|int Will return FALSE if failed or person ID if successful.
   */
  public function addCustomerPerson(
    int $contactID,
    int $customerID,
    string $name,
    string $email
  ) {

    // Add data into `me_contact_person`
    $this->conn->dbh->insert(
      "me_contact_person",
      array(
        'person_contact_id' => $contactID,
        'person_customer_id' => $customerID,
        'person_name' => $name,
        'person_email' => $email,
        'person_is_primary' => '1',
        'person_added_id' => '1',
        'person_added_dtm' => date('Y-m-d H:i:s')
      )
    );

    // Check if added successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Return ID
      return $this->conn->dbh->getInsert_Id();
    } // Unable to add. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  ########################
  ####    BALANCES    ####
  ########################

  /**
   * Add Customer Contact Balances
   *
   * @param int $contactID ID of the customer contact
   *
   * @return bool Will return FALSE if failed or TRUE if successful.
   */
  public function addCustomerContactBalances(int $contactID)
  {

    // Check data is valid
    if (!empty($contactID) && is_numeric($contactID)) {

      // Add
      $this->conn->dbh->insert(
        "me_contact_balance",
        array(
          'balance_contact_id' => $contactID
        )
      );

      // Check if added to log successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // Unable to add. Return FALSE.
    } // Invalid data. Return FALSE

    // Return FALSE
    return FALSE;
  }

  /**
   * Add Customer Balances
   *
   * @param int $customerID ID of the customer
   *
   * @return bool Will return FALSE if failed or TRUE if successful.
   */
  public function addCustomerBalances(int $customerID)
  {

    // Check data is valid
    if (!empty($customerID) && is_numeric($customerID)) {

      // Add
      $this->conn->dbh->insert(
        "me_customer_balance",
        array(
          'balance_customer_id' => $customerID
        )
      );

      // Check if added to log successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // Unable to add. Return FALSE.
    } // Invalid data. Return FALSE

    // Return FALSE
    return FALSE;
  }
}
