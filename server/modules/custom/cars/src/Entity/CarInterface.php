<?php

namespace Drupal\cars\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Car entities.
 *
 * @ingroup cars
 */
interface CarInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Car creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Car.
   */
  public function getCreatedTime();

  /**
   * Sets the Car creation timestamp.
   *
   * @param int $timestamp
   *   The Car creation timestamp.
   *
   * @return \Drupal\cars\Entity\CarInterface
   *   The called Car entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Car published status indicator.
   *
   * Unpublished Car are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Car is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Car.
   *
   * @param bool $published
   *   TRUE to set this Car to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\cars\Entity\CarInterface
   *   The called Car entity.
   */
  public function setPublished($published);

  public function getPlate();
  public function getColor();
  public function getKilometers();
  public function getPicture();


}
