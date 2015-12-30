<?php

/*
 * Global config file for Elevenfox MVC
 * @Author: Elevenfox
 */


Class Config {
  static $setting_values = array();

  public static function load() {
    
    if (!file_exists(CONFIG_PATH . 'settings.local.ini')) {
      $settings_file = CONFIG_PATH . 'settings.default.ini';
    }
    else {
      $l_values = parse_ini_file(CONFIG_PATH . 'settings.local.ini', true);
      $current_sfile = isset($l_values['current_settings_file']) ? $l_values['current_settings_file'] : '' ;
      if(empty($current_sfile) || $current_sfile=='settings.default.ini') {
        $settings_file = CONFIG_PATH . 'settings.default.ini';
      }
      else {
        $settings_file = file_exists(CONFIG_PATH . 'conf.d/' . $current_sfile) ? CONFIG_PATH . 'conf.d/' . $current_sfile : CONFIG_PATH . 'settings.default.ini';
      }
    }
    
    if (!file_exists($settings_file)) {
      print 'Cannot find settings file:' . $settings_file;
      exit();
    }  

    Config::$setting_values = parse_ini_file($settings_file, true);
    //ZDebug::my_print(Config::$setting_values);
    
    if (!empty(Config::$setting_values['php_ini'])) {
      Config::overridePHPIni(Config::$setting_values['php_ini']);
    }
  }
  
  private static function overridePHPIni($iniArray) {
    foreach ($iniArray as $ini_kay=>$ini_value) {
      ini_set($ini_kay, $ini_value);
    }
  }

  public static function get($key) {
    foreach (Config::$setting_values as $section) {
      if(isset($section[$key])) return $section[$key];
    }
  }
}
?>
