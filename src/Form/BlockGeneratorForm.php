<?php

namespace Drupal\bits_developer_tool\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bits_developer_tool\Common\ClassName;
use Drupal\bits_developer_tool\Common\TypeOfFile;

class BlockGeneratorForm extends GenericGeneratorForm {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'block_generator_form';
  }

  public function className() {
    return ClassName::BLOCK;
  }

  public function typeOfFile() {
    return TypeOfFile::BLOCK;
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    // Adicionar campo Identificador del Bloque.
    $form['generator_container']['regional']['block_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Identificador del bloque'),
      '#default_value' => '',
      '#description' => t("Machine name o idenficador del bloque " . $this->className()),
    ];

    // Adicionar campo Admin Label.
    $form['generator_container']['regional']['admin_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Admin Label'),
      '#default_value' => '',
      '#description' => t("Nombre del Bloque Admin Label" . $this->className()),
    ];

    // A partir de aqui van los campos propios de la clase del block
    // Checbox para selección de métodos a generar.
    $list_keys_generation = [
      'generator_container' => 'regional_logic',
      'generator_container2' => 'integration_logic',
    ];
    foreach ($list_keys_generation as $key => $vars) {
      $form[$key][$vars]['optional_metod'] = [
        '#type' => 'details',
        '#title' => t('Generación de los métodos opcionales'),
      ];
      $list_option_metod = [
        'defaultConfiguration' => 'Configuración por defecto',
        'blockForm' => 'Fromulario del bloque',
        'blockAccess' => 'Acceso al Bloque',
        'blockValidate' => 'Validar el Bloque',
        'blockSubmit' => 'Salvar el Bloque',
      ];
      foreach ($list_option_metod as $keyOption => $variable) {
        $form[$key][$vars]['optional_metod'][$keyOption] = [
          '#type' => 'checkbox',
          '#title' => t($variable),
        ];
      }

    }


    return $form;
  }

  /**
   * Submit del bloque
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $generate_methods = [
      "defaultConfiguration" => $form_state->getValue('defaultConfiguration'),
      "blockForm" => $form_state->getValue('blockForm'),
      "blockAccess" => $form_state->getValue('blockAccess'),
      "blockValidate" => $form_state->getValue('blockValidate'),
      "blockSubmit" => $form_state->getValue('blockSubmit')
    ];

    $block_id = $form_state->getValue('block_id');

    $admin_label = $form_state->getValue('admin_label');

    $module = $form['module']['#options'][$form_state->getValue('module')];

    if ($form_state->getValue('only_logic') == 0) {
        $service_regional = $form_state->getValue('service_regional');
        $class_regional = $form_state->getValue('class_regional');
        $class_regional_logic = $form_state->getValue('class_regional_logic');
        $builder_controller = \Drupal::service('bits_developer.reg-block.builder');

        $builder_controller->addClassComments($class_regional, $block_id, $admin_label);
        $builder_controller->addClass($class_regional);
        $builder_controller->addIdentificator($service_regional);
        $builder_controller->addLogicClass($class_regional_logic);

    }
    else{
        $service_integration = $form_state->getValue('service_integration');
        $class_integration = $form_state->getValue('class_integration');
        $class_integration_logic = $form_state->getValue('class_integration_logic');
        $builder_controller = \Drupal::service('bits_developer.integration-block.builder');

        $builder_controller->addClassComments($class_integration, $block_id, $admin_label);
        $builder_controller->addClass($class_integration);
        $builder_controller->addIdentificator($service_integration);
        $builder_controller->addLogicClass($class_integration_logic);
    }
    $builder_controller->addImplementToClass();
    $builder_controller->addModule($module);
    $builder_controller->addMethodList($generate_methods);

    $success = $builder_controller->buildFiles();
  }

}
