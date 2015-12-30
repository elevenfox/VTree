<?php

/*
 * home page
 * @author: Elevenfox
 */

Class homePageController extends ControllerCore {
  
  public function __construct(Request $request) {
    parent::__construct($request);
    $this->initMenu();
  }
  
  private function initMenu() {
    
  }
  
  public function preStart() {
    parent::preStart();
  }
}
?>
