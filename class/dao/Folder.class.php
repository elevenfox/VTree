<?php

/*
 * Class to operate folder table
 * @author: Elevenfox
 */

Class Folder {
  
  public function saveFolder(array $folder) { 
    
    if($this->getFolder($folder['path'])) {
      $op = 'UPDATE';
      $where = "where path = '" . $folder['path'] . "'";
    }  
    else {
      $op = 'INSERT INTO';
      $where = '';
    }
    
    $folderToSave = $this->buildFolderToSave($folder);
    //ZDebug::my_print($folderToSave, 'folderToSave');
    $setString = implode(', ', $folderToSave);
    
    $sql = $op . " `folders` SET " . $setString . ' ' . $where;
    //ZDebug::my_echo('sql='.$sql);    
    
    $res = DB::$dbInstance->query($sql);
    if($res) {
      if($op == 'UPDATE') return $res;
      else return DB::$dbInstance->insertId();
    }
    else {
      Logger::log($sql);
      return FALSE;
    }
  }
  
  public function getFolder($key, $where = '') {
    if(isId($key)) $field = 'fd_id';
    else $field = 'path';
    
    $where = formatWhere($where);
    
    $res = DB::$dbInstance->getRows("SELECT * FROM folders WHERE $field='". mysql_escape_string($key) . "'" . $where);
    $folder = $res ? $res[0]: array();
    return $folder;
  }
  
  public function deleteFolder($id) {
    if(!isId($id)) {
      ZDebug::my_echo('Param error in deleteFolder!');
      return FALSE;  
    }
    
    $res = DB::$dbInstance->query("DELETE FROM folders WHERE fd_id = " . $id . "");
    $res = DB::$dbInstance->query("DELETE FROM folders WHERE parent_id = " . $id . "");
    $res = DB::$dbInstance->query("DELETE FROM files WHERE fd_id = " . $id . "");
    return $res;
  }
  
  /*
   * return a result array of files or an empty array
   */
  public function getFilesInFolder($folderId, $options=array()) {
    $where = empty($options['where']) ? '' : $options['where'];
    $orderBy = empty($options['orderBy']) ? 'order by fid' : $options['orderBy'];
    $limit = empty($options['limit']) ? 0 : $options['limit'];
    $start = empty($options['start']) ? 0 : $options['start'];
    
    $where = formatWhere($where);
    if($limit > 0) $limit = ' limit ' . $start . ', ' . $limit;
    else $limit = '';

    $sql = "SELECT * FROM files WHERE fd_id='". $folderId . "' " . 
            $where . ' ' . $orderBy . $limit;
    
    return DB::$dbInstance->getRows($sql);
  }
  
  public function getFilesInFolderTotal($folderId, $options=array()) {
    $where = empty($options['where']) ? '' : $options['where'];
    
    $where = formatWhere($where);
    
    $res = DB::$dbInstance->getRows("SELECT count(*) as num FROM files WHERE fd_id=" . $folderId . $where);
    return $res[0]['num'];
  }
  
  /*
   * return a result array of folders or an empty array
   */
  public function getSubFolder($parentId, $options=array()) {
    $where = empty($options['where']) ? '' : $options['where'];
    $orderBy = empty($options['orderBy']) ? 'order by fd_id' : $options['orderBy'];
    $limit = empty($options['limit']) ? 0 : $options['limit'];
    $start = empty($options['start']) ? 0 : $options['start'];
    
    $where = formatWhere($where);
    if($limit > 0) $limit = ' limit ' . $start . ', ' . $limit;
    else $limit = '';
    
    return DB::$dbInstance->getRows("SELECT * FROM folders WHERE parent_id=" . $parentId . 
                      $where . ' ' . $orderBy . $limit);
  }
  
  public function getSubFolderTotal($parentId, $options=array()) {
    $where = empty($options['where']) ? '' : $options['where'];
    
    $where = formatWhere($where);
    
    $res = DB::$dbInstance->getRows("SELECT count(*) as num FROM folders WHERE parent_id=" . $parentId . $where);
    return $res[0]['num'];
  }
  
  public function getFolderCover($folderId) {
    
    $defaultCover = Config::get('default_folder_cover');
    if(empty($defaultCover)) $defaultCover = '/images/default_cover.jpg';
    
    $files = $this->getFilesInFolder($folderId, array("where"=>"and image_type>0", 'limit'=>100));
    if(empty($files)) {
      $subFolders = $this->getSubFolder($folderId);
      if(empty($subFolders)) return $defaultCover;
      else {
        $theFolder = $subFolders[array_rand($subFolders)];
        return $this->getFolderCover($theFolder['fd_id']);
      }      
    }
    else {
      $theFile = $files[array_rand($files)];
    }
    return $theFile['path'];
  }
  
  private function buildFolderToSave($folder) {
    
    if(!isset($folder['path'])) die('Folder name cannot be null.');
    if(!isset($folder['parent_id'])) die('Folder parent id cannot be null.');
    
    $folderToSave['path'] = "path='" . mysql_escape_string($folder['path']) . "'";
    $folderToSave['parent_id'] = "parent_id=" . $folder['parent_id'];
    
    $folderToSave['name'] = isset($folder['name']) && !empty($folder['name']) 
                          ? "name='" . mysql_escape_string($folder['name']) . "'"
                          : "name='" . mysql_escape_string(end(explode(DIRECTORY_SEPARATOR, $folder['path']))) . "'";
    $folderToSave['sys_created'] = isset($folder['sys_created']) && !empty($folder['sys_created']) 
                          ? "sys_created='" . mysql_escape_string($folder['sys_created']) . "'"
                          : "sys_created='" . my_filectime($folder['path']) . "'";
    $folderToSave['last_modified'] = isset($folder['last_modified']) && !empty($folder['last_modified']) 
                          ? "last_modified='" . mysql_escape_string($folder['last_modified']) . "'"
                          : "last_modified='" . my_filemtime($folder['path']) . "'";
    $folderToSave['page_title'] = isset($folder['page_title']) && !empty($folder['page_title']) 
                          ? "page_title='" . mysql_escape_string($folder['page_title']) . "'"
                          : "page_title='" . mysql_escape_string(end(explode(DIRECTORY_SEPARATOR, $folder['path']))) . "'";
    $folderToSave['page_meta'] = isset($folder['page_meta']) && !empty($folder['page_meta']) 
                          ? "page_meta='" . mysql_escape_string($folder['page_meta']) . "'"
                          : "page_meta=''";
    $folderToSave['locked'] = isset($folder['locked']) && !empty($folder['locked']) 
                          ? "locked=" . $folder['locked'] 
                          : "locked=0";
    
    return $folderToSave;
  }
}
?>
