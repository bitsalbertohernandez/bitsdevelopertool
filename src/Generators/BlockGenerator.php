<?php

namespace Drupal\bits_developer_tool\Generators;

use Drupal\bits_developer_tool\Common\GenericGenerator;


class BlockGenerator extends GenericGenerator {
  
  /**
   * Blocks class Comments with decorators.
   *
   * @var
   */
  protected $classComments;
  
  /**
   * Add Class Comments to Blocks
   *
   * @param $className
   */
  public function addClassCommentBlock($class_name, $id_block, $admin_label) {
    $this->classComments = [
      "Provides a '" . $class_name . "' block.",
      '@Block(\nid = "'. $id_block .'",\nadmin_label = @Translation("'. $admin_label .'"),)',
    ];
    
    foreach ($this->classComments as $comment) {
      $this->addClassComment($comment);
    }
  }
}
