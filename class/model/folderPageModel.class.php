<?php

/*
 * This is the model for home page
 * @author: Eric
 */

Class folderPageModel extends ModelCore {
  
  public $listSubFolder = FALSE;
  public $listFiles = FALSE;


  public function __construct(Request $request) {
    parent::__construct($request);
    $this->initBlock();
  }
  
  public function initBlock() {
   
  }
  
  public function make() {
    parent::make();
    $this->listContent();
  }
  
  public function defaultMake() {
    $this->listSubFolder = TRUE;
    $this->listFiles = TRUE;
  }

  private function listContent() {
    
    $listSubFolder = $this->listSubFolder;
    $listFiles = $this->listFiles;
    
    $folderId = $this->request->arg[1];
    $page = empty($this->request->arg[2]) ? 1 : $this->request->arg[2];
    $limitNum = Config::get('default_file_pager');
    $startId = ($page-1) * $limitNum;
    
    $this->data['limitNum'] = $limitNum;
    $this->data['page'] = $page;
    
    import('dao.Folder');
    $folder = new Folder();
    
    $myFolder = $folder->getFolder($folderId);
    if(empty($myFolder)) goToUrl('/pageNotFound');
    
    $this->data['currentFolderId'] = $myFolder['fd_id'];
    
    if($listSubFolder) {
      $options = array('limit' => $limitNum, 'start'=>$startId);    
      $subFolders = $folder->getSubFolder($myFolder['fd_id'], $options);
      foreach ($subFolders as $key=>$subFolder) {
        $defaultCoverThumb = Config::get('default_cover_thumbnail');
        $defaultCoverThumb = empty($defaultCoverThumb) ? '/images/default_cover_thumbnail.jpg' : $defaultCoverThumb;

        $folderCover = $folder->getFolderCover($subFolder['fd_id']);

        $coverThumb = imageCache::cacheImage($folderCover, 160,120);
        $subFolder['thumbnail'] = $coverThumb ? $coverThumb : $defaultCoverThumb;

        $subFolder['filesNum'] = count($folder->getFilesInFolder($subFolder['fd_id'], array("where"=>"and image_type>0")));
        $subFolder['subFoldersNum'] = count($folder->getSubFolder($subFolder['fd_id']));

        $subFolders[$key] = $subFolder;
      }
      $this->data['folders'] = $subFolders;
      $this->data['subFolderTotal'] = $folder->getSubFolderTotal($folderId);
      
      if($listFiles && count($this->data['subFolderTotal'])>$limitNum) $this->data['subFolderPager'] = 'more';
      else        $this->data['subFolderPager'] = 'full';
    }  
    
    if($listFiles) {
      $options = array(
          "where" => "and image_type>0",
          "limit" => $limitNum,
          'start'=>$startId,
      );
      
      $myFiles = $folder->getFilesInFolder($folderId, $options);
      foreach ($myFiles as $key=>$myFile) {
        $defaultThumb = Config::get('default_thumbnail');
        $defaultThumb = empty($defaultThumb) ? '/images/default_thumbnail.jpg' : $defaultThumb;

        $myThumb = imageCache::cacheImage($myFile['path'], 160, 120);
        $myFile['thumbnail'] = $myThumb ? $myThumb : $defaultThumb;
        $myFiles[$key] = $myFile;
      }
      //ZDebug::my_print($myFiles, 'files');
      $this->data['files'] = $myFiles;
      $this->data['filesTotal'] = $folder->getFilesInFolderTotal($folderId);
      
      if($listSubFolder && count($this->data['filesTotal'])>$limitNum) $this->data['filesPager'] = 'more';
      else        $this->data['filesPager'] = 'full';
    }
    
    $this->data['currentFolderName'] = $myFolder['name'];
    
    $this->data['nav'] = $this->genNav($myFolder, $folder);
  }
  
  public function listSubFolders() {
    $this->listSubFolder = TRUE;
  }
  
  public function listFiles() {
    $this->listFiles = TRUE;
  }
  
  public function listFilesAjax() {
    $this->listFiles = TRUE;
    $this->listContent();
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($this->data);
    exit;
  }
  
  private function genNav($myFolder, $folder) {
    $pathArr = explode(DIRECTORY_SEPARATOR, str_replace(FILE_ROOT, '', $myFolder['path']));
    $nav = l('/', '首页');
    for($i=0; $i<count($pathArr); $i++) {
      $newPathArr = $pathArr;
      for($j=0; $j<(count($pathArr)-$i-1); $j++) {
        array_pop($newPathArr);
      }
      $newPath = FILE_ROOT . implode(DIRECTORY_SEPARATOR, $newPathArr);
      $theFolder = $folder->getFolder($newPath);
      $nav .= ' —> ' . l('/list/' . $theFolder['fd_id'], $theFolder['name']);
    }
    
    return $nav;
  }
}
?>
