<?php
namespace Drupal\bits_developer_tool\Common;

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

}
