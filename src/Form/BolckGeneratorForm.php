<?php

namespace Drupal\bits_developer_tool\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bits_developer_tool\Common\ClassName;
use Drupal\bits_developer_tool\Common\TypeOfFile;

class BolckGeneratorForm extends GenericGeneratorForm
{

  /**
   * {@inheritdoc}.
   */
  public function getFormId()
  {
    return 'block_generator_form';
  }

  public function className()
  {
    return ClassName::BLOCK;
  }

  public function typeOfFile()
  {
    return TypeOfFile::BLOCK;
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildForm($form, $form_state);
    // A partir de aqui van los campos propios de la clase del block
    // Checbox para selección de métodos a generar.
    $list_keys_generation=[
      'generator_container' => 'regional_logic',
      'generator_container2' => 'integration_logic',
    ];
    $list_option_metod=[
      'defaultConfiguration' => 'Configuración por defecto',
      'blockForm' => 'Fromulario del bloque',
      'blockAccess' => 'Acceso al Bloque',
      'blockValidate' => 'Validar el Bloque',
      'blockSubmit' => 'Salvar el Bloque',
    ];
    foreach ($list_keys_generation as $key => $vars) {
        $form[$key][$vars]['optional_metod'] = [
            '#type' => 'details',
            '#title' => t('Generación de los métodos opcionales'),
        ];
        foreach ($list_option_metod as $key_fc => $vars_fc) {
            $form[$key][$vars]['optional_metod'][$key_fc] = [
                    '#type' => 'checkbox',
                    '#title' => t($vars_fc),
            ];
        }

    }

    return $form;
  }
}
