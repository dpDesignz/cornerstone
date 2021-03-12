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
    <?php if (1 === 2) { ?>
      <li><a href="<?php echo get_site_url('account/lists'); ?>" class="csc-pmitem<?php echo (!empty($pgMenu) && $pgMenu === 1) ? ' csc-pmitem--active' : ''; ?> csc-pmitem--disabled" data-tippy-content="Coming soon">Lists</a></li>
    <?php } ?>
    <li><a href="<?php echo get_site_url('account/orders'); ?>" class="csc-pmitem<?php echo (!empty($pgMenu) && $pgMenu === 3) ? ' csc-pmitem--active' : ''; ?>">Order History</a></li>
    <li><a href="<?php echo get_site_url('account/payment-options'); ?>" class="csc-pmitem<?php echo (!empty($pgMenu) && $pgMenu === 4) ? ' csc-pmitem--active' : ''; ?>">Payment Options</a></li>
    <li><a href="<?php echo get_site_url('account/addresses'); ?>" class="csc-pmitem<?php echo (!empty($pgMenu) && $pgMenu === 5) ? ' csc-pmitem--active' : ''; ?>">Saved Addresses</a></li>
    <li><a href="<?php echo get_site_url('account/settings'); ?>" class="csc-pmitem<?php echo (!empty($pgMenu) && $pgMenu === 10) ? ' csc-pmitem--active' : ''; ?>">Settings</a></li>
  </ol>
</nav>