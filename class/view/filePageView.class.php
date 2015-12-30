<?php

/*
 * admin view class
 * @author: Elevenfox
 */
Class filePageView extends ViewCore {
  
  public function preDisplay() {
    parent::preDisplay();
    
    // Set header if needed
    $this->setHeader($this->data['file']['page_title'] . ' - ' . SITE_NAME, 'title');
    
    if(isset($this->data['file'])) {
      $file = $this->data['file'];
      
      if($file['width']/$file['height'] > 1) {
        $menuItem = $this->request->getMenuItem();
        $menuItem['template'] = 'page-file-horizontal';
        $this->request->setMenuItem($menuItem);
        //ZDebug::my_print($menuItem, 'menu item');
      }
    }
    
    $this->addJS('js/filePage.js', 'footer');
    
  }
}
?>
