<?php

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) 2020, dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 *
 * Based on https://www.sitepoint.com/role-based-access-control-in-php/
 */

/**
 * User Role Class
 */

class Role
{

  // Set Properties
  protected $conn;
  protected $permissions;
  protected $isMasterUser;

  /**
   * Constructor
   */
  public function __construct()
  {
    // Create a database connection
    $this->conn = new CornerstoneDBH;
    // Create array in $permissions property
    $this->permissions = array();
    // Set master user to false
    $this->isMasterUser = FALSE;
  }

  /**
   * Get the role permissions
   *
   * @param	int	$roleID The ID of the role to fetch permissions for
   *
   * (No return)
   */
  protected function getRolePerms(int $roleID)
  {
    // Check if data is valid
    if (!empty($roleID) && is_numeric($roleID)) {
      // Data is valid

      // Get the role permissions
      $rolePermsResults = $this->conn->dbh->selecting(
        DB_PREFIX . "role_perms AS rpl",
        "rp.rp_key",
        leftJoin(
          "rpl",
          DB_PREFIX . "role_permissions",
          "rpl_rp_id",
          "rp_id",
          "rp"
        ),
        where(
          eq('rpl.rpl_role_id', $roleID)
        )
      );

      // Check if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($rolePermsResults)) {

        // Set permissions
        foreach ($rolePermsResults as $permissionData) {
          $this->permissions[$permissionData->rp_key] = true;
        }
      } // No results
    } // Data is invalid
  }

  /**
   * Check if the user is a master user
   *
   * @param	int	$roleID The ID of the role to check
   *
   * (No return)
   */
  protected function setMasterUser(int $roleID)
  {
    // Check if data is valid
    if (!empty($roleID) && is_numeric($roleID)) {
      // Data is valid

      // Get the role permissions
      $roleResults = $this->conn->dbh->selecting(
        DB_PREFIX . "roles",
        "role_key",
        where(
          eq('role_id', $roleID)
        )
      );

      // Check if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($roleResults)) {

        // Check and set if master user
        $this->isMasterUser = ($roleResults[0]->role_key === "master") ? TRUE : FALSE;
        return;
      } // No results. Set as FALSE
    } // Data is invalid. Set as FALSE

    $this->isMasterUser = FALSE;
  }

  /**
   * Check if a permissions is available
   *
   * @param	string	$permission The permission to check for
   *
   * @return bool
   */
  public function canDo(string $permission)
  {
    return ($this->isMasterUser() || isset($this->permissions[trim($permission)])) ? TRUE : FALSE;
  }

  /**
   * Check if is a master user
   *
   * (No params)
   *
   * @return bool
   */
  public function isMasterUser()
  {
    return $this->isMasterUser;
  }

  /**
   * Get a users role
   *
   * @param	int	$userID The ID of the user to fetch role for
   *
   * @return int Will return a role ID if found
   */
  public function getUsersRole(int $userID)
  {
    // Check if data is valid
    if (!empty($userID) && is_numeric($userID)) {
      // Data is valid

      // Get the role
      $userRoleResults = $this->conn->dbh->selecting(
        DB_PREFIX . "users",
        "user_role_id",
        where(
          eq('user_id', $userID)
        )
      );

      // Check if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($userRoleResults)) {

        // Return users role ID
        return $userRoleResults[0]->user_role_id;
      } // No results. Return "0"
    } // Data is invalid. Return "0"

    // Return "0"
    return 0;
  }

  /**
   * Set a users permissions
   *
   * @param	int	$userID The ID of the user to fetch permissions for
   *
   * (No direct return)
   */
  public function setUserPermissions(int $userID)
  {
    // Check if data is valid
    if (!empty($userID) && is_numeric($userID)) {
      // Data is valid

      // Get the role
      $userRoleResults = $this->conn->dbh->selecting(
        DB_PREFIX . "users AS u",
        "u.user_role_id,
        r.role_key",
        leftJoin(
          "u",
          DB_PREFIX . "roles",
          "user_role_id",
          "role_id",
          "r"
        ),
        where(
          eq('u.user_id', $userID)
        )
      );

      // Check if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($userRoleResults)) {

        // Get users permissions
        $this->getRolePerms((int) $userRoleResults[0]->user_role_id);
        // Check and set if master user
        $this->isMasterUser = ($userRoleResults[0]->role_key === "master") ? TRUE : FALSE;
      } // No results
    } // Data is invalid
  }
}
