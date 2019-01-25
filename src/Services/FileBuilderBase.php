<?php

namespace Drupal\bits_developer_tool\Services;

use Nette\PhpGenerator\PhpFile;

class FileBuilderBase {
  
  protected $class;
  
  protected $type_of_file;
  
  protected $class_name;
  
  protected $name_space;
  
  protected $module_class_used;
  
  protected $class_used;
  
  private $file_builder;
  
  public function __construct(PhpFile $file_builder) {
    $this->file_builder = $file_builder;
  }
  
  public function buildClass($constain_use) {
    $current_name_space = $this->file_builder->addNamespace($this->name_space);
    if ($constain_use) {
      $use = "Drupal\\" + $this->module + "\\" + $this->type_of_file + \\ + $this->class_used;
      $current_name_space->addUse($use);
    }
    
    $this->class = $current_name_space->addClass($this->class_name);
  }
  
  public function saveClass($path){
    //todo: averiguar como guardar los ficheros.
  }
  
}