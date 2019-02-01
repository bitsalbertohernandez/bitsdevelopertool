<?php

namespace Drupal\bits_developer_tool\Common;


use Drupal\file\Entity\File;
use Symfony\Component\Yaml\Yaml;

class FileManager
{
  /**
   * Salvar fichero en un directorio
   *
   * @param array $data
   *  Datos para poner en el fichero
   * @param string $module
   *  Nombre del módulo donde se generará el archivo
   * @param string $dir_file
   *  Ruta del archivo dentro del módulo
   * @param string $file_name
   *  Nombre del archivo
   * @return void
   */
  public function saveFile(array $data, $module, $dir_file, $file_name)
  {
    $dir_file = drupal_get_path('module', $module) . $dir_file . $file_name;
    $file = File::create([
      'uid' => 1,
      'filename' => $file_name,
      'uri' => $dir_file,
      'status' => 1,
    ]);
    $path = dirname($file->getFileUri());
    if (!file_exists($path)) {
      mkdir($path, 0770, true);
    }
    file_put_contents($file->getFileUri(), $data);
  }
  /**
   * Copiar en un archivo yaml
   *
   * @param string $dir
   *  Directorio del archivo
   * @param array $data
   *  Configuraciones
   * @return void
   */
  public function saveYAML($dir, array $data)
  {
    $yaml = Yaml::dump($data);
    file_put_contents($dir, $yaml);
  }
  /**
   * Obtener datos del YAML
   *
   * @param string $dir
   * @return void
   */
  public function getYAMLData($dir)
  {
    return Yaml::parseFile($dir);
  }

}
