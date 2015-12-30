<?php

/*
 * Bootstrap for Elevenfox MVC
 * @Author: Elevenfox
 */

define('APP_PATH',        rtrim(dirname(__FILE__), '/\\') . DIRECTORY_SEPARATOR);
define('CONFIG_PATH',     APP_PATH.'conf' . DIRECTORY_SEPARATOR);
define('CLASS_PATH',      APP_PATH.'class' . DIRECTORY_SEPARATOR);
define('CORE_PATH',       APP_PATH.'class/core' . DIRECTORY_SEPARATOR);
define('MODEL_PATH',      APP_PATH.'class/model' . DIRECTORY_SEPARATOR);
define('CONTROLLER_PATH', APP_PATH.'class/controller' . DIRECTORY_SEPARATOR);
define('VIEW_PATH',       APP_PATH.'class/view' . DIRECTORY_SEPARATOR);
define('DOC_ROOT',     APP_PATH.'docRoot' . DIRECTORY_SEPARATOR);
define('THEME_PATH',   DOC_ROOT.'theme' . DIRECTORY_SEPARATOR);

// Will turn on display and set to E_ALL here, then we can set this in settings file
ini_set("display_errors", 1);
error_reporting(E_ALL);

// Load some class
import('CommonFuns');
import('ZDebug');

// Load all settings
import("Config");
Config::load();

define('FILE_ROOT', Config::get('file_root') . DIRECTORY_SEPARATOR);

// set time zone
$timeZone = Config::get('time_zone');  
$timeZone = empty($timeZone) ? 'America/Los_Angeles' : $timeZone;
date_default_timezone_set($timeZone);


// Load all core classes
import("core.*"); 

// Load all menus from all controllers
Menu::genMenus();

// Load all blocks from all models
Block::genBlocks();

// Load DB instance
if (!isset(DB::$dbInstance)) {
  $dbDriver = Config::get('db_driver');
  if (!in_array($dbDriver, array('foxMysqli', 'foxMysql'))) {
    exit($dbDriver . ": The DB driver does not exist");
  }
  import('core.DB', $dbDriver);
  $db = new $dbDriver( 
              Config::get('db_main_host'),
              Config::get('db_main_port'),
              Config::get('db_main_user'),
              Config::get('db_main_pass'),
              Config::get('db_main_dbname')	
		);
  DB::$dbInstance = $db;
}

// init image cache
imageCache::init();

///////////////////////////////////////////////////
/*
 * Import class function, similar to java
 *  - import("d");              //import class d in classPath root
 *  - import("fox.test",'*');   //import fox.test.*
 *  - import("fox.test.*");     //import fox.test.*
 *  - import("fox.test",'a,b'); //import fox.test.a、fox.test.b
 *  - import("fox.test.a,b");   //import fox.test.a、fox.test.b
 */
function import($classPackege, $className = null, $quietMode = FALSE) {
  $classPathArray= explode('.', $classPackege);
  $classPath= CLASS_PATH;
  if($className == null) { //如果未指定类名，则使用包名的最后一部分作为类名
    $className= array_pop($classPathArray);
  }
  
  if(!empty($classPathArray)) {
    foreach($classPathArray as $pathItem) {
      $classPath.= $pathItem.'/';
    }
  }
  
  if($className =='*') { //跟java类似，如果使用了 *，那将导入指定包下的所有类（慎用）
    $char_pattern = '/^[_a-zA-Z]{1}[_a-zA-Z0-9]*.class.php$/';
    $handle = opendir($classPath);
    while (false !== ($classFileName = readdir($handle))) {
      $classFile= $classPath. $classFileName;
      if('file' == filetype($classFile) && preg_match($char_pattern, $classFileName)) {
        if (file_exists($classFile)) {
          //var_dump('loading class '.$classFile);
          require_once $classFile;
        }
        else {
          if(!$quietMode) {
            print 'Class not found! Class name: ' . $className . "\n";
            exit();
          }
          else return FALSE;
        } 
      }
    }
  }
  else {
    $classNameArray = get_fields($className);
    foreach($classNameArray as $className) {
      $classFile= $classPath. $className.'.class.php';
      if(file_exists($classFile)) {
        require_once $classFile;
      } 
      else {
        if(!$quietMode) {
          print 'Class is not found! Class name: ' . $className . "\n";
          exit();
        }
        else return FALSE;
      }
    }
  }
  return TRUE;
}

function get_fields($fields) {
  $result= array();
  if(is_array($fields)) {
    foreach( $fields as $field_item) {
      $field_array = get_fields($field_item );
      foreach($field_array as $field_value) {
          $result[]= $field_value;
      }
    }
    return $result;
  }
  else {
    $fields= str_replace(',',' ',$fields);
    $fields= explode(' ',$fields);
    foreach( $fields as $field_value) {
      if(trim($field_value) != null) {
          $result[]= $field_value;
      }
    }
    return $result;
  }
}
