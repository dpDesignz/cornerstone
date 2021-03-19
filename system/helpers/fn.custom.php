<?php

/**
 * The "Custom" related functions file
 *
 * @package Custom Package Name
 */

/**
 * Output menu
 *
 * @param object $menuItems
 * @param string $pathMatch
 *
 * @return string Will return the menu as a string
 */
function outputMenu(object $menuItems, string $pathMatch = null)
{
  // Replace the following code to make your own menu output
  // This function is location in the fn.output.php helper file
  return base_outputMenu($menuItems, $pathMatch);
}
