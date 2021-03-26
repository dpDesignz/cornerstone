<?php

/**
 * Cornerstone DB Session Handler Class
 *
 * session_set_save_handler docs available at {@link https://www.php.net/manual/en/function.session-set-save-handler.php the PHP Manual}
 *
 * @package Cornerstone
 */

use function ezsql\functions\{
  selecting,
  replacing,
  deleting,
  where,
  eq,
  lt
};

class CornerstoneSessionHandler implements SessionHandlerInterface
{

  // Set database connection property
  private $conn;

  /**
   * Construct the Session
   * No parameters required, nothing will be returned
   */
  public function __construct()
  {

    // Create a database connection
    $this->conn = new CornerstoneDBH;
    $this->conn->dbh->tableSetup('session', DB_PREFIX);

    // Set the handler to overide SESSION
    session_set_save_handler(
      array($this, "open"),
      array($this, "close"),
      array($this, "read"),
      array($this, "write"),
      array($this, "destroy"),
      array($this, "gc")
    );

    // Set the shutdown function
    register_shutdown_function('session_write_close');

    /** Define and initialize the Session Handler */
    session_start();
  }

  /**
   * Set the open callback
   *
   * @param string $savePath
   * @param string $sessionName
   *
   * @return bool return value should be true for success or false for failure
   */
  public function open($savePath, $sessionName)
  {

    // Check that the DB connection is set
    return ((!empty($csdb)) && $this->conn->dbh->isConnected() != 1) ? FALSE : TRUE;
  }

  /**
   * Set the close callback
   *
   * @return bool return value can only be true for success
   */
  public function close()
  {
    return TRUE;
  }

  /**
   * Set the read callback
   *
   * @param string $sessionID
   *
   * @return string return value should be the session data or an empty string
   */
  public function read($sessionID)
  {

    // Get the session from the database
    $this->conn->dbh->tableSetup('session', DB_PREFIX);
    $readResults = selecting(
      "session_data",
      where(
        eq('session_id', $sessionID)
      )
    );

    // If results returned, continue
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($readResults)) {

      // Get the data
      return $readResults[0]->session_data;
    } else { // Else return an empty string

      return '';
    }
  }

  /**
   * Set the write callback
   *
   * @param string $sessionID
   * @param string $data
   *
   * @return bool return value should be true for success
   */
  public function write($sessionID, $data)
  {

    // Set the time stamp
    $access_dtm = new \DateTime();

    // Get user ID
    $userID = (!empty($_SESSION['_cs']['user']['uid'])) ? $_SESSION['_cs']['user']['uid'] : '';

    // Replace the data
    $this->conn->dbh->tableSetup('session', DB_PREFIX);
    replacing(
      array(
        'session_id' => $sessionID,
        'session_ip_address' => $_SERVER['REMOTE_ADDR'],
        'session_user_id' => $userID,
        'session_data' => $data,
        'session_access_dtm' => $access_dtm->format('Y-m-d H:i:s')
      )
    );

    // Return true
    return TRUE;
  }

  /**
   * Set the destroy callback
   *
   * @param string $sessionID
   *
   * @return bool return value should be true for success
   */
  public function destroy($sessionID)
  {

    // Delete the session from the database
    $this->conn->dbh->tableSetup('session', DB_PREFIX);
    deleting(
      where(
        eq('session_id', $sessionID)
      )
    );

    // Return true
    return TRUE;
  }

  /**
   * Set the garbage collector callback
   *
   * @param string $lifetime
   *
   * @return bool return value should be true for success
   */
  public function gc($lifetime)
  {

    // Set the date calculation
    $expiredTime = new \DateTime();
    $expiredTime->modify('-' . $lifetime . ' seconds');

    // Get the session from the database
    $this->conn->dbh->tableSetup('session', DB_PREFIX);
    deleting(
      where(
        lt('session_access_dtm', $expiredTime->format('Y-m-d H:i:s'))
      )
    );

    // Return true
    return TRUE;
  }
}
