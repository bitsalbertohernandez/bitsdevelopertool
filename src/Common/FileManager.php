<?php
namespace Drupal\bits_developer_tool\Common;

use Drupal\file\Entity\File;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;

class FileManager
{

  private $yaml;
  private const PATH_PREFIX = "{modulo}";
  private const ID_CONFIG = "developer_tool_form_config";

  public function __construct()
  {
    $this->yaml = new SymfonyYaml();
    $this->config = \Drupal::config(FileManager::ID_CONFIG);
  }
  /**
   * Salvar fichero en un directorio.
   *
   * @param string $data
   *  Datos para poner en el fichero.
   * @param string $dir_file
   *  Ruta del archivo dentro del módulo.
   * @return void
   */
  public function saveGenerateFile($dir_file, $data)
  {
    // No se ha probado aun...
    if (!file_exists($dir_file)) {
      mkdir($dir_file, 0770, true);
    }
    return $this->saveFile($dir_file, $data);
  }

  /**
   * Copiar configuraciones en un archivo YAML.
   *
   * @param string $dir
   *  Dirección del archivo YAML.
   * @param string $type
   *  Tipo de archivo yaml.
   * @param array $data
   *  Configuraciones.
   * @return void
   */
  public function saveYAML($dir, $type = YAMLType::INFO_FILE, array $data)
  {
    $data_file = $this->getYAMLData($dir);
    foreach ($data as $key => $value) {
      if ($type == YAMLType::SERVICES_FILE) {
        $data_file['services'][$key] = $value;
      } else {
        $data_file[$key] = $value;
      }
    }
    $yaml_data = $this->yaml->dump($data_file, 2, 2);
    return $this->saveFile($dir, $yaml_data);
  }

  /**
   * Obtener datos del YAML.
   *
   * @param string $dir
   *  Dirección del archivo YAML.
   * @return void
   */
  public function getYAMLData($dir)
  {
    $content = $this->getFileContent($dir);
    return $this->yaml->parse($content);
  }

  /**
   * Saber si existe una clave en el archivo YAML.
   *
   * @param string $dir
   *  Directorio del archivo.
   * @param string $key
   *  Clave a buscar.
   * @return boolean
   */
  public function existKeyInYAMLFile($dir, $key)
  {
    $yaml_content = $this->getYAMLData($dir);
    $array_key = array_keys($yaml_content);
    return in_array($key, $array_key);
  }

  /**
   * Obtener la ruta a un archivo YAML.
   *
   * @param string $module_name
   *  Nombre del módulo.
   * @param string $type_file
   *  Tipo de archivo contenido en la clase YAMLType.
   * @return string
   */
  public function getYAMLPath($module_name, $type_file)
  {
    $module_dir = $this->modulePath($module_name);
    return $module_dir . "/$module_name.$type_file";
  }

  /**
   * Obtener la dirección del módulo.
   *
   * @param string $module_name
   *  Nombre del módulo.
   * @return string
   */
  public function modulePath($module_name)
  {
    return drupal_get_path('module', $module_name);
  }

  /**
   * Obtener el directorio por el tipo de archivo
   *
   * @param string $type
   *  Tipo de archivo(Controlador, Servicio, Bloque, Formulario).
   * @param string $file_name
   *  Nombre del fichero.
   * @param string $module_name
   *  Nombre del módulo.
   * @return string
   */
  public function getFilePath($module_name, $file_name, $type)
  {
    $dir = $this->modulePath($module_name);
    $config_path = $this->config->get($type);
    $config_path = str_replace(FileManager::PATH_PREFIX, "", $config_path);
    return $dir . $config_path . '/' . $file_name;
  }

  /**
   * Obtener el contenido de un archivo
   *
   * @param string $dir
   *  Dirección del archivo.
   * @return string
   */
  public function getFileContent($dir)
  {
    return file_get_contents($dir);
  }

  /**
   * Guardar datos en el fichero.
   *
   * @param string $data
   *  Datos para guardar.
   * @param string $dir_file
   *  Directorio del fichero.
   * @return void
   */
  public function saveFile($dir_file, $data)
  {
    return file_put_contents($dir_file, $data);
  }

}
