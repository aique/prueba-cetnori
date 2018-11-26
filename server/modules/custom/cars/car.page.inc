<?php

/**
 * @file
 * Contains car.page.inc.
 *
 * Page callback for Car entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Car templates.
 *
 * Default template: car.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_car(array &$variables) {
  // Fetch Car Entity Object.
  $car = $variables['elements']['#car'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
