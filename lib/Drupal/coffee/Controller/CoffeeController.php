<?php

/**
 * @file
 * Contains \Drupal\coffee\Controller\CoffeeController.
 */

namespace Drupal\coffee\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for coffee.module.
 */
class CoffeeController extends ControllerBase {

  /**
   * Outputs the data that is used for the Coffee autocompletion in JSON.
   */
  public function coffeeData() {
    $output = array();

    // @todo Placeholder.
    for ($i=1; $i<9; $i++){
      $output[] = array(
        'label' => 'label'.$i,
        'value' => 'value'.$i,
        'command' => 'command',
      );
    }


    return new JsonResponse($output);
  }

}
