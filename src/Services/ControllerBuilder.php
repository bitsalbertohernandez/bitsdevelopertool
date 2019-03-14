<?php
namespace Drupal\bits_developer_tool\Services;

abstract class ControllerBuilder
{
  protected $class;
  protected $module;
  /**
  * @var \Drupal\bits_developer_tool\Common\FileManager
   */
  protected $file_manager;

  /**
   * @var \Drupal\bits_developer_tool\Generators\ControllerGenerator
   */
  protected $controller_generator;

  /**
   * @var \Drupal\bits_developer_tool\Common\NameSpacePathConfig
   */
  protected $namespace_path;
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
  abstract public function buildFiles();
}
