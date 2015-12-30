<?php

/*
 * Class to operate file table
 * @author: Elevenfox
 */

Class File {
  
  /*
   * this is a importman function, to save/update file
   * @param:
   *   an array, must have: path, fd_id
   *   optional: page_meta, page_title
   *   also optional: name, sys_created, last_modified, created,deleted,file_size
   *                  ext_name,width,height,image_type,bits,channel,wh_string,mime 
   */
  public function saveFile(array $file) {
    
    if($this->getFile($file['path'])) {
      $op = 'UPDATE';
      $where = "where path = '" . $file['path'] . "'";
    }  
    else {
      $op = 'INSERT INTO';
      $where = '';
    }
    
    $fileToSave = $this->buildFileToSave($file);
    //ZDebug::my_print($fileToSave, 'fileToSave');
    $setString = implode(', ', $fileToSave);
    
    $sql = $op . " `files` SET " . $setString . ' ' . $where;
    //ZDebug::my_echo('sql='.$sql);    
    
    $res = DB::$dbInstance->query($sql);
    
    if($op == 'UPDATE') return $res;
    else return DB::$dbInstance->insertId();
  }
  
  public function getFile($key, $where = '') {
    if(isId($key)) $field = 'fid';
    else $field = 'path';
    
    $key = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $key);
    
    $where = formatWhere($where);
    
    $res = DB::$dbInstance->getRows("SELECT * FROM files WHERE " . $field . " = '". mysql_escape_string($key) . "'" . $where);
    $file = $res ? $res[0]: array();
    return $file;
  }
  
  public function deleteFile($key) {
    if(isId($key)) $field = 'fid';
    else $field = 'path';
    
    $key = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $key);
    
    $sql = 'DELETE FROM files WHERE ' . $field . ' = \'' . $key . '\'';   
    return DB::$dbInstance->query($sql);
  }
  
  private function buildFileToSave($file) {
    if(!isset($file['path'])) die('File path cannot be null.');
    if(!isset($file['fd_id'])) die('Folder id cannot be null.');
    
    $file['path'] = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $file['path']);
    
    $fileInfo = getimagesize($file['path']) ? getimagesize($file['path']) : array();
    $fileInfo[0] = empty($fileInfo[0]) ? 0 : $fileInfo[0];
    $fileInfo[1] = empty($fileInfo[1]) ? 0 : $fileInfo[1];
    $fileInfo[2] = empty($fileInfo[1]) ? 0 : $fileInfo[2];
    $fileInfo[3] = empty($fileInfo[3]) ? '' : $fileInfo[3];
    $fileInfo['bits'] = empty($fileInfo['bits']) ? 0 : $fileInfo['bits'];
    $fileInfo['channels'] = empty($fileInfo['channels']) ? 0 : $fileInfo['channels'];
    $fileInfo['mime'] = empty($fileInfo['mime']) ? 0 : $fileInfo['mime'];
      
    $fileToSave['path'] = "path='" . mysql_escape_string($file['path']) . "'";
    $fileToSave['fd_id'] = "fd_id=" . $file['fd_id'];
    
    $fileToSave['name'] = !empty($file['name']) 
                          ? "name='" . mysql_escape_string($file['name']) . "'"
                          : "name='" . mysql_escape_string(end(explode(DIRECTORY_SEPARATOR, $file['path']))) . "'";
    $fileToSave['ext_name'] = !empty($file['ext_name']) 
                          ? "ext_name='" . mysql_escape_string($file['ext_name']) . "'"
                          : "ext_name='" . mysql_escape_string(end(explode('.', end(explode(DIRECTORY_SEPARATOR, $file['path']))))) . "'";
    $fileToSave['sys_created'] = !empty($file['sys_created']) 
                          ? "sys_created='" . mysql_escape_string($file['sys_created']) . "'"
                          : "sys_created='" . my_filectime($file['path']) . "'";
    $fileToSave['last_modified'] = !empty($file['last_modified']) 
                          ? "last_modified='" . mysql_escape_string($file['last_modified']) . "'"
                          : "last_modified='" . my_filemtime($file['path']) . "'";
    $fileToSave['page_title'] = !empty($file['page_title']) 
                          ? "page_title='" . mysql_escape_string($file['page_title']) . "'"
                          : "page_title='" . mysql_escape_string(end(explode(DIRECTORY_SEPARATOR, $file['path']))) . "'";
    $fileToSave['page_meta'] = !empty($file['page_meta']) 
                          ? "page_meta='" . mysql_escape_string($file['page_meta']) . "'"
                          : "page_meta=''";
    $fileToSave['file_size'] = !empty($file['file_size']) 
                          ? "file_size=" . $file['file_size']
                          : "file_size=" . filesize($file['path']);
    $fileToSave['width'] = !empty($file['width']) 
                          ? "width=" . $file['width']
                          : "width=" . $fileInfo[0];
    $fileToSave['height'] = !empty($file['height']) 
                          ? "height=" . $file['height']
                          : "height=" . $fileInfo[1];
    $fileToSave['image_type'] = !empty($file['image_type']) 
                          ? "image_type=" . $file['image_type']
                          : "image_type=" . $fileInfo[2];
    $fileToSave['bits'] = !empty($file['bits']) 
                          ? "bits=" . $file['bits']
                          : "bits=" . $fileInfo['bits'];
    $fileToSave['channels'] = !empty($file['channels']) 
                          ? "channels=" . $file['channels']
                          : "channels=" . $fileInfo['channels'];
    $fileToSave['size_string'] = !empty($file['size_string']) 
                          ? "size_string='" . mysql_escape_string($file['size_string']) . "'"
                          : "size_string='" . mysql_escape_string($fileInfo[3]) . "'";
    $fileToSave['mime'] = !empty($file['mime']) 
                          ? "mime='" . mysql_escape_string($file['mime']) . "'"
                          : "mime='" . $fileInfo['mime']   . "'";
    
    return $fileToSave;
  }
  
  public function getNextFile($fid, $where='') {
    $where = formatWhere($where);
    
    $res = DB::$dbInstance->getRows("SELECT * FROM files WHERE fid > ". $fid . " " . $where . ' ORDER BY fid ASC limit 1');
    $file = $res ? $res[0]: array();
    return $file;
  }
  
  public function getPreviousFile($fid, $where='') {
    $where = formatWhere($where);
    
    $res = DB::$dbInstance->getRows("SELECT * FROM files WHERE fid < ". $fid . " " . $where . '  ORDER BY fid DESC limit 1');
    $file = $res ? $res[0]: array();
    return $file;
  }
  
  public function getNextPreviousFolder($fd_id, $where='') {   
    $where = formatWhere($where);
    
    $res = DB::$dbInstance->getRows("SELECT MAX(fd_id) as max_fd_id FROM folders");
    $max_id = $res[0]['max_fd_id'];
    $rand_id = rand(0, ($max_id-3));
    
    $res = DB::$dbInstance->getRows("SELECT * FROM folders WHERE fd_id!=$fd_id and fd_id>$rand_id  $where  limit 2");
    $folders = $res ? $res: array();
    return $folders;
  }
}
?>
