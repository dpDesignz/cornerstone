<?php

/**
 * Custom authentication values.
 * An extension of the Admin "userauth.php" file that isn't overwritten on update.
 *
 * @package Cornerstone
 */

use function ezsql\functions\{
  selecting,
  where,
  eq
};

/**
 * Set what custom $_SESSION information you're wanting to.
 *
 * This function is called from the Admin "userauth.php" file at authenticateUser()
 * and is used to set any extra $_SESSION items you're wanting to set.
 *
 * Already set: User ID, Email, Login Name, Full Name, Language, Timezone, Date Format
 * If you would like to over-ride or delete any of these, do so here
 *
 * Whatever custom $_SESSION items you set here should also be copied to the `clearCustomAuth()`
 * function below to unset them at logout if the $_SESSION items was not set under $_SESSION['_cs']...
 */
// example - $_SESSION['_cs']['custom_item'] = 'item value';
// or $_SESSION['_cs']['custom_item'] = $option->getUser(1, array('user_meta_item' => 1));
