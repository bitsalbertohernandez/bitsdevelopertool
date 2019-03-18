<?php

namespace Drupal\bits_developer_tool\Services;

use Drupal\bits_developer_tool\Generators\FormGenerator;
use Drupal\bits_developer_tool\Common\FileManager;
use Drupal\bits_developer_tool\Common\TypeOfFile;
use Drupal\bits_developer_tool\Common\YAMLType;
use Drupal\bits_developer_tool\Common\RegionalUse;

class RegionalFormBuilder {
  
  private $class;
  
  private $module;
  
  private $identificator;
  
  private $logic_Class;

  private $regional_extend = "FormBase";
  
  private $regional_property = "logic_instance";

  private $form_instance = 'instance';
  
  private $regional_property_comment = '@var \\';

  private $form_id;
  
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
   * Add Class Function.
   *
   * @param $class
   */
  public function addClass($class) {
    $this->class = $class;
  }

  /**
   * Set Form Id.
   *
   * @param $form_id
   */
  public function setFormId($form_id) {
    $this->form_id = $form_id;
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
   * Build Files Function.
   */
  public function buildFiles() {
    if ($this->generateFormClass(TypeOfFile::FORM)) {
      $successYaml = $this->generateYAMLConfig();
      $successClass = $this->generateFormLogicClass(TypeOfFile::FORM_LOGIC);
      return ($successClass && $successYaml);
    }
    return false;
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
   * Array of Construct Form Comments
   *
   * @return array
   */
  private function createInstanceComments($namespace) {
    $configuration_instance = "$" . $this->regional_property;
    return [
      "Create instance. \n",
      "@param \\$namespace\\$this->class $this->form_instance \n  Form Class.",
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
   * Array of buildForm Method Form Comments
   *
   * @return array
   */
  private function functionsFormBaseComments($function, $arguments) {
    $comments = [];
    $comments = ["$function Method Form Class. \n"];
    foreach ($arguments as $param) {
      $type = $param["type"];
      $name = $param["name"];
      array_push($comments, "@param \\$type $name \n");
    }
    return $comments;
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
  private function generateGetFormIdBodyFormBase() {
    return '$this->'."$this->regional_property->getFormId();";
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
   * Create Form Base Class.
   *
   * @param $form_generator
   * @param $namespace_logic
   */
  private function createFormBase(&$form_generator, $namespace_logic) {
    $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::FORM));
    
    $form_generator->addUse('Drupal\Core\Form\FormBase');
    $form_generator->addUse('Drupal\Core\Form\FormStateInterface');
    $form_generator->addExtend($namespace . "\\" . $this->regional_extend);
    $form_generator->addNameSpace($namespace);
    $regional_comment = $this->regional_property_comment.$namespace_logic."\\".$this->logic_Class;
    $form_generator->addClassProperty($this->regional_property, $regional_comment, "", FALSE, 'protected');
    
    // Constructor code.
    $bodyContruct = $this->generateContructFormBaseClassBody();
    $form_generator->addMethod(
      '__construct',
      $bodyContruct,
      $this->constructComments($namespace_logic ."\\". $this->logic_Class),
      $this->constructArguments($this->regional_property, $namespace_logic ."\\". $this->logic_Class)
    );
    $form_generator->addMethod('getFormId', $this->generateGetFormIdBodyFormBase(), $this->getFormIdComments());

    $buildFormArguments = $this->functionArguments('buildForm');
    $form_generator->addMethod('buildForm', $this->generateFunctionFormClassBody('buildForm'), $this->functionsFormBaseComments('buildForm' ,$buildFormArguments),$buildFormArguments );

    $submitFormArgument = $this->functionArguments('submitForm');
    $form_generator->addMethod('submitForm', $this->generateFunctionFormClassBody('submitForm'), $this->functionsFormBaseComments('submitForm', $submitFormArgument), $submitFormArgument);

    $validateFormArgument = $this->functionArguments('validateForm');
    $form_generator->addMethod('validateForm', $this->generateFunctionFormClassBody('validateForm'), $this->functionsFormBaseComments('validateForm', $validateFormArgument), $validateFormArgument);
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
    $form_generator->addUse('Drupal\Core\Form\FormBase');
    $form_generator->addUse('Drupal\Core\Form\FormStateInterface');
    $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::FORM));
    $form_generator->addUse($namespace."\\".$this->class);
    $form_comment = $this->regional_property_comment.$namespace."\\".$this->class;
    $form_generator->addClassProperty($this->form_instance, $form_comment, "", FALSE, 'protected');

    $form_generator->addMethod('createInstance',$this->generateCreateInstanceBodyFormClass('form'),$this->createInstanceComments($namespace),$this->createInstanceArguments($namespace."\\".$this->class));

    $form_generator->addMethod('getFormId',$this->generateGetFormIdBodyLogicClass(),$this->getFormIdComments(),[]);

    $buildFormArguments = $this->functionArguments('buildForm');
    $form_generator->addMethod('buildForm', "", $this->functionsFormBaseComments('buildForm' ,$buildFormArguments), $buildFormArguments);

    $submitFormArguments = $this->functionArguments('submitForm');
    $form_generator->addMethod('submitForm', "", $this->functionsFormBaseComments('submitForm' ,$submitFormArguments), $submitFormArguments);

    $validateFormArguments = $this->functionArguments('validateForm');
    $form_generator->addMethod('validateForm', "", $this->functionsFormBaseComments('validateForm' ,$validateFormArguments), $validateFormArguments);

  }
  
  /**
   * Generate Path And Code in Base Class.
   *
   * @param $form_generator
   * @param $class
   *
   * @return array
   */
  private function generatePathAndCodeBase($form_generator, $class) {
    $code = $form_generator->generateClass($class);
    $path = $this->file_manager->modulePath($this->module) . str_replace(FileManager::PATH_PREFIX, '', $this->namespace_path->getPath(TypeOfFile::FORM));
    if (!$this->file_manager->pathExist($path)) {
      $this->file_manager->createPath($path);
    }
    $dir_file = $path . '/' . $class . '.php';
    return ['code' => $code, 'dir_file' => $dir_file];
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
    $path = $this->file_manager->modulePath($this->module) . str_replace(FileManager::PATH_PREFIX, '', $this->namespace_path->getPathLogic(TypeOfFile::FORM));
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
  private function  generateFormClass($type) {

    $form_generator = $this->form_generator;
    $success = FALSE;
    $namespace_logic = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::FORM));
    $code = "";
    $dir_file = "";
    $dir_module = $this->file_manager->modulePath($this->module, $dir_file);
    $this->createFormBase($form_generator, $namespace_logic);
    $path_code = $this->generatePathAndCodeBase($form_generator, $this->class);
    $code = $path_code['code'];
    $dir_file = $path_code['dir_file'];
    return $this->file_manager->saveFile($dir_file, "<?php \n \n" . $code);
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
    $namespace_logic = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::FORM));
    $code = "";
    $dir_file = "";
    $dir_module = $this->file_manager->modulePath($this->module, $dir_file);
    $this->createFormClassLogic($form_generator, $namespace_logic);
    $path_code = $this->generatePathAndCodeLogicClass($form_generator, $this->logic_Class);
    $code = $path_code['code'];
    $dir_file = $path_code['dir_file'];
    return $this->file_manager->saveFile($dir_file, "<?php \n \n" . $code);
  }
  
  private function generateYAMLConfig() {
    $class = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::FORM)) . '\\' . $this->logic_Class;
    $data[$this->identificator]['class'] = $class;
    $data[$this->identificator]['arguments'] = [];
    $yaml_dir = $this->file_manager->getYAMLPath($this->module, YAMLType::SERVICES_FILE);
    return $this->file_manager->saveYAMLConfig($yaml_dir, $data, YAMLType::SERVICES_FILE);
    
  }
}
