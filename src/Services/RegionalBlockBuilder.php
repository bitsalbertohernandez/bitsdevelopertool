<?php

namespace Drupal\bits_developer_tool\Services;

use Drupal\bits_developer_tool\Generators\BlockGenerator;
use Drupal\bits_developer_tool\Common\FileManager;
use Drupal\bits_developer_tool\Common\TypeOfFile;
use Drupal\bits_developer_tool\Common\YAMLType;
use Drupal\bits_developer_tool\Services\ParentBlockBuilder;

class RegionalBlockBuilder extends ParentBlockBuilder {
  
  /**
   * Add Class Comments to Blocks
   *
   * @param $class_name Class Name.
   * @param $id_block New block id.
   * @param $admin_label Internacionalization Block label.
   */
  public function addClassComments($class_name, $id_block, $admin_label) {
    $this->block_generator->addClassCommentBlock($class_name, $id_block, $admin_label);
  }
  
  /**
   * Add Implements to a Class
   *
   */
  public function addImplementToClass() {
    $namespace = str_replace(
      FileManager::PATH_PREFIX, $this->module,
      $this->namespace_path->getNameSpace(TypeOfFile::BLOCK)
    );
    
    $this->block_generator->addUse($this->interface);
    $this->block_generator->addImplement($namespace . "\\" . $this->interface_name);
  }
  
  
  /**
   * Array of Construct Block Comments
   *
   * @return array
   */
  private function constructComments($namespace) {
    $configuration_instance = "$" . $this->regional_property;
    return [
      "Contructor Block Class. \n",
      "@param array $this->configuration_prop \n Block configuration.",
      "@param string $this->plugin_id_prop \n Block identification.",
      "@param mixed $this->plugin_definition_prop \n Plugin definition.",
      "@param $namespace $configuration_instance \n Logic class of block.",
    ];
  }
  
//  /**
//   * Array of Create Methods Block Comments
//   *
//   * @return array
//   */
//  private function createComments() {
//    $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::BLOCK));
//    $this->block_generator->addUse($this->container_interface);
//    $container = $this->container_interface . ' $container';
//    return [
//      "Create Block Class. \n",
//      "@param $namespace\\$this->container_name \n Block container.",
//      "@param array $this->configuration_prop \n Block configuration.",
//      "@param string $this->plugin_id_prop \n Plugin identification.",
//      "@param mixed $this->plugin_definition_prop \n Plugin definition.",
//      "\n\n@return static",
//    ];
//  }
  
  
  /**
   * Array of Set Config Methods Comments
   *
   * @return array
   */
  private function setConfigComments() {
    $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::BLOCK));
    $container = $this->container_interface . ' $container';
    return [
      "Set Config Method. \n",
      "@param $namespace\\$this->class $$this->logic_instance_property",
      "@param $$this->logic_config_property",
      "\n@return void",
    ];
  }
  
  /**
   * Generate Body of Contruct Block Base Class.
   *
   * @return string
   */
  private function generateContructBlockBaseClassBody() {
    $instance = "// Store our dependency. \n" . '$this->' . $this->regional_property . ' = $' . $this->regional_property;
    $parent = "\n\n// Call parent construct method. \n" . 'parent::__construct(' . $this->configuration_prop . ', ' . $this->plugin_id_prop . ', ' . $this->plugin_definition_prop . ');';
    $set_config = "\n\n// Set init config. \n" . '$this->configurationInstance->setConfig($this, $this->configuration);';
    return $instance . $parent . $set_config;
  }
  
  /**
   * Genetate Body of Create Method Block Base Class.
   *
   * @return string
   */
  private function generateCreateBlockBaseClassBody() {
    $ident = "'$this->identificator'";
    $containter = '$container->get(' . $ident . ')';
    return "return new static(\n  $this->configuration_prop,\n  $this->plugin_id_prop,\n  $this->plugin_definition_prop,\n  $containter\n);";
  }
  
  /**
   * Array of Create Arguments
   *
   * @return array
   */
  private function createArguments() {
    
    return [
      ["name" => "container", "type" => $this->container_interface],
      ["name" => "configuration", "type" => "array"],
      ["name" => "plugin_id"],
      ["name" => "plugin_definition"],
    ];
  }
  
  /**
   * Array of Contruct Arguments
   *
   * @return array
   */
  private function constructArguments($config_instance, $config_class, $namespace_logic) {
    $this->block_generator->addUse($namespace_logic . "\\" . $this->logic_Class);
    return [
      ["name" => "configuration", "type" => "array"],
      ["name" => "plugin_id"],
      ["name" => "plugin_definition"],
      ["name" => $config_instance, "type" => $config_class],
    ];
  }
  
  /**
   * Array of set config Arguments
   *
   * @return array
   */
  private function setConfigArguments(&$block_generator) {
    $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::BLOCK));
    $block_generator->addUse($namespace . "\\" . $this->class);
    
    return [
      [
        "name" => $this->logic_instance_property,
        "type" => $namespace . "\\" . $this->class,
        "reference" => TRUE,
      ],
      [
        "name" => $this->logic_config_property,
        "type" => "",
        "reference" => TRUE,
      ],
    ];
  }
  
  
  /**
   * Generate Body of setConfig Block Logic Class.
   *
   * @return string
   */
  private function getSetConfigBody() {
    $instance = '$this->' . $this->logic_instance_property . ' = &$' . $this->logic_instance_property . ";";
    $config = "\n" . '$this->' . $this->logic_config_property . ' = &$' . $this->logic_config_property . ";";
    return $instance . $config;
  }
  
  /**
   * Create Block Base Class.
   *
   * @param $block_generator
   * @param $namespace_logic
   */
  private function createBlockBase(&$block_generator, $namespace_logic) {
    $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::BLOCK));
    
    $block_generator->addUse($this->regional_use);
    $block_generator->addExtend($namespace . "\\" . $this->regional_extend);
    $block_generator->addNameSpace($namespace);
    $block_generator->addClassProperty($this->regional_property, $this->regional_property_comment . "$namespace_logic\\$this->logic_Class", "", FALSE, 'protected');
    
    // Create Contruct Method.
    $bodyContruct = $this->generateContructBlockBaseClassBody();
    $block_generator->addMethod(
      '__construct',
      $bodyContruct,
      $this->constructComments($namespace_logic . "\\" . $this->logic_Class),
      $this->constructArguments($this->regional_property, $namespace . "\\" . $this->logic_Class, $namespace_logic)
    );
    
    // Create method code.
    $bodyCreate = $this->generateCreateBlockBaseClassBody();
    $create_method = $block_generator->addMethod('create', $bodyCreate, parent::createComments(), $this->createArguments(), 'static');
    
    // Generate selected methodsfrom Form.
    $this->generateMethods($block_generator);
  }
  
  /**
   * Generate Path And Code in Base And Logic Class.
   *
   * @param $block_generator
   * @param $class
   *
   * @return array
   */
  private function generatePathAndCode($block_generator, $class, $is_base = TRUE) {
    $code = $block_generator->generateClass($class);
    $path = $this->getPathByType($is_base);
    if (!$this->file_manager->pathExist($path)) {
      $this->file_manager->createPath($path);
    }
    $dir_file = $path . '/' . $class . '.php';
    return ['code' => $code, 'dir_file' => $dir_file];
  }
  
  /**
   * Add Generate Method Into Block Base and Logic Block Base.
   *
   * @param $generate_list
   *
   * @return void
   */
  public function addMethodList($generate_list = []) {
    $this->addMethodListToGenerate($generate_list);
  }
  
  /**
   * Build Files Function.
   */
  public function buildFiles() {
    if ($this->generateBlockClass(TypeOfFile::BLOCK)) {
      $this->generateYAMLConfig();
      $this->generateBlockClass(TypeOfFile::BLOCK_LOGIC);
    }
  }
  
  /**
   * Generate Block Class Function.
   *
   * @param $type
   *
   * @return bool
   */
  protected function generateBlockClass($type) {
    
    $block_generator = $this->block_generator;
    $success = FALSE;
    $namespace_logic = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::BLOCK));
    $code = "";
    $dir_file = "";
    $dir_module = $this->file_manager->modulePath($this->module, $dir_file);
    if ($type == TypeOfFile::BLOCK) {
      $this->createBlockBase($block_generator, $namespace_logic);
      $path_code = $this->generatePathAndCode($block_generator, $this->class);
      $code = $path_code['code'];
      $dir_file = $path_code['dir_file'];
    }
    else {
      $this->createBlockClassLogic($block_generator, $namespace_logic);
      $path_code = $this->generatePathAndCode($block_generator, $this->logic_Class, FALSE);
      $code = $path_code['code'];
      $dir_file = $path_code['dir_file'];
    }
    
    
    return $this->file_manager->saveFile($dir_file, "<?php \n \n" . $code);
  }
}
