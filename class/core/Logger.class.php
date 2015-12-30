<?php

/*
 * Logger class
 */

Class Logger {
  
  public static function log($message) {
    
    $logFileFullName = Config::get('veetree_log');
    $logFileFullName = $logFileFullName ? $logFileFullName : '/tmp/zzz';
    
    file_put_contents($logFileFullName, date('Y-m-d H:i:s', time()) . "\n", FILE_APPEND);
    file_put_contents($logFileFullName, ' -- ' . $message . "\n", FILE_APPEND);
  }
}
?>
