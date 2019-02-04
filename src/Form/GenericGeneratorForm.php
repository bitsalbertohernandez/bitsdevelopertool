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
  public function getFormId()
  {
    return 'generic_generator_form';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $this->global_config = \Drupal::config(FileManager::ID_CONFIG);
    $module_list = \Drupal::service('bits_developer.util.operation')->listModule();
     // Select de módulos.
    $form['generator']['module'] = [
      '#type' => 'select',
      '#empty_value' => '',
      '#empty_option' => '- Selecione módulo -',
      '#options' => $module_list,
      '#ajax' => [
        'callback' => [$this, 'mySelectChange'],
        'event' => 'change',
        'wrapper' => 'replace_container',
      ],
    ];

    // Checbox para saber si es integración.
    $form['generator']['only_logic'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Generar integración'),
    ];

    // Contenedor de las tablas.
    $form['generator_container'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'replace_container',
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
      '#description' => "Namespace del " . $this->className(),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form['generator_container']['regional']['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio'),
      '#default_value' => $this->global_config->get('fisic_dir_base_' . $this->typeOfFile()),
      '#description' => "Directorio físico del " . $this->className(),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form['generator_container']['regional']['service'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Identificador del servicio'),
      '#default_value' => '',
      '#description' => "El identificador no debe contener espacios no caracteres extraños",
      '#required' => true
    ];

    // Tabla de las clases lógicas de las clases regionales.
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

    // Boton para generar las clases.
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
   * Coloca el nombre del módulo en las rutas de los namespace y los directorios físicos.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   * @return void
   */
  public function mySelectChange(array &$form, FormStateInterface &$form_state)
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

  // Metodo que devuelve el nombre de la clase.Ejemplo (Controlador, Bloque, Formulario, Servicio)
  public abstract function className();

  // Método que devuelve el tipo de servicio. Ejemplo (controller, block, form, rest )
  public abstract function typeOfFile();
}
