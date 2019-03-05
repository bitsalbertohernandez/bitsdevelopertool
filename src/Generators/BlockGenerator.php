<?php

namespace Drupal\bits_developer_tool\Generators;

use Drupal\bits_developer_tool\Common\GenericGenerator;
use Nette\PhpGenerator\PhpNamespace as NameSpaceGenerator;
use Nette\PhpGenerator\PhpLiteral as PhpLiteralGenerator;

class BlockGenerator extends GenericGenerator {
  
  /**
   * Blocks class Comments with decorators.
   *
   * @var
   */
  protected $classComments;
  
  /**
   * Add Class Comments to Blocks.
   *
   * @param $class_name
   * @param $id_block
   * @param $admin_label
   */
  public function addClassCommentBlock($class_name, $id_block, $admin_label) {
    $id = 'id ="' . $id_block . '"';
    $label = 'admin_label = @Translation("' . $admin_label . '"),';
    $this->classComments = [
      "Provides a '" . $class_name . "' block.",
      "@Block(\n $id,\n $label \n)",
    ];
    
    foreach ($this->classComments as $comment) {
      $this->addClassComment($comment);
    }
  }
}
