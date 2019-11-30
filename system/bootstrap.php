<?php
  /**
   * Used to load the Cornerstone Framework.
   *
   * Allows for some configuration in the cs-config.php
   *
   * @package Cornerstone
   * @version 0.1.2
   * @author Damien Peden <support@dpdesignz.co>
   * @copyright 2019 dpDesignz. All Rights Reserved
   * @license This script can be used FREE of charge for any commercial or personal projects. Enjoy!
   * - LIMITATIONS
   * --- This script cannot be sold.
   * --- This script should have the copyright notice intact. Please dont remove it.
   * --- This script may not be provided for download except from its original site.
   * --- For further usage, please feel free to contact me.
   */

  // Load Composer vendor autoload [https://getcomposer.org/] (This is required for ezSQL, PHPMailer, random_compat, and WhichBrowser)
  require_once(DIR_SYSTEM . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

  // Autoload Core Libraries
  function cornerstoneAutoLoader($className) {
    // Fix director seperator
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    // Set the full path to the classes
    $classesPath = DIR_SYSTEM . 'cornerstone' . DIRECTORY_SEPARATOR . strtolower($className) . '.class.php';
    // Check if path exists and return false if it doesn't, else include the path file
    if(!file_exists($classesPath)) {
      return false;
    } else {
      require_once($classesPath);
    }
  }
  spl_autoload_register('cornerstoneAutoLoader');

  // Load files required for initialization.
  require_once( DIR_HELPERS . 'constants.php' ); // Load the default constants
  require_once( DIR_HELPERS . 'fn.errors.php' ); // Load the error handler

  // Load the master helper file
  require_once( DIR_HELPERS . 'fn.master.php' );

  // Set the default timezone for the site
  date_default_timezone_set(get_option("site_timezone"));

  // Load the session settings file
  require_once( DIR_HELPERS . 'fn.session.php' );

  // Load the rest of the Cornerstone Helper files
  include( DIR_HELPERS . 'fn.generate.php' ); // Generate related functions
  include( DIR_HELPERS . 'fn.output.php' ); // Output/Page related functions

  // Include the "custom" functions file for the site specfic functions
  // This file isn't over-written when updating
  if(file_exists( DIR_HELPERS . 'fn.custom.php' ) ) {
    include( DIR_HELPERS . 'fn.custom.php' );
  }

  // Show message on site if `testSite` is set in `cs_options`
  // if(get_option("test_site")) {$alertArray['info'][] = 'This site is a staging site and the data is NOT live.';}

  /**
   * Redirect to "offline" page if site set to `offline` in `cs_options`
   */
  // if(get_option("site_offline")) {header('Location: /offline');exit();}