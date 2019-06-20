<?php

namespace Drupal\bits_developer_tool\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bits_developer_tool\Common\FileManager;
use Drupal\bits_developer_tool\Common\TypeOfFile;
use Drupal\bits_developer_tool\Common\MessageType;
use Drupal\bits_developer_tool\Common\ClassName;

abstract class GenericGeneratorForm extends FormBase {
  protected $type_sms = MessageType::STATUS;
  private $global_config;
  /**
   * @var \Drupal\bits_developer_tool\Common\NameSpacePathConfig
   */
  private $namespace_path_config;
  private $namespace;
  private $namespace_logic;
  private $path;
  private $path_logic;
  protected $modules;

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $type = $this->typeOfFile();
    $this->modules = \Drupal::service('bits_developer.util.operation')->listModule();
    $this->global_config = \Drupal::config(FileManager::ID_CONFIG);

    $this->namespace_path_config = \Drupal::service('bits_developer.namespace.path');

    $this->namespace = $this->namespace_path_config->getNameSpace($type);
    $this->path = $this->namespace_path_config->getPath($type);

    $this->namespace_logic = $this->namespace_path_config->getNameSpaceLogic($type);
    $this->path_logic = $this->namespace_path_config->getPathLogic($type);

    // Checbox para saber si es integración.
    $form['only_logic'.$type] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Generar integración'),
    ];

     // Select de módulos.
    $form['module' . $type] = [
      '#type' => 'textfield',
      '#title' => $this->t('Módulo donde se generarán los archivos'),
      '#autocomplete_route_name' => 'bits_developer_tool.autocomplete_module',
      '#autocomplete_route_parameters' => array('count' => 10),
      '#states' => [
        'invisible' => [
          ':input[name="only_logic'.$type.'"]' => ['checked' => true],
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'changeRegionalConfig'],
        'event' => 'autocompleteclose',
        'wrapper' => 'replace_container' . $type,
      ],

    ];

    // Contenedor de las tablas regionales.
    $form[ 'generator_container' . $type] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'replace_container' . $type,
      ],
    ];
    // Contenedor de las tablas integración.
    $form[ 'generator_container2' . $type] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'replace_container2' . $type,
      ],
    ];

    // Tablas de las clases bases de regional.
    $form[ 'generator_container' . $type]['regional'] = [
      '#type' => 'details',
      '#title' => t('Definir ' . $this->className()),
      '#open' => true,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic'.$type.'"]' => ['checked' => true],
        ],
      ],
    ];

    $form[ 'generator_container' . $type]['regional']['name_space_regional'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#default_value' => $this->namespace,
      '#description' => t("Namespace del " . $this->className()),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form[ 'generator_container' . $type]['regional']['path_regional'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio'),
      '#default_value' => $this->path,
      '#description' => t("Directorio físico del " . $this->className()),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form[ 'generator_container' . $type]['regional']['service_regional'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Identificador del servicio'),
      '#default_value' => '',
      '#description' => t("El identificador no debe contener espacios no caracteres extraños"),
      //'#required' => true
    ];
    $form[ 'generator_container' . $type]['regional']['class_regional'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase'),
      '#default_value' => '',
      '#description' => t("Nombre con el que se generará la clase."),
      //'#required' => true
    ];

    // Tabla de las clases lógicas regionales.
    $form[ 'generator_container' . $type]['regional_logic'] = [
      '#type' => 'details',
      '#title' => t('Definir lógica del ' . $this->className()),
      '#open' => true,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic'.$type.'"]' => ['checked' => true],
        ],
      ],
    ];

    $form[ 'generator_container' . $type]['regional_logic']['name_space_regional_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#default_value' => $this->namespace_logic,
      '#description' => "Namespace de la clase lógica del " . $this->className(),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form[ 'generator_container' . $type]['regional_logic']['path_regional_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio'),
      '#default_value' => $this->path_logic,
      '#description' => "Directorio físico de la clase lógica del " . $this->className(),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form[ 'generator_container' . $type]['regional_logic']['class_regional_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase'),
      '#default_value' => '',
      '#description' => t("Nombre con el que se generará la clase."),
      //'#required' => true
    ];

    // Tablas para las integraciones
    $form[ 'generator_container2' . $type]['integration'] = [
      '#type' => 'details',
      '#title' => t('Definir clase lógica regional del ' . $this->className()),
      '#open' => true,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic'.$type.'"]' => ['checked' => false],
        ],
      ],
    ];
    // todo: ver como filtro por el paquete regional
    $form[ 'generator_container2' . $type]['integration']['module_integration'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Módulo de la clase regional'),
      '#autocomplete_route_name' => 'bits_developer_tool.autocomplete_module',
      '#autocomplete_route_parameters' => array('count' => 10),
      '#ajax' => [
        'callback' => [$this, 'changeIntegrationConfig'],
        'event' => 'autocompleteclose',
        'wrapper' => 'replace_container2'. $type,
      ],
    ];
    $form[ 'generator_container2' . $type]['integration']['name_space_integration'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#default_value' => $this->namespace,
      '#description' => t("Namespace del " . $this->className() . " regional"),
      '#attributes' => ['readonly' => 'readonly'],
    ];

    $form[ 'generator_container2' . $type]['integration']['service_integration'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Identificador del servicio regional'),
      '#default_value' => '',
      '#description' => t("El identificador no debe contener espacios, ni caracteres extraños"),
      //'#required' => true
    ];
    $form[ 'generator_container2' . $type]['integration']['class_integration'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase'),
      '#default_value' => '',
      '#description' => t("Nombre de la clase lógica regional."),
      //'#required' => true
    ];

      // Tabla de las clases lógicas de integration.
    $form[ 'generator_container2' . $type]['integration_logic'] = [
      '#type' => 'details',
      '#title' => t('Definir lógica del ' . $this->className()),
      '#open' => true,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic'.$type.'"]' => ['checked' => false],
        ],
      ],
    ];

    $form[ 'generator_container2' . $type]['integration_logic']['module_integration_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Módulo donde se generará la clases'),
      '#autocomplete_route_name' => 'bits_developer_tool.autocomplete_module',
      '#autocomplete_route_parameters' => array('count' => 10),
      '#ajax' => [
        'callback' => [$this, 'changeIntegrationConfig'],
        'event' => 'autocompleteclose',
        'wrapper' => 'replace_container2' .$type,
      ],
    ];

    $form[ 'generator_container2' . $type]['integration_logic']['name_space_integration_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#default_value' => $this->namespace_logic,
      '#description' => "Namespace de la clase lógica del " . $this->className(),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form[ 'generator_container2' . $type]['integration_logic']['path_integration_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio'),
      '#default_value' => $this->path_logic,
      '#description' => "Directorio físico de la clase lógica del " . $this->className(),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form[ 'generator_container2' . $type]['integration_logic']['class_integration_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase'),
      '#default_value' => '',
      '#description' => t("Nombre con el que se generará la clase."),
      //'#required' => true
    ];

    // Boton para generar las clases.
    $form['actions'] = [
      '#type' => 'submit',
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
  public function changeRegionalConfig(array &$form, FormStateInterface &$form_state) {
    $type = $this->typeOfFile();
    $module_name = $form_state->getValue( 'module' . $type);

    if (isset($module_name) && in_array($module_name, $this->modules)) {

      $form[ 'generator_container' . $type]['regional']['name_space_regional']['#value'] = str_replace(FileManager::PATH_PREFIX, $module_name, $this->namespace);
      $form[ 'generator_container' . $type]['regional']['path_regional']['#value'] = str_replace(FileManager::PATH_PREFIX, $module_name, $this->path);

      // Re emplazando el nombre del módulo en las rutas reginales lógicas.
    /*  $name_space_logic = $this->global_config->get('namespace_logic_' . $type);
      $path_logic = $this->global_config->get('fisic_dir_logic_' . $type);*/

      $form[ 'generator_container' . $type]['regional_logic']['name_space_regional_logic']['#value'] = str_replace(FileManager::PATH_PREFIX, $module_name, $this->namespace_logic);
      $form[ 'generator_container' . $type]['regional_logic']['path_regional_logic']['#value'] = str_replace(FileManager::PATH_PREFIX, $module_name, $this->path_logic);
     // $form_state->setRebuild(true);
      return $form[ 'generator_container' . $type];
    }

  }

  /**
   * Coloca el nombre del módulo en las rutas de los namespace y los directorios físicos de las integraciones.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   * @return void
   */
  public function changeIntegrationConfig(array &$form, FormStateInterface &$form_state) {
    $type = $this->typeOfFile();
    $module = $form_state->getValue( 'module_integration');
    $module_logic = $form_state->getValue( 'module_integration_logic');

    if (isset($module) && in_array( $module, $this->modules)) {

      $form[ 'generator_container2' . $type]['integration']['name_space_integration']['#value'] = str_replace(FileManager::PATH_PREFIX, $module, $this->namespace_logic);
      $form[ 'generator_container2' . $type]['integration']['path_integration']['#value'] = str_replace(FileManager::PATH_PREFIX, $module, $this->path_logic);
    }

      // Re emplazando el nombre del módulo en las rutas reginales lógicas.
    if (isset($module_logic) && in_array( $module_logic, $this->modules)) {
      $form[ 'generator_container2' . $type]['integration_logic']['name_space_integration_logic']['#value'] = str_replace(FileManager::PATH_PREFIX, $module_logic, $this->namespace_logic);
      $form[ 'generator_container2' . $type]['integration_logic']['path_integration_logic']['#value'] = str_replace(FileManager::PATH_PREFIX, $module_logic, $this->path_logic);
    }

    return $form['generator_container2'.$type];
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

  // Metodo que devuelve el nombre de la clase.Ejemplo (Controlador, Bloque, Formulario, Servicio)
  public abstract function className();

  // Método que devuelve el tipo de servicio. Ejemplo (controller, block, form, rest )
  public abstract function typeOfFile();

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('only_logic') == 0)
      $this->validateRegionalInputs($form_state);
    else {
      $this->validateIntegrationInput($form_state);
    }
  }
  protected function validateRegionalInputs(FormStateInterface $form_state) {
    $module = $form_state->getValue('module');
    $regional_service = $form_state->getValue('service_regional');
    $class_regional = $form_state->getValue('class_regional');
    $class_regional_logic = $form_state->getValue('class_regional_logic');
    if ($module == '')
      $form_state->setErrorByName('module', $this->t('Debe seleccionar un modulo.'));

    if (str_replace(' ', '', $regional_service) != $regional_service) {
      $form_state->setErrorByName('service_regional', $this->t('El id del servicio no puede contener espacios en blanco.'));
    }
    if ($class_regional == '') {
      $form_state->setErrorByName('class_regional', $this->t('El nombre de la clase no puede ser vacio.'));
    }
    if ($class_regional_logic == '') {
      $form_state->setErrorByName('class_regional_logic', $this->t('El nombre de la clase no puede ser vacio.'));
    }
  }
  protected  function validateIntegrationInput(FormStateInterface $form_state) {
    $module_int = $form_state->getValue('module_integration');
    $class_integration = $form_state->getValue('class_integration');
    $module_imp = $form_state->getValue('module_integration_logic');
    $class_specific_logic = $form_state->getValue('class_integration_logic');
    $service_int = $form_state->getValue('service_integration');
    if ($module_int == '') {
      $form_state->setErrorByName('module_integration', $this->t('Debe seleccionar un modulo.'));
    }
    if ($module_imp == '') {
      $form_state->setErrorByName('module_integration_logic', $this->t('Debe seleccionar un modulo.'));
    }
    if ($class_integration == '') {
      $form_state->setErrorByName('class_integration', $this->t('Debe introducir un nombre para la clase.'));
    }
    if ($class_specific_logic == '') {
      $form_state->setErrorByName('class_integration_logic', $this->t('Debe introducir un nombre para la clase.'));
    }
    if ($service_int == '') {
      $form_state->setErrorByName('service_integration', $this->t('Debe introducir un identificador valido.'));
    }
    if ($class_integration != '') {
      $path = str_replace(FileManager::PATH_PREFIX, $module_int, $form_state->getValue('path_regional_logic'));
      $file = DRUPAL_ROOT. '/modules/custom/' . $path . '/' . $class_integration . '.php';
      $file_manager = \Drupal::service('bits_developer.file.manager');
      $exist = $file_manager->pathExist(str_replace('\\','/',$file));
      if (!$exist)
        $form_state->setErrorByName('class_integration', 'La clase nombrada no existe en ese modulo');
    }
  }


  // Mostrar mensajes de confirmación.
  public function confirmationMessage($sms) {
    drupal_set_message($sms,$this->type_sms);
  }

  // Mensaje satisfactorio por defecto.
  public function defaultSucessMessage(){
    $this->type_sms = MessageType::STATUS;
    return "Se generó satisfactoriamente los archivos del ".$this->classNameToLower($this->typeOfFile());
  }

  // Mensaje de error por defecto.
  public function defaultErrorMessage(){
    $this->type_sms = MessageType::ERROR;
    return "Ocurrió un error al generar los archivos del ".$this->classNameToLower($this->typeOfFile());
  }

  // Retornar en minúscula el nombre de la clase del tipo de archivo.
  private function classNameToLower($type_of_file) {
    switch ($type_of_file) {
      case TypeOfFile::BLOCK:
        $file_name = strtolower(ClassName::BLOCK);
        break;
      case TypeOfFile::FORM:
        $file_name = strtolower(ClassName::FORM);
        break;
      case TypeOfFile::SERVICE:
        $file_name = strtolower(ClassName::REST);
        break;

      default:
        $file_name = strtolower(ClassName::CONTROLLER);
        break;
    }
    return $file_name;
  }
}
