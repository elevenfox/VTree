<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ZDebug {
  public static function my_print($val, $name = '') {
    if (!isset($_SERVER['HTTP_USER_AGENT']) || 
            (isset($_SERVER['CONTENT_TYPE']) && strstr($_SERVER['CONTENT_TYPE'], "application/json"))) {
      print "\n\n";
      print "----". $name . "----\n";
      print_r($val);
      print "\n\n";
    }
    else {
      print "<hr color=\"#ff0000\">\n";
      print "<pre>\n";
      print '-----' . $name . "-----<br>\n";
      print_r($val);
      print "\n</pre>\n";
    }
  }
  public static function my_echo($val) {
    if (!isset($_SERVER['HTTP_USER_AGENT']) || 
            (isset($_SERVER['CONTENT_TYPE']) && strstr($_SERVER['CONTENT_TYPE'], "application/json"))) {
      $enclose = "\n";
    }
    else $enclose = "<br>\n";
    
    echo $val . $enclose;
  }
}  
?>
