<?php
namespace Drupal\bits_developer_tool\Common;

use Symfony\Component\Yaml\Yaml;


class Util
{
  public function listModule()
  {
    $exclude_package = ['Core', 'Field types', 'Other'];
    $exclude_module = ['bits_developer_tool'];
    $custom_modules = [];
    $modules = system_get_info('module');
    foreach ($modules as $key => $module) {
      if (!in_array($module['package'], $exclude_package) && !in_array($key, $exclude_module)) {
        array_push($custom_modules, $key);
      }
    }
    return $custom_modules;
  }
  /**
   * Saber si existe una clave en el archivo yml
   *
   * @param string $dir
   *  Directorio del archivo
   * @param string $key
   *  Clave a buscar
   * @return void
   */
  public function existKeyInYMLFile($dir, $key)
  {
    $yaml = Yaml::parseFile($dir);
    $array_key = array_keys($yaml);
    return in_array($key, $array_key);
  }
}
