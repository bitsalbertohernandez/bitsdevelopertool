<?php
/**
 * Created by PhpStorm.
 * User: Albe
 * Date: 02-Oct-18
 * Time: 9:54 PM
 */

namespace Drupal\bits_developer_tool\Common;


class TypeOfFile {
  
  public static $CONTROLLER = "Controller";
  
  public static $SERVICE = "Service";
  
  public static $FORM = "Form";
  
  public static $REST = "Rest";
  
  public static $CONTROLLER_PATH = "src/Controller/";
  
  public static $SERVICE_PATH = "src/Services/";
  
  public static $FORM_PATH = "src/Form/";
  
  public static $REST_PATH = "src/Plugin/rest/resource/";
  
}