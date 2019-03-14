<?php
namespace Drupal\bits_developer_tool\Services;

use Drupal\bits_developer_tool\Common\FileManager;
use Drupal\bits_developer_tool\Common\TypeOfFile;
use Drupal\bits_developer_tool\Generators\ControllerGenerator;

class IntegrationControllerBuilder extends ControllerBuilder
{
  private $regional_module;
  private $regional_class;
  public function addRegionalModule( $regional_module)
  {
    $this->regional_module = $regional_module;
  }
  public function addRegionalClass( $regional_class)
  {
    $this-> regional_class = $regional_class;
  }
  public function buildFiles()
  {
    $this->generateControllerLogicClass();
  }

  private function generateControllerLogicClass()
  {
    $controller_generator = new ControllerGenerator();
    $code = "";

    $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::CONTROLLER));
    $namespace_regional = str_replace(FileManager::PATH_PREFIX, $this->regional_module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::CONTROLLER));
    $use = $namespace_regional.'\\'.$this->regional_class;
    $controller_generator->addUse($use);
    $controller_generator->addExtend($namespace . "\\" . $this->regional_class);
    $controller_generator->addNameSpace($namespace);

    $code = $controller_generator->generateClass($this->class);
    $path = $this->file_manager->modulePath($this->module) . str_replace(FileManager::PATH_PREFIX, '', $this->namespace_path->getPathLogic(TypeOfFile::CONTROLLER));

    if (!$this->file_manager->pathExist($path)) {
      $this->file_manager->createPath($path);
    }

    $dir_file = $path . '/' . $this->class . '.php';

    return $this->file_manager->saveFile($dir_file, "<?php \n \n" . $code);
  }
}
