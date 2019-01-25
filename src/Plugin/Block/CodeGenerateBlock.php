<?php

namespace Drupal\bits_developer_tool\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'CodeGenerateBlock' block.
 *
 * @Block(
 *  id = "code_generate_block",
 *  admin_label = @Translation("Generador de código"),
 * )
 */
class CodeGenerateBlock extends BlockBase {
  
  public function blockForm($form, FormStateInterface $form_state) {
    $form ['table_controller'] = [
      '#type' => 'details',
      '#title' => t('Configuraciones del controlador'),
      '#open' => TRUE,
    ];
    $form ['table_controller']['path'] = [
      '#type' => 'textfield',
      '#title' => 'Ruta',
      '#description' => 'Directorio físico de los controladores',
      '#default_value' => isset($this->configuration['controller']['path']) ? $this->configuration['controller']['path'] : '{modulo}/src/Controller',
      '#require' => TRUE,
    ];
    $form ['table_controller']['name_space'] = [
      '#type' => 'textfield',
      '#title' => 'Namespace ',
      '#description' => 'Namespace de los controladores',
      '#default_value' => isset($this->configuration['controller']['name_space']) ? $this->configuration['controller']['name_space'] : 'Drupal\{modulo}\Controller',
      '#require' => TRUE,
    ];
    $form ['table_controller']['path_logic'] = [
      '#type' => 'textfield',
      '#title' => 'Ruta clase lógica',
      '#description' => 'Directorio físico de la lógica del controlador',
      '#default_value' => isset($this->configuration['controller']['path_logic']) ? $this->configuration['controller']['path_logic'] : '{modulo}/src/Services/Controller',
      '#require' => TRUE,
    ];
    $form ['table_controller']['name_space_logic'] = [
      '#type' => 'textfield',
      '#title' => 'Namespace clase lógica ',
      '#description' => 'Namespace de la clase lógica del controlador',
      '#default_value' => isset($this->configuration['controller']['name_space_logic']) ? $this->configuration['controller']['name_space_logic'] : 'Drupal\{modulo}\Services\Controller',
      '#require' => TRUE,
    ];
    return $form;
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function build() {
    // formulario del controlador
    $form_controller = \Drupal::formBuilder()->getForm("\Drupal\bits_developer_tool\Form\ControllerGeneratorForm");
    
    $form_controller['controller_ajax_container']['table_controller']['name_space_controller']['#value'] = $this->configuration['controller']['name_space'];
    $form_controller['controller_ajax_container']['table_controller']['path_controller']['#value'] = $this->configuration['controller']['path'];
    ////
    $form_controller['controller_ajax_container']['table_controller_logic']['name_space_controller_logic']['#value'] = $this->configuration['controller']['name_space_logic'];
    $form_controller['controller_ajax_container']['table_controller_logic']['path_controller_logic']['#value'] = $this->configuration['controller']['path_logic'];
    $form ["controller"] = $form_controller;
    
    // formulario del bloque
    //$form ["block"] = \Drupal::formBuilder()
    //  ->getForm("\Drupal\bits_developer_tool\Form\BlockGeneratorForm");
    $build  ["#form"] = $form;
    $build["#theme"] = "bits_generate_tool";
    $build['#attached'] = [
      'library' => [
        "bits_developer_tool/generate_code",
      ],
    ];
    
    return $build;
  }
  
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['controller']['name_space_logic'] = $form_state->getValue([
      'table_controller',
      'name_space_logic',
    ]);
    $this->configuration['controller']['path_logic'] = $form_state->getValue([
      'table_controller',
      'path_logic',
    ]);
    $this->configuration['controller']['name_space'] = $form_state->getValue([
      'table_controller',
      'name_space',
    ]);
    $this->configuration['controller']['path'] = $form_state->getValue([
      'table_controller',
      'path',
    ]);
  }
  
}
