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
  public function constructComments($namespace) {
    $configuration_instance = "$" . $this->regional_property;
    return [
      "Contructor Block Class. \n",
      "@param array $this->configuration_prop \n Block configuration.",
      "@param string $this->plugin_id_prop \n Block identification.",
      "@param mixed $this->plugin_definition_prop \n Plugin definition.",
      "@param \\$namespace $configuration_instance \n Logic class of block.",
    ];
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
   * Array of Set Config Methods Comments
   *
   * @return array
   */
  public function setConfigComments() {
    $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::BLOCK));
    $container = $this->container_interface . ' $container';
    return [
      "Set Config Method. \n",
      "@param \\$namespace\\$this->class $$this->logic_instance_property",
      "@param $$this->logic_config_property",
      "\n@return void",
    ];
  }

  /**
   * Generate Body of Contruct Block Base Class.
   *
   * @return string
   */
  public function generateContructBlockBaseClassBody() {
    $instance = "// Store our dependency. \n" . '$this->' . $this->regional_property . ' = $' . $this->regional_property . ';';
    $parent = "\n\n// Call parent construct method. \n" . 'parent::__construct(' . $this->configuration_prop . ', ' . $this->plugin_id_prop . ', ' . $this->plugin_definition_prop . ');';
    $set_config = "\n\n// Set init config. \n" . '$this->'. $this->regional_property .'->setConfig($this, $this->configuration);';
    return $instance . $parent . $set_config;
  }

  /**
   * Genetate Body of Create Method Block Base Class.
   *
   * @return string
   */
  public function generateCreateBlockBaseClassBody() {
    $ident = "'$this->identificator'";
    $containter = '$container->get(' . $ident . ')';
    return "return new static(\n  $this->configuration_prop,\n  $this->plugin_id_prop,\n  $this->plugin_definition_prop,\n  $containter\n);";
  }

  /**
   * Array of Create Arguments
   *
   * @return array
   */
  public function createArguments() {

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
  public function constructArguments($config_instance, $config_class, $namespace_logic) {
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
  public function setConfigArguments(&$block_generator) {
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
  public function getSetConfigBody() {
    $instance = '$this->' . $this->logic_instance_property . ' = &$' . $this->logic_instance_property . ";";
    $config = "\n" . '$this->' . $this->logic_config_property . ' = &$' . $this->logic_config_property . ";";
    return $instance . $config;
  }


  /**
   * Generate Body of default configuration method.
   *
   * @return string
   */
  public function getBodyDefaultConfiguration() {
    $body = 'if (method_exists($this->' . $this->regional_property . ', "defaultConfiguration")) {'
      . "\n" . '   return $this->' . $this->regional_property . '->defaultConfiguration();'
      . "\n}\n" . 'return parent::defaultConfiguration();';

    return $body;
  }

  /**
   * Generate Body of block Access Method Base.
   *
   * @return string
   */
  public function getBodyBlockAccessBaseClass() {
    $body = 'if (method_exists($this->' . $this->regional_property . ', "blockAccess")) {'
      . "\n" . '   return $this->' . $this->regional_property . '->blockAccess($account);'
      . "\n}\n" . 'return parent::blockAccess($account);';

    return $body;
  }

  /**
   * Generate Body of Block Form Method Base.
   *
   * @return string
   */
  public function getBodyBlockBaseClass($type) {
    $body = 'if (method_exists($this->' . $this->regional_property . ', "block' . $type . '")) {'
      . "\n" . '   return $this->' . $this->regional_property . '->block' . $type . '($form, $form_state);'
      . "\n}\n" . 'return parent::block' . $type . '($form, $form_state);';

    return $body;
  }

  /**
   * Generate Body of Block Form Method Base.
   *
   * @return string
   */
  public function getBodyBlockBaseClassWithConfig($type) {
    $body = 'if (method_exists($this->' . $this->regional_property . ', "block' . $type . '")) {'
      . "\n" . '   return $this->' . $this->regional_property . '->block' . $type . '($form, $form_state, $this->' . $this->configuration_instance . ');'
      . "\n}\n" . 'return parent::block' . $type . '($form, $form_state);';

    return $body;
  }


  /**
   * Generate Body of Build Block Method Base.
   *
   * @return string
   */
  public function getBodyBuildMethodBase() {
    $body = 'if (method_exists($this->' . $this->regional_property . ', "build")) {'
      . "\n" . '   return $this->' . $this->regional_property . '->build($this, $this->' . $this->configuration_instance . ');'
      . "\n}\n" . 'return parent::build($this, $this->' . $this->configuration_instance . ');';

    return $body;
  }


  /**
   * Generate Path And Code in Base And Logic Class.
   *
   * @param $block_generator
   * @param $class
   *
   * @return array
   */
  public function generatePathAndCode($block_generator, $class, $is_base = TRUE) {
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

}
