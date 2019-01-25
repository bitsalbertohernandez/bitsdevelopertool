<?php

namespace Drupal\bits_developer_tool\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class BlockGeneratorForm extends FormBase {
  
  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'block_generator_form_block';
  }
  
  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $form['age'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your age'),
      '#default_value' => '',
    ];
    
    $form['location'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your location'),
      '#default_value' => '',
    ];
    
    $form['actions']['previous'] = [
      '#type' => 'link',
      '#title' => $this->t('Previous'),
      '#attributes' => [
        'class' => ['button'],
      ],
      '#weight' => 0,
      '#url' => '',
    ];
    
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }
}