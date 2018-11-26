<?php

namespace Drupal\cars\Plugin\rest\resource;

use Drupal\cars\Entity\Car;
use Drupal\rest\Plugin\ResourceBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @RestResource(
 *   id = "cars_resource",
 *   label = @Translation("Cars resource"),
 *   uri_paths = {
 *     "canonical" = "/api/cars"
 *   }
 * )
 */
class CarsResource extends ResourceBase {

  public function get() {
      $carIds = \Drupal::entityQuery('car')->execute();
      $cars = Car::loadMultiple($carIds);

      return new JsonResponse($this->getJson($cars));
  }

  private function getJson(array $cars) {
      $json = [];

      foreach ($cars as $car) {
          $json[] = $car->jsonSerialize();
      }

      return $json;
  }

}
