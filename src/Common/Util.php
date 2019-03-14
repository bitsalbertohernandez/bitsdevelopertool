<?php
namespace Drupal\bits_developer_tool\Common;

use Symfony\Component\Yaml\Yaml;


class Util
{

  /**
   * Obtener el listado de módulos.
   *
   * @return array
   *  Retorna un arreglo con los nombres de los módulos.
   */
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
   * Obtener el listado de módulos de acuerdo al nombre de un paquete.
   *
   * @param string $package
   *
   * @return array
   *  Retorna un arreglo con los nombres de los módulos.
   */
  public function listModuleByPackage($package)
  {
    //$exclude_package = ['Core', 'Field types', 'Other'];
    //$exclude_module = ['bits_developer_tool'];
    $custom_modules = [];
    $modules = system_get_info('module');
    foreach ($modules as $key => $module) {
      if ($module['package'] == $package) {
        array_push($custom_modules, $key);

      }
    }
    return $custom_modules;
  }
}

