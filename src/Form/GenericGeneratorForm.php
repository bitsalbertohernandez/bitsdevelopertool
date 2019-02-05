<?php

namespace Drupal\bits_developer_tool\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bits_developer_tool\Common\FileManager;

abstract class GenericGeneratorForm extends FormBase
{
  private $global_config;

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $this->global_config = \Drupal::config(FileManager::ID_CONFIG);
    $module_list = \Drupal::service('bits_developer.util.operation')->listModule();


    // Checbox para saber si es integración.
    $form['only_logic'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Generar integración'),
    ];

     // Select de módulos.
    $form['module'] = [
      '#type' => 'select',
      '#title' => $this->t('Módulo donde se generarán los archivos'),
      '#empty_value' => '',
      '#empty_option' => '- Selecione módulo -',
      '#options' => $module_list,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic"]' => ['checked' => true],
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'changeRegionalConfig'],
        'event' => 'change',
        'wrapper' => 'replace_container',
      ],
    ];

    // Contenedor de las tablas regionales.
    $form['generator_container'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'replace_container',
      ],
    ];
    // Contenedor de las tablas integración.
    $form['generator_container2'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'replace_container2',
      ],
    ];

    // Tablas de las clases bases de regional.
    $form['generator_container']['regional'] = [
      '#type' => 'details',
      '#title' => t('Definir ' . $this->className()),
      '#open' => true,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic"]' => ['checked' => true],
        ],
      ],
    ];

    $form['generator_container']['regional']['name_space'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#default_value' => $this->global_config->get('namespace_base_' . $this->typeOfFile()),
      '#description' => t("Namespace del " . $this->className()),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form['generator_container']['regional']['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio'),
      '#default_value' => $this->global_config->get('fisic_dir_base_' . $this->typeOfFile()),
      '#description' => t("Directorio físico del " . $this->className()),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form['generator_container']['regional']['service'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Identificador del servicio'),
      '#default_value' => '',
      '#description' => t("El identificador no debe contener espacios no caracteres extraños"),
      '#required' => true
    ];
    $form['generator_container']['regional']['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase'),
      '#default_value' => '',
      '#description' => t("Nombre con el que se generará la clase."),
      '#required' => true
    ];

    // Tabla de las clases lógicas regionales.
    $form['generator_container']['regional_logic'] = [
      '#type' => 'details',
      '#title' => t('Definir lógica del ' . $this->className()),
      '#open' => true,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic"]' => ['checked' => true],
        ],
      ],
    ];

    $form['generator_container']['regional_logic']['name_space'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#default_value' => $this->global_config->get('namespace_logic_' . $this->typeOfFile()),
      '#description' => "Namespace de la clase lógica del " . $this->className(),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form['generator_container']['regional_logic']['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio'),
      '#default_value' => $this->global_config->get('fisic_dir_logic_' . $this->typeOfFile()),
      '#description' => "Directorio físico de la clase lógica del " . $this->className(),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form['generator_container']['regional_logic']['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase'),
      '#default_value' => '',
      '#description' => t("Nombre con el que se generará la clase."),
      '#required' => true
    ];

    // Tablas para las integraciones
    $form['generator_container2']['integration'] = [
      '#type' => 'details',
      '#title' => t('Definir clase lógica regional del ' . $this->className()),
      '#open' => true,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic"]' => ['checked' => false],
        ],
      ],
    ];
    // todo: ver como filtro por el paquete regional
    $form['generator_container2']['integration']['module_integration'] = [
      '#type' => 'select',
      '#title' => $this->t('Módulo de la clase regional'),
      '#empty_value' => '',
      '#empty_option' => '- Selecione módulo -',
      '#options' => $module_list,
      '#ajax' => [
        'callback' => [$this, 'changeIntegrationConfig'],
        'event' => 'change',
        'wrapper' => 'replace_container2',
      ],
    ];
    $form['generator_container2']['integration']['name_space'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#default_value' => $this->global_config->get('namespace_base_' . $this->typeOfFile()),
      '#description' => t("Namespace del " . $this->className() . " regional"),
      '#attributes' => ['readonly' => 'readonly'],
    ];

    $form['generator_container2']['integration']['service'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Identificador del servicio regional'),
      '#default_value' => '',
      '#description' => t("El identificador no debe contener espacios, ni caracteres extraños"),
      '#required' => true
    ];
    $form['generator_container2']['integration']['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase'),
      '#default_value' => '',
      '#description' => t("Nombre de la clase lógica regional."),
      '#required' => true
    ];

      // Tabla de las clases lógicas de integration.
    $form['generator_container2']['integration_logic'] = [
      '#type' => 'details',
      '#title' => t('Definir lógica del ' . $this->className()),
      '#open' => true,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic"]' => ['checked' => false],
        ],
      ],
    ];

    $form['generator_container2']['integration_logic']['module_integration_logic'] = [
      '#type' => 'select',
      '#title' => $this->t('Módulo donde se generará la clases'),
      '#empty_value' => '',
      '#empty_option' => '- Selecione módulo -',
      '#options' => $module_list,
      '#ajax' => [
        'callback' => [$this, 'changeIntegrationConfig'],
        'event' => 'change',
        'wrapper' => 'replace_container2',
      ],
    ];

    $form['generator_container2']['integration_logic']['name_space'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#default_value' => $this->global_config->get('namespace_logic_' . $this->typeOfFile()),
      '#description' => "Namespace de la clase lógica del " . $this->className(),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form['generator_container2']['integration_logic']['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio'),
      '#default_value' => $this->global_config->get('fisic_dir_logic_' . $this->typeOfFile()),
      '#description' => "Directorio físico de la clase lógica del " . $this->className(),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form['generator_container2']['integration_logic']['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase'),
      '#default_value' => '',
      '#description' => t("Nombre con el que se generará la clase."),
      '#required' => true
    ];

    // Boton para generar las clases.
    $form['actions'] = [
      '#type' => 'button',
      '#value' => $this->t('Generar'),
    ];

    return $form;
  }

  /**
   * Coloca el nombre del módulo en las rutas de los namespace y los directorios físicos de regional.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   * @return void
   */
  public function changeRegionalConfig(array &$form, FormStateInterface &$form_state)
  {
    $module_name = $form_state->getTriggeringElement()['#options'][$form_state->getValue('module')];

    if (isset($module_name)) {

      // Re emplazando el nombre del módulo en las rutas reginales
      $name_space = $this->global_config->get('namespace_base_' . $this->typeOfFile());
      $path = $this->global_config->get('fisic_dir_base_' . $this->typeOfFile());

      $form['generator_container']['regional']['name_space']['#value'] = str_replace(FileManager::PATH_PREFIX, $module_name, $name_space);
      $form['generator_container']['regional']['path']['#value'] = str_replace(FileManager::PATH_PREFIX, $module_name, $path);

      // Re emplazando el nombre del módulo en las rutas reginales lógicas.
      $name_space_logic = $this->global_config->get('namespace_logic_' . $this->typeOfFile());
      $path_logic = $this->global_config->get('fisic_dir_logic_' . $this->typeOfFile());

      $form['generator_container']['regional_logic']['name_space']['#value'] = str_replace(FileManager::PATH_PREFIX, $module_name, $name_space_logic);
      $form['generator_container']['regional_logic']['path']['#value'] = str_replace(FileManager::PATH_PREFIX, $module_name, $path_logic);
      return $form['generator_container'];
    }

  }

  /**
   * Coloca el nombre del módulo en las rutas de los namespace y los directorios físicos de las integraciones.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   * @return void
   */
  public function changeIntegrationConfig(array &$form, FormStateInterface &$form_state)
  {
    $module = $form_state->getTriggeringElement()['#options'][$form_state->getValue('module_integration')];
    $module_logic = $form_state->getTriggeringElement()['#options'][$form_state->getValue('module_integration_logic')];

      // Re emplazando el nombre del módulo en las rutas de integración
    $name_space_logic = $this->global_config->get('namespace_logic_' . $this->typeOfFile());
    $path_logic = $this->global_config->get('fisic_dir_logic_' . $this->typeOfFile());

    if (isset($module)) {

      $form['generator_container2']['integration']['name_space']['#value'] = str_replace(FileManager::PATH_PREFIX, $module, $name_space_logic);
      $form['generator_container2']['integration']['path']['#value'] = str_replace(FileManager::PATH_PREFIX, $module, $path_logic);
    }

      // Re emplazando el nombre del módulo en las rutas reginales lógicas.
    if (isset($module_logic)) {
      $form['generator_container2']['integration_logic']['name_space']['#value'] = str_replace(FileManager::PATH_PREFIX, $module_logic, $name_space_logic);
      $form['generator_container2']['integration_logic']['path']['#value'] = str_replace(FileManager::PATH_PREFIX, $module_logic, $path_logic);
    }

    return $form['generator_container2'];
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    parent::submitForm($form, $form_state);
  }

  // Metodo que devuelve el nombre de la clase.Ejemplo (Controlador, Bloque, Formulario, Servicio)
  public abstract function className();

  // Método que devuelve el tipo de servicio. Ejemplo (controller, block, form, rest )
  public abstract function typeOfFile();
}
