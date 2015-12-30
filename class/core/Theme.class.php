<?php

/*
 * Core theme class
 * @author: Elevenfox
 */

/*
 * Theme basic:
 * 1) Theme has some an ini file to config
 * 2) We can set css and js in ini file
 * 3) Theme has regions, must at least have footer and header regions
 * 4) A theme has its own template path
 * 5) 
 */
Class Theme {
  
  public $templatePath;
  public $themeName;
  public $headerJSArr = array();
  public $footerJSArr = array();
  public $cssArr = array();
  public $cssIEArr = array();
  public $regions = array();
  public $header = array(
                'attr' => array(), 
                'title' => '',
                'meta' => array()
         );

  public function __construct($theme_name) {
    $this->templatePath = THEME_PATH . $theme_name . DIRECTORY_SEPARATOR;
    $this->themeName = $theme_name;
    $this->loadTheme();
  }
  
  private function loadTheme() {
    $themeINI = $this->templatePath . 'theme.ini';
    if (!file_exists($themeINI)) {
      print 'Cannot find theme ini file:' . $themeINI;
      exit();
    }  

    $themeSettings = parse_ini_file($themeINI, TRUE);
    $this->cssArr = $this->processSettins($themeSettings['css_all']);
    $this->cssIEArr = $this->processSettins($themeSettings['css_ie']);
    $this->headerJSArr = $this->processSettins($themeSettings['js_header']);
    $this->footerJSArr = $this->processSettins($themeSettings['js_footer']);
    $this->regions = $this->processSettins($themeSettings['region']);
  }
  
  private function processSettins($array) {
    $arr = array();
    foreach ($array as $var) {
      if(!empty($var)) $arr[] = '/theme/' . $this->themeName . '/' . trim(str_replace(' ', '', $var));   
    }
    return $arr;
  }


  /*
   * Core theme fucntion to generate the html codes
   * Based on 
   */
  public function render($data, $template) {
    ob_start();
    include($template);
    $out = ob_get_clean();
    return $out;
  }
  
  /*
   * Core theme fucntion to generate the html codes
   * Based on 
   */
  public function display($data, $template) {
    
    include($template);
  }
}
?>
