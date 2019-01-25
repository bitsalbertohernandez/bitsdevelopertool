<?php
/**
 * Created by PhpStorm.
 * User: Albe
 * Date: 02-Oct-18
 * Time: 9:09 PM
 */

namespace Drupal\bits_developer_tool\Services;


use Nette\PhpGenerator\PhpFile;

class ControllerBuilder extends FileBuilderBase {
  
  public function __construct(PhpFile $file_builder) {
    parent::__construct($file_builder);
    $this->type_of_file = TypeOfFile::$CONTROLLER;
  }
  
  public function addContructor($logic_class) {
    $constructor = $this->class->addMethod('__construct');
    $constructor->addParameter('controller_logic', [])
      ->setTypeHint($logic_class);
  }
  
}