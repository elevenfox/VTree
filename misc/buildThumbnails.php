<?php
include_once '../bootstrap.inc';
set_time_limit(0);

$script_name = "buildThumbnails_exe.php";

$rows = DB::$dbInstance->getRows("SELECT max(fid) as mfid FROM files");
$maxId = $rows[0]['mfid'];

echo "--Start---\n";

$batch = 100;
for($i=1; $i<=$maxId; $i=$i+$batch)
{
  $j=$i+$batch;
  exec("php ".$script_name." ".$i." ".$j."     2>/dev/null 1>/dev/null &", $outputArray, $returnVar);
  echo "php ".$script_name." ".$i." ".$j."     2>/dev/null 1>/dev/null & <br>\n";
  my_sleep();
}
  
function my_sleep()
{
  global $script_name;
  exec("ps -ef|grep php", $results, $error);
  $p_num = 0;
  //include('z_control_config.php');
  $max_dm = isset($max_dm) ? $max_dm : 3;
  for($i=0;$i<count($results);$i++)
  {
        if(strstr($results[$i], $script_name)) $p_num=$p_num+1;
  }
  if($p_num >= $max_dm)
  {
        echo "--Already running ".$max_dm ." ".$script_name." deamon... wait for next time ... \n";
		sleep(1);
		my_sleep();
	}
	return;
}

?>