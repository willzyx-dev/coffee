<?php

/**
 * @file
 * Contains \Drupal\coffee\CoffeeConfigurationForm.
 */

namespace Drupal\coffee;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Extension\ModuleHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Coffee for this site.
 */
class CoffeeConfigurationForm extends ConfigFormBase {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $modulehandler;

  /**
   * Constructs a \Drupal\coffee\CoffeeConfiguration Form object.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Extension\ModuleHandler $module_handler
   *   The module handler.
   */
  public function __construct(ConfigFactory $config_factory, ModuleHandler $module_handler) {
    parent::__construct($config_factory);
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
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
  }

}
