<?php
/**
 * @file
 * The hooks that are used by Coffee
 * includes an example of hook_coffee_commands().
 */

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
  $em = \Drupal::entityTypeManager();
  // Only use node types the user has access to.
  foreach ($em->getStorage('node_type')->loadMultiple() as $type) {
    $node_type = $type->label();
    if ($em->getAccessControlHandler('node')->createAccess($node_type)) {
      $commands[] = array(
        'value' => Url::fromRoute('node.add', ['node_type' => $type->id()])->toString(),
        'label' => $node_type,
        'command' => $command . ' ' . $node_type,
      );
    }
  }

  return $commands;
}
