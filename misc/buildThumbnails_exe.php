<?php
$enclose = "\n";
if (isset($_SERVER['HTTP_USER_AGENT'])) {
  $enclose = "<br>\n";
  $start_fid = isset($_REQUEST['start_fid']) ? $_REQUEST['start_fid'] : show_error();
  $end_fid = isset($_REQUEST['end_fid']) ? $_REQUEST['end_fid'] : show_error();
}
elseif ($_SERVER['argc'] == 3) {
  $start_fid  = trim($_SERVER['argv'][1]);
  $end_fid = trim($_SERVER['argv'][2]);
}
else show_error();

function show_error() {
  global $enclose;
  echo "Wrong Parameter! " . $enclose;
  echo "Usage via command line: " . $enclose;
  echo "  ---- buildThumbnails.php <start_fid> <end_fid>" . $enclose;
  echo "Usage via browser: " . $enclose;
  echo "  ---- http://foo.com/buildThumbnails.php?start_fid=123&end_fid=12345" . $enclose;
  exit();
}

include_once '../bootstrap.inc';

set_time_limit(0);

$files = array();
$files = DB::$dbInstance->getRows('SELECT * FROM `files` where image_type>0 and fid>' . $start_fid . ' and fid<' . $end_fid);

foreach ($files as $file) {
  $fileFullName = $file['path'];
  $myThumb = imageCache::cacheImage($fileFullName, 160, 120, TRUE);
  if(!$myThumb) ZDebug::my_echo ('error build thumbnail for ' . $fileFullName);
  
  $myThumb = imageCache::cacheImage($fileFullName, 120, 90, TRUE);
  if(!$myThumb) ZDebug::my_echo ('error build thumbnail for ' . $fileFullName);
}

?>
