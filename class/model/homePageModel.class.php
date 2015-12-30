<?php

/*
 * This is the model for home page
 * @author: Eric
 */

Class homePageModel extends ModelCore {
  
  public function __construct(Request $request) {
    parent::__construct($request);
    $this->initBlock();
  }
  
  public function initBlock() {
    $items['hotFiles'] = array(
      'weight' => 1,
      'callBack' => 'getHotFiles',
      'param' => array(0),  
    );
    $items['hotFolder'] = array(
      'weight' => 1,
      'callBack' => 'getHotFolder',
      'param' => array(0),
    );
    
    Block::setBlock($items, __CLASS__);
  }
  
  public function getHotFiles($folderId = 0) {
    if(!empty($this->request->arg[3])) $folderId = $this->request->arg[3];
    $res = $this->db->getRows('select * from folders where parent_id=' . $folderId);
    ZDebug::my_print($res, 'foldList result');
    return $res;
  }
  
  public function getHotFolder($folderId = 0) {
    return $this->db->getRows('select * from files where fd_id=' . $folderId);
  }
}
?>
