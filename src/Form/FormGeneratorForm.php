<?php

namespace Drupal\bits_developer_tool\Form;


use Drupal\Console\Bootstrap\Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bits_developer_tool\Common\ClassName;
use Drupal\bits_developer_tool\Common\TypeOfFile;
use Drupal\bits_developer_tool\Common\FileManager;


class FormGeneratorForm extends GenericGeneratorForm
{
  private $global_config;
  /**
   * @var \Drupal\bits_developer_tool\Common\NameSpacePathConfig
   */
  private $namespace_path_config;
  private $namespace;
  private $namespace_logic;
  private $path;
  private $path_logic;
  private $namespace_regional_class;
  private $file_manager;

  /**
   * {@inheritdoc}.
   */
  public function getFormId()
  {
    return 'form_generator_form';
  }
  public function className()
  {
    return ClassName::FORM;
  }

  public function typeOfFile()
  {
    return TypeOfFile::FORM;
  }

  private function initConfigVar() {
    $this->global_config = \Drupal::config(FileManager::ID_CONFIG);

    $this->namespace_path_config = \Drupal::service('bits_developer.namespace.path');

    $this->namespace = $this->namespace_path_config->getNameSpace($this->typeOfFile());
    $this->path = $this->namespace_path_config->getPath($this->typeOfFile());

    $this->namespace_logic = $this->namespace_path_config->getNameSpaceLogic($this->typeOfFile());
    $this->path_logic = $this->namespace_path_config->getPathLogic($this->typeOfFile());
    $this->file_manager = \Drupal::service('bits_developer.file.manager');
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $this->initConfigVar();
    $util_service = \Drupal::service('bits_developer.util.operation');
    $module_list = $util_service->listModule();

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
    ];

    // Tablas de las clases bases de REGIONAL!!!!.
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

    $form['generator_container']['regional']['name_space_regional'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#default_value' => $this->namespace,
      '#description' => t("Namespace del " . $this->className()),
      '#attributes' => ['readonly' => 'readonly'],
    ];

    $form['generator_container']['regional']['path_regional'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio'),
      '#default_value' => $this->path,
      '#description' => t("Directorio físico del " . $this->className()),
      '#attributes' => ['readonly' => 'readonly'],
    ];

    $form['generator_container']['regional']['formId'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Identificacion del Formulario'),
      '#default_value' => '',
      '#description' => t('Cadena de caracteres que identifica a la clase Formulario'),
    ];

    $form['generator_container']['regional']['class_regional'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase Formulario'),
      '#default_value' => '',
      '#description' => t("Nombre con el que se generará la clase Formulario."),
      //'#required' => true
    ];

    $form['generator_container']['regional']['service_regional'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Identificador del servicio'),
      '#default_value' => '',
      '#description' => t("El identificador del servicio de la configuracion. Este valor no debe contener espacios ni caracteres extraños"),
      //'#required' => true
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

    $form['generator_container']['regional_logic']['name_space_regional_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#default_value' => $this->namespace_logic,
      '#description' => "Namespace de la clase lógica del " . $this->className(),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form['generator_container']['regional_logic']['path_regional_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio'),
      '#default_value' => $this->path_logic,
      '#description' => "Directorio físico de la clase lógica del " . $this->className(),
      '#attributes' => ['readonly' => 'readonly'],
    ];
    $form['generator_container']['regional_logic']['class_regional_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase logica'),
      '#default_value' => '',
      '#description' => t("Nombre con el que se generará la clase logica."),
      //'#required' => true
    ];

    // Tablas de las clases bases de INTEGRACION!!!!.



