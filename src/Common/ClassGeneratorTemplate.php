<?php

namespace Drupal\bits_developer_tool\Common;

use Nette\PhpGenerator\Method as MethodGenerator;
use Nette\PhpGenerator\ClassType as ClassGenerator;
use Nette\PhpGenerator\PhpNamespace as NameSpaceGenerator;
use Nette\PhpGenerator\PsrPrinter as PrinterGenerator;

class ClassGeneratorTemplate
{
  protected $namespace;
  protected $method;
  protected $extend;
  protected $implement;
  protected $comment;
  protected $use;
  private $file_manager;

  public function __construct(FileManager $file_manager)
  {
    $this->method = [];
    $this->extend = [];
    $this->comment = [];
    $this->use = [];
    $this->file_manager = $file_manager;
  }

  /**
   * Crear un método.
   *
   * @param string $name
   *  Nombre del método.
   * @param array $comment
   *  Comentarios del método
   * @param array $arg
   *  Argumentos del método. Ejemplo "$arg = ['arg1','arg2','arg3'];" se traduce a "function ejemplo($arg1, $arg2, $arg3)".
   * @param array $arg_default
   *  Argumentos con valores por defecto. Ejemplo "$arg_default['name'] = 'nombre';" se traduce a "function ejemplo ($name = 'nombre')".
   * @return void
   */
  public function addMethod($name, $comment = [], $arg = [], $arg_default = [])
  {
    $method = new MethodGenerator($name);
    foreach ($comment as $element) {
      $method->addComment($element);
    }
    foreach ($arg_default as $key => $value) {
      $method->addParameter($key, $value);
    }
    foreach ($arg as $elemnt) {
      $method->addParameter($elemnt);
    }
    array_push($this->method, $method);
  }

  /**
   * Implementar una interfaz.
   *
   * @param string $interface
   *  Nombre de la clase interfaz.
   * @return void
   */
  public function addImplement($interface)
  {
    $this->implement = $interface;
  }

  /**
   * Extender de una clase.
   *
   * @param string $class
   *  Nombre de la clase a extender.
   * @return void
   */
  public function addExtend($class)
  {
    $this->extend = $class;
  }

  /**
   * Adicionar el namespace de la clase.
   *
   * @param string $module
   *  Nombre del módulo al que pertenece el archivo.
   *
   * @param string $file_type
   *  Tipo de fichero que se va a generar.
   * @return void
   */
  private function addNameSpace($module, $file_type)
  {
    $this->namespace = $this->file_manager->getFileNameSpaceByType($module, $file_type);
    return new NameSpaceGenerator($this->namespace);
  }

  /**
   * Adicionar comentario a la clase.
   *
   * @param string $comment
   *  Comentario.
   * @return void
   */
  public function addClassComment($comment)
  {
    array_push($this->comment, $coment);
  }

  /**
   * Adicionar use a la clase.
   *
   * @param string $use
   *  Use.
   * @return void
   */
  public function addUse($use)
  {
    $this->use = $use;
  }

  /**
   * Generar clase
   *
   * @param string $module
   *  Nombre del módulo al que pertenece el archivo.
   *
   * @param string $file_type
   *  Tipo de fichero que se va a generar.
   *
   * @param string $class
   *  Nombre de la clase.
   * @return string
   *  Clase generada en un string.
   */
  public function generateClass($module, $file_type, $class)
  {

    $namespace = $this->addNameSpace($module, $file_type);

    foreach ($this->use as $element) {
      $namespace->addUse($element);
    }

    $class_generated = $namespace->addClass($class);

    foreach ($this->comment as $comment) {
      $class_generated->addComment($comment);
    }

    if ($this->extend) {
      $class_generated->setExtends($this->extend);
    }

    if ($this->implement) {
      $class_generated->setImplements($this->implement);
    }

    foreach ($this->method as $value) {
      $class_generated->addMethod($value);
    }

    $printer = new PrinterGenerator();

    return $printer->printClass($class_generated);
  }
}
