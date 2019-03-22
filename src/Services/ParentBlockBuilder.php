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
   *
   * @return void
   */
  protected function getPathByType($isBase = TRUE) {
    $real_path = $isBase == TRUE ? $this->namespace_path->getPath(TypeOfFile::BLOCK) : $this->namespace_path->getPathLogic(TypeOfFile::BLOCK);
    $path = $this->file_manager->modulePath($this->module) . str_replace(FileManager::PATH_PREFIX, '', $real_path);
    return $path;
  }

  /**
   * Add Metoths to Generate.
   *
   * @param array $generate_list
   *
   * @return void
   */
  protected function addMethodListToGenerate($generate_list = []) {
    $this->methods = $generate_list;
  }

  /**
   * Generate methods from $this->methods
   *
   * @param [type] $generate_list
   *
   * @return void
   */
  protected function generateMethods(&$block_generator, $is_base = TRUE) {
    for ($i = 0; $i < count(array_keys($this->methods)); $i++) {
      $variable = array_keys($this->methods)[$i];
      switch ($variable) {
        case 'defaultConfiguration':
          if ($this->methods[$variable] == 1) {
            $this->generateDefaultConfiguration($block_generator, $is_base);
          }
          break;
        case 'blockAccess':
          if ($this->methods[$variable] == 1) {
            $this->generateBlockAccess($block_generator, $is_base);
          }
          break;
        case 'blockForm':
          if ($this->methods[$variable] == 1) {
            $this->generateBlockForm($block_generator, $is_base);
          }
          break;
        case 'blockValidate':
          if ($this->methods[$variable] == 1) {
            $this->generateBlockValidate($block_generator, $is_base);
          }
          break;
        case 'blockSubmit':
          if ($this->methods[$variable] == 1) {
            $this->generateBlockSubmit($block_generator, $is_base);
          }
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
   *
   * @return void
   */
  protected function generateDefaultConfiguration(&$block_generator, $is_base) {
    if ($is_base) {
      $body = $this->getBodyDefaultConfiguration();

      $block_generator->addMethod(
        "defaultConfiguration",
        $body,
        ["{@inheritdoc}"],
        []
      );
    }
    else {
      $block_generator->addMethod(
        "defaultConfiguration",
        "return [];",
        ["{@inheritdoc}"],
        []
      );
    }
  }


  /**
   * Generate Build Method(Base And Logic).
   *
   * @param [type] $is_base
   *
   * @return void
   */
  protected function generateBuildMethod(&$block_generator, $is_base) {
    if ($is_base) {
      $body = $this->getBodyBuildMethodBase();

      $block_generator->addMethod(
        "build",
        $body,
        ["{@inheritdoc}"],
        []
      );
    }
    else {
      $body_logic = $this->getBodyBuildMethodLogic();

      $namespace_logic = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::BLOCK));

      $block_generator->addMethod(
        "build",
        $body_logic,
        ["{@inheritdoc}"],
        [
          [
            "name" => $this->logic_instance_property,
            "type" => "$namespace_logic\\$this->class",
            "reference" => TRUE,
          ],
          ["name" => $this->logic_config_property, "reference" => TRUE],
        ]
      );
    }
  }


  /**
   * Generate Body of Build Block Method Logic.
   *
   * @return string
   */
  public function getBodyBuildMethodLogic() {
    $inicial = '$this->' . $this->logic_instance_property . " = &$this->logic_instance_property;\n" .
      '$this->' . str_replace("$", "", $this->configuration_prop)
      . " = &$this->logic_config_property;\n";

    $build = "\n// Here define " . '$build' . " variable with theme and library.\n" . '$build = [];' .
      "\n" . '$this->instance->setValue("build", $build);';
    $other_config = "\n\n// Here define " . '$other_config' . " variable with endpoint and other data.\n" . '$other_config = [];' .
      "\n" . '$config_block = $this->instance->cardBuildConfigBlock("", $other_config);';
    $directive = "\n\n// Here define necesary info to pass to object drupal.js\n" .
      '$this->instance->cardBuildAddConfigDirective($config_block, "");' .
      "\n\n" . 'return $this->' . $this->logic_instance_property . '->getValue("build");';

    return $inicial . $build . $other_config . $directive;
  }


  /**
   * Generate block Access Method (Base and Logic).
   *
   * @param [type] $block_generator
   * @param [type] $is_base
   *
   * @return void
   */
  protected function generateBlockAccess(&$block_generator, $is_base) {
    if ($is_base) {
      $body = $this->getBodyBlockAccessBaseClass();
      $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::BLOCK));
      $block_generator->addUse("Drupal\Core\Session\AccountInterface");

      $block_generator->addMethod(
        "blockAccess",
        $body,
        ["{@inheritdoc}"],
        [
          ["name" => 'account', "type" => "$namespace\\AccountInterface"],
        ]
      );
    }
    else {
      $namespace_logic = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpaceLogic(TypeOfFile::BLOCK));
      $block_generator->addUse("Drupal\Core\Session\AccountInterface");
      $block_generator->addUse("Drupal\Core\Access\AccessResult");
      $body_logic_block_access = $this->getBodyBlockAccessLogicClass();

      $block_generator->addMethod(
        "blockAccess",
        $body_logic_block_access,
        ["{@inheritdoc}"],
        [
          ["name" => 'account', "type" => "$namespace_logic\\AccountInterface"],
        ]
      );
    }

  }


  /**
   * Generate Block Form Method (Base and Logic).
   *
   * @param [type] $block_generator
   * @param [type] $is_base
   *
   * @return void
   */
  protected function generateBlockForm(&$block_generator, $is_base) {
    if ($is_base) {
      $body = $this->getBodyBlockBaseClass('Form');
      $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::BLOCK));
      $block_generator->addUse("Drupal\Core\Form\FormStateInterface");

      $block_generator->addMethod(
        "blockForm",
        $body,
        ["{@inheritdoc}"],
        [
          ["name" => 'form'],
          ["name" => 'form_state', "type" => "$namespace\\FormStateInterface"],
        ]
      );
    }
    else {
      $block_generator->addUse("Drupal\Core\Form\FormStateInterface");
      $body_logic = $this->getBodyBlockFormLogicClass();

      $block_generator->addMethod(
        "blockForm",
        $body_logic,
        ["{@inheritdoc}"],
        [
          ["name" => 'form', "reference" => TRUE],
          ["name" => 'form_state', "reference" => TRUE],
        ]
      );
    }
  }


  /**
   * Generate Block Validate Method (Base and Logic).
   *
   * @param [type] $block_generator
   * @param [type] $is_base
   *
   * @return void
   */
  protected function generateBlockValidate(&$block_generator, $is_base) {
    if ($is_base) {
      $body = $this->getBodyBlockBaseClass('Validate');
      $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::BLOCK));
      $block_generator->addUse("Drupal\Core\Form\FormStateInterface");

      $block_generator->addMethod(
        "blockValidate",
        $body,
        ["{@inheritdoc}"],
        [
          ["name" => 'form'],
          ["name" => 'form_state', "type" => "$namespace\\FormStateInterface"],
        ]
      );
    }
    else {
      $block_generator->addUse("Drupal\Core\Form\FormStateInterface");

      $block_generator->addMethod(
        "blockValidate",
        '// Add validates.',
        ["{@inheritdoc}"],
        [
          ["name" => 'form', "reference" => TRUE],
          ["name" => 'form_state', "reference" => TRUE],
        ]
      );
    }
  }

  /**
   * Generate Block Submit Method (Base and Logic).
   *
   * @param [type] $block_generator
   * @param [type] $is_base
   *
   * @return void
   */
  protected function generateBlockSubmit(&$block_generator, $is_base) {
    if ($is_base) {
      $body = $this->getBodyBlockBaseClassWithConfig('Submit');
      $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::BLOCK));
      $block_generator->addUse("Drupal\Core\Form\FormStateInterface");

      $block_generator->addMethod(
        "blockSubmit",
        $body,
        ["{@inheritdoc}"],
        [
          ["name" => 'form'],
          ["name" => 'form_state', "type" => "$namespace\\FormStateInterface"],
        ]
      );
    }
    else {
      $block_generator->addUse("Drupal\Core\Form\FormStateInterface");

      $block_generator->addMethod(
        "blockSubmit",
        '// Add data to configuration variable.',
        ["{@inheritdoc}"],
        [
          ["name" => 'form', "reference" => TRUE],
          ["name" => 'form_state', "reference" => TRUE],
          ["name" => $this->logic_config_property, "reference" => TRUE],
        ]
      );
    }
  }


  /**
   * Generate Body of block Access Logic Class.
   *
   * @return string
   */
  public function getBodyBlockAccessLogicClass() {
    $anonimus = 'if ($account->isAnonymous()) {' . "\n  return AccessResult::forbidden();\n}";
    $roles = '$roles = $account->getRoles();';
    $compare = "if (in_array('administrator', " . '$roles' . ")) { \n" .
      "  return AccessResult::allowed();\n} \n\nreturn AccessResult::forbidden();";

    return $anonimus . "\n\n" . $roles . "\n\n" . $compare;
  }

  /**
   * Generate Body of Block Form Logic Class.
   *
   * @return string
   */
  public function getBodyBlockFormLogicClass() {
    $return = '$form = $this->' . $this->logic_instance_property . '->cardBlockForm();'
      . "\nreturn " . '$form;';
    return $return;
  }



  // /**
  //  * Array of Create Methods Block Comments
  //  *
  //  * @return array
  // */
  // public function createComments() {
  //     $namespace = str_replace(FileManager::PATH_PREFIX, $this->module, $this->namespace_path->getNameSpace(TypeOfFile::BLOCK));
  //     $this->block_generator->addUse($this->container_interface);
  //     $container = $this->container_interface . ' $container';
  //     return [
  //         "Create Block Class. \n",
  //         "@param $namespace\\$this->container_name \n Block container.",
  //         "@param array $this->configuration_prop \n Block configuration.",
  //         "@param string $this->plugin_id_prop \n Plugin identification.",
  //         "@param mixed $this->plugin_definition_prop \n Plugin definition.",
  //         "\n\n@return static",
  //     ];
  // }

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

    $this->generateBuildMethod($block_generator, FALSE);
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



  /**
   * Create Block Base Class.
   *
   * @param $block_generator
   * @param $namespace_logic
   */
  protected function createBlockBase(&$block_generator, $namespace_logic) {
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
    $create_method = $block_generator->addMethod('create', $bodyCreate, $this->createComments(), $this->createArguments(), 'static');

    // Generate selected methodsfrom Form.
    $this->generateMethods($block_generator);

    // Generate Buil Method.
    $this->generateBuildMethod($block_generator, true);
  }



}
