<?php

/*
 * Request Class
 */

Class Request {
  private $server;
  private $request;
  private $session;
  private $cookie;
  private $env;
  private $file;
  private $menuItem;
  
  public $data = array();

  public $arg = array();
  
  public function __construct() {
    $this->server = $_SERVER;
    $this->request = $_REQUEST;
    //$this->session = $_SESSION;
    $this->cookie = $_COOKIE;
    $this->env = $_ENV;
    $this->file = $_FILES;
  }
  
  public function getSysRequest() {
    return $this->request;
  }
  
  public function getServer() {
    return $this->server;
  }
  
  public function getsession() {
    return $this->session;
  }
  
  public function getCookie() {
    return $this->cookie;
  }
  
  public function getFile() {
    return $this->file;
  }
  
  public function getEnv() {
    return $this->env;
  }
  
  public function setData($key, $val) {
    $this->data[$key] = $val;
  }
  
  public function getData() {
    return $this->data;
  }
  
  public function setMenuItem(array $menu) {
    
    $this->menuItem = $menu;
    
    // Build url arg array, save to request object
    $this->buildArg($menu['url']);
  }
  
  public function getMenuItem() {
    return $this->menuItem;
  }
  
  private function buildArg($menuUrl) {
    $this->arg = explode('/', $menuUrl);
  }
}
?>
