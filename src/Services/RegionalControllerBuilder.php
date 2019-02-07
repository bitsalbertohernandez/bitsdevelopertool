<?php
namespace Drupal\bits_developer_tool\Services;

use Drupal\bits_developer_tool\Generators\ControllerGenerator;
use Drupal\bits_developer_tool\Common\FileManager;
use Drupal\bits_developer_tool\Common\TypeOfFile;
use Drupal\bits_developer_tool\Common\NameSpacePathConfig;
use Drupal\bits_developer_tool\Common\YAMLType;

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

  public function buildFiles()
  {
    if ($this->generateControllerClass(TypeOfFile::CONTROLLER)) {
      $this->generateYAMLConfig();
    }
  }

  private function generateControllerClass($type)
  {
    $controller_generator = new ControllerGenerator($this->file_manager);
    $success = false;
    $code = "";
    if ($type == TypeOfFile::CONTROLLER) {
      $controller_generator->addNameSpace(str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::CONTROLLER)));
      $code = $controller_generator->generateClass($this->class);
      $dir_file = str_replace(FileManager::PATH_PREFIX, '', $this->namespace_path->getPath(TypeOfFile::CONTROLLER)) . '/' . $this->class . '.php';
      $dir_module = $this->file_manager->modulePath($this->module, $dir_file);
      $dir_file = $dir_module . $dir_file;
      $success = $this->file_manager->saveFile($dir_file, $code);
    } /*else {

    }*/
    return $success;
  }
  private function generateYAMLConfig()
  {
    $class = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::CONTROLLER)) . '\\' . $this->class;
    $data[$this->identificator]['class'] = $class;
    $data[$this->identificator]['arguments'] = [];
    $yaml_dir = $this->file_manager->getYAMLPath($this->module, YAMLType::SERVICES_FILE);
    return $this->file_manager->saveYAMLConfig($yaml_dir, $data, YAMLType::SERVICES_FILE);

  }
}
