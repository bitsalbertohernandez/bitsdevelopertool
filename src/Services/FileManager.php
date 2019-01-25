<?php
/**
 * Created by PhpStorm.
 * User: Albe
 * Date: 03-Oct-18
 * Time: 8:24 AM
 */

namespace Drupal\bits_developer_tool\Services;


use Drupal\file\Entity\File;

class FileManager {
  
  public function listModule() {
    $list_of_modules = [];
    $custom_modules = [];
    $modules = system_get_info('module');
    foreach ($modules as $key => $module) {
      if (!in_array($module['package'], $list_of_modules)) {
        //$item = [$key => $key];
        array_push($custom_modules, $key);
      }
    }
    return $custom_modules;
  }
  
  public function saveFile($data, $module, $type_class, $file_name) {
    $dir_file = drupal_get_path('module', $module) . $type_class . $file_name;
    $file = File::create([
      'uid' => 1,
      'filename' => 'albe.php',
      'uri' => $dir_file,
      'status' => 1,
    ]);
    $path = dirname($file->getFileUri());
    if (!file_exists($path)) {
      mkdir($path, 0770, TRUE);
    }
    file_put_contents($file->getFileUri(), $data);
  }
  
}