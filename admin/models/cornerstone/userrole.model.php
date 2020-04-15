<?php

/**
 * Admin User Role Model
 *
 * @package Cornerstone
 */

class UserRole
{

  // Set the default properties
  private $conn;
  private $optn;

  /**
   * Construct the User
   * No parameters required, nothing will be returned
   */
  public function __construct($option)
  {

    // Create a database connection
    $this->conn = new CornerstoneDBH;
    // Set the options
    $this->optn = $option;
  }

  /**
   * Get role
   *
   * @param int $role ID of the role to retrieve
   *
   * @return object Return object with role data
   */
  public function getRole(int $role)
  {

    // Check the data is valid
    if (!empty($role) && is_numeric($role)) {
      $roleData = $this->conn->dbh->selecting(
        DB_PREFIX . "roles",
        "role_id,
        role_key,
        role_name,
        role_meta",
        where(
          eq('role_id', $role)
        )
      );

      // Return if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($roleData)) {

        // Return results
        return $roleData[0];
      } // No results. Return FALSE.
    } // Data invalid. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Get list of roles
   *
   * @param array $params Multiple parameters as required
   *
   * @return object Return object with list of roles
   */
  public function listRoles($params = array())
  {

    // Set if counting or not
    $countResults = (!empty($params['count']) && $params['count'] == TRUE) ? TRUE : FALSE;

    // Build query
    $this->sql = array();
    $this->whereArray = array();

    // Check for search
    if (!empty($params['search'])) {
      $this->whereArray[] = like("UPPER(role_name)", "%" . strtoupper($params['search']) . "%", _AND);
    }

    // Combine where
    if (!empty($this->whereArray)) {
      $this->sql[] = where(...$this->whereArray);
    }

    // Check for sort
    if (!$countResults && !empty($params['sort']) && !empty($params['order'])) {
      $this->sql[] = orderBy($params['sort'], $params['order']);
    } else {
      $this->sql[] = orderBy('role_name', 'ASC');
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
        DB_PREFIX . "roles",
        "COUNT(role_id) AS total_results",
        ...$this->sql
      );
    } else {

      // Run query to find data
      $results = $this->conn->dbh->selecting(
        DB_PREFIX . "roles",
        "role_id,
        role_key,
        role_name,
        role_meta,
        (SELECT COUNT(rpl_role_id) FROM " . DB_PREFIX . "role_perms WHERE rpl_role_id = role_id) AS total_permissions",
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
   * Add Role
   *
   * @param string $name Name of the user role
   * @param string $key Key of user role
   *
   * @return bool|int Will return FALSE if failed or inserted ID if successful.
   */
  public function addRole(string $name, string $key)
  {

    // Add data
    $this->conn->dbh->insert(
      DB_PREFIX . "roles",
      array(
        'role_key' => $key,
        'role_name' => $name,
        'role_meta' => json_encode(array('color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF))))
      )
    );

    // Check if added successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Return new ID
      return $this->conn->dbh->getInsert_Id();
    } // Unable to add. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Edit Role
   *
   * @param int $roleID ID of the user role
   * @param string $name Name of the user role
   * @param string $key Key of user role
   *
   * @return int Will return FALSE if failed or TRUE if successful.
   */
  public function editRole(int $roleID, string $name, string $key)
  {

    // Make sure the data is valid
    if (!empty($roleID) && is_numeric($roleID)) {

      // Update row
      $updateResult = $this->conn->dbh->update(
        DB_PREFIX . "roles",
        array(
          'role_key' => $key,
          'role_name' => $name,
          'role_edited_id' => $_SESSION['_cs']['user']['uid'],
          'role_edited_dtm' => date('Y-m-d H:i:s')
        ),
        eq("role_id", $roleID)
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

  ###########################
  ####    PERMISSIONS    ####
  ###########################

  /**
   * Count total permissions
   *
   * @return int Return number of permissions
   */
  public function countTotalPermissions()
  {

    // Run query to find products
    $this->conn->dbh->selecting(
      DB_PREFIX . "role_permissions",
      "rp_id"
    );

    // Return total
    return $this->conn->dbh->getNum_Rows();
  }

  /**
   * Get list of permissions
   *
   * @return object Return object with list of permissions
   */
  public function listPermissions()
  {
    // Run query to find data
    $permissionResults = $this->conn->dbh->selecting(
      DB_PREFIX . "role_permissions",
      "rp_id,
      rp_key",
      orderBy('rp_key', "ASC")
    );

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($permissionResults)) {

      // Return results
      return $permissionResults;
    } // No results. Return FALSE.

    // Return FALSE
    return false;
  }

  /**
   * Get list of role permissions
   *
   * @param int $roleID ID of the role to retrieve
   *
   * @return object Return object with list of role permissions
   */
  public function listRolePermissions(int $roleID)
  {
    // Run query to find data
    $rolePermsResults = $this->conn->dbh->selecting(
      DB_PREFIX . "role_perms",
      "rpl_rp_id",
      where(
        eq("rpl_role_id", $roleID)
      ),
      orderBy('rpl_rp_id', "ASC")
    );

    // Return if results
    if ($this->conn->dbh->getNum_Rows() > 0 && !empty($rolePermsResults)) {

      // Return results
      return $rolePermsResults;
    } // No results. Return empty object.

    // Return empty object
    return (object) array();
  }

  /**
   * Add Permission
   *
   * @param string $key Key of user role
   *
   * @return bool|int Will return FALSE if failed or inserted ID if successful.
   */
  public function addPermission(string $key)
  {

    // Add data
    $this->conn->dbh->insert(
      DB_PREFIX . "role_permissions",
      array(
        'rp_key' => $key
      )
    );

    // Check if added successfully
    if ($this->conn->dbh->affectedRows() > 0) {

      // Return new ID
      return $this->conn->dbh->getInsert_Id();
    } // Unable to add. Return FALSE.

    // Return FALSE
    return FALSE;
  }

  /**
   * Add Role Permission
   *
   * @param int $roleID ID of the role
   * @param int $permissionID ID of the permission
   *z`
   * @return bool|int Will return FALSE if failed or true if successful.
   */
  public function addRolePermission(int $roleID, int $permissionID)
  {

    // Make sure the data is valid
    if (!empty($roleID) && is_numeric($roleID) && !empty($permissionID) && is_numeric($permissionID)) {

      // Add link
      $this->conn->dbh->insert(
        DB_PREFIX . "role_perms",
        array(
          'rpl_role_id' => $roleID,
          'rpl_rp_id' => $permissionID
        )
      );

      // Check if added successfully
      if ($this->conn->dbh->affectedRows() > 0) {

        // Return TRUE
        return TRUE;
      } // Unable to add. Return FALSE
    } // Data invalid. Return FALSE

    // Return FALSE
    return FALSE;
  }

  /**
   * Delete Role/Permission Link
   *
   * @param int $roleID ID of the role
   * @param int $permissionID ID of the permission
   *
   * @return bool Will return FALSE if failed or TRUE if successful.
   */
  public function deleteRPLink(int $roleID, int $permissionID)
  {

    // Make sure the data is valid
    if (!empty($roleID) && is_numeric($roleID) && !empty($permissionID) && is_numeric($permissionID)) {

      // Run query to delete
      $this->conn->dbh->delete(
        DB_PREFIX . "role_perms",
        where(
          eq("rpl_role_id", $roleID, _AND),
          eq("rpl_rp_id", $permissionID)
        )
      );

      // Check if any rows affected
      if ($this->conn->dbh->affectedRows() > 0) {
        // Return TRUE
        return TRUE;
      } // No rows affected. Return FALSE.

    } // Data isn't valid. Return FALSE

    // Return FALSE
    return FALSE;
  }
}
