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

  protected $container_name = 'ContainerInterface';

  protected $interface = "Drupal\Core\Plugin\ContainerFactoryPluginInterface";

  protected $interface_name = 'ContainerFactoryPluginInterface';

  protected $logic_instance_property = "instance";

  protected $logic_config_property = "config";

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
   * @var array
   */
  protected $methods;

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
  /**
   * Add or Update Data into YML.
   *
   * @return void
   */
  protected function generateYAMLConfig() {
    $class = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::BLOCK)) . '\\' . $this->logic_Class;
    $data[$this->identificator]['class'] = $class;
    $data[$this->identificator]['arguments'] = [];
    $yaml_dir = $this->file_manager->getYAMLPath($this->module, YAMLType::SERVICES_FILE);
    return $this->file_manager->saveYAMLConfig($yaml_dir, $data, YAMLType::SERVICES_FILE);
  }

  /**
   * Obtain path of Block Base or Block Logic.
   *
   * @param boolean $isBase
   * @return void
   */
  protected function getPathByType($isBase = true){
    $real_path = $isBase == true ? $this->namespace_path->getPath(TypeOfFile::BLOCK) : $this->namespace_path->getPathLogic(TypeOfFile::BLOCK);
    $path = $this->file_manager->modulePath($this->module) . str_replace(FileManager::PATH_PREFIX, '', $real_path);
    return $path;
  }

  /**
   * Add Metoths to Generate.
   *
   * @param array $generate_list
   * @return void
   */
  protected function addMethodListToGenerate($generate_list = []){
    $this->methods = $generate_list;
  }

  /**
   * Generate methods from $this->methods
   *
   * @param [type] $generate_list
   * @return void
   */
  protected function generateMethods(&$block_generator, $is_base = true){
    for ($i=0; $i <count(array_keys($this->methods)); $i++) {
      $variable = array_keys($this->methods)[$i];
      switch ($variable) {
        case 'defaultConfiguration' && $this->methods[$variable] == 1 :
          $this->generateDefaultConfiguration($block_generator, $is_base);
          break;
        default:
          break;
      }
    }
  }

  /**
   * GenerateDefaultConfigurations Methods (Base And Logic).
   *
   * @param [type] $is_base
   * @return void
   */
  protected function generateDefaultConfiguration(&$block_generator, $is_base){
    if($is_base){
      $body = 'if (method_exists($this->'. $this->regional_property .', "defaultConfiguration")) {'
        . "\n" .  '   return $this->'. $this->regional_property .'->defaultConfiguration();'
        . "\n}\n" . 'return parent::defaultConfiguration();' ;
      $block_generator->addMethod(
        "defaultConfiguration",
        $body,
        ["{@inheritdoc}"],
        []
      );
    }
    else{
      $block_generator->addMethod(
        "defaultConfiguration",
        "return [];",
        ["{@inheritdoc}"],
        []
      );
    }
  }

  protected function generateBlockAccess($is_base){
    if($is_base){
      $body = 'if (method_exists($this->'. $this->regional_property .', "defaultConfiguration")) {'
        . "\n" .  '   return $this->'. $this->regional_property .'->defaultConfiguration();'
        . "\n}\n" . 'return parent::defaultConfiguration();' ;
      $this->block_generator->addMethod(
        "defaultConfiguration",
        $body,
        ["{@inheritdoc}"],
        []
      );
    }
    else{
      $this->block_generator->addMethod(
        "defaultConfiguration",
        "$a = 1;",
        ["{@inheritdoc}"],
        []
      );
    }

  }

  /**
   * Array of Create Methods Block Comments
   *
   * @return array
  */
  public function createComments() {
      $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::BLOCK));
      $this->block_generator->addUse($this->container_interface);
      $container = $this->container_interface . ' $container';
      return [
          "Create Block Class. \n",
          "@param $namespace\\$this->container_name \n Block container.",
          "@param array $this->configuration_prop \n Block configuration.",
          "@param string $this->plugin_id_prop \n Plugin identification.",
          "@param mixed $this->plugin_definition_prop \n Plugin definition.",
          "\n\n@return static",
      ];
  }

  /**
   * Create Block Class Logic.
   *
   * @param $block_generator
   * @param $namespace_logic
   */
  public function createBlockClassLogic(&$block_generator, $namespace_logic) {
      $block_generator = new BlockGenerator();
      $block_generator->addNameSpace($namespace_logic);

      // Generate properties of Logic Class.
      $block_generator->addClassProperty($this->logic_instance_property, "", "", FALSE, 'protected');
      $block_generator->addClassProperty($this->logic_config_property, "", "", FALSE, 'protected');

      // Generate methods default setConfig.
      $set_config_body = $this->getSetConfigBody();

      $block_generator->addMethod(
         'setConfig',
          $set_config_body,
          $this->setConfigComments(),
          $this->setConfigArguments($block_generator)
      );

      // Generate selected Methods from Form.
      $this->generateMethods($block_generator, FALSE);
  }


}
