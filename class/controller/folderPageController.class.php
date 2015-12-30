<?php

/*
 * This is the folder page controller
 * @Author: Elevenfox
 */

Class folderPageController extends ControllerCore {

  public function __construct(Request $request) {
    parent::__construct($request);
    $this->initMenu();
  }
  
  private function initMenu() {
    $items = array();
  
    $items['list/%'] = array(
      'title' => 'List the pictures in a folder',
      'access callback' => TRUE,
      'template' => 'page-folder',  
    );
    $items['list_subfolders/%'] = array(
      'title' => 'list the sub folders',
      'access callback' => TRUE,
      'modelMethod' => 'listSubFolders',
        'template' => 'page-folder', 
    );
    $items['list_files/%'] = array(
      'title' => 'list the files in the folders',
      'access callback' => TRUE,
      'modelMethod' => 'listFiles',
        'template' => 'page-folder', 
    );
    $items['list_files_ajax/%'] = array(
      'title' => 'list the files in the folders',
      'access callback' => TRUE,
      'modelMethod' => 'listFilesAjax',
    );

    Menu::setMenu($items, __CLASS__);
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
