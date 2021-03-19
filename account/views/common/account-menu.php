<?php

/**
 * Account Menu File
 *
 * @package Cornerstone
 * @subpackage Mission Equine
 */ ?>
<nav class="csc-pagemenu">
  <ol>
    <li><a href="<?php echo get_site_url('account'); ?>" class="csc-pmitem<?php echo (empty($pgMenu) || $pgMenu === 0) ? ' csc-pmitem--active' : ''; ?>">Account Overview</a></li>
    <li><a href="<?php echo get_site_url('account/settings'); ?>" class="csc-pmitem<?php echo (!empty($pgMenu) && $pgMenu === 100) ? ' csc-pmitem--active' : ''; ?>">Settings</a></li>
  </ol>
</nav>