<?php

/*
 * This is the model for home page
 * @author: Eric
 */

Class filePageModel extends ModelCore {
  
  private $file;
  private $folder;
  public $noPrevFile;
  public $noNextFile;

  public function __construct(Request $request) {
    parent::__construct($request);
    $this->initBlock();
  }
  
  public function initBlock() {
   
  }
  
  public function preMake() {
    import('dao.File');
    $this->file = new File();
    
    import('dao.Folder');
    $this->folder = new Folder();
  }
  public function make() {
    parent::make();
  }  
  
  public function defaultMake() {
    //ZDebug::my_print($this->request->arg, 'request args');
    $fid = $this->request->arg[1];
    
    $myFile = $this->file->getFile($fid);
    if(empty($myFile)) goToUrl('/pageNotFound');
    
    // Get picture thumbnail
    $myFile['thumbnail'] = $this->getFileThumbnail($myFile);
    
    $this->data['file'] = $myFile;
    
    $this->getSameFiles($myFile['fd_id'], $myFile['fid']);
    
    $this->data['nav'] = $this->genNav($myFile);
   
    // Todo: Generate last folder and next folder
    $fileConnections = $this->getFileConnections($myFile);
    $this->data['nextFolder'] = $fileConnections['nextFolder'];
    $this->data['previousFolder'] =  $fileConnections['previousFolder'];
    $this->data['nextFile'] = $fileConnections['nextFile'];
    $this->data['previousFile'] = $fileConnections['previousFile'];
  }

  private function getFileThumbnail($file) {
    
    if($file['height'] > 0) {
      if($file['width']/$file['height'] > 1) {
        $theThumb = imageCache::cacheImage($file['path'], 1000);
      }
      else {
        $theThumb = imageCache::cacheImage($file['path'], 800);
      }
      $thumbnail = $theThumb ? $theThumb : $imageNotFound;
      return $thumbnail;
    }
  }
  
//  private function getFileThumbnailBiggest($file) {
//    
//    if($file['height'] > 0) {
//      if($file['width']/$file['height'] > 1) {
//        $theThumb = imageCache::formatSrcFileInfo($file['path'], 1000);
//      }
//      else {
//        $theThumb = imageCache::formatSrcFileInfo($file['path'], 800);
//      }
//      
//      if($theThumb) return $theThumb['cacheUrl'];
//      else        return FALSE;
//    }
//  }
  
  public function getFileAjax() {
    $fid = $this->request->arg[2];
    
    $myFile = $this->file->getFile($fid);
    
    if(empty($myFile)) goToUrl('/pageNotFound');
    
    // Get picture thumbnail
    $myFile['thumbnail'] = $this->getFileThumbnail($myFile);
    
    // Get file connections
    $fileConnections = $this->getFileConnections($myFile);
    $myFile['nextFolder'] = $fileConnections['nextFolder'];
    $myFile['previousFolder'] =  $fileConnections['previousFolder'];
    $myFile['nextFile'] = $fileConnections['nextFile'];
    $myFile['previousFile'] = $fileConnections['previousFile'];
    
    veetreeJson($myFile);
  }
  
  private function genNav($myFile) {
    $pathArr = explode(DIRECTORY_SEPARATOR, str_replace(FILE_ROOT, '', $myFile['path']));
    array_pop($pathArr);
    $nav = l('/', '首页');
    for($i=0; $i<count($pathArr); $i++) {
      $newPathArr = $pathArr;
      for($j=0; $j<(count($pathArr)-$i-1); $j++) {
        array_pop($newPathArr);
      }
      $newPath = FILE_ROOT . implode(DIRECTORY_SEPARATOR, $newPathArr);
      $theFolder = $this->folder->getFolder($newPath);
      $nav .= ' —> ' . l('/list/' . $theFolder['fd_id'], $theFolder['name']);
    }
    
    $nav .= ' —> ' . l('/phptp/' . $myFile['fid'], $myFile['name']);
    
    return $nav;
  }
      
  private function getSameFiles($fd_id, $fid) {
    //$limitNum = Config::get('default_file_pager');
    $limitNum = 10;
    
    $sameFiles_part1_tmp = $this->getSameFilesPrev($fd_id, $fid, $limitNum+1);
    $sameFiles_part1 = $this->getSameFilesPrev($fd_id, $fid, $limitNum/2);
    
    $sameFiles_part2_tmp = $this->getSameFilesNext($fd_id, $fid, $limitNum);
    $sameFiles_part2 = $this->getSameFilesNext($fd_id, $fid, $limitNum - count($sameFiles_part1));
    $this->data['sameFilesNextMore'] = 0;
    if(count($sameFiles_part2_tmp) > count($sameFiles_part2)) {
      $this->data['sameFilesNextMore'] = 1;
    }
    
    $sameFiles_part0 = array();
    $numTotal = count($sameFiles_part1)+count($sameFiles_part2);
    if($numTotal < $limitNum) {
      $theFile = $sameFiles_part1[count($sameFiles_part1)-1];
      $sameFiles_part0 = $this->getSameFilesPrev($fd_id, $theFile['fid'], $limitNum-$numTotal);
    }
    $this->data['sameFilesPrevMore'] = 0;
    if(count($sameFiles_part1_tmp) > (count($sameFiles_part1) + count($sameFiles_part0))) {
      $this->data['sameFilesPrevMore'] = 1;
    }
    
    $sameFiles = array_merge(array_reverse($sameFiles_part0), array_reverse($sameFiles_part1));
    $sameFiles = array_merge($sameFiles, $sameFiles_part2);
    for($i=0; $i<count($sameFiles); $i++) {
      $sameFiles[$i]['thumbnail'] = imageCache::cacheImage($sameFiles[$i]['path'], 120, 90);
      //$sameFiles[$i]['thumbnail_biggest'] = $this->getFileThumbnailBiggest($sameFiles[$i]);
    }
    
    //return $sameFiles;
    $this->data['sameFiles'] = $sameFiles;
  }
  
  private function getSameFilesPrev($fd_id, $fid, $count, $ajax=FALSE) {
    if($ajax) $cond = "fid < $fid";
    else $cond = "fid <= $fid";
    $options = array(
          "where" => 'image_type>0 and '.$cond,
          "limit" => $count,
          "orderBy" => 'order by fid desc',
      );
    return $this->folder->getFilesInFolder($fd_id, $options);
  }
  
  private function getSameFilesNext($fd_id, $fid, $count) {
    $options = array(
          "where" => 'image_type>0 and fid>'.$fid,
          "limit" => $count,
    );
    return $this->folder->getFilesInFolder($fd_id, $options);
  }
  
  private function getFileConnections($myFile) {
    $connectedFolders = $this->file->getNextPreviousFolder($myFile['fd_id']);
    
    $fileConnections = array();
    $fileConnections['nextFolder'] = $connectedFolders[0];
    if(!empty($connectedFolders[0])) {
      $folderCover = $this->folder->getFolderCover($connectedFolders[0]['fd_id']);
      $fileConnections['nextFolder']['thumbnail'] = imageCache::cacheImage($folderCover, 120,90);
    }
    $fileConnections['previousFolder'] =  $connectedFolders[1];
    if(!empty($connectedFolders[1])) {
      $folderCover = $this->folder->getFolderCover($connectedFolders[1]['fd_id']);
      $fileConnections['previousFolder']['thumbnail'] = imageCache::cacheImage($folderCover, 120,90);
    }
    $fileConnections['nextFile'] = $this->file->getNextFile($myFile['fid'], 'image_type>0');
    $fileConnections['previousFile'] = $this->file->getPreviousFile($myFile['fid'], 'image_type>0');
    
    return $fileConnections;
  }
  
  public function getSameFilesNextAjax() {
    $fd_id = $this->request->arg[2];
    $fid = $this->request->arg[3];
    $sameFiles = $this->getSameFilesNext($fd_id, $fid, 10);
    for($i=0; $i<count($sameFiles); $i++) {
      $sameFiles[$i]['thumbnail'] = imageCache::cacheImage($sameFiles[$i]['path'], 120, 90);
    }
    veetreeJson($sameFiles);
  }
  
  public function getSameFilesPrevAjax() {
    $fd_id = $this->request->arg[2];
    $fid = $this->request->arg[3];
    $sameFiles = $this->getSameFilesPrev($fd_id, $fid, 10, TRUE);
    for($i=0; $i<count($sameFiles); $i++) {
      $sameFiles[$i]['thumbnail'] = imageCache::cacheImage($sameFiles[$i]['path'], 120, 90);
    }
    
    veetreeJson($sameFiles);
  }
}
?>
