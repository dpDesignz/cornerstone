<?php

namespace Cornerstone;

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) 2020, dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 *
 * Based on https://www.sitepoint.com/role-based-access-control-in-php/
 */

use function ezsql\functions\{
  selecting,
  leftJoin,
  where,
  eq
};

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
  public function __construct($cdbh)
  {

    // Set the database connection
    $this->conn = $cdbh;
    $this->conn->dbh->tableSetup('roles', DB_PREFIX);
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
      $this->conn->dbh->tableSetup('role_perms AS rpl', DB_PREFIX);
      $rolePermsResults = selecting(
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
   * Get the user permissions
   *
   * @param	int	$userID The ID of the user to fetch permissions for
   *
   * (No return)
   */
  protected function getUserPerms(int $userID)
  {
    // Check if data is valid
    if (!empty($userID) && is_numeric($userID)) {
      // Data is valid

      // Get user permissions
      $this->conn->dbh->tableSetup('user_perms AS upl', DB_PREFIX);
      $userPermsResults = selecting(
        "rp.rp_key,
        upl.upl_access",
        leftJoin(
          "upl",
          DB_PREFIX . "role_permissions",
          "upl_rp_id",
          "rp_id",
          "rp"
        ),
        where(
          eq('upl.upl_user_id', $userID)
        )
      );


      // Check if results
      if ($this->conn->dbh->getNum_Rows() > 0 && !empty($userPermsResults)) {

        // Set permissions
        foreach ($userPermsResults as $permissionData) {
          // Permission allowed. Check to add to array
          if (!isset($this->permissions[$permissionData->rp_key]) && (int) $permissionData->upl_access === 1)
            $this->permissions[$permissionData->rp_key] = true;

          // Permission not allowed. Remove from array
          if (isset($this->permissions[$permissionData->rp_key]) && (int) $permissionData->upl_access === 2)
            unset($this->permissions[$permissionData->rp_key]);
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
      $this->conn->dbh->tableSetup('roles', DB_PREFIX);
      $roleResults = selecting(
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
      $this->conn->dbh->tableSetup('users', DB_PREFIX);
      $userRoleResults = selecting(
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
      $this->conn->dbh->tableSetup('users AS u', DB_PREFIX);
      $userRoleResults = selecting(
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
        $this->getUserPerms((int) $userID);
        // echo '<pre>';
        // print_r($this->permissions);
        // echo '</pre>';
        // exit;
        // Check and set if master user
        $this->isMasterUser = ($userRoleResults[0]->role_key === "master") ? TRUE : FALSE;
      } // No results
    } // Data is invalid
  }
}
