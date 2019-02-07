<?php
namespace Drupal\bits_developer_tool\Services;

use Drupal\bits_developer_tool\Genrators\ControllerGenrator;
use Drupal\bits_developer_tool\Common\FileManager;

class IntegrationControllerBuilder
{
  private $class;
  private $module;
  private $identificator;
  private $logic_Class;
  private $file_manager;
  private $controller_generator;

  public function __construct(ControllerGenerator $controller_generator, FileManager $file_manager)
  {
    $this->file_manager = $file_manager;
    $this->controller_generator = $controller_generator;
  }

  public function setClass($class)
  {
    $this->class = $class;
  }
  public function setModule($module)
  {
    $this->module = $module;
  }
  public function setIdentificator($identificator)
  {
    $this->identificator = $identificator;
  }
  public function setLogicClass($logic_Class)
  {
    $this->logic_Class = $logic_Class;
  }

  public function buildController()
  {

  }
}
