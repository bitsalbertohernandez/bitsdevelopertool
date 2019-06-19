<?php
namespace Drupal\bits_developer_tool\Common;

use Symfony\Component\Yaml\Yaml;


class Util {

  /**
   * Obtener el listado de módulos.
   *
   * @return array
   *  Retorna un arreglo con los nombres de los módulos.
   */
  public function listModule() {
    $exclude = \Drupal::service('bits_developer.exclude');
    $exclude_package = $exclude->getPackages();
    $exclude_module = $exclude->getModules();
    $custom_modules = [];
    $modules = system_get_info('module');
    foreach ($modules as $key => $module) {
      if (in_array($module['package'], $exclude_package) || in_array( $module['name'], $exclude_module)) {
        continue;
      } else{
        array_push($custom_modules, $key);
      }
    }
    return $custom_modules;
  }
}

