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

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('only_logic') == 0)
      $this->validateFormRegionalInputs($form_state);
    else {
      $this->validateFormIntegrationInput($form, $form_state);
    }

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('only_logic') == 0)
      $this->generateRegionalClasses($form, $form_state);
    else {
      $this->generateIntegrationClasses($form, $form_state);
    }
  }
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $form['generator_container']['regional']['formId'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Identificador del formulario'),
      '#default_value' => '',
      '#description' => t("El identificador no debe contener espacios no caracteres extraños"),
    ];
    $form['generator_container2']['integration']['form_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase Formulario'),
      '#default_value' => '',
      '#description' => t("Clase formulario que instancia la clase logica seleccionada"),
    ];
    $form['generator_container2']['integration_logic']['submit_method_integration_logic'] = [
      '#type' => 'checkbox',
      '#title' => t('Function submitForm'),
      '#size' => 10,
      '#maxlength' => 255,
      '#default_value' => 1,
      '#description' => '<p>' . t('Se declarara la function en la clase.') . '</p>',
    ];
    $form['generator_container2']['integration_logic']['build_method_integration_logic'] = [
      '#type' => 'checkbox',
      '#title' => t('Function buildForm'),
      '#size' => 10,
      '#maxlength' => 255,
      '#default_value' => 1,
      '#description' => '<p>' . t('Se declarara la function en la clase.') . '</p>',
    ];
    $form['generator_container2']['integration_logic']['validate_method_integration_logic'] = [
      '#type' => 'checkbox',
      '#title' => t('Function validateForm'),
      '#size' => 10,
      '#maxlength' => 255,
      '#default_value' => 1,
      '#description' => '<p>' . t('Se declarara la function en la clase.') . '</p>',
    ];
    return $form;
  }

  private function generateIntegrationClasses(array $form, FormStateInterface $form_state) {
    $methods = [];
    $class_integration = $form_state->getValue('class_integration');
    $module_int = $module = $form['generator_container2']['integration']['module_integration']['#options'][$form_state->getValue('module_integration')];;
    $module_imp = $form['generator_container2']['integration_logic']['module_integration_logic']['#options'][$form_state->getValue('module_integration_logic')];
    $class_specific_logic = $form_state->getValue('class_integration_logic');
    $service_int = $form_state->getValue('service_integration');
    $form_class = $form_state->getValue('form_class');
    if ($form_state->getValue('submit_method_integration_logic') == true)
      $methods[] = 'submitForm';
    if ($form_state->getValue('build_method_integration_logic') == true)
      $methods[] = 'buildForm';
    if ($form_state->getValue('validate_method_integration_logic') == true)
      $methods[] = 'validateForm';
    //ksm($methods);
    $builder_controller = \Drupal::service('bits_developer.int-form.builder');
    $builder_controller->addFormClass($form_class);
    $builder_controller->addLogicClass($class_specific_logic);
    $builder_controller->addModuleInt($module_int);
    $builder_controller->addModuleImpl($module_imp);
    $builder_controller->setIntegrationClass($class_integration);
    $builder_controller->setMethodImpl($methods);
    $success = $builder_controller->buildFiles();
    drupal_set_message($success?t('Operacion realizada con exito'):t('Fallo la operacion'));
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

  private function validateFormRegionalInputs(FormStateInterface $form_state) {
    parent::validateRegionalInputs($form_state);
    $form_id = $form_state->getValue('formId');
    if ($form_id == '')
      $form_state->setErrorByName('formId', $this->t('Debe un identificador para el formulario.'));

  }

  private function validateFormIntegrationInput(array $form, FormStateInterface $form_state) {
    $form_class = $form_state->getValue('form_class');
    if ($form_class == '') {
      $form_state->setErrorByName('form_class', $this->t('Debe introducir el nombre de la clase Formulario.'));
    }
    debug($form_state->getValue('form_class'), 'validateFormIntegrationInput', false);
    parent::validateIntegrationInput($form_state);
  }
}
