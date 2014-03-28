<?php

/**
 * @file
 * Contains \Drupal\coffee\Form\CoffeeConfigurationForm.
 */

namespace Drupal\coffee\Form;

use Drupal\Core\Form\ConfigFormBase;


/**
 * Configure Coffee for this site.
 */
class CoffeeConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'coffee_configuration_form';
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::buildForm().
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->configFactory->get('coffee.configuration');

    $menus = menu_get_menus();
    $menu_options = array();

    foreach ($menus as $name => $title) {
      $menu_options[$name] = check_plain($title);
    }

    if (!empty($menu_options)) {
      // Settings for coffee.
      $form['coffee_menus'] = array(
        '#type' => 'checkboxes',
        '#title' => 'Menus to include',
        '#description' => 'Select the menus that should be used by Coffee to search.',
        '#options' => $menu_options,
        '#required' => TRUE,
        '#default_value' => $config->get('coffee_menus'),
      );
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::submitForm().
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    $this->configFactory->get('coffee.configuration')
    ->set('coffee_menus', $form_state['values']['coffee_menus'])
    ->save();

    // @todo Implement Cache::invalidateTags().
  }

}
