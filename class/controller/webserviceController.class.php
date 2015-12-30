<?php

/*
 * home page
 * @author: Elevenfox
 */

Class webserviceController extends ControllerCore {
  
  public function __construct(Request $request) {
    parent::__construct($request);
    $this->initMenu();
  }
  
  private function initMenu() {
    $items = array();
  
    $items['webservice/submitFiles'] = array(
      'title' => 'Adding files to the system',
      'accessCallback' => 'HTTP_BASIC',
      'modelMethod' => 'submitFilesAPI',
    );
        
    Menu::setMenu($items, __CLASS__);
  }
  
  public function preStart() {
    parent::preStart();
  }
}
?>
