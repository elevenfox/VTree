<?php

/*
 * admin view class
 * @author: Elevenfox
 */
Class adminView extends ViewCore {
  
  public function preDisplay() {
    parent::preDisplay();
    
    // Set header if needed
    $this->setHeader("美女图片管理" . ' - ' . SITE_NAME, 'title');
    
    // Add css files if needed
    $this->addCSS('/css/admin.css');
    //$this->addCSS('/css/ie.css', 'IE');
    
    // Add js files if needed
    //$this->addJS('/js/main.js', 'header');
    //$this->addJS('/js/footer.js', 'footer');
  }
}
?>
