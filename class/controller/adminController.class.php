<?php

/*
 * Admin panel
 * @author: Elevenfox
 */

Class adminController extends ControllerCore {
  
  public function __construct(Request $request) {
    parent::__construct($request);
    $this->initMenu();
  }
  
  private function initMenu() {
    $items = array();
  
    $items['admin'] = array(
      'title' => 'Admin home page',
      'accessCallback' => 'HTTP_BASIC',
    );
    
    $items['admin/file_manger'] = array(
      'title' => 'Manage folders and files',
      'accessCallback' => 'HTTP_BASIC',
      'modelMethod' => 'buildFileManager',
      'template' => 'file_manager',
      'blocks' => array('blockFolderList', 'blockFileList')
    );
    
    $items['admin/ajax/folder_list/%'] = array(
      'title' => 'Get folder list based on a folder id',
      'modelMethod' => 'folderList',
    );
    
    Menu::setMenu($items, __CLASS__);
  }
  
  public function preStart() {
    parent::preStart();
  }
  /*
   * This run fucntion is a core function for a controller, it will call model
   * class to get data, and then call view class to render html with templates.
   */
//  public function run() {
//    $menuItem = $this->request->getMenuItem();
//    
//    import('model', 'adminModel');
//    $model = new adminModel($this->request);
//
//    $data = $model->$menuItem['model_method']();
//    ZDebug::my_print($data);
//    
//    import('view', 'adminView');
//    new adminView($data);
//  }
}
?>
