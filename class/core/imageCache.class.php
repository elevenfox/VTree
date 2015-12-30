<?php

/*
 * Core class of image cache and resize
 * @author: Elevenfox
 */

Class imageCache {

  public static $imageCachePath;
  public static $imageCacheUrl;
  
  public static function init() {
    
    $imageCachePathRelative = Config::get('image_cache_path');
    $imageCachePathRelative = empty($imageCachePathRelative) ? 'image_cache' : $imageCachePathRelative;
    self::$imageCachePath = DOC_ROOT . $imageCachePathRelative . DIRECTORY_SEPARATOR;
    self::$imageCacheUrl =  '/'. $imageCachePathRelative . '/';
  }
  
  public static function cacheImage($srcFile, $toW=75, $toH=0, $verbose=false) {
    $imageNotFound = Config::get('image_not_found');
    
    $fileInfo = self::formatSrcFileInfo($srcFile, $toW, $toH);
    
    if(!$fileInfo) return $imageNotFound;
    
    if (file_exists($fileInfo['cacheFullName'])) {
      //return fopen($fileInfo['cacheFullName'], 'rb');
      if($verbose) {
          echo "-- Cached image exist: {$fileInfo['cacheUrl']}, ignore.... \n";
      }
      return $fileInfo['cacheUrl'];
    }
    else {
      $thumbnailFullName = self::resize($srcFile, $toW, $toH);
      if(!$thumbnailFullName)        return $imageNotFound;
      else return $thumbnailFullName;
    }
  }


  public static function resize($srcFile, $new_width=75, $new_height=0) 
  {
    if($new_width ==0 && $new_width == 0) {
      Logger::log('zero size?');
      return FALSE; 
    } 
    $srcFileInfo = self::formatSrcFileInfo($srcFile, $new_width, $new_height);
    
    // if the file is not a image file or not supported, return false 
    if(!$srcFileInfo) { 
      Logger::log('cannot get file image info');
      return FALSE;
    }
    //ZDebug::my_print($srcFileInfo, 'to file');
    $toFile = $srcFileInfo['cacheFullName'];
    //ZDebug::my_print($toFile, 'to file');
    
    $type = exif_imagetype($srcFile);
        
    switch ($type) 
    {
      case IMAGETYPE_GIF:
        if(!function_exists("imagecreatefromgif")){
          //Logger::log("你的GD库不能使用GIF格式的图片，请使用Jpeg或PNG格式！<a href='javascript:go(-1);'>返回</a>");
          Logger::log('imagecreatefromgif error!');
          return FALSE;
        }
        $src_img = ImageCreateFromGIF($srcFile);
        break;
      case IMAGETYPE_JPEG:
        if(!function_exists("imagecreatefromjpeg")){
          //Logger::log("你的GD库不能使用jpeg格式的图片，请使用其它格式的图片！<a href='javascript:go(-1);'>返回</a>");
          Logger::log('imagecreatefromjpeg error!');
          return FALSE;
        }
        $src_img = @ImageCreateFromJpeg($srcFile);    
        break;
      case IMAGETYPE_PNG:
        $src_img = @ImageCreateFromPNG($srcFile);    
        break;
    }
    
    $w = @imagesx($src_img); 
    $h = @imagesy($src_img); 
    
    if($w==0 || $h==0) {
      Logger::log('image format error, cannot get image size!');
      return FALSE;
    }
    
    if ($new_width == 0 ) $new_width = $w * ($new_height/$h); 
    if ($new_height == 0 ) $new_height = $h * ($new_width/$w); 
    
    // if resized file is smaller than the src file, do nothing, just copy
    if($w*$h < $new_width*$new_height) {
      self::touchFile($toFile);
      copy($srcFile, $toFile);
      return $srcFileInfo['cacheUrl'];
    }
    
    $ratio_w = 1.0 * $new_width / $w; 
    $ratio_h = 1.0 * $new_height / $h; 
    $ratio = 1.0; 
    // 生成的图像的高宽比原来的都小，或都大 ，原则是 取大比例放大，取大比例缩小（缩小的比例就比较小了） 
    if( ($ratio_w < 1 && $ratio_h < 1) || ($ratio_w > 1 && $ratio_h > 1)) { 
      if($ratio_w < $ratio_h) { 
        $ratio = $ratio_h ; // 情况一，宽度的比例比高度方向的小，按照高度的比例标准来裁剪或放大 
      }
      else { 
        $ratio = $ratio_w ; 
      } 
      // 定义一个中间的临时图像，该图像的宽高比 正好满足目标要求 
      $inter_w = (int)($new_width / $ratio); 
      $inter_h = (int) ($new_height / $ratio); 
      $inter_img = imagecreatetruecolor($inter_w , $inter_h); 
      $srcx = (int)(($w - $inter_w)/2);
      $srcy = (int)(($h - $inter_h)/2);
      imagecopy($inter_img, $src_img, 0,0,$srcx,$srcy,$inter_w,$inter_h); 
      // 生成一个以最大边长度为大小的是目标图像$ratio比例的临时图像 
      // 定义一个新的图像 
      $new_img = imagecreatetruecolor($new_width,$new_height); 
      imagecopyresampled($new_img,$inter_img,0,0,0,0,$new_width,$new_height,$inter_w,$inter_h); 
    } // end if 1 
    // 2 目标图像 的一个边大于原图，一个边小于原图 ，先放大平普图像，然后裁剪 
    // =if( ($ratio_w < 1 && $ratio_h > 1) || ($ratio_w >1 && $ratio_h <1) ) 
    else { 
      $ratio = $ratio_h>$ratio_w? $ratio_h : $ratio_w; //取比例大的那个值 
      // 定义一个中间的大图像，该图像的高或宽和目标图像相等，然后对原图放大 
      $inter_w = (int)($w * $ratio); 
      $inter_h = (int)($h * $ratio);
      if ($ratio_h > $ratio_w) {
        $srcx = (int)(($inter_w - $w)/2);
        $srcy = 0;
      }
      else{ 
        $srcx = 0;
        $srcy = (int)(($inter_h - $h)/2);
      }
      $inter_img = imagecreatetruecolor($inter_w , $inter_h); 
      //将原图缩放比例后裁剪 
      imagecopyresampled($inter_img,$srcFile,0,0,$srcx,$srcy,$inter_w,$inter_h,$w,$h); 
      // 定义一个新的图像 
      $new_img = imagecreatetruecolor($new_width,$new_height); 
      imagecopy($new_img, $inter_img, 0,0,0,0,$new_width,$new_height);  
    }// if3 
    
    self::touchFile($toFile);
    
    switch($type) { 
      case IMAGETYPE_JPEG : 
        //imagejpeg($new_img, $toFile,90); 
        // 存储图像 
        if(function_exists('imagejpeg')) ImageJpeg($new_img, $toFile);
        else ImagePNG($new_img, $toFile);
        break; 
      case IMAGETYPE_PNG : 
        imagepng($new_img, $toFile, 90); 
        break; 
      case IMAGETYPE_GIF : 
        if(function_exists('imagegif')) imagegif($new_img, $toFile,90);
        else ImagePNG($new_img, $toFile);
        break; 
      default: 
      break; 
    }
    
    //return $toFile;
    return $srcFileInfo['cacheUrl'];
  }
  
  public static function formatSrcFileInfo($fileFullName, $width=75, $height=0) {
    //$picFileExt = Config::get('pic_file_ext');
    //$picFileExt = empty($picFileExt) ? 'jpg, gif, png, bmp' : $picFileExt;
        
    //if(!strstr($picFileExt, strtolower(end(explode('.', $fileFullName))))) {
    if(!file_exists($fileFullName)) {
      Logger::log('File not exist!' . $fileFullName);
      return FALSE;
    }
    if(!exif_imagetype($fileFullName)) {
      Logger::log('Not an image file!');
      return FALSE;
    }
    $file['fullName'] = $fileFullName;
    //$file['name'] = end(explode(DIRECTORY_SEPARATOR, $fileFullName));    
    //$tmpArr = explode('.', $file['name']);
    //$file['mainName'] = $tmpArr[0];
    //$file['extName'] = $tmpArr[1];
    $file['cacheRelativeName'] = str_replace(FILE_ROOT, '', $fileFullName);
    $file['cacheFullName'] = self::$imageCachePath . $width . 'x' . $height . DIRECTORY_SEPARATOR . $file['cacheRelativeName'];
    $file['cacheUrl'] = self::$imageCacheUrl . $width . 'x' . $height . '/' . $file['cacheRelativeName'];
    
    
    return $file;
  }
  
  public static function touchFile($fileFullName) {
    $fileArr = explode(DIRECTORY_SEPARATOR, $fileFullName);
    for ($i=0; $i<count($fileArr); $i++) {
      $tmpArr = array();
      for($j=0; $j<=$i; $j++) {
        $tmpArr[] = $fileArr[$j];
      }
      $tmpfileFullName = implode(DIRECTORY_SEPARATOR, $tmpArr);
      //ZDebug::my_print($tmpfileFullName, 'path');
      if(!empty($tmpfileFullName)) {
        if($i==(count($fileArr)-1)) {
          if(file_exists($tmpfileFullName) && is_file($tmpfileFullName)) {
            return TRUE;
          }
          else {
            return file_put_contents($tmpfileFullName, '');
          }
        }
        else {
          if(file_exists($tmpfileFullName) && is_dir($tmpfileFullName)) {
          }
          else {
            mkdir($tmpfileFullName);
          }
        }
      }
    }
  }
} 
?>
