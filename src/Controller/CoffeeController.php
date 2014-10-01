<?php

/**
 * @file
 * Contains \Drupal\coffee\Controller\CoffeeController.
 */

namespace Drupal\coffee\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Provides route responses for coffee.module.
 */
class CoffeeController extends ControllerBase {

  /**
   * Outputs the data that is used for the Coffee autocompletion in JSON.
   */
  public function coffeeData() {
    $output = array();

    // Get configured menus from configuration.
    $menus = \Drupal::config('coffee.configuration')->get('coffee_menus');
    if ($menus !== NULL) {
      foreach ($menus as $v) {
        if ($v === '0') {
          continue;
        }

        // Build the menu tree.
        $menu_tree_parameters = new MenuTreeParameters();
        $tree = \Drupal::menuTree()->load($v, $menu_tree_parameters);

        foreach ($tree as $key => $link) {

          $command = ($v == 'user-menu') ? ':user' : NULL;
          $this->coffee_traverse_below($link, $output, $command);

        }
      }
    }

    module_load_include('inc', 'coffee', 'coffee.hooks');
    $commands = array();

    foreach (\Drupal::moduleHandler()->getImplementations('coffee_commands') as $module) {
      $commands = array_merge($commands, \Drupal::moduleHandler()->invoke($module, 'coffee_commands', array()));
    }

    if (!empty($commands)) {
      $output = array_merge($output, $commands);
    }

    foreach ($output as $k => $v) {
      if ($v['value'] == '<front>') {
        unset($output[$k]);
        continue;
      }

      // Filter out XSS.
      $output[$k]['label'] = Xss::filter($output[$k]['label']);

    }

    // Re-index the array.
    $output = array_values($output);

    return new JsonResponse($output);
  }

  /**
   * Function coffee_traverse_below().
   *
   * Helper function to traverse down through a menu structure.
   */
  protected function coffee_traverse_below($link, &$output, $command = NULL) {
    $l = isset($link->link) ? $link->link : array();


   // Only add link if user has access.
   //if (isset($l->access) && $l->access) {
      $title = $l->getTitle();
      $url = $l->getUrlObject()->toString();
      $label = (!empty($title) ? $title : 'test');
      $output[] = array(
        'value' => $url,
        'label' => $label,
        'command' => $command,
      );
    //}

    if ($l->subTree === 000) {
      foreach ($l->subTree as $below_link) {
        $this->coffee_traverse_below($below_link, $output);
      }
    }
  }

}
