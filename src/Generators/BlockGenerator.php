<?php

namespace Drupal\bits_developer_tool\Generators;

use Drupal\bits_developer_tool\Common\GenericGenerator;
use Nette\PhpGenerator\PhpNamespace as NameSpaceGenerator;
use Nette\PhpGenerator\PhpLiteral as PhpLiteralGenerator;

class BlockGenerator extends GenericGenerator {
  
  /**
   * Blocks class Comments with decorators.
   *
   * @var
   */
  protected $classComments;
  
  /**
   * Add Class Comments to Blocks.
   *
   * @param $class_name
   * @param $id_block
   * @param $admin_label
   */
  public function addClassCommentBlock($class_name, $id_block, $admin_label) {
    $id = 'id ="' . $id_block . '"';
    $label = 'admin_label = @Translation("' . $admin_label . '"),';
    $this->classComments = [
      "Provides a '" . $class_name . "' block.",
      "@Block(\n $id,\n $label \n)",
    ];
    
    foreach ($this->classComments as $comment) {
      $this->addClassComment($comment);
    }
  }
  
  /**
   * Generar clase.
   *
   * @param string $class
   *  Nombre de la clase.
   *
   * @return string
   *  Clase generada en un string.
   */
  public function generateClass($class) {
    $namespace = new NameSpaceGenerator($this->namespace);
    $class_generated = $namespace->addClass($class);
    
    foreach ($this->use as $element) {
      $namespace->addUse($element);
    }
    
    foreach ($this->comment as $comment) {
      $class_generated->addComment($comment);
    }
    
    if ($this->extend) {
      $class_generated->setExtends($this->extend);
    }
    
    if ($this->implement) {
      $class_generated->setImplements([$this->implement]);
    }
    
    foreach ($this->property as $value) {
      if ($value['value'] !== "" && $value['is_literar']) {
        $property = $class_generated->addProperty($value['name'], new PhpLiteralGenerator($value['value']));
      }
      elseif ($value['value'] !== "") {
        $property = $class_generated->addProperty($value['name'], $value['value']);
      }
      else {
        $property = $class_generated->addProperty($value['name']);
      }
      
      if ($value['static']) {
        $property->setStatic();
      }
      $property->setVisibility($value['visibility']);
      if ($value['comment']) {
        $property->addComment($value['comment']);
      }
    }
    
    foreach ($this->method as $value) {
      $method = $class_generated->addMethod($value['name']);
      
      if (isset($value['type'])) {
        if ($value['type'] == 'static') {
          $method->setStatic();
        }
        if ($value['type'] == 'final') {
          $method->setFinal();
        }
      }
      if ($value['body']) {
        $method->setBody($value['body']);
      }
      foreach ($value['comment'] as $comment) {
        $method->addComment($comment);
      }
      foreach ($value['arg'] as $arg) {
        $param = $method->addParameter($arg[0]);
        if (isset($arg[1])) {
          $param->setTypeHint($arg[1]);
        }
      }
    }
    return $namespace;
  }
  
}
