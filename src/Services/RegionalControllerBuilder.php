<?php
namespace Drupal\bits_developer_tool\Services;

use Drupal\bits_developer_tool\Common\FileManager;
use Drupal\bits_developer_tool\Common\TypeOfFile;
use Drupal\bits_developer_tool\Common\YAMLType;
use Drupal\bits_developer_tool\Generators\ControllerGenerator;
class RegionalControllerBuilder extends ControllerBuilder {
  private $identificator;
  private $logic_class;
  private $regional_use = "Drupal\Core\Controller\ControllerBase";
  private $regional_extend = "ControllerBase";
  private $regional_property = "configuration_instance";
  private $regional_property_comment = '@var \\';

  public function addIdentificator($identificator) {
    $this->identificator = $identificator;
  }

  public function addLogicClass($logic_class) {
    $this->logic_class = $logic_class;
  }

  public function buildFiles() {
    if ($this->generateControllerClass()) {
      $this->generateYAMLConfig();
      $this->generateControllerLogicClass();
    }
  }

  private function generateControllerClass() {
    $controller_generator = new ControllerGenerator();
    $namespace_logic = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::CONTROLLER));
    $code = "";

    $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::CONTROLLER));

    $controller_generator->addUse($this->regional_use);
    $controller_generator->addExtend($namespace . "\\" . $this->regional_extend);
    $controller_generator->addNameSpace($namespace);
    $controller_generator->addClassProperty($this->regional_property, $this->regional_property_comment . "$namespace_logic\\$this->logic_class", "", false, "protected");
    $controller_generator->addMethod('__construct', '$this->' . $this->regional_property . " = \Drupal::service('" . $this->identificator . "');");

    $code = $controller_generator->generateClass($this->class);
    $path = $this->file_manager->modulePath($this->module) . str_replace(FileManager::PATH_PREFIX, '', $this->namespace_path->getPath(TypeOfFile::CONTROLLER));

    if (!$this->file_manager->pathExist($path)) {
      $this->file_manager->createPath($path);
    }

    $dir_file = $path . '/' . $this->class . '.php';

    return $this->file_manager->saveFile($dir_file, "<?php \n \n" . $code);
  }

  private function generateControllerLogicClass() {
    $controller_generator = new ControllerGenerator();
    $namespace_logic = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::CONTROLLER));

    $controller_generator->addNameSpace($namespace_logic);
    $code = $controller_generator->generateClass($this->logic_class);
    $path = $this->file_manager->modulePath($this->module) . str_replace(FileManager::PATH_PREFIX, '', $this->namespace_path->getPathLogic(TypeOfFile::CONTROLLER));

    if (!$this->file_manager->pathExist($path)) {
      $this->file_manager->createPath($path);
    }

    $dir_file = $path . '/' . $this->logic_class . '.php';

    return $this->file_manager->saveFile($dir_file, "<?php \n \n" . $code);
  }

  private function generateYAMLConfig() {
    $class = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::CONTROLLER)) . '\\' . $this->logic_class;

    $data[$this->identificator]['class'] = $class;
    $data[$this->identificator]['arguments'] = [];

    $yaml_dir = $this->file_manager->getYAMLPath($this->module, YAMLType::SERVICES_FILE);

    return $this->file_manager->saveYAMLConfig($yaml_dir, $data, YAMLType::SERVICES_FILE);
  }
}
