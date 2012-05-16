<?php
/**
 * @file
 * The hooks that are used by Coffee
 * includes an example of hook_coffee_command()
 */

/**
 * The default implementation of Coffee.
 * It will look in the menu_links table for administrative links based on
 * the user input
 *
 * @param string $input
 *   The user input that will be used to query the database.
 */
function coffee_redirect($input) {

  // Glue the arguments together if there are more than 3 arguments.
  // This happens when the user $input typed a / in the input field.
  $arguments = arg();
  if (count($arguments) > 3) {
    $argument_query = array_splice($arguments, 3);
    $input = implode('/', $argument_query);
  }

  // Add the % for the LIKE query.
  $input = '%' . $input . '%';

  // Build the query for the result set.
  // Look up items in the menu_links table.
  // Limit the result set to 7.

  // Join menu_router table, for the menu link description.
  // Exclude % in link_path.
  // Exclude 'help' in link_path.
  // Filter out empty link_title.

  // Execute the query above.
  $query = "SELECT
  					  path, title
  					FROM
  					  {menu_router} mr
  					WHERE
  					  (
  					   mr.path LIKE '%s' OR
  					   mr.title LIKE '%s'
  					  )
  					  AND mr.title <> '%%'
  					  AND mr.path NOT LIKE '%help%'
  					  AND mr.path NOT LIKE '%\%%'
  					  ORDER BY mr.path ASC
  					  LIMIT 0, 75
  					";
  $args = array('admin/' . $input, 'admin/' . $input);
  $query = db_query($query, $args);

  while ($result = db_fetch_object($query)) {
    $results[] = $result;
  }

  if (is_array($results)) {
    $return = array();
    foreach ($results as $record) {

      // Load the menu item and check if the user has persmission.
      $item = menu_get_item($record->path);

      // Only add items to the return array if the user has access.
      if ($item['access']) {
        $match = array(
          'path' => $record->path,
          'title' => $record->title,
        );
        // Make this the top item if the title is an exact match.
        if (drupal_strtolower(trim($record->title)) == drupal_strtolower(trim($input, '% '))) {
          array_unshift($return, $match);
        }
        else {
          $return[] = $match;
        }
      }
    }
  }
  else {
    return FALSE;
  }

  // Return only a slice of a maximum of 7 results.
  if (is_array($return)) {
    $number_of_items = variable_get('coffee_number_of_results', 7);
    $return = array_slice($return, 0, $number_of_items);
  }

  return $return;

}

/**
 * Implements hook_coffee_command().
 */
function coffee_coffee_command($op) {
  switch ($op) {

    // Display the links of the node/add page.
    case 'add' :
      // This method is used in the core nodes module.
      $path = 'node/add';
      $item = menu_get_item($path);
      $content_types = system_admin_menu_block($item);

      $return = array();
      foreach ($content_types as $content_type) {
          $return[] = array(
            'path' => $content_type['link_path'],
            'title' => $content_type['link_title'],
          );
      }


      break;

  }

  if (isset($return)) {
    return $return;
  }
}
