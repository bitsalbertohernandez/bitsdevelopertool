<?php

namespace Drupal\bits_developer_tool\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bits_developer_tool\Common\ClassName;
use Drupal\bits_developer_tool\Common\TypeOfFile;

class ControllerGeneratorForm extends GenericGeneratorForm
{

  /**
   * {@inheritdoc}.
   */
  public function getFormId()
  {
    return 'controller_generator_form';
  }
  public function className()
  {
    return ClassName::CONTROLLER;
  }

  public function typeOfFile()
  {
    return TypeOfFile::CONTROLLER;
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

      $success = $builder_controller->buildFiles();
    }

    //$builder_controller->addClass('')
  }
}
