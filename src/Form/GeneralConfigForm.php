<?php

namespace Drupal\bits_developer_tool\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class GeneralConfigForm.
 */
class GeneralConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'bits_developer_tool.generalconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'general_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('bits_developer_tool.generalconfig');
    
    // Campos para guadar Controladores.
    $form ['details_controllers'] = [
      '#type' => 'details',
      '#title' => t('Configuraciones de los Controladores'),
      '#open' => TRUE,
    ];

    $form ['details_controllers']['fisic_dir_base_controller'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio Clase Base Controladores'),
      '#maxlength' => 255,
      '#description' => $this->t('Directorio Físico de la Clase Base de los Controladores'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('fisic_dir_base_controller') != null  ? $config->get('fisic_dir_base_controller') : "{modulo}/src/Controller",
    ];
    $form['details_controllers']['namespace_base_controller'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace Clase Base Controladores'),
      '#maxlength' => 255,
      '#description' => $this->t('Namespace de la Clase Base de los Controladores'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('namespace_base_controller') != null  ? $config->get('namespace_base_controller') : "Drupal\{modulo}\Controller",
    ];
    $form['details_controllers']['fisic_dir_logic_controller'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio Clase Lógica Controladores'),
      '#maxlength' => 255,
      '#description' => $this->t('Directorio Físico de la Clase Lógica de los Controladores'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('fisic_dir_logic_controller') != null  ? $config->get('fisic_dir_logic_controller') : "{modulo}/src/Services/Controller",
    ];
    $form['details_controllers']['namespace_logic_controller'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace Clase Lógica Controladores'),
      '#maxlength' => 255,
      '#description' => $this->t('Namespace de la Clase Lógica de los Controladores'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('namespace_logic_controller') != null  ? $config->get('namespace_logic_controller') : "Drupal\{modulo}\Services\Controller",
    ];

    // Campos para guadar Bloques.
    $form ['details_blocks'] = [
      '#type' => 'details',
      '#title' => t('Configuraciones de los Bloques'),
      '#open' => TRUE,
    ];

    $form ['details_blocks']['fisic_dir_base_block'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio Clase Base Bloques'),
      '#maxlength' => 255,
      '#description' => $this->t('Directorio Físico de la Clase Base de los Bloques'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('fisic_dir_base_block') != null ? $config->get('fisic_dir_base_block') : "{modulo}/src/Plugin/Block",
    ];
    $form['details_blocks']['namespace_base_block'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace Clase Base Bloques'),
      '#maxlength' => 255,
      '#description' => $this->t('Namespace de la Clase Base de los Bloques'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('namespace_base_block') != null ? $config->get('namespace_base_block') : "Drupal\{modulo}\Plugin\Block",
    ];
    $form['details_blocks']['fisic_dir_logic_block'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio Clase Lógica Bloques'),
      '#maxlength' => 255,
      '#description' => $this->t('Directorio Físico de la Clase Lógica de los Bloques'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('fisic_dir_logic_block') != null ? $config->get('fisic_dir_logic_block') : "{modulo}/src/Plugin/Config/Block",
    ];
    $form['details_blocks']['namespace_logic_block'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace Clase Lógica Bloques'),
      '#maxlength' => 255,
      '#description' => $this->t('Namespace de la Clase Lógica de los Bloques'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('namespace_logic_block') != null ? $config->get('namespace_logic_block') : "Drupal\{modulo}\Plugin\Config\Block",
    ];

    // Campos para guadar Formularios.
    $form ['details_form'] = [
      '#type' => 'details',
      '#title' => t('Configuraciones de Formularios'),
      '#open' => TRUE,
    ];

    $form ['details_form']['fisic_dir_base_form'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio Clase Base Formularios'),
      '#maxlength' => 255,
      '#description' => $this->t('Directorio Físico de la Clase Base de los Formularios'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('fisic_dir_base_form') != null ? $config->get('fisic_dir_base_form') : "{modulo}/src/Form",
    ];
    $form['details_form']['namespace_base_form'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace Clase Base Formularios'),
      '#maxlength' => 255,
      '#description' => $this->t('Namespace de la Clase Base de los Formularios'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('namespace_base_form') != null ? $config->get('namespace_base_form') : "Drupal\{modulo}\Form",
    ];
    $form['details_form']['fisic_dir_logic_form'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio Clase Lógica Formularios'),
      '#maxlength' => 255,
      '#description' => $this->t('Directorio Físico de la Clase Lógica de los Formularios'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('fisic_dir_logic_form')  != null ? $config->get('fisic_dir_logic_form') : "{modulo}/src/Plugin/Config/Form"
    ];
    $form['details_form']['namespace_logic_form'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace Clase Lógica Formularios'),
      '#maxlength' => 255,
      '#description' => $this->t('Namespace de la Clase Lógica de los Formularios'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('namespace_logic_form')  != null ? $config->get('namespace_logic_form') : "Drupal\{modulo}\Plugin\Config\Block"
    ];

    // Campos para guadar Formularios.
    $form ['details_rest_services'] = [
      '#type' => 'details',
      '#title' => t('Configuraciones de Servicios Rest'),
      '#open' => TRUE,
    ];

    $form ['details_rest_services']['fisic_dir_base_rest'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio Clase Base Servicio REST'),
      '#maxlength' => 255,
      '#description' => $this->t('Directorio Físico de la Clase Base de los Servicios REST'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('fisic_dir_base_rest') != null ? $config->get('fisic_dir_base_rest') : "{modulo}/src/Plugin/rest/resource",
    ];
    $form['details_rest_services']['namespace_base_rest'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace Clase Base Servicio REST'),
      '#maxlength' => 255,
      '#description' => $this->t('Namespace de la Clase Base de los Servicios REST'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('namespace_base_rest') != null ? $config->get('namespace_base_rest') : 'Drupal\{modulo}\Plugin\rest\resource',
    ];
    $form['details_rest_services']['fisic_dir_logic_rest'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directorio Clase Lógica Servicio REST'),
      '#maxlength' => 255,
      '#description' => $this->t('Directorio Físico de la Clase Lógica de los Servicios REST'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('fisic_dir_logic_rest') != null ? $config->get('fisic_dir_logic_rest') : "{modulo}/src/Services/Rest",
    ];
    $form['details_rest_services']['namespace_logic_rest'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace Clase Lógica Servicio REST'),
      '#maxlength' => 255,
      '#description' => $this->t('Namespace de la Clase Lógica de los Servicios REST'),
      '#size' => 60,
      '#require' => TRUE,
      '#default_value' => $config->get('namespace_logic_rest') != null ? $config->get('namespace_logic_rest') : "Drupal\{modulo}\Services\Rest",
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
        
    $this->config('bits_developer_tool.generalconfig')
      ->set('fisic_dir_base_controller', $form_state->getValue('fisic_dir_base_controller'))
      ->set('namespace_base_controller', $form_state->getValue('namespace_base_controller'))
      ->set('fisic_dir_logic_controller', $form_state->getValue('fisic_dir_logic_controller'))
      ->set('namespace_logic_controller', $form_state->getValue('namespace_logic_controller'))
      ->set('fisic_dir_base_block', $form_state->getValue('fisic_dir_base_block'))
      ->set('namespace_base_block', $form_state->getValue('namespace_base_block'))
      ->set('fisic_dir_logic_block', $form_state->getValue('fisic_dir_logic_block'))
      ->set('namespace_logic_block', $form_state->getValue('namespace_logic_block'))
      ->set('fisic_dir_base_form', $form_state->getValue('fisic_dir_base_form'))
      ->set('namespace_base_form', $form_state->getValue('namespace_base_form'))
      ->set('fisic_dir_logic_form', $form_state->getValue('fisic_dir_logic_form'))
      ->set('namespace_logic_form', $form_state->getValue('namespace_logic_form'))
      ->set('fisic_dir_base_rest', $form_state->getValue('fisic_dir_base_rest'))
      ->set('namespace_base_rest', $form_state->getValue('namespace_base_rest'))
      ->set('fisic_dir_logic_rest', $form_state->getValue('fisic_dir_logic_rest'))
      ->set('namespace_logic_rest', $form_state->getValue('namespace_logic_rest'))
      ->save();
  }

}
