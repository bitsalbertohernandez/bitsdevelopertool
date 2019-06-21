<?php

namespace Drupal\bits_developer_tool\Common;


class NameSpacePathConfig {
  private $config;

  public function __construct() {
    $this->config = \Drupal::config(FileManager::ID_CONFIG);
  }

  public function getNameSpace($type) {
    return $this->config->get("namespace_base_$type");
  }

  public function getPath($type) {
    return $this->config->get("fisic_dir_base_$type");
  }

  public function getNameSpaceLogic($type) {
    return $this->config->get("namespace_logic_$type");
  }

  public function getPathLogic($type) {
    return $this->config->get("fisic_dir_logic_$type");
  }
}
