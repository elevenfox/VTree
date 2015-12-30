<?php

/*
 * This is the model for home page
 * @author: Eric
 */

Class webserviceModel extends ModelCore {
  
  public function __construct(Request $request) {
    parent::__construct($request);
  }
  
  public function make() {
    parent::make();
  }
  
  public function submitFilesAPI() {
   
    $data = file_get_contents("php://input");
    
    $dataArr = (array)json_decode($data);
    
    if (empty($dataArr)) {
      ZDebug::my_echo('Error: Json format error!');
      //header('HTTP/1.1 500 Json format error');
      $this->submitFileHelper();
      exit;
    }
    
    // $dataArr['folderFullName'] cannot be null
    if(!isset($dataArr['folderFullName']) || empty($dataArr['folderFullName'])) {
      ZDebug::my_echo('Error: folderFullName cannot be null!');
      $this->submitFileHelper();
      exit;
    }
    
    if(!isset($dataArr['files']) || count($dataArr['files'])==0) {
      ZDebug::my_echo('Error: no file in the submit!');
      $this->submitFileHelper();
      exit;
    }
    
    // Build folder and sub folders in file root if not exist
    mkdirInFileRoot($dataArr['folderFullName']);
    
    // Adding files
    foreach ( $dataArr['files'] as $fileItem ) { 
      $myFile['url'] = $fileItem->url;
      $myFile = array_merge($myFile, (array)json_decode($fileItem->fields));
         
      // Get folder real full name - absolute path
      $folderABSName = FILE_ROOT . DIRECTORY_SEPARATOR . $dataArr['folderFullName'];
      
      // Download file to folder
      $fileFullName = $this->downloadFile($myFile['url'], $folderABSName);
      
      if(!$fileFullName) {
        ZDebug::my_echo('Download failed! URL: ' . $myFile['url']);
        continue;
      }
      
      // Import this file to DB
      import('SyncFiles');
      SyncFiles::importFiles($dataArr['folderFullName']);
      
      // Update the file info with data in json
      import('dao.File');
      $file = new File();
      $fileToSave = $file->getFile($fileFullName);
      if($fileToSave) {
        $fileToSave['page_title'] = $myFile['Text'];
        $fileToSave['page_meta'] = $fileItem->fields;
        $file->saveFile($fileToSave);
      }
      else {
        ZDebug::my_echo('Import to DB failed! ' . $fileFullName);
      }
      // Build thumbnails here?
      //$myThumb = imageCache::cacheImage($fileFullName, 160, 120);
      //if(!$myThumb) ZDebug::my_echo ('error build thumbnail for ' . $fileFullName);
      
      ZDebug::my_print($myFile, 'Adding file');
      
    }
    
    ZDebug::my_echo('Success to add ' . count($dataArr['files']) . ' files to folder: ' . $dataArr['folderFullName']);
    
    exit;
  }
  
  public function downloadFile($url, $folderFullName) {
    $fileName = end(explode('/', $url));
    
    $fileFullName = $folderFullName . DIRECTORY_SEPARATOR . $fileName;
    
    if(file_put_contents($fileFullName, $this->get_data($url))) return $fileFullName;
    else return FALSE;
  }
  
  public function get_data($url)
  {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }

  private function submitFileHelper() {
    //////
    
    $submittedArr = array(
        'folderFullName' => '车展美女/MM写真-100万MM美女车模',
        'files' => array(
             array(
                'url' => 'http://pic.baa.bitautotech.com/temp/2011531101247402.jpg',
                'fields' => '{"Title":"【美女|美女图片-MM写真-100万MM美女车模图片】-易车网BitAuto.com","MetaDescription":"美女图片：易车网天猫美女图片库是国内天猫美女图片量最丰富、图片清晰度最高的专业天猫商城图片频道，我们团队的顶级摄影师为您呈现出，车展美女、日韩美女、欧美美女、美女写真，让您在选择爱车的同时并享受视觉大餐。","MetaKeywords":"美女，美女图片，美女写真，日韩美女，欧美美女，中国美女，美女车模，天猫","Text":"娇媚车模梁言可爱迷人"}',
                 ),
             array(
                'url' => 'http://pic.baa.bitautotech.com/temp/201153110194299.jpg',
                'fields' => '{"Title":"【美女|美女图片-MM写真-100万MM美女车模图片】-易车网BitAuto.com","MetaDescription":"美女图片：易车网天猫美女图片库是国内天猫美女图片量最丰富、图片清晰度最高的专业天猫商城图片频道，我们团队的顶级摄影师为您呈现出，车展美女、日韩美女、欧美美女、美女写真，让您在选择爱车的同时并享受视觉大餐。","MetaKeywords":"美女，美女图片，美女写真，日韩美女，欧美美女，中国美女，美女车模，天猫","Text":"车模制服私房照曝光"}',
                 ),
        ),
      );
    
    ZDebug::my_print($submittedArr, 'The array to submit should be like this:');
    
    $data = json_encode($submittedArr);
    ZDebug::my_print($data, 'The json to submit should be like this:');
    
    $dataArr = json_decode($data);
    ZDebug::my_print($dataArr, 'After the json is decoded in server side, the data object would look like this:');
    exit;
  }
 
}
?>
