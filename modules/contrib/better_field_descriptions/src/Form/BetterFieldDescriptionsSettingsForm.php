<?php

namespace Drupal\better_field_descriptions\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityFieldManager;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Displays the better_field_descriptions settings form.
 */
class BetterFieldDescriptionsSettingsForm extends ConfigFormBase {

  /**
   * EntityFieldManager services object.
   *
   * @var Drupal\Core\Entity\EntityFieldManager
   */
  private $entityFieldManager;

  /**
   * EntityTypeManager services object.
   *
   * @var Drupal\Core\Entity\EntityTypeManager
   */
  private $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityFieldManager $entityFieldManager, EntityTypeManager $entityTypeManager) {
    parent::__construct($config_factory);
    $this->entityFieldManager = $entityFieldManager;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_field.manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['better_field_descriptions.settings'];
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::getFormID().
   */
  public function getFormId() {
    return 'better_field_descriptions_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get info on bundles.
    $bundles = entity_get_bundles('node');
    // Get list of fields selected for better descriptions.
    $bfds = $this->config('better_field_descriptions.settings')->get('better_field_descriptions_settings');

    $form['descriptions'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Select fields that should have better descriptions.'),
    ];

    $form['bundles'] = [
      '#type' => 'item',
      '#prefix' => '<div id="better-descriptions-form-id-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    ];

    foreach ($bundles as $bundle_machine_name => $bundle) {
      // Array to hold fields in the node.
      $fields_instances = [];
      // Get info on pseudo fields, like title.
      $extra_fields = $this->entityFieldManager->getExtraFields('node', $bundle_machine_name, 'form');
      if (isset($extra_fields['title'])) {
        $fields_instances['title'] = $extra_fields['title']['label'];
      }

      // Get info on regular fields to the bundle.
      $entityTypeManager = $this->entityFieldManager;
      $fields = $entityTypeManager->getFieldDefinitions('node', $bundle_machine_name);
      foreach ($fields as $field_machine_name => $field) {
        if ($field->getFieldStorageDefinition()->isBaseField() == FALSE) {
          $fields_instances[$field_machine_name] = $field->getLabel() . ' (' . $field_machine_name . ')';
        }
      }

      // Compute default values.
      $default_values = [];
      if (isset($bfds[$bundle_machine_name])) {
        $default_values = array_intersect_key($bfds[$bundle_machine_name], $fields_instances);
      }

      // Generate checkboxes.
      $form['bundles'][$bundle_machine_name] = [
        '#type' => 'checkboxes',
        '#title' => $bundle['label'],
        '#options' => $fields_instances,
        '#default_value' => $default_values,
        '#description' => $this->t('Choose which fields should have better descriptions.'),
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // We don't want our settings to contain 0-values, only selected values.
    $bfds = [];

    foreach ($form_state->getValue('bundles') as $bundle_machine_name => $bundle) {
      foreach ($bundle as $field_machine_name => $value) {

        // $value is (int) 0 if the field was not selected in the form.
        if (is_string($value)) {
          $bfds[$bundle_machine_name][$field_machine_name] = $field_machine_name;
        }
      }
    }

    $config = $this->config('better_field_descriptions.settings')->set('better_field_descriptions_settings', $bfds);
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
