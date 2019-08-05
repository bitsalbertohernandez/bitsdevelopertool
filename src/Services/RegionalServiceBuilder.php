<?php

namespace Drupal\bits_developer_tool\Services;

use Drupal\bits_developer_tool\Common\FileManager;
use Drupal\bits_developer_tool\Common\TypeOfFile;
use Drupal\bits_developer_tool\Common\YAMLType;
use Drupal\bits_developer_tool\Common\RegionalUse;
use Drupal\bits_developer_tool\Generators\ServiceGenerator;

class RegionalServiceBuilder {

  private $class;
  private $module;
  private $identificator;
  private $label;
  private $logic_class;
  private $regional_use;
  private $regional_extend;
  private $regional_property = "currentUser";
  private $regional_property_comment = '@var \\';
  private $request_methods;
  /**
   * @var \Drupal\bits_developer_tool\Generators\ServiceGenerator
   */
  private $service_generator;

  /**
   * RegionalBlockBuilder constructor.
   */
  public function __construct() {
    $this->file_manager = \Drupal::service('bits_developer.file.manager');
    $this->service_generator = \Drupal::service('bits_developer.service.generator');
    $this->namespace_path = \Drupal::service('bits_developer.namespace.path');
    $this->request_methods = [];
    $this->regional_use = [
      'Drupal\Core\Session\AccountProxyInterface',
      'Drupal\rest\Plugin\ResourceBase',
      'Symfony\Component\DependencyInjection\ContainerInterface',
      'Psr\Log\LoggerInterface',
    ];


  }

