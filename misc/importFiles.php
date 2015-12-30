<?php
//echo 'Now is: ' . date('Y-m-d H:i:s', time()) . "\n";
//echo 'File modified: ' . date('Y-m-d H:i:s', filemtime('/Users/eric/temp/test2/')) . "\n";
//echo 'File created: ' . date('Y-m-d H:i:s', filectime('/Users/eric/temp/test2/')) . "\n";
//echo 'File accessed: ' . date('Y-m-d H:i:s', fileatime('/Users/eric/temp/test2/')) . "\n";
//$a = array();
//var_dump(empty($a));
//var_dump($a);
//if($a) echo "TRUE\n";
//else echo "FALSE\n";
//
//var_dump(is_null($a));
//var_dump(isset($a));

//$a = '2010a';
//var_dump(preg_match("/^\d*$/",$a));
//$b = (int)$a;
//if($b > 0) echo 'a = ' . $a . ", is a integer.\n";
//else echo 'a = ' . $a . ", is NOT a interger.\n";
//exit;

$enclose = "\n";
if (isset($_SERVER['HTTP_USER_AGENT'])) {
  $enclose = "<br>\n";
  $folderStr = isset($_REQUEST['folder']) ? $_REQUEST['folder'] : show_error();
  $mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 0;
  $debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] : 0;
}
elseif ($_SERVER['argc'] >= 2) {
  $folderStr  = trim($_SERVER['argv'][1]);
  $mode = isset($_SERVER['argv']['2']) ? trim($_SERVER['argv']['2']) : 0;
  $debug = isset($_SERVER['argv']['3']) ? trim($_SERVER['argv']['3']) : 0;
}
else show_error();

function show_error() {
  global $enclose;
  echo "Wrong Parameter! " . $enclose;
  echo "Usage via command line: " . $enclose;
  echo "  ---- importFiles.php <folerId/folderFullPath> <mode> <debug>" . $enclose;
  echo "Usage via browser: " . $enclose;
  echo "  ---- http://foo.com/importFiles.php?folder=123&mode=0&debug=0" . $enclose;
  echo "Note: mode is optional" . $enclose;
  exit();
}

//////////////////////////////////////////

include_once '../bootstrap.inc';

import('SyncFiles');
SyncFiles::importFiles($folderStr, $mode, $debug);

?>