    $form['generator_container']['integration'] = [
      '#type' => 'details',
      '#title' => t('Definir ' . $this->className()),
      '#open' => true,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic"]' => ['checked' => false],
        ],
      ],
    ];

    $list = $util_service->listModuleByPackage('BITS');
    $form['generator_container']['integration']['module_integration'] = [
      '#type' => 'select',
      '#title' => $this->t('Modulo regional para realizar la integracion'),
      '#empty_value' => '',
      '#empty_option' => '- Selecione el modulo -',
      '#options' => $list,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic"]' => ['checked' => false],
        ],
      ],
    ];

    $form['generator_container']['integration']['class_integration'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase Logica del Formulario'),
      '#default_value' => '',
      '#description' => t("Nombre de la clase Logica del Formulario."),
      //'#required' => true
    ];

    $list = $util_service->listModuleByPackage('BITS');
    $form['generator_container']['integration']['module_imp'] = [
      '#type' => 'select',
      '#title' => $this->t('Modulo regional para realizar la integracion'),
      '#empty_value' => '',
      '#empty_option' => '- Selecione el modulo -',
      '#options' => $list,
      '#states' => [
        'invisible' => [
          ':input[name="only_logic"]' => ['checked' => false],
        ],
      ],
    ];
    $form['generator_container']['integration']['class_specific_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase logica integrada'),
      '#default_value' => '',
      '#description' => t("Nombre con el que se generará la clase logica integrada."),
      //'#required' => true
    ];

    // Boton para generar las clases.
    $form['actions'] = [
      '#type' => 'submit',
      '#value' => $this->t('Generar'),
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('only_logic') == 0)
      $this->validateRegionalInputs($form_state);
    else {
      $this->validateIntegrationInput($form, $form_state);
    }

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    if ($form_state->getValue('only_logic') == 0)
      $this->generateRegionalClasses($form, $form_state);
    else {
     // ksm($form_state->getValue('only_logic'));
      $module_int = $form['generator_container']['integration']['module_integration']['#options'][$form_state->getValue('module_integration')];
      $class_integration = $form_state->getValue('class_integration');
      ksm( $this->file_manager->getNamespace($module_int,TypeOfFile::FORM,$class_integration));

    }
  }

  private function generateRegionalClasses(array $form, FormStateInterface $form_state) {
    $class_regional = $form_state->getValue('class_regional');

    $module = $form['module']['#options'][$form_state->getValue('module')];

    $service_regional = $form_state->getValue('service_regional');

    $form_id = $form_state->getValue('formId');

    $class_regional_logic = $form_state->getValue('class_regional_logic');
    $builder_controller = \Drupal::service('bits_developer.reg-form.builder');
    $builder_controller->addClass($class_regional);
    $builder_controller->setFormId($form_id);
    $builder_controller->addModule($module);
    $builder_controller->addIdentificator($service_regional);
    $builder_controller->addLogicClass($class_regional_logic);
    $success = $builder_controller->buildFiles();
    drupal_set_message($success?t('Operacion realizada con exito'):t('Fallo la operacion'));
  }

  private function validateRegionalInputs(FormStateInterface $form_state) {
    $form_id = $form_state->getValue('formId');
    if ($form_id == '')
      $form_state->setErrorByName('formId', $this->t('Debe un identificador para el formulario.'));
    $module = $form_state->getValue('module');
    if ($module == '')
      $form_state->setErrorByName('module', $this->t('Debe seleccionar un modulo.'));
    $regional_service = $form_state->getValue('service_regional');
    if ( str_replace(' ','', $regional_service) != $regional_service) {
      $form_state->setErrorByName('service_regional', $this->t('El id del servicio no puede contener espacios en blanco.'));
    }
    if ($form_state->getValue('class_regional') == '') {
      $form_state->setErrorByName('class_regional', $this->t('El nombre de la clase no puede ser vacio.'));
    }
    if ($form_state->getValue('class_regional_logic') == '') {
      $form_state->setErrorByName('class_regional_logic', $this->t('El nombre de la clase no puede ser vacio.'));
    }
  }

  private function validateIntegrationInput(array $form, FormStateInterface $form_state) {
    $class_integration = $form_state->getValue('class_integration');
    $module_int = $form['generator_container']['integration']['module_integration']['#options'][$form_state->getValue('module_integration')];

    $exists = $this->file_manager->existClass($module_int,TypeOfFile::FORM,$class_integration.'.php');
    $module_imp = $form_state->getValue('module_imp');
    $class_specific_logic = $form_state->getValue('class_specific_logic');
    if ($module_int == '') {
      $form_state->setErrorByName('module_integration', $this->t('Debe seleccionar un modulo.'));
    }
    if ($class_integration == '' || !$exists) {
      $form_state->setErrorByName('class_integration', $this->t('Debe introducir un nombre valido para la clase.'));
    }
    if ($module_imp == '') {
      $form_state->setErrorByName('module_imp', $this->t('Debe seleccionar un modulo.'));
    }
    if ($class_specific_logic == '') {
      $form_state->setErrorByName('class_specific_logic', $this->t('Debe introducir un nombre para la clase.'));
    }


  }
}
