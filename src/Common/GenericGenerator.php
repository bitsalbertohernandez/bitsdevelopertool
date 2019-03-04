<?php

namespace Drupal\bits_developer_tool\Common;

use Nette\PhpGenerator\Method as MethodGenerator;
use Nette\PhpGenerator\ClassType as ClassGenerator;
use Nette\PhpGenerator\PhpNamespace as NameSpaceGenerator;
use Nette\PhpGenerator\PsrPrinter as PrinterGenerator;
use Nette\PhpGenerator\PhpLiteral as PhpLiteralGenerator;

class GenericGenerator {
  
  protected $namespace;
  
  protected $method;
  
  protected $extend;
  
  protected $implement;
  
  protected $comment;
  
  protected $use;
  
  /**
   * @var \Drupal\bits_developer_tool\Common\FileManager
   */
  private $file_manager;
  
  protected $property;
  
  public function __construct() {
    $this->method = [];
    $this->comment = [];
    $this->use = [];
    $this->implement = [];
    $this->property = [];
    $this->file_manager = \Drupal::service('bits_developer.file.manager');
  }
  
  /**
   * Crear un método.
   *
   * @param string $name
   *  Nombre del método.
   * @param string $body
   *  Cuerpo del método.
   * @param array $comment
   *  Comentarios del método
   * @param array $arg
   *  Argumentos del método. Ejemplo "$arg = ['name' => 'nombre', 'type' => 'array','value'=> '[]'];". Se traduce a "function ejemplo(array $nombre = [])".
   *
   * @return void
   *
   * @param string $type
   *  Type of function. Ejemplo: static, final
   *
   * @return void
   */
  public function addMethod($name, $body = "", $comment = [], $arg = [], $type = NULL) {
    $method['name'] = $name;
    $method['body'] = $body;
    $method['comment'] = $comment;
    $method['arg'] = $arg;
    $method['type'] = $type;
    array_push($this->method, $method);
  }
  
  /**
   * Implementar una interfaz.
   *
   * @param string $interface
   *  Nombre de la clase interfaz.
   *
   * @return void
   */
  public function addImplement($interface) {
    array_push($this->implement, $interface);
  }
  
  /**
   * Extender de una clase.
   *
   * @param string $class
   *  Nombre de la clase a extender.
   *
   * @return void
   */
  public function addExtend($class) {
    $this->extend = $class;
  }
  
  /**
   * Adicionar el namespace de la clase.
   *
   * @param string $namespace
   *  Namespace de la clase.
   *
   * @return void
   */
  public function addNameSpace($namespace) {
    $this->namespace = $namespace;
  }
  
  /**
   * Adicionar comentario a la clase.
   *
   * @param string $comment
   *  Comentario.
   *
   * @return void
   */
  public function addClassComment($comment) {
    array_push($this->comment, $comment);
  }
  
  /**
   * Adicionar use a la clase.
   *
   * @param string $use
   *  Use.
   *
   * @return void
   */
  public function addUse($use) {
    array_push($this->use, $use);
  }
  
  /**
   * Agregar atributo a la clase
   *
   * @param string $name
   *  Nombre del atributo.
   * @param string $value
   *  Valor del atributo.
   * @param boolean $is_literar
   *  Si true el valor se asigna tal cual se escribe.
   * @param string $visibility
   *  Tipos de visibilidad (private, protected, public).
   * @param boolean $static
   *  True si el atributo es estático.
   *
   * @return void
   */
  public function addClassProperty($name, $comment = "", $value = "", $is_literar = FALSE, $visibility = 'private', $static = FALSE) {
    $property['name'] = $name;
    $property['value'] = $value;
    $property['visibility'] = $visibility;
    $property['static'] = $static;
    $property['is_literar'] = $is_literar;
    $property['comment'] = $comment;
    array_push($this->property, $property);
  }
  
  
  /**
   * Generate Type Method (Static, Final)
   *
   * @param $type
   * @param $method
   */
  public function generateTypeMethod($type, &$method) {
    if (isset($type)) {
      if ($type == TypeOfMethod::STATIC_METHOD) {
        $method->setStatic();
      }
      if ($type == TypeOfMethod::FINAL_METHOD) {
        $method->setFinal();
      }
    }
  }
  
  /**
   * Generar clase
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
      $class_generated->setImplements($this->implement);
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
      
      $this->generateTypeMethod($value['type'], $method);
      
      if ($value['body']) {
        $method->setBody($value['body']);
      }
      foreach ($value['comment'] as $comment) {
        $method->addComment($comment);
      }
      foreach ($value['arg'] as $arg) {
        $param = $method->addParameter($arg['name']);
        if (isset($arg['type'])) {
          $param->setTypeHint($arg['type']);
        }
        if (isset($arg['value'])) {
          $param->setDefaultValue($arg['value']);
        }
      }
    }
    return $namespace;
  }
}
