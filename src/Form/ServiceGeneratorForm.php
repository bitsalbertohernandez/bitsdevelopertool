<?php

namespace Drupal\bits_developer_tool\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bits_developer_tool\Common\ClassName;
use Drupal\bits_developer_tool\Common\TypeOfFile;

class ServiceGeneratorForm extends GenericGeneratorForm
{

  /**
   * {@inheritdoc}.
   */
  public function getFormId()
  {
    return 'service_generator_form';
  }
  public function className()
  {
    return ClassName::REST;
  }

  public function typeOfFile()
  {
    return TypeOfFile::SERVICE;
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['generator_container']['regional']['class_regional'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase del servicio rest'),
      '#default_value' => 'DefaultRestResource',
      '#description' => t("Nombre con el que se generará la clase correspondiente al servicio rest."),
      '#required' => true
    ];
    $form['generator_container']['regional']['service_regional'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Identificador del servicio'),
      '#default_value' => '',
      '#description' => t("El identificador no debe contener espacios no caracteres extraños"),
      '#required' => true
    ];

    $form['generator_container']['regional']['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Etiqueta del servicio rest'),
      '#default_value' => 'Default',
      '#description' => t("Etiqueta(Label) del servicio rest."),
      '#required' => true
    ];
    $form['generator_container']['regional']['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Url del servicio rest'),
      '#default_value' => '',
      '#description' => t("Url de acceso al servicio rest"). "\n Ejemplo: /amount \n /amount/{number}",
      '#required' => true
    ];

    $form['generator_container']['regional']['methods'] = [
      '#type' => 'label',
      '#title' => t('Definir Métodos del Servicio Rest '),
    ];

    $form['generator_container']['regional']['service']['GET'] = [
      '#type' => 'checkbox',
      '#title' => 'GET',
      '#default_value' => 1,
    ];
    $form['generator_container']['regional']['service']['POST'] = [
      '#type' => 'checkbox',
      '#title' => 'POST',
      '#default_value' => 0,
    ];
    $form['generator_container']['regional']['service']['PUT'] = [
      '#type' => 'checkbox',
      '#title' => 'PUT',
      '#default_value' => 0,
    ];
    $form['generator_container']['regional']['service']['DELETE'] = [
      '#type' => 'checkbox',
      '#title' => 'DELETE',
      '#default_value' => 0,
    ];

    $form['generator_container']['regional_logic']['class_regional_logic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nombre de la clase'),
      '#default_value' => 'DefaultRestService',
      '#description' => t("Nombre con el que se generará la clase."),
      //'#required' => true
    ];
    $form['generator_container2']['integration']['service_integration'] = [
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    if ($form_state->getValue('only_logic') == 0) {
      $module = $form['module']['#options'][$form_state->getValue('module')];
      $class_regional = $form_state->getValue('class_regional');
      $class_regional_logic = $form_state->getValue('class_regional_logic');
      $builder_service = \Drupal::service('bits_developer.reg-rest-resource.builder');
      $service_id = $form_state->getValue('service_regional');
      $label = $form_state->getValue('label');
      $uri_path =  $form_state->getValue('url');
      $service_options =[];
     // foreach ($)
      array_push($service_options,[
        'name'=> 'get',
        'value' =>$form_state->getValue('GET')]) ;
      array_push($service_options,[
        'name'=> 'post',
        'value' =>$form_state->getValue('POST')]) ;
      array_push($service_options,[
        'name'=> 'put',
        'value' =>$form_state->getValue('PUT')]) ;
      array_push($service_options,[
        'name'=> 'delete',
        'value' =>$form_state->getValue('DELETE')]) ;

      $builder_service->addClassComments($service_id, $label, $uri_path);
      $builder_service->addLabel($label);
      $builder_service->addClass($class_regional);
      $builder_service->addModule($module);
      $builder_service->addIdentificator($service_id);
      $builder_service->addLogicClass($class_regional_logic);
      $builder_service->addRequestMethod($service_options);
      $builder_service->buildFiles();
    }
    else{
      $module = $form['generator_container2']['integration']['module_integration']['#options'][$form_state->getValue('module_integration')];
      $logic_module = $form['generator_container2']['integration_logic']['module_integration_logic']['#options'][$form_state->getValue('module_integration_logic')];

      $class = $form_state->getValue('class_integration');
      $logic_class = $form_state->getValue('class_integration_logic');
      $builder_service = \Drupal::service('bits_developer.int-controller.builder');
      $builder_service->addClass($class);
      $builder_service->addLogicClass($logic_class);
      $builder_service->addModule($module);
      $builder_service->addLogicModule($logic_module);
      $builder_service->buildFiles();
      }
  }
}
