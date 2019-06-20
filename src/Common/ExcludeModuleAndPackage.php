<?php

namespace Drupal\bits_developer_tool\Common;

class ExcludeModuleAndPackage {
  private $config;
  public function __construct() {
    $this->config = \Drupal::config(FileManager::ID_CONFIG);
  }
  public function getModules() {
    return explode(',',$this->config->get("exclude_module"));
  }
  public function getPackages() {
    return explode(',',$this->config->get("exclude_package"));
  }
}
