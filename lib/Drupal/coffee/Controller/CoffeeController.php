<?php

/**
 * @file
 * Contains \Drupal\coffee\Controller\CoffeeController.
 */

namespace Drupal\coffee\Controller;

use Drupal\Core\Controller\ControllerBase;
use \Drupal\Component\Utility\Json;

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
    $output[] = array(
      'label' => 'label',
      'value' => 'value',
      'command' => 'command',
    );

    return \Drupal\Component\Utility\Json::encode($output);
  }

}
