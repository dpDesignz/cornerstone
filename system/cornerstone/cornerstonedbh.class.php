<?php
  /**
   * Cornerstone DB Connection using ezSQL
   *
   * ezSQL docs available at {@link https://github.com/ezSQL/ezsql/wiki/Documentation the ezSQL Github documentation}
   *
   * @package Cornerstone
   * @subpackage Database - ezSQL/ezSQL v4.0.7
   */

  // ** Load ezSQL Database Class ** //
  use ezsql\Database;

  class CornerstoneDBH extends ezsql\Database {

    // Set the properties
    private $ezsql_type = EZSQL_TYPE;
    private $db_hostname = DB_HOSTNAME;
    private $db_name = DB_NAME;
    private $db_charset = DB_CHARSET;
    private $db_user = DB_USER;
    private $db_password = DB_PASSWORD;
    public $dbh;

    // Construct the DB
    public function __construct() {

      // Init the database connection
      $this->dbh = Database::initialize($this->ezsql_type, ['mysql:host='.$this->db_hostname.';dbname='.$this->db_name.';charset='.$this->db_charset, $this->db_user, $this->db_password]);

      // Turn prepared statment support on
      $this->dbh->prepareOn();

      // Enable debug echoing
      $this->dbh->debugOn();

    }
  }