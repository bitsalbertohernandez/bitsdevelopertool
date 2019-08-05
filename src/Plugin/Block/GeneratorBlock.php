<?php
namespace Drupal\bits_developer_tool\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'GeneratorBlock' block.
 *
 * @Block(
 *  id = "generator_tab_block",
 *  admin_label = @Translation("Generador de código"),
 * )
 */
class GeneratorBlock extends BlockBase{

  public function build() {
    return [
      '#theme' => 'generator_tab',
      '#attached' => [
        'library' => [
          'bits_developer_tool/generate_code'
      ]
    ],
      '#forms' => $this->forms(),
    ];
  }

  private function forms() {
     $form_data = [];
     $form_config_data = $this->getFormData();
     foreach ( $form_config_data as $key => $value) {
       array_push($form_data, [
         'title' => $value['title'],
         'id'=> $key,
         'form' => \Drupal::formBuilder()->getForm($value['path_form']),
       ]);
     }
     return $form_data;
  }

  private function getFormData() {
    return [
      'controller'=>[
        'title' => 'Controlador',
        'path_form' => \Drupal\bits_developer_tool\Form\ControllerGeneratorForm::class
      ],
      'block'=>[
        'title' => 'Bloque',
        'path_form' => \Drupal\bits_developer_tool\Form\BlockGeneratorForm::class
      ],
      'rest'=>[
        'title' => 'Servicio Rest',
        'path_form' => \Drupal\bits_developer_tool\Form\ServiceGeneratorForm::class
      ],
      'form'=>[
        'title' => 'Formulario',
        'path_form' => \Drupal\bits_developer_tool\Form\FormGeneratorForm::class
      ],
    ];
  }
}
