<?php

/**
 * Account Menu File
 *
 * @package Cornerstone
 */ ?>
<nav class="csc-tabs">
  <ol>
    <li><a href="<?= get_site_url('account'); ?>" class="csc-tab<?= (empty($pgMenu) || $pgMenu === 0) ? ' csc-tab--active' : ''; ?>">Account Overview</a></li>
    <li><a href="<?= get_site_url('account/settings'); ?>" class="csc-tab<?= (!empty($pgMenu) && $pgMenu === 100) ? ' csc-tab--active' : ''; ?>">Settings</a></li>
  </ol>
</nav>