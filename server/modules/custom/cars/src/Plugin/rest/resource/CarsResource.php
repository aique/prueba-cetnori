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
class CarsResource extends ResourceBase
{

    const CARS_LIST_CACHE_KEY = 'cars_list';

    public function get()
    {
        if ($cacheCarList = $this->getCarListFromCache()) {
            $cars = $cacheCarList->data;
        } else {
            $cars = $this->queryCarListFromDB();
            $this->storeInCache($cars);
        }

        return new JsonResponse($this->getJson($cars));
    }

    private function getCarListFromCache() {
        return \Drupal::cache()->get(self::CARS_LIST_CACHE_KEY);
    }

    private function storeInCache(array $cars) {
        \Drupal::cache()->set(self::CARS_LIST_CACHE_KEY, $cars);
    }

    private function queryCarListFromDB() {
        $carIds = \Drupal::entityQuery('car')->execute();
        $cars = Car::loadMultiple($carIds);

        return $cars;
    }

    private function getJson(array $cars)
    {
        $json = [];

        foreach ($cars as $car) {
            $json[] = $car->jsonSerialize();
        }

        return $json;
    }

}
