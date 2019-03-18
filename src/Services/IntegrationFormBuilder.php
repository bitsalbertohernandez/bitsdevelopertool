<?php

namespace Drupal\bits_developer_tool\Services;

use Drupal\bits_developer_tool\Generators\FormGenerator;
use Drupal\bits_developer_tool\Common\FileManager;
use Drupal\bits_developer_tool\Common\TypeOfFile;
use Drupal\bits_developer_tool\Common\YAMLType;
use Drupal\bits_developer_tool\Common\RegionalUse;

class IntegrationFormBuilder {
  
  private $form_Class;
  
  private $module_int;

  private $module_impl;
  
  private $identificator;
  
  private $logic_Class;
  
  private $integration_extend = '';
  
  private $regional_property = "logic_instance";

  private $form_instance = 'instance';
  
  private $regional_property_comment = '@var \\';

  private $form_id;

  private $methodImpl = [];
  
  /**
   * @var \Drupal\bits_developer_tool\Common\FileManager
   */
  private $file_manager;
  
  /**
   * @var \Drupal\bits_developer_tool\Generators\FormGenerator
   */
  private $form_generator;
  
  /**
   * @var \Drupal\bits_developer_tool\Common\NameSpacePathConfig
   */
  private $namespace_path;
  
  /**
   * RegionalFormBuilder constructor.
   */
  public function __construct() {
    $this->file_manager = \Drupal::service('bits_developer.file.manager');
    $this->form_generator = \Drupal::service('bits_developer.form.generator');
    $this->namespace_path = \Drupal::service('bits_developer.namespace.path');
  }
  
  /**
   * Add Class Comments to Forms
   *
   * @param $class_name Class Name.
   * @param $id_form New form id.
   * @param $admin_label Internacionalization Form label.
   */
  public function addClassComments($class_name, $id_form, $admin_label) {
    $this->form_generator->addClassCommentForm($class_name, $id_form, $admin_label);
  }
  
  /**
   * Add Implements to a Class
   *
   */
  public function addImplementToClass() {
    $namespace = str_replace(
      FileManager::PATH_PREFIX, $this->module_int,
      $this->namespace_path->getNameSpace(TypeOfFile::FORM)
    );
    $this->form_generator->addUse($this->interface);
    $this->form_generator->addImplement($namespace . "\\" . $this->interface_name);
  }
  
  /**
   * Add Class Function.
   *
   * @param $class
   */
  public function addFormClass($class) {
    $this->form_Class = $class;
  }

  /**
   * Set Form Id.
   *
   * @param $form_id
   */
  public function setFormId($form_id) {
    $this->form_id = $form_id;
  }

  public function setMethodImpl($method) {
    $this->methodImpl = $method;
  }

  public function setIntegrationClass($integration_class) {
    $this->integration_extend = $integration_class;
  }
  
  /**
   * Add Module Function.
   *
   * @param $module
   */
  public function addModuleInt($module) {
    $this->module_int = $module;
  }

