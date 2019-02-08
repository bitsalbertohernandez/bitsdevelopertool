<?php
namespace Drupal\bits_developer_tool\Common;

use Drupal\file\Entity\File;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;

class FileManager
{

  private $yaml;
  public const PATH_PREFIX = "{modulo}";
  public const ID_CONFIG = "bits_developer_tool.generalconfig";
  private $namespace_path;

  public function __construct()
  {
    $this->yaml = new SymfonyYaml();
    $this->namespace_path = \Drupal::service('bits_developer.namespace.path');
  }

  /**
   * Copiar configuraciones en archivo YAML.
   *
   * @param string $dir
   *  Ruta del archivo YAML.
   * @param string $type
   *  Tipo de archivo yaml.
   * @param array $data
   *  Configuraciones.
   * @return boolean
   *  Retorna true si se salvó la información y false en caso contrario.
   */
  public function saveYAMLConfig($dir, array $data = [], $type = YAMLType::INFO_FILE)
  {
    $level = 2;
    $data_file = $this->getYAMLData($dir);
    foreach ($data as $key => $value) {
      if ($type == YAMLType::SERVICES_FILE) {
        $level = 3;
        $data_file['services'][$key] = $value;
      } else {
        $data_file[$key] = $value;
      }
    }
    $yaml_data = $this->yaml->dump($data_file, $level, 2);
    $yaml_data = str_replace('{ ','[',$yaml_data);
    $yaml_data = str_replace(' }',']',$yaml_data);
    return $this->saveFile($dir, $yaml_data);
  }

  /**
   * Obtener datos del archivo YAML.
   *
   * @param string $dir
   *  Ruta del archivo.
   * @return array
   *  Configuraciones que contine el fichero.
   */
  public function getYAMLData($dir)
  {
    $content = $this->getFileContent($dir);
    return $this->yaml->parse($content);
  }

  /**
   * Saber si existe una clave en la raiz del archivo YAML.
   *
   * @param string $dir
   *  Ruta del archivo.
   * @param string $key
   *  Clave a buscar.
   * @return boolean
   * Retorna true si existe la clave y false en caso contrario.
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
   *  Ruta del archivo.
   *
   */
  public function getYAMLPath($module_name, $type_file)
  {
    $module_dir = $this->modulePath($module_name);
    return $module_dir . "/$module_name.$type_file";
  }

  /**
   * Obtener la ruta del módulo.
   *
   * @param string $module_name
   *  Nombre del módulo.
   * @return string
   *  Ruta del módulo
   */
  public function modulePath($module_name)
  {
    return drupal_get_path('module', $module_name);
  }

  /**
   * Obtenerla ruta por el tipo de archivo.
   *
   * @param string $type
   *  Tipo de archivo(Controlador, Servicio, Bloque, Formulario).
   * @param string $file_name
   *  Nombre del fichero.
   * @param string $module_name
   *  Nombre del módulo.
   * @return string
   *  Ruta del archivo.
   *
   */
  public function getFilePath($module_name, $file_dir)
  {
    // ver como accedo a la configuracion de la ruta(Alejandro)
    $dir = $this->modulePath($module_name);
    $config_path = str_replace(FileManager::PATH_PREFIX, "", $file_dir);
    return $dir . $config_path;
  }


  /**
   * Obtener el contenido de un archivo
   *
   * @param string $dir
   *  Dirección del archivo.
   * @return string
   *  Contenido del archivo.
   */
  public function getFileContent($dir)
  {
    return file_get_contents($dir);
  }

  /**
   * Guardar datos en fichero.
   *
   * @param string $data
   *  Datos para guardar.
   * @param string $dir_file
   *  Ruta del fichero.
   * @return boolean
   *  Retorna true si se salvó la información y false en caso contrario.
   */
  public function saveFile($dir_file, $data)
  {
    return (bool)file_put_contents($dir_file, $data);
  }

  /**
   * Crear un directorio.
   *
   * @param string $path
   *  Ruta del directorio.
   * @return void
   */
  public function createPath($path){
   return mkdir($path, 0770, true);
  }

  /**
   * Saber si existe un directorio.
   *
   * @param string $path
   *  Ruta del directorio.
   * @return void
   */
  public function pathExist($path){
     return file_exists($path);
  }
}
