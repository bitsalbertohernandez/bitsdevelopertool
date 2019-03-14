<?php
namespace Drupal\bits_developer_tool\Services;

use Drupal\bits_developer_tool\Genrators\ControllerGenrator;
use Drupal\bits_developer_tool\Common\FileManager;
use Drupal\bits_developer_tool\Generators\ControllerGenerator;
use Drupal\bits_developer_tool\Common\TypeOfFile;

class IntegrationControllerBuilder
{
  private $class;
  private $module;
  private $logic_class;
  private $logic_module;
  /**
   * @var \Drupal\bits_developer_tool\Common\FileManager
   */
  private $file_manager;

  /**
   * @var \Drupal\bits_developer_tool\Generators\ControllerGenerator
   */
  private $controller_generator;

  /**
   * @var \Drupal\bits_developer_tool\Common\NameSpacePathConfig
   */
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

  public function addLogicClass($class)
  {
    $this->logic_class = $class;
  }

  public function addModule($module)
  {
    $this->module = $module;
  }

  public function addLogicModule($module)
  {
    $this->logic_module = $module;
  }
  public function buildFiles()
  {
    $this->generateControllerClass();
  }

  public function generateControllerClass()
  {
    $controller_generator = new ControllerGenerator();
    $namespace_logic = str_replace(FileManager::PATH_PREFIX, $this->logic_module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::CONTROLLER));
    $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::CONTROLLER));
    $code = "";
    $dir_file = "";
    $dir_module = $this->file_manager->modulePath($this->logic_module, $dir_file);
    $controller_generator->addUse($namespace.'\\'.$this->class);
    $controller_generator->addExtend($namespace_logic . "\\" . $this->class);
    $controller_generator->addNameSpace($namespace_logic);
    $code = $controller_generator->generateClass($this->logic_class);

    $path = $this->file_manager->modulePath($this->logic_module) . str_replace(FileManager::PATH_PREFIX, '', $this->namespace_path->getPathLogic(TypeOfFile::CONTROLLER));
    if (!$this->file_manager->pathExist($path)) {
      $this->file_manager->createPath($path);
    }
    $dir_file = $path . '/' . $this->logic_class . '.php';
    return $this->file_manager->saveFile($dir_file, "<?php \n \n" . $code);
  }
}
