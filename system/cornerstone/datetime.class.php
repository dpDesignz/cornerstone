<?php

namespace Cornerstone;

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 *
 * @since 0.7.0 Introduced.
 */

/**
 * DateTime Class
 */

class DateTime extends \DateTime
{
  // Define properties
  protected $user;

  /**
   * Constructs a new instance of datetime, expanded to include an argument to inject
   * the user context and modify the timezone to the users selected timezone if one is not set.
   *
   * @param user $user object for context. Normally $_SESSION['_cs']['user']
   * @param string $time String in a format accepted by strtotime().
   * @param \DateTimeZone|null $timezone Time zone of the time.
   */
  public function __construct($user, $time = 'now', \DateTimeZone $timezone = null)
  {
    $this->user = (object) $user;
    $timezone = $timezone ?: $this->user->timezone;

    if ($time === "now") {
      parent::__construct($time, $timezone);
    } else {
      parent::__construct($time, new \DateTimeZone(date_default_timezone_get()));
      $this->setTimezone($timezone);
    }
  }

  /**
   * Returns timezone difference to Greenwich time (GMT/UTC)
   *
   * @param string $timezone Returns the difference as a string.
   */
  public function getTimeDifference()
  {
    $now = new self($this->user);
    $timeDifference = $now->format('P');
    return ($timeDifference !== "+00:00") ? $timeDifference : '';
  }
}
