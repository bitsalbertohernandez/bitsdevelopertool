<?php
namespace Drupal\bits_developer_tool\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AutocompleteModuleController extends ControllerBase {

   public function loadModules(Request $request, $count) {

    $result = [];
    $name = $request->query->get('q');
    $modules = \Drupal::service('bits_developer.util.operation')->listModule();
    foreach ( $modules as  $module) {
      if($count < 0) {
        break;
      }
      if(strpos($module, $name) !== FALSE){
        array_push(     $result,
          [
            'value' => $module,
            'label' => $module,
          ]
        );         
      $count--;
      }
    }
    return new JsonResponse( $result);
   }
}
