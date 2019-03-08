<?php

namespace Drupal\bits_developer_tool\Form;


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
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $this->initConfigVar();

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
    $form['generator_container']['regional']['service_regional'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Identificador del servicio'),
      '#default_value' => '',
      '#description' => t("El identificador no debe contener espacios no caracteres extraños"),
      //'#required' => true
    ];
    $form['generator_container']['regional']['class_regional'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase'),
      '#default_value' => '',
      '#description' => t("Nombre con el que se generará la clase."),
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

  public function validateForm(array &$form, FormStateInterface $form_state) {
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

  public function submitForm(array &$form, FormStateInterface $form_state) {

  }
}
