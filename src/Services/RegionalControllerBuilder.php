<?php
namespace Drupal\bits_developer_tool\Services;

use Drupal\bits_developer_tool\Generators\ControllerGenerator;
use Drupal\bits_developer_tool\Common\FileManager;
use Drupal\bits_developer_tool\Common\TypeOfFile;
use Drupal\bits_developer_tool\Common\YAMLType;
use Drupal\bits_developer_tool\Common\RegionalUse;

class RegionalControllerBuilder
{
  private $class;
  private $module;
  private $identificator;
  private $logic_Class;
  private $regional_use ="Drupal\Core\Controller\ControllerBase";
  private $regional_extend ="ControllerBase";
  private $regional_property= "configuration_instance";
  private $regional_property_comment= '@var \\';
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
      $this->generateControllerClass(TypeOfFile::CONTROLLER_LOGIC);
    }
  }

  private function generateControllerClass($type)
  {
    $controller_generator = new ControllerGenerator();
    $success = false;
    $namespace_logic= str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::CONTROLLER));
    $code = "";
    $dir_file = "";
    $dir_module = $this->file_manager->modulePath($this->module, $dir_file);
    if ($type == TypeOfFile::CONTROLLER) {
      $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::CONTROLLER));
      $controller_generator->addUse($this->regional_use);
      $controller_generator->addExtend($namespace . "\\".$this->regional_extend);
      $controller_generator->addNameSpace($namespace);
      $controller_generator->addClassProperty($this->regional_property, $this->regional_property_comment."$namespace_logic\\$this->logic_Class");
      $controller_generator->addMethod('__construct','$this->'.$this->regional_property. " = \Drupal::service('".$this->identificator."');");
      $code = $controller_generator->generateClass($this->class);
      $path = $this->file_manager->modulePath($this->module). str_replace(FileManager::PATH_PREFIX, '', $this->namespace_path->getPath(TypeOfFile::CONTROLLER));
      if(!$this->file_manager->pathExist($path)){
        $this->file_manager->createPath($path);
      }
      $dir_file = $path . '/' . $this->class . '.php';
     // $dir_file = $dir_module . $dir_file;
    } else {
      $controller_generator->addNameSpace($namespace_logic);
      $code = $controller_generator->generateClass($this->logic_Class);
      $path = $this->file_manager->modulePath($this->module) . str_replace(FileManager::PATH_PREFIX, '', $this->namespace_path->getPathLogic(TypeOfFile::CONTROLLER));
      if (!$this->file_manager->pathExist($path)) {
        $this->file_manager->createPath($path);
      }
      $dir_file = $path . '/' . $this->logic_Class . '.php';
     // $dir_file = $dir_module . $dir_file;
    }
    return $this->file_manager->saveFile($dir_file, "<?php \n \n" . $code);
  }
  private function generateYAMLConfig()
  {
    $class = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::CONTROLLER)) . '\\' . $this->logic_Class;
    $data[$this->identificator]['class'] = $class;
    $data[$this->identificator]['arguments']=[];
    $yaml_dir = $this->file_manager->getYAMLPath($this->module, YAMLType::SERVICES_FILE);
    return $this->file_manager->saveYAMLConfig($yaml_dir, $data, YAMLType::SERVICES_FILE);

  }
}
