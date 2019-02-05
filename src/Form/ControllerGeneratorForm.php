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
}
