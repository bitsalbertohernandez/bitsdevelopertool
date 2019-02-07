<?php
namespace Drupal\bits_developer_tool\Services;

use Drupal\bits_developer_tool\Generators\ControllerGenerator;
use Drupal\bits_developer_tool\Common\FileManager;
use Drupal\bits_developer_tool\Common\TypeOfFile;
use Drupal\bits_developer_tool\Common\NameSpacePathConfig;

class RegionalControllerBuilder
{
  private $class;
  private $module;
  private $identificator;
  private $logic_Class;
  private $file_manager;
  private $controller_generator;
  private $namespace_path;

  public function __construct()
  {
    $this->file_manager = \Drupal::service('bits_developer.file.manager');
    $this->controller_generator = \Drupal::service('bits_developer.controller.generator');
    $this->namespace_path = \Drupal::service('bits_developer.namespace.path');
  }

  public function addClass($class)
  {
    $this->class = $class;
  }
  public function addModule($module)
  {
    $this->module = $module;
  }
  public function addIdentificator($identificator)
  {
    $this->identificator = $identificator;
  }
  public function addLogicClass($logic_Class)
  {
    $this->logic_Class = $logic_Class;
  }

  public function buildController()
  {
    $controller = $this->generateControllerClass(TypeOfFile::CONTROLLER);
    $dir_file = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getPath(TypeOfFile::CONTROLLER));
    $dir_file = $dir_file . '/' . $this->class . '.php';
    $success = $this->file_manager->saveGenerateFile($dir_file, $controller);
    return $success;
  }

  private function generateControllerClass($type)
  {
    $controller_generator = new ControllerGenerator($this->file_manager);
    $code = "";
    if ($type == TypeOfFile::CONTROLLER) {
      $controller_generator->addNameSpace(str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::CONTROLLER)));
      $code = $controller_generator->generateClass($this->class);
    } /*else {

    }*/
    return $code;
  }
}
