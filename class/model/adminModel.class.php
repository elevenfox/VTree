<?php

/*
 * Admin model
 * @auther: Elevenfox
 */

Class adminModel extends ModelCore {
  
  public function __construct(Request $request) {
    parent::__construct($request);
    $this->initBlock();
  }
  
  public function initBlock() {
    $items['blockFolderList'] = array(
      'weight' => 1,
      'callBack' => 'folderList',
      'param' => array(0),  
    );
    $items['blockFileList'] = array(
      'weight' => 1,
      'callBack' => 'fileList',
      'param' => array(0),
    );
    
    Block::setBlock($items, __CLASS__);
  }
  
  public function folderList($folderId = 0) {
    if(!empty($this->request->arg[3])) $folderId = $this->request->arg[3];
    $res = $this->db->getRows('select * from folders where parent_id=' . $folderId);
    ZDebug::my_print($res, 'foldList result');
    return $res;
  }
  
  public function fileList($folderId = 0) {
    return $this->db->getRows('select * from files where fd_id=' . $folderId);
  }
  
  public function buildFileManager() {
    $this->data['content'] = 'This is a test returned data.';
  }
  
  public function make() {
    parent::make();
  }
          
}
?>
