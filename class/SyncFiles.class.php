<?php

/*
 * Global config file for Elevenfox MVC
 * @Author: Elevenfox
 */


Class SyncFiles {
  
  private static $mode = 0;
  private static $debug = 0;
  
  public static function importFiles($folderStr, $mode=0, $debug=0) {
    
    self::$debug = $debug;
    self::$mode = $mode;
    // Get file root
    $fileRoot = Config::get('file_root');
    
    
    import('dao.Folder');    
    $folder = new Folder();

    if($folderStr == 0 || $folderStr == $fileRoot) {
      self::syncFolders (array('path'=>$fileRoot, 'fd_id'=>0, 'locked'=>0));
      return;
    }

    // If folderStr is not integer (fd_id) 
    // and not a sub-folder of file-root and not exist physically, exit.
    if((!isId($folderStr) && !is_dir($folderStr)) || (!isId($folderStr) && !strstr($fileRoot, $folderStr))) {
      echo 'Invalid folder!' . $enclose;
      return;
    }

    // Get the folder in DB, based on folderId or folderFullName from parameter
    $theFolder = $folder->getFolder($folderStr);

    // If the folder row is not empty, we need to update the folder, otherwise 
    // we need to add a folder if the folderFullName is a sub-folder of file root
    if(is_null($theFolder)) {
      list($newFolderFullName, $newFolderParentId) = locateNewFolder($theFolder['path']);
      self::addForder($newFolderFullName, $newFolderParentId);
    }
    else {
      self::syncFolders($theFolder);
    }
  }
  

  /*
  * Find the real new folder in up level folder based on the given folder name
  */
 public static function locateNewFolder($folderFullName) {
    
    $fileRoot = Config::get('file_root');
    
    import('dao.Folder');    
    $folder = new Folder();
     
    // Get up level folder full name
    $folderArr = explode(DIRECTORY_SEPARATOR, $folderFullName);
    array_pop($folderArr);
    $upLevelFolderFullName = implode(DIRECTORY_SEPARATOR, $folderArr);

    // If up level is file root, return current folder with parent_id=0;
    if($upLevelFolderFullName == $fileRoot) return array($folderFullName, 0);

    // To see if up level folder is in DB
    $upLevelFolder = $folder->getFolder($upLevelFolderFullName);
    // If yes, return current folder, and parent id is the up level folder id
    // otherwise, recursive
    if(!is_null($upLevelFolder)) return array($folderFullName, $upLevelFolder['fd_id']);
    else   self::locateNewFolder($upLevelFolder['path']);
  }

  /*
  * Add folder, sub folders and files into DB
  */
 public static function addFolder($folderFullName, $parentId) {
    $debug = self::$debug;
    
    import('dao.Folder');    
    $folder = new Folder();
    
    import('dao.File');
    $file = new File();
    
    // If the folder is not exist physically, exit.
    if(!is_dir($folderFullName)) {
      if($debug) ZDebug::my_echo('Not exist folder: ' . $folderFullName);
      return FALSE;
    }

    // Step 1: Save the folder
    $insertedFolderId = $folder->saveFolder(array('path'=>$folderFullName, 'parent_id'=>$parentId));
    if($debug) ZDebug::my_echo('Adding new folder in DB: ' . $folderFullName);

    // Step 2: Read this folder to get all files and sub folders
    $hdl=opendir($folderFullName);
    while($item = readdir($hdl)) {
      $fileFullName = $folderFullName . DIRECTORY_SEPARATOR . $item;
      if (($item != ".") && ($item != "..")) {
        // Step 3: If is dir, then add folder
        if(is_dir($fileFullName)) {
          self::addFolder($fileFullName, $insertedFolderId);
        }
        // Step 4: If is file, then add file
        else {
          $file->saveFile(array('path'=>$fileFullName, 'fd_id'=>$insertedFolderId));    
          if($debug>1) ZDebug::my_echo ('Adding new file in DB: ' . $fileFullName);
        }
      }
    }
    closedir($hdl);  
  }

  /*
  * Read sub-folders from the folder, update/delete in DB
  * Need to recursive until all sub-folders are processed.
  */
 public static function syncFolders($folderToSync) {
    $debug = self::$debug;
    $mode = self::$mode;
    
    import('dao.Folder');    
    $folder = new Folder();
    
    import('dao.File');
    $file = new File();

    $folderFullName = $folderToSync['path'];
    $folderId = $folderToSync['fd_id'];

    // If folder is locked, ignore. Unless set mode greater than 0.
    if($folderToSync['locked'] && $mode == 0) {
      if($debug) ZDebug::my_echo('Ignore locked folder: ' . $folderFullName . '(' . $folderId . ')');
      return TRUE;
    }

    // Step 1: If folder is not physically exist, set to deleted in DB.
    if (!is_dir($folderFullName)) {
      $folder->deleteFolder($folderId);
      if($debug) ZDebug::my_echo ('Delete folder in DB: ' . $folderFullName . '(' . $folderId . ')');
      return TRUE;
    }

    // Step 2: Get the result set of files under this folder
    $filesInFolder = $folder->getFilesInFolder($folderId);  
    $fileNameArr = array();
    foreach ($filesInFolder as $theFile) {    
      // Step 3: If a file is not physically exist, delete it in table files.
      if(!file_exists($theFile['path'])) {
        $file->deleteFile($theFile['fid']);
        if($debug) ZDebug::my_echo ('Delete file in DB: ' . $theFile['path'] . '(' . $theFile['fid'] . ')');
      }
      // Step 4: If file exists but modified, update the file in table files.
      elseif($theFile['last_modified'] != my_filemtime($theFile['path'])) {
        $theFile['last_modified'] = my_filemtime($theFile['path']);
        $file->saveFile($theFile);
        if($debug) ZDebug::my_echo ('Update file in DB: ' . $theFile['path'] . '(' . $theFile['fid'] . ')');
      }
      elseif($mode > 1) {
        $file->saveFile($theFile);
        if($debug) ZDebug::my_echo ('Update file in DB: ' . $theFile['path'] . '(' . $theFile['fid'] . ')');
      }
      else {}
      $fileNameArr[] = $theFile['name'];
    }

    // Step 5: Get result set of sub-folders under this folder
    $subFoldera = $folder->getSubFolder($folderId);
    $folderNameArr = array();
    foreach ($subFoldera as $theFolder) {
      // Step 6: If a folder is not physically exist, set deleted flag in table folders.
      if(!file_exists($theFolder['path'])) {
        $folder->deleteFolder($theFolder['fd_id']);
        if($debug) ZDebug::my_echo ('Delete folder in DB: ' . $theFolder['path'] . '(' . $theFolder['fd_id'] . ')');
      }
      // Step 7: If folder exists but modified, update the folder in table folders.
      // and recursive call syncFolder
      elseif($theFolder['last_modified'] != my_filemtime($theFolder['path'])) {
        $theFolder['last_modified'] = my_filemtime($theFolder['path']);
        $folder->saveFolder($theFolder);
        if($debug) ZDebug::my_echo ('Update folder in DB: ' . $theFolder['path'] . '(' . $theFolder['fd_id'] . ')');
        self::syncFolders($theFolder);
      }
      else {
        self::syncFolders($theFolder);
      }
      $folderNameArr[] = $theFolder['name'];
    }

    $hdl=opendir($folderFullName);  
    while($item = readdir($hdl)) { 
      $itemFullName = $folderFullName . DIRECTORY_SEPARATOR . $item;

      // Step 8: If physical file is not in DB file result set, then add a file
      if (($item != ".") && ($item != "..") && is_file($itemFullName) && !in_array($item, $fileNameArr)) {
        $file->saveFile(array('path'=>$itemFullName, 'fd_id'=>$folderId));
        if($debug>1) ZDebug::my_echo ('Adding new file in DB: ' . $itemFullName);
      }
      // Step 9: if physical folder is not in DB folder result set, then add a folder
      if(($item != ".") && ($item != "..") && is_dir($itemFullName) && !in_array($item, $folderNameArr)) {
        self::addFolder($itemFullName, $folderId);
      }
    }
    closedir($hdl);

  }
}
?>
