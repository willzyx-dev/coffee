<?php
/**
 * @file
 * The hooks that are used by Coffee
 * includes an example of hook_coffee_commands().
 */

use Drupal\Core;
use Drupal\Core\Url;

/**
 * Implements hook_coffee_commands().
 */
function coffee_coffee_commands() {
  $commands = array();

  // Add in a link for the frontpage.
  $commands[] = array(
    'value' => '',
    'label' => t('Go to front page'),
    'command' => ':front',
  );

  // The command is ":add" and includes the link_title to empower the
  // autocompletion.
  $command = ':add';
  $em = \Drupal::entityManager();
  // Only use node types the user has access to.
  foreach ($em->getStorage('node_type')->loadMultiple() as $type) {
    if ($em->getAccessControlHandler('node')->createAccess($type->type)) {
      $commands[] = array(
        'value' => Url::fromUri('base://node/add/' . $type->type)->toString(),
        'label' => $type->type,
        'command' => $command . ' ' . $type->type,
      );
    }
  }

  return $commands;
}

