<?php

namespace Drupal\bits_developer_tool\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bits_developer_tool\Common\ClassName;
use Drupal\bits_developer_tool\Common\TypeOfFile;

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


  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    if ($form_state->getValue('only_logic') == 0) {

      $module = $form['module']['#options'][$form_state->getValue('module')];

      $class_regional = $form_state->getValue('class_regional');

      $class_regional_logic = $form_state->getValue('class_regional_logic');

      $service_regional = $form_state->getValue('service_regional');

      $builder_controller = \Drupal::service('bits_developer.reg-controller.builder');

      $builder_controller->addClass($class_regional);
      $builder_controller->addModule($module);
      $builder_controller->addIdentificator($service_regional);
      $builder_controller->addLogicClass($class_regional_logic);
      $builder_controller->buildFiles();
    }
    else{
      $module = $form['generator_container2']['integration']['module_integration']['#options'][$form_state->getValue('module_integration')];
      $logic_module = $form['generator_container2']['integration_logic']['module_integration_logic']['#options'][$form_state->getValue('module_integration_logic')];

      $class = $form_state->getValue('class_integration');
      $logic_class = $form_state->getValue('class_integration_logic');
      $builder_controller = \Drupal::service('bits_developer.int-controller.builder');
      $builder_controller->addClass($class);
      $builder_controller->addLogicClass($logic_class);
      $builder_controller->addModule($module);
      $builder_controller->addLogicModule($logic_module);
      $builder_controller->buildFiles();
      }
  }
}
