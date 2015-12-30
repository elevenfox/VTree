<?php

/*
 * This is the file page controller
 * @Author: Elevenfox
 */

Class filePageController extends ControllerCore {

  public function __construct(Request $request) {
    parent::__construct($request);
    $this->initMenu();
  }
  
  private function initMenu() {
    $items = array();
  
    $items['picture/%'] = array(
      'title' => 'Show the picture',
      'access callback' => TRUE,
      'template' => 'page-file', 
    );
    $items['photo/%'] = array(
      'title' => 'Show the picture',
      'access callback' => TRUE,
      'template' => 'page-file', 
    );
    $items['picture/ajax/%'] = array(
      'title' => 'Edit the picture',
      'access callback' => TRUE,
      'modelMethod' => 'getFileAjax',
    );
    $items['same_picture/next_ajax/%'] = array(
      'title' => 'Edit the picture',
      'access callback' => TRUE,
      'modelMethod' => 'getSameFilesNextAjax',
    );
    $items['same_picture/prev_ajax/%'] = array(
      'title' => 'Edit the picture',
      'access callback' => TRUE,
      'modelMethod' => 'getSameFilesPrevAjax',
    );

    Menu::setMenu($items, __CLASS__);
  }
  
  public function preStartViewDisplay() {
    parent::preStartViewDisplay();
  }
  /*
   * This run fucntion is a core function for a controller, it will call model
   * class to get data, and then call view class to render html with templates.
   */
  public function start() {
    parent::start();
  }
          
}
?>
