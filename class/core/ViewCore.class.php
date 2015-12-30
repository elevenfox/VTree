<?php

/*
 * admin view class
 * @author: Elevenfox
 */
Class ViewCore {
  
  public $request;
  public $data;
  
  public $theme;
  public $header = array();


  public function __construct(Request $request, array $data) {
    define('SITE_NAME', Config::get('site_name'));
    $this->request = $request;
    $this->data = $data;
    
    $theme_name = !empty(Config::$setting_values['site_conf']['theme']) ? Config::$setting_values['site_conf']['theme'] : 'default';
    $this->theme = new Theme($theme_name);
    
    // Set global/default header if needed
    $this->setHeader(SITE_NAME, 'title');
    $this->setHeader('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
', 'meta');
  }
  
  public function addJS($jsFullName, $pos) {
    if (file_exists(THEME_PATH . $this->theme->themeName . '/' . $jsFullName) && $pos == 'header') { 
      $this->theme->headerJSArr[] = '/theme/' . $this->theme->themeName . '/' . $jsFullName;  
    }
    if (file_exists(THEME_PATH  . $this->theme->themeName . '/' . $jsFullName) && $pos == 'footer') {
      $this->theme->footerJSArr[] = '/theme/' . $this->theme->themeName . '/' . $jsFullName;   
    }
  }

  public function addCSS($cssFullName, $type = 'nonIE') {
    if (file_exists(THEME_PATH . $this->theme->themeName . '/' . $cssFullName) && $type == 'nonIE') {
      $this->theme->cssArr[] = '/theme/' . $this->theme->themeName . '/' . $cssFullName; 
    }
    if (file_exists(THEME_PATH . $this->theme->themeName . '/' . $cssFullName) && $type == 'IE') {
      $this->theme->cssIEArr[] = '/theme/' . $this->theme->themeName . '/' . $cssFullName; 
    }
  }
  
  /*
   * Set header variables if needed
   * Header varialbe include:
   *  - title
   *  - meta
   */
  public function setHeader($headerStr, $type = NULL) {
    if($type == NULL ) $this->theme->header['attrs'][] = $headerAttr;
    if(strtolower($type) == 'meta') $this->theme->header['metas'][] = $headerStr;
    if(strtolower($type) == 'title') $this->theme->header['title'] = $headerStr;
  }
  
  public function preDisplay() {
    
  }
  /*
   *  Basic concept:
   *   - Pages and blocks are basic elements for a theme
   *   - Pages can have basic template or name based overriden template
   *   - Blocks have basic block teplate or name based overriden template
   *   - page have regions
   *   - blocks will be placed into a region
   */
  public function display() {
    /* 
     * Load template
     * Step1: Find template which is defined in menu, if no - 
     * Step2: Find template based on menu name, if no -
     * Step3: Find basic page.tpl
     */
    
    $this->preDisplay();
    
    // Format header data
    $this->formatHeader();   
    
    // Format footer data
    $this->formatFooter();   
    
    // Format blocks
    $this->formatBlocks();   
    
    // Display page
    $this->theme->display($this->data, $this->getTemplate());
  }
  
  /*
   * Format header data, include:
   *  - header attributes
   *  - page title
   *  - meta
   *  - style/css
   *  - JS in header
   */
  private function formatHeader() {
    $this->data['header_attrs'] = implode("\n", $this->theme->header['attr']);
    $this->data['page_title'] = $this->theme->header['title'];
    $this->data['metas'] = implode("\n", $this->theme->header['metas']);
    
    $this->data['styles'] = '';
    foreach ($this->theme->cssArr as $css) {      
      $this->data['styles'] .= '<link type="text/css" rel="stylesheet" media="all" href="' . $css . "\" />\n";
    }
    if(!empty($this->theme->cssIEArr)) {
      $this->data['styles'] .= "<!--[if IE]>\n";
      foreach ($this->theme->cssIEArr as $cssIE) {
        $this->data['styles'] .= '<link type="text/css" rel="stylesheet" media="all" href="' . $cssIE . "\" />\n";
      }
      $this->data['styles'] .= "<![enif]-->\n";
    }
    
    $this->data['scripts_header'] = '';
    foreach ($this->theme->headerJSArr as $jsHeader) {
      $this->data['scripts_header'] .= '<script type="text/javascript" src="' . $jsHeader . "\"></script>\n";
    }
  }
  
  /*
   * Format footer data
   *  - JS in footer
   */
  private function formatFooter() {
    $this->data['scripts_footer'] = '';
    foreach ($this->theme->footerJSArr as $jsFooter) {
      $this->data['scripts_footer'] .= '<script type="text/javascript" src="' . $jsFooter . "\"></script>\n";
    }
  }
  
  /*
   * Format block
   */
  private function formatBlocks() {
    $menuItem = $this->request->getMenuItem();
    
    $blockNames = empty($menuItem['blocks']) ? array() : $menuItem['blocks'];
    foreach ($blockNames as $blockName) {
      // Get block template
      $blockTemplate = $this->theme->templatePath . 'block-' . $blockName . 'tpl.php';
      if (!file_exists($blockTemplate)) {
        $blockTemplate = $this->theme->templatePath . 'block.tpl.php';;
      }
      
      // Get block data
      $blockData = empty($this->data['blocks'][$blockName]['data']) ? array() : $this->data['blocks'][$blockName]['data'];
      $this->data['blockHtml'][$blockName] = $this->theme->render($blockData, $blockTemplate);
    }
  }
  
  /*
   * Get template full name based on:
   *  1) template set in menu
   *  2) template based on url
   *  3) page standard template of the theme
   */
  private function getTemplate() {
    $menuItem = $this->request->getMenuItem();
    
    $templateNameDef = empty($menuItem['template']) ? '' : $this->theme->templatePath . str_replace('.tpl', '', str_replace('.php', '', $menuItem['template'])) . '.tpl.php';
    $templateNameUrl = empty($menuItem['url']) ? '' : $this->theme->templatePath . 'page-' . str_replace('/', '-', $menuItem['url']) . '.tpl.php';
    $templateName = $this->theme->templatePath . 'page.tpl.php';
    if(file_exists($templateNameDef)) {
      $templateName = $templateNameDef;
    }
    elseif (file_exists($templateNameUrl)) {
      $templateName = $templateNameUrl;    
    }
    
    return $templateName;
  }
}
?>
