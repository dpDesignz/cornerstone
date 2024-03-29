<?php

namespace Cornerstone;

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) 2019-2020, dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 */

use function ezsql\functions\{
  table_setup,
  selecting,
  inserting,
  updating,
  where,
  eq,
  orderBy,
  limit
};

/**
 * Notification Manager
 */

class NotificationManager
{

  // Protected Properties
  protected $notificationAdapter;
  protected $conn;

  /**
   * Constructor
   */
  public function __construct()
  {
    // Create a database connection
    $this->conn = new CornerstoneDBH;
    $this->conn->dbh->tableSetup('notification', DB_PREFIX);
  }

  /**
   * Check if notification already exists
   *
   * @param string $type Type of notification
   * @param int $forID The ID of the user OR group the notification is for
   * @param int $typeID `[optional]` The referenced ID of the notification type. Defaults to "0"
   *
   * @return bool Will return TRUE if already exists or FALSE if no match found
   */
  public function isDoublicate(Notification $notification)
  {
    // Run query to find if notification already exists
    $this->conn->dbh->tableSetup('notification', DB_PREFIX);
    $doublicateResults = selecting(
      "noti_id",
      where(
        eq("noti_type", $notification->type()),
        eq("noti_for_id", $notification->recipient()),
        eq("noti_type_id", $notification->typeID())
      )
    );

    // If there are results, return TRUE
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($doublicateResults)) {

      // Return TRUE
      return TRUE;
    } // No match. Return FALSE

    // Return FALSE
    return FALSE;
  }

  /**
   * Add notification
   *
   * @param string $type Type of notification
   * @param array $content Content for the notification (stored in an array format, e.g. array("subject" => "This is my subject", "Text" => "This is my notification text"))
   * @param int $forID The ID of the user OR group the notification is for
   * @param int $typeID `[optional]` The referenced ID of the notification type. Defaults to "0"
   * @param int $forGroup `[optional]` If the notification is for a group or not. Defaults to "0"
   *
   * @return bool Will return TRUE if successful or FALSE if failed
   */
  public function add(Notification $notification)
  {
    // Check if the notification already exists
    if (!(self::isDoublicate($notification))) {
      // Notification doesn't exist. Insert.

      // Insert the notification
      $this->conn->dbh->tableSetup('notification', DB_PREFIX);
      inserting(
        array(
          'noti_type' => $notification->type(),
          'noti_content' => json_encode($notification->content()),
          'noti_status' => "0",
          'noti_for_id' => $notification->recipient(),
          'noti_for_group' => $notification->group(),
          'noti_type_id' => $notification->typeID(),
          'noti_created_at' => 'NOW()'
        )
      );

      // Check if added successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // Unable to add. Return FALSE.

      // Return FALSE
      return FALSE;
    } else { // Notification already exists. Set latest notification as unread

      // Update data
      $this->conn->dbh->tableSetup('notification', DB_PREFIX);
      updating(
        array(
          'noti_status' => "0",
          'noti_created_at' => 'NOW()'
        ),
        eq("noti_type", $notification->type()),
        eq("noti_for_id", $notification->recipient()),
        eq("noti_type_id", $notification->typeID())
      );

      // Check if updated successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Set status as unread
        $notification->setStatus(FALSE);

        // Return TRUE
        return TRUE;
      } // Unable to edit. Return FALSE.

      // Return FALSE
      return FALSE;
    }
  }

  /**
   * Count all unread messages for a user
   *
   * @param int $userID The user ID
   *
   * @return int Will return number of unread messages
   */
  public function countUnread(Notification $notification)
  {
    // Get data
    $this->conn->dbh->tableSetup('notification', DB_PREFIX);
    selecting(
      "noti_id",
      where(
        eq("noti_for_id", $notification->recipient()),
        eq("noti_status", "0")
      )
    );

    // Return results
    return $this->conn->dbh->getNum_Rows();
  }

  /**
   * Get notifications
   *
   * @param int $unseen `[optional]` If wanting to only see unseen notifications. Defaults to "0"
   * @param int $limit `[optional]` Amount of results to retrieve. Defaults to "10"
   * @param int $offset `[optional]` Amount to offset retrieved results by. Defaults to "0"
   *
   * @return object Will return an object with the retrieved information in it
   */
  public function get(
    int $unseen = 0,
    int $limit = 10,
    int $offset = 0
  ) {
    // Build query
    $this->sql = array();
    $this->whereArray = array();

    // Set the user ID
    $this->whereArray[] = eq("noti_for_id", $_SESSION['_cs']['user']['uid']);

    // Check if wanting unseen only
    if ($unseen) {
      $this->whereArray[] = eq("noti_status", "0");
    }

    // Combine where
    if (!empty($this->whereArray)) {
      $this->sql[] = where(...$this->whereArray);
    }

    // Set the order by
    $this->sql[] = orderBy("noti_created_at", "DESC");

    // Set the limit
    $this->sql[] = limit($limit, $offset);

    // Run query to get results
    $this->conn->dbh->tableSetup('notification', DB_PREFIX);
    $getResults = selecting(
      "noti_id,
      noti_type,
      noti_content,
      noti_status,
      noti_type_id,
      noti_created_at",
      ...$this->sql
    );

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($getResults)) {

      // Return results
      return $getResults;
      exit;
    } // No results. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Update notification status
   *
   * @param int $id The notification ID
   *
   * @return bool Will return TRUE if successful or FALSE if failed
   */
  public function markStatus(Notification $notification)
  {
    // Update data
    $this->conn->dbh->tableSetup('notification', DB_PREFIX);
    updating(
      array(
        'noti_status' => $notification->status(),
        'noti_read_at' => 'NOW()'
      ),
      eq("noti_id", $notification->id())
    );

    // Check if updated successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Return TRUE
      return TRUE;
    } // Unable to edit. Return FALSE.

    return FALSE;
  }

  /**
   * Mark all user notification as read
   *
   * @param int $userID The user ID
   *
   * @return bool Will return TRUE if successful or FALSE if failed
   */
  public function markAllStatus(Notification $notification)
  {
    // Update data
    $this->conn->dbh->tableSetup('notification', DB_PREFIX);
    updating(
      array(
        'noti_status' => $notification->status(),
        'noti_read_at' => 'NOW()'
      ),
      eq("noti_for_id", $notification->recipient()),
      eq("noti_status", "0")
    );

    // Check if updated successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Return TRUE
      return TRUE;
    } // Unable to edit. Return FALSE.

    return FALSE;
  }
}
