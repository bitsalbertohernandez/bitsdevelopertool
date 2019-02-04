<?php

namespace Drupal\bits_developer_tool\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class GenericGeneratorForm extends FormBase
{
  /**
   * {@inheritdoc}.
   */
  public function getFormId()
  {
    return 'generic_generator_form';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $module_list = \Drupal::service('bits_developer.util.operation')->listModule();
    if (count($module_list) == 0) {
      $module_list = ['prueba1', 'prueba2'];
    }
    $form['generic_ajax_container'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'generic_ajax_container',
      ],
    ];
    $form['generic_ajax_container']['module'] = [
      '#type' => 'select',
      '#title' => $this->t('Módulo'),
      '#empty_value' => '',
      '#empty_option' => '- Selecione módulo -',
      '#options' => $module_list,
      '#ajax' => [
        'callback' => [$this, 'mySelectChange'],
        'event' => 'change',
        'wrapper' => 'generic_ajax_container',
      ],
    ];

    $form['generic_ajax_container']['only_logic'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Solo generar clase lógica'),
    ];
/*
    $form['generic_ajax_container']['table_extend'] = [
      '#type' => 'details',
      '#title' => t('Clase base'),
      '#open' => true,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic"]' => ['checked' => true],
        ],
      ],
    ];
    $form['generic_ajax_container']['table_extend']['name_space_extend'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#default_value' => '',
      '#description' => "Namespace de la clase que se heredará.",
    ];
    $form['generic_ajax_container']['table_extend']['name_extend'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre'),
      '#default_value' => '',
      '#description' => "Nombre de la clase que se heredará.",
    ];*/
    $form['generic_ajax_container']['table_controller'] = [
      '#type' => 'details',
      '#title' => t('Clase controladora'),
      '#open' => true,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic"]' => ['checked' => true],
        ],
      ],
    ];
    $form['generic_ajax_container']['table_controller']['name_space_controller'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#default_value' => '',
      '#description' => "Namespace del controlador.",
      '#attributes' => ['readonly' => 'readonly'],

    ];
    $form['generic_ajax_container']['table_controller']['path_controller'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio'),
      '#default_value' => '',
      '#description' => "Directorio físico del controlador.",
      '#attributes' => ['readonly' => 'readonly'],

    ];
    $form['generic_ajax_container']['table_controller']['name_controller'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre'),
      '#default_value' => '',
      '#description' => "Al nombre se le agregará el sufijo configurado.",
    ];

    $form['service_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Servicio'),
      '#default_value' => '',
      '#description' => "Identificador del servicio de la clase lógica.",
      '#states' => [
        'invisible' => [
          ':input[name="only_logic"]' => ['checked' => true],
        ],
      ],
    ];
/*
    $form['generic_ajax_container']['table_extend_logic'] = [
      '#type' => 'details',
      '#title' => t('Clase lógica base'),
      '#open' => true,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic"]' => ['checked' => false],
        ],
      ],
    ];
    $form['generic_ajax_container']['table_extend_logic']['name_space_extend_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#default_value' => '',
      '#description' => "Namespace de la clase lógica que se heredará.",
    ];
    $form['generic_ajax_container']['table_extend_logic']['name_extend_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre'),
      '#default_value' => '',
      '#description' => "Nombre de la clase lógica que se heredará.",
    ];
    $form['generic_ajax_container']['table_controller_logic'] = [
      '#type' => 'details',
      '#title' => t('Clase lógica del controlador'),
      '#open' => true,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic"]' => ['checked' => false],
        ],
      ],
    ];
    $form['generic_ajax_container']['table_controller_logic']['name_space_controller_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#default_value' => '',
      '#description' => "Namespace de la clase lógica del controlador.",
      '#attributes' => ['readonly' => 'readonly'],

    ];
    $form['generic_ajax_container']['table_controller_logic']['path_controller_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio'),
      '#default_value' => '',
      '#description' => "Directorio físico de la lógica del controlador.",
      '#attributes' => ['readonly' => 'readonly'],

    ];
    $form['generic_ajax_container']['table_controller_logic']['name_controller_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre'),
      '#default_value' => '',
      '#description' => "Al nombre se le agregará el sufijo configurado.",
    ];*/
    $form['generic_ajax_container']['old_module'] = array(
      '#type' => 'hidden',
      '#value' => '{modulo}',
    );

    $form['actions'] = [
      '#type' => 'button',
      '#value' => $this->t('Generar'),
      '#states' => [
        'invisible' => [
          ':input[name="module"]' => ['value' => ''],
        ],
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {

  }

  public function mySelectChange(array &$form, FormStateInterface &$form_state)
  {
    $module_name = $form_state->getTriggeringElement()['#options'][$form_state->getValue('module')];

    if (isset($module_name)) {
      $old_module = $form_state->getValue('old_module');
      $name_space_controller = $form_state->getValue('name_space_controller');
      $path_controller = $form_state->getValue('path_controller');
      $name_space_controller_logic = $form_state->getValue('name_space_controller_logic');
      $path_controller_logic = $form_state->getValue('path_controller_logic');

      $form['generic_ajax_container']['table_controller']['name_space_controller']['#value'] = str_replace($old_module, $module_name, $name_space_controller);
      $form['generic_ajax_container']['table_controller']['path_controller']['#value'] = str_replace($old_module, $module_name, $path_controller);
      $form['generic_ajax_container']['table_controller_logic']['name_space_controller_logic']['#value'] = str_replace($old_module, $module_name, $name_space_controller_logic);
      $form['generic_ajax_container']['table_controller_logic']['path_controller_logic']['#value'] = str_replace($old_module, $module_name, $path_controller_logic);
      $form_state->set('old_module', $module_name);
      $form_state->setRebuild(false);
      return $form['generic_ajax_container'];
    }

  }
}
