<?php

namespace Cornerstone;

/**
 * @package		Cornerstone
 * @author		Damien Peden
 * @copyright	Copyright (c) 2019-2020, dpDesignz (https://www.dpdesignz.co.nz/)
 * @link		https://github.com/dpDesignz/cornerstone
 */

/**
 * Notification Class
 */

class Notification
{

  // Protected Properties
  protected $id;
  protected $recipient;
  protected $group = 0;
  protected $status = 0;
  protected $type;
  protected $content;
  protected $typeID = 0;
  protected $createdAt;

  /**
   * Constructor
   */
  public function __construct($values = [])
  {
    // If values are specified, then the object is hydrated.
    if (!empty($values)) {
      $this->hydrate($values);
    }
  }

  /**
   * Hydrater
   */
  public function hydrate($data)
  {
    foreach ($data as $attribute => $value) {

      // Get the prefix
      $method = 'set' . ucfirst($attribute);

      if (is_callable([$this, $method])) {
        $this->$method($value);
      }
    }
  }

  /* SET */

  public function setId($id)
  {
    $this->id = (int) $id;
  }

  public function setRecipient($recipient)
  {
    $this->recipient = (int) $recipient;
  }

  public function setGroup($group)
  {
    $this->group = (int) $group;
  }

  public function setStatus($status)
  {
    $this->status = (int) $status;
  }

  public function setType($type)
  {
    $this->type = $type;
  }

  public function setContent($content)
  {
    $this->content = (object) $content;
  }

  public function setTypeID($typeID)
  {
    $this->typeID = (int) $typeID;
  }

  public function setCreated($createdAt)
  {
    $this->createdAt = $createdAt;
  }

  /* GET */

  public function id()
  {
    return $this->id;
  }

  public function recipient()
  {
    return $this->recipient;
  }

  public function group()
  {
    return $this->group;
  }

  public function status()
  {
    return $this->status;
  }

  public function type()
  {
    return $this->type;
  }

  public function content()
  {
    return $this->content;
  }

  public function typeID()
  {
    return $this->typeID;
  }

  public function createdAt()
  {
    return $this->createdAt;
  }
}