  /**
   * Add Module Function.
   *
   * @param $module
   */
  public function addModuleImpl($module) {
    $this->module_impl = $module;
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
   * Build Files Function.
   */
  public function buildFiles() {
    return $this->generateFormLogicClass(TypeOfFile::FORM_LOGIC);
  }
  
  /**
   * Array of Construct Form Comments
   *
   * @return array
   */
  private function constructComments($namespace) {
    $configuration_instance = "$" . $this->regional_property;
    return [
      "Contructor Form Class. \n",
      "@param \\$namespace $configuration_instance \n Logic class of form.",
    ];
  }

  /**
   * Array of getFormId Method Form Comments
   *
   * @return array
   */
  private function getFormIdComments() {
    return [
      "getFormId Method Form Class. \n",
      "@return string \n identification string of form.",
    ];
  }

  
  /**
   * Generate Body of Contruct Form Base Class.
   *
   * @return string
   */
  private function generateContructFormBaseClassBody() {
    $instance = "// Store our dependency. \n" . '$this->' . $this->regional_property . ' = $' . $this->regional_property.';';
    $set_config = "\n" . '$this->'.$this->regional_property.'->createInstance($this);';
    return $instance  . $set_config;
  }

  /**
   * Generate Body of Function Form Base Class.
   *
   * @return string
   */
  private function generateFunctionFormClassBody($function) {
    $if = "if (method_exists(".'$this->'."$this->regional_property, "."'$function'".")) {\n";
    $body = '    $this->'."$this->regional_property->$function(".'$'."form, ".'$'."form_state); \n}";
    return $if  . $body;
  }

  /**
   * Generate Body of Function createInstance.
   *
   * @return string
   */
  private function generateCreateInstanceBodyFormClass($params) {

    $body = '    $this->'."$this->form_instance".' = &$'."$params;";
    return  $body;
  }

  /**
   * Generate body of method getFormId.
   *
   * @return string
   */
  private function generateGetFormIdBodyLogicClass() {

    return "return '$this->form_id';";
  }
  
  /**
   * Array of Contruct Arguments
   *
   * @return array
   */
  private function constructArguments($config_instance, $config_class) {
    return [
      ["name" => $config_instance, "type" => $config_class],
    ];
  }

  private function createInstanceArguments($formBaseClass) {
    return [
      ["name" => "form", "type" => $formBaseClass, "reference" => true],
    ];
  }
  /**
   * Array of Arguments
   *
   * @return array
   */
  private function functionArguments($function) {
    switch ($function) {
      case 'buildForm' :
        return [
          ["name" => "form", "type" => "array"],
          ["name" => "form_state", "type" => "Drupal\Core\Form\FormStateInterface"],
        ]; break;
      case 'validateForm' :
        return [
          ["name" => "form", "type" => "array", "reference" => true],
          ["name" => "form_state", "type" => "Drupal\Core\Form\FormStateInterface"],
        ]; break;
      case 'submitForm' :
        return [
          ["name" => "form", "type" => "array", "reference" => true],
          ["name" => "form_state", "type" => "Drupal\Core\Form\FormStateInterface"],
        ]; break;
    }
  }

  /**
   * Array of Construct Form Comments
   *
   * @return array
   */
  private function createInstanceComments($namespace) {
    $configuration_instance = "$" . $this->regional_property;
    return [
      "Create instance. \n",
      "@param \\$namespace\\$this->form_Class $this->form_instance \n  Form Class.",
    ];
  }

  private function functionsFormClassComments($function, $arguments) {
    $comments = [];
    $comments = ["$function Method Form Class Logic. \n"];
    foreach ($arguments as $param) {
      $type = $param["type"];
      $name = $param["name"];
      array_push($comments, "@param \\$type $name \n");
    }
    return $comments;
  }
  
  /**
   * Create Form Class Logic.
   *
   * @param $form_generator
   * @param $namespace_logic
   */
  private function createFormClassLogic(&$form_generator, $namespace_logic) {
    $form_generator = new FormGenerator();
    $form_generator->addNameSpace($namespace_logic);
    $namespace_form_class = str_replace(FileManager::PATH_PREFIX, $this->module_int, $this->namespace_path->getNameSpace(TypeOfFile::FORM));
    $form_generator->addUse($namespace_form_class."\\".$this->form_Class);

    $namespace_extend_class = str_replace(FileManager::PATH_PREFIX, $this->module_int, $this->namespace_path->getNameSpaceLogic(TypeOfFile::FORM));
    $form_generator->addUse($namespace_extend_class."\\".$this->integration_extend);
    $form_generator->addUse('Drupal\Core\Form\FormBase');
    $form_generator->addUse('Drupal\Core\Form\FormStateInterface');
    $form_generator->addExtend($namespace_extend_class."\\".$this->integration_extend);

    $form_comment = $this->regional_property_comment.$namespace_form_class."\\".$this->form_Class;
    $form_generator->addClassProperty($this->form_instance, $form_comment, "", FALSE, 'protected' );

    $form_generator->addMethod('createInstance',$this->generateCreateInstanceBodyFormClass('form'),$this->createInstanceComments($namespace_form_class),$this->createInstanceArguments($namespace_form_class."\\".$this->form_Class));
    $this->generateMethodLogicClass($form_generator);
  }

  private function generateMethodLogicClass(&$form_generator) {
    foreach ($this->methodImpl as $method) {
      $arguments = $this->functionArguments($method);
      $form_generator->addMethod($method, "", $this->functionsFormClassComments($method, $arguments),$arguments);
    }
  }

  /**
   * Generate Path And Code in Logic Class.
   *
   * @param $form_generator
   * @param $class
   *
   * @return array
   */
  private function generatePathAndCodeLogicClass($form_generator, $class) {
    $code = $form_generator->generateClass($class);
    $path = $this->file_manager->modulePath($this->module_impl) . str_replace(FileManager::PATH_PREFIX, '', $this->namespace_path->getPathLogic(TypeOfFile::FORM));
    if (!$this->file_manager->pathExist($path)) {
      $this->file_manager->createPath($path);
    }
    $dir_file = $path . '/' . $class . '.php';
    return ['code' => $code, 'dir_file' => $dir_file];
  }

  /**
   * Generate Form Class Function.
   *
   * @param $type
   *
   * @return bool
   */
  private function  generateFormLogicClass($type) {
    $form_generator = $this->form_generator;
    $success = FALSE;
    $namespace_logic = str_replace(FileManager::PATH_PREFIX, $this->module_impl, $this->namespace_path->getNameSpaceLogic(TypeOfFile::FORM));
    $code = "";
    $dir_file = "";
    $dir_module = $this->file_manager->modulePath($this->module_impl, $dir_file);
    $this->createFormClassLogic($form_generator, $namespace_logic);
    $path_code = $this->generatePathAndCodeLogicClass($form_generator, $this->logic_Class);
    $code = $path_code['code'];
    $dir_file = $path_code['dir_file'];
    return $this->file_manager->saveFile($dir_file, "<?php \n \n" . $code);
  }
}
