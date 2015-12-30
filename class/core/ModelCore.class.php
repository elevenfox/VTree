<?php

/*
 * Model core class
 * @auther: Elevenfox
 */

Class ModelCore {
  public $request;
  public $db;
  public $data = array();

  public function __construct(Request $request) {
    $this->request = $request;
    $this->db = DB::$dbInstance;
    $this->initCoreBlock();
  }
  
  /*
   * initBlock is a neccesary function for model class
   * to define block
   */
  public function initCoreBlock() {
    $items['blockHeader'] = array(
      'weight' => 1,
      'callBack' => 'blockHeaderData'
    );
    $items['blockFooter'] = array(
      'weight' => 1,
      'callBack' => 'blockFooterData'
    );
    
    Block::setBlock($items, __CLASS__);
  }
  
  public function blockHeaderData() {
    
    $headerData['site_logo'] = Config::get('site_logo');
    
    $menuLinks = array();
    import('dao.Folder');
    $folder = new Folder();
    $folders = $folder->getSubFolder(0);
    foreach ($folders as $folderItem) {
      $menuLinks[] = array('name'=>$folderItem['name'], 'url'=>'/list/'.$folderItem['fd_id']);
    }
    $headerData['menuLinks'] = $menuLinks;
    
    return $headerData;
  }
  
  public function blockFooterData() {
    
  }
  
  public function preMake() {
    
  }
  
  public function make() {
    $this->preMake();
    
    $menuItem = $this->request->getMenuItem();
    if (!empty($menuItem['modelMethod'])) $this->$menuItem['modelMethod']();
    else  $this->defaultMake();
    
    $this->afterMake();
  }
  
  public function defaultMake() {
    
  }
  
  public function afterMake() {
    
  }
          
}
?>
