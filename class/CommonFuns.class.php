<?php

/*
 * Some common functions
 */
function my_filectime($fileFullName) {
  return date('Y-m-d H:i:s', filectime($fileFullName));
}

function my_filemtime($fileFullName) {
  return date('Y-m-d H:i:s', filemtime($fileFullName));
}

function formatWhere($where) {
  if(!empty($where)) {
      $where = trim($where);
      if(strtoupper(substr($where, 0, 3)) != 'AND') $where = ' AND ' . $where;      
    }
  return $where;
}

function isId($string) {
  return preg_match("/^\d*$/",$string);
}

function goToUrl($url) {
  header('location: ' . $url);
  exit;
}

function mkdirInFileRoot($folderFullName) {
    $folderArr = explode(DIRECTORY_SEPARATOR, $folderFullName);
    for ($i=0; $i<count($folderArr); $i++) {
      $tmpArr = array();
      for($j=0; $j<=$i; $j++) {
        $tmpArr[] = $folderArr[$j];
      }
      $tmpfolderFullName = FILE_ROOT . implode(DIRECTORY_SEPARATOR, $tmpArr);
      //ZDebug::my_print($tmpfileFullName, 'path');
      if(!empty($tmpfolderFullName)) {
        if(file_exists($tmpfolderFullName) && is_dir($tmpfolderFullName)) {
        }
        else {
          mkdir($tmpfolderFullName);
        }
      }
    }
}

function l($link, $text) {
  return '<a href="' . $link . '">' . $text . '</a>';
}

function veetreeJson($str) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($str);
  exit;
}
?>
