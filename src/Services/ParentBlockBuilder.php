<?php

namespace Drupal\bits_developer_tool\Services;

use Drupal\bits_developer_tool\Generators\BlockGenerator;
use Drupal\bits_developer_tool\Common\FileManager;
use Drupal\bits_developer_tool\Common\TypeOfFile;
use Drupal\bits_developer_tool\Common\YAMLType;

class ParentBlockBuilder {

  protected $class;

  protected $module;

  protected $identificator;

  protected $logic_Class;

  protected $regional_use = 'Drupal\tbo_general\CardBlockBase';

  protected $regional_extend = "CardBlockBase";

  protected $regional_property = "configuration_instance";

  protected $regional_property_comment = '@var \\';

  protected $configuration_prop = '$configuration';

  protected $plugin_id_prop = '$plugin_id';

  protected $plugin_definition_prop = '$plugin_definition';

  protected $container_interface = '\Symfony\Component\DependencyInjection\ContainerInterface';

  protected $interface = "Drupal\Core\Plugin\ContainerFactoryPluginInterface";

  protected $interface_name = 'ContainerFactoryPluginInterface';

  /**
   * @var \Drupal\bits_developer_tool\Common\FileManager
   */
  protected $file_manager;

  /**
   * @var \Drupal\bits_developer_tool\Generators\BlockGenerator
   */
  protected $block_generator;

  /**
   * @var \Drupal\bits_developer_tool\Common\NameSpacePathConfig
   */
  protected $namespace_path;

  /**
   * RegionalBlockBuilder constructor.
   */
  public function __construct() {
    $this->file_manager = \Drupal::service('bits_developer.file.manager');
    $this->block_generator = \Drupal::service('bits_developer.block.generator');
    $this->namespace_path = \Drupal::service('bits_developer.namespace.path');
  }

  /**
   * Add Class Function.
   *
   * @param $class
   */
  public function addClass($class) {
    $this->class = $class;
  }

  /**
   * Add Module Function.
   *
   * @param $module
   */
  public function addModule($module) {
    $this->module = $module;
  }

  /**
   * Add Identificator Function.
   *
   * @param $identificator
   */
  public function addIdentificator($identificator) {
    $this->identificator = $identificator;
  }

  /**
   * Add Logic Class Function.
   *
   * @param $logic_Class
   */
  public function addLogicClass($logic_Class) {
    $this->logic_Class = $logic_Class;
  }

  protected function generateYAMLConfig() {
    $class = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::BLOCK)) . '\\' . $this->logic_Class;
    $data[$this->identificator]['class'] = $class;
    $data[$this->identificator]['arguments'] = [];
    $yaml_dir = $this->file_manager->getYAMLPath($this->module, YAMLType::SERVICES_FILE);
    return $this->file_manager->saveYAMLConfig($yaml_dir, $data, YAMLType::SERVICES_FILE);
  }

  protected function getPathByType($isBase = true){
    $real_path = $isBase == true ? $this->namespace_path->getPath(TypeOfFile::BLOCK) : $this->namespace_path->getPathLogic(TypeOfFile::BLOCK);
    $path = $this->file_manager->modulePath($this->module) . str_replace(FileManager::PATH_PREFIX, '', $real_path);
    return $path;
  }
}
