<?php

namespace Drupal\bits_developer_tool\Generators;

use Drupal\bits_developer_tool\Common\GenericGenerator;


class ServiceGenerator extends GenericGenerator
{
  /**
   * Blocks class Comments with decorators.
   *
   * @var
   */
  protected $classComments;

  /**
   * Add comments to the service class .
   *
   * @param $class_name
   *  Class name.
   * @param $id_service
   *  Service id.
   * @param $label
   *  Label.
   * @param uri_path
   * Access link.
   *
   */
  public function addComment($id_service, $label, $uri_path) {
    $id = 'id ="' . $id_service . '"';
    $label = 'label = @Translation("' . $label . '"),';
    $uri_path = '{"canonical" ='. $uri_path .'"}';
    $this->classComments = [
      'Provides a resource to get view modes by entity and bundle.',
      "@RestResource(\n $id,\n $label \n $uri_path \n)",
    ];

    foreach ($this->classComments as $comment) {
      $this->addClassComment($comment);
    }
  }
}