  /**
   * Add Comments to Service Class.
   *
   * @param $class_name
   *  Class Name.
   * @param $id_service
   *  Service id.
   * @param $label
   *  Label class.
   * @param $uri_path
   *  Link for accessing the rest.
   */
  public function addClassComments($id_service, $label, $uri_path) {
    $this->service_generator->addComment($id_service, $label, $uri_path);
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
   * Add Label Function.
   *
   * @param $class
   */
  public function addLabel($label) {
    $this->label = $label;
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
   * Add Identificator Function.
   *
   * @param $identificator
   */
  public function addExtend($class) {
    $this->regional_extend = $class;
  }
  /**
   * Add Logic Class Function.
   *
   * @param $logic_Class
   */
  public function addLogicClass($logic_class) {
    $this->logic_class = $logic_class;
  }

  /**
   * Add Logic Class Function.
   *
   * @param $logic_Class
   */
  public function addRequestMethod($request_method) {
    $this->request_methods = $request_method;
  }

  /**
   * Build Files Function.
   */
  public function buildFiles() {
    if ($this->generateServiceClass(TypeOfFile::SERVICE)) {
      $this->generateYAMLConfig();
      $this->generateServiceLogicClass(TypeOfFile::SERVICE_LOGIC);
    }
  }

  // <editor-fold defaultstate="collapsed" desc="Construct Methods">

  /**
   * Array of  Service comments Construct
   *
   * @return array
   */
  private function constructComments($type) {
    if ($type == TypeOfFile::SERVICE) {
      return [
        "Construct Service Class . \n",
        "@param array " . '$configuration' . " \n A configuration array containing information about the plugin instance.",
        "@param string plugin_id " . " \n The plugin_id for the plugin instance.",
        "@param mixed " . '$plugin_definition' . " \n The plugin implementation definition.",
        "@param array " . '$serializer_formats' . " \n The available serialization formats.",
        "@param " . '\Psr\Log\LoggerInterface $logger' . " \n A logger instance.",
        "@param " . '\Drupal\Core\Session\AccountProxyInterface $current_user' . " \n Logic class of block.",
      ];
    }
    else {
      return [
        "Construct Rest Service Class . \n",
        "@param " . 'mixed $api' . " \n An instance of your apiClient",
      ];
    }

  }

  /**
   * Array of Contruct Arguments
   *
   * @return array
   */
  private function constructArguments($type) {
    if ($type == TypeOfFile::SERVICE) {
      return [
        ["name" => "configuration", "type" => "array"],
        ["name" => "plugin_id"],
        ["name" => "plugin_definition"],
        ["name" => "serializer_formats", "type" => "array"],
        ["name" => "logger", "type" => 'Psr\\Log\\' . 'LoggerInterface'],
        ["name" => "current_user", "type" => 'Drupal\\Core\\Session\\' . 'AccountProxyInterface'],
      ];
    }
    else {
      return [
        ["name" => "tbo_config"],
        ["name" => "api"],
      ];
    }
  }

  /**
   * Generate Body of Contruct Block Base Class.
   *
   * @return string
   */
  private function generateConstructBody($type) {
    if ($type == TypeOfFile::SERVICE) {
      $parent = 'parent::__construct( $configuration, $plugin_id,$plugin_definition,$serializer_formats,$logger);';
      $set_config = '$this->currentUser = $current_user;';
      return $parent . $set_config;
    }
    else {
      $set_config = '// Actualice el parámetro api como instancia de la clase que utiliza'."\n".
        ' //para el consumo de sus endpoints.' . "\n" . '$this->api = $api;';
      return $set_config;
    }
  }
  // </editor-fold>


  /**
   * Generate Array of Comments to generate in create method.
   *
   * @return array
   * Array of Comments to generate in create method.
   */
  private function createMethodComments() {
    return [
      '{@inheritdoc}',
    ];
  }

  /**
   * Generate Array of Arguments to generate in create method.
   *
   * @return array
   * Array of Arguments to generate in create method.
   */
  private function generatecreateMethodArguments() {
    return [
      ["name" => "container", "type" => 'Symfony\\Component\\DependencyInjection\\ContainerInterface'],
      ["name" => "configuration", "type" => "array"],
      ["name" => "plugin_id"],
      ["name" => "plugin_definition"],
    ];
  }


  /**
   * Generate Body to generate in create method.
   *
   * @return string
   * Return a string with body elements.
   */
  private function generateCreateMethodBody() {
    $parent = "return new static(\n " .
      '$configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter(\'serializer.formats\'),
      $container->get(\'logger.factory\')->get(\'' . $this->module . '\'),
      $container->get(\'current_user\')
    );';
    return $parent;
  }

  /**
   * Generate get method body.
   *
   * @return string
   * Return a string with body elements.
   */
  private function generateGetMethodBody($type) {
    if($type == TypeOfFile::SERVICE) {
      $cache = "\\Drupal::service('page_cache_kill_switch')->trigger();";
      $return = " return \\Drupal::service('" . $this->module . "." . $this->identificator . "_rest')->get();";
      return $cache . "\n" . $return;
    }
    else {
      $body= "// You must to implement the logic of your REST Resource here.\n".
        '$response = $this->api->getExampleData();'.
        "\n".'return new ResourceResponse($response);';
      return $body;
    }
  }

  /**
   * Generate post method body.
   *
   * @return string
   * Return a string with body elements.
   */
  private function generatePostMethodBody($type) {
    if($type==TypeOfFile::SERVICE) {
      $data = '$data';
      $cache = "\\Drupal::service('page_cache_kill_switch')->trigger();";
      $return = " return \\Drupal::service('" . $this->module . "." . $this->identificator . "_rest')->post($data);";
      return $cache . "\n" . $return;
    }
    else {
      $body= "// You must to implement the logic of your REST Resource here.\n".
        '$response = $this->api->postExampleData($data);'.
        "\n".'return new ModifiedResourceResponse($response);';
      return $body;
    }
  }

  /**
   * Generate put method body.
   *
   * @return string
   * Return a string with body elements.
   */
  private function generatePutMethodBody($type) {
    if ($type == TypeOfFile::SERVICE) {
      $data = '$data';
      $cache = "\\Drupal::service('page_cache_kill_switch')->trigger();";
      $return = " return \\Drupal::service('" . $this->module . "." . $this->identificator . "_rest')->put($data);";
      return $cache . "\n" . $return;
    }
    else {
      $body = "// You must to implement the logic of your REST Resource here.\n" .
        '$response = $this->api->putExampleData($data);' .
        "\n" . 'return new ModifiedResourceResponse($response);';
      return $body;
    }
  }

  /**
   * Generate delete method body.
   *
   * @return string
   * Return a string with body elements.
   */
  private function generateDeleteMethodBody($type) {
    if($type == TypeOfFile::SERVICE) {
      $id = '$id';
      $cache = "\\Drupal::service('page_cache_kill_switch')->trigger();";
      $return = " return \\Drupal::service('" . $this->module . "." . $this->identificator . "_rest')->delete($id);";
      return $cache . "\n" . $return;
    }
    else {
      $body= "// You must to implement the logic of your REST Resource here.\n".
        '$response = $this->api->deleteExampleData($id);'.
        "\n".'return new ResourceResponse($response);';
      return $body;
    }
  }

  private function generateRequestMethodBody($method_name, $type) {
    switch ($method_name) {
      case 'get':
        return $this->generateGetMethodBody($type);
        break;
      case 'post':
        return $this->generatePostMethodBody($type);
        break;
      case 'put':
        return $this->generatePutMethodBody($type);
        break;
      case 'delete':
        return $this->generateDeleteMethodBody($type);
      default:
        break;
    }
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
   * Create Service Rest Resouuce Class.
   *
   * @param $namespace_logic
   *  Namespace.
   */
  private function createRestResourceBase($namespace_logic, $type) {

    $this->service_generator = new ServiceGenerator();
    $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace($type));
    foreach ($this->regional_use as $use) {
      $this->service_generator->addUse($use);
    }
    // $this->service_generator->addUse($this->regional_use);
    $this->service_generator->addExtend($namespace . "\\" . $this->regional_extend);
    $this->service_generator->addNameSpace($namespace);
    $this->service_generator->addClassProperty($this->regional_property, "", "", FALSE, 'protected');

    // Generating Construct Method code.
    $body_construct = $this->generateConstructBody($type);
    $this->service_generator->addMethod(
      '__construct',
      $body_construct,
      $this->constructComments($type),
      $this->constructArguments($type)
    );

    // Generating Create Method code.
    $this->service_generator->addMethod(
      'create',
      $this->generateCreateMethodBody(),
      $this->createMethodComments(),
      $this->generatecreateMethodArguments()
    );

    // Generating get, post, put, delete Method code.
    foreach ($this->request_methods as $method) {
      // Generating Get Method code.
      if ($method['value'] == 1) {
        $this->service_generator->addMethod(
          $method['name'],
          $this->generateRequestMethodBody($method['name'],$type),
          NULL,
          $this->generateRequestMethodArguments($method['name'])

        );
      }
    }
  }

  private function generateRequestMethodArguments($name) {
    if ($name == 'put' || $name == 'post') {
      return [
        ["name" => "data"],
      ];
    }
    if ($name == 'delete') {
      return [
        ["name" => "id"],
      ];
    }
    return NULL;
  }

  /**
   * Create Service Rest Resource Class.
   *
   * @param $namespace_logic
   *  Namespace.
   */
  private function createRestResourceLogic($namespace_logic, $type) {

    $this->service_generator = new ServiceGenerator();
    $this->service_generator->addUse(' Drupal\rest\ResourceResponse;');
    $this->service_generator->addUse(' Drupal\rest\ModifiedResourceResponse;');
    //$this->service_generator->addExtend($namespace . "\\" . $this->regional_extend);
    $this->service_generator->addNameSpace($namespace_logic);
    $this->service_generator->addClassProperty('api', "", "", FALSE, 'private');

    // Generating Construct Methodcode.
    $body_construct = $this->generateConstructBody($type);
    $this->service_generator->addMethod(
      '__construct',
      $body_construct,
      $this->constructComments($type),
      $this->constructArguments($type)
    );

    // Generating get, post, put, delete Method code.
    foreach ($this->request_methods as $method) {
      // Generating Get Method code.
      if ($method['value'] == 1) {
        $this->service_generator->addMethod(
          $method['name'],
          $this->generateRequestMethodBody($method['name'],$type),
          NULL,
          $this->generateRequestMethodArguments($method['name']));
      }
    }

  }


  /**
   * Generate Path And Code in Base And Logic Class.
   *
   * @param $block_generator
   * @param $class
   *
   * @return array
   */
  private function generatePathAndCode($class, $typeOfFile) {
    $code = $this->service_generator->generateClass($class);
    if ($typeOfFile == TypeOfFile::SERVICE) {
      $path = $this->file_manager->modulePath($this->module) . str_replace(FileManager::PATH_PREFIX, '', $this->namespace_path->getPath(TypeOfFile::SERVICE));
    }
    else {
      $path = $this->file_manager->modulePath($this->module) . str_replace(FileManager::PATH_PREFIX, '', $this->namespace_path->getPathLogic(TypeOfFile::SERVICE));
    }

    //cast($typeOfFile)
    if (!$this->file_manager->pathExist($path)) {
      $this->file_manager->createPath($path);
    }
    $dir_file = $path . '/' . $class . '.php';
    return ['code' => $code, 'dir_file' => $dir_file];
  }

  /**
   * Generate Rest Resource Class Function.
   *
   * @param $type
   *
   * @return bool
   */
  private function generateServiceClass($type) {

    $success = FALSE;
    $namespace_logic = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic($type));
    $dir_file = "";
    $this->createRestResourceBase($namespace_logic, $type);
    $path_code = $this->generatePathAndCode($this->class, $type);
    $code = $path_code['code'];
    $dir_file = $path_code['dir_file'];
    return $this->file_manager->saveFile($dir_file, "<?php \n \n" . $code);
  }

  /**
   * Generate Rest Resource Logic Class Function.
   *
   * @param $type
   *
   * @return bool
   */
  private function generateServiceLogicClass($type) {
    $success = FALSE;
    $namespace_logic = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::SERVICE));
    $code = "";
    $dir_file = "";
    $this->createRestResourceLogic($namespace_logic, $type);
    $path_code = $this->generatePathAndCode($this->logic_class, $type);
    $code = $path_code['code'];
    $dir_file = $path_code['dir_file'];
    return $this->file_manager->saveFile($dir_file, "<?php \n \n" . $code);
  }

  private function generateYAMLConfig() {
    $class = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::SERVICE)) . '\\' . $this->logic_class;
    $data[$this->identificator . '_rest']['class'] = $class;
    $data[$this->identificator . '_rest']['arguments'] = [];
    $yaml_dir = $this->file_manager->getYAMLPath($this->module, YAMLType::SERVICES_FILE);
    return $this->file_manager->saveYAMLConfig($yaml_dir, $data, YAMLType::SERVICES_FILE);

  }
}
