<?php

/**
 * Base class for MVC routing
 *
 * @author Elevenfox
 */

Class Router
{
  private $controllerName = '';
  
  private $request;
  
  private $originalUrl;

  /*
   * The router contrunct function is to init request, and get controller 
   * via url (it's actually request['q']). We must enabled apache rewrite, 
   * then there's a .htaccess file in docRoot will rewrite all question to
   * index.php?q=xxxx format.
   */
  public function __construct() {
    $this->request = new Request();
    $requestSys = $this->request->getSysRequest();
    
    if(!isset($requestSys['q'])) {
      $this->originalUrl = '';
      $controllerName = 'homePageController';
      $menuItem = array(
          'controller'=> $controllerName,
          'url'=>'',
          'blocks'=>array('blockHeader', 'blockFooter')
      );
      $this->request->setMenuItem($menuItem);
    }
    else {
      $this->originalUrl = $requestSys['q'];
      $controllerName = $this->getControllerNameByUrl($this->originalUrl);
    }
    
    // If cannot find controller name based on menus, then suer pageNotFound controller
    $this->controllerName = empty($controllerName) ? 'pageNotFoundController': $controllerName;
  }
  
  /*
   * Return the controller name based on url
   * and also add url & block_header, block_footer to menuItem
   */
  public function getControllerNameByUrl($url) {
    
    if('/' == substr($url, -1)) $url = rtrim($url, '/');
    $urlArr = explode('/', $url);
    
    $controller ='';
    
    if(count($urlArr) > 0) {
      foreach (Menu::$menus as $menuUrl=>$menuItem) {
        if ($this->matchedMenu($url, $menuUrl)) {
          $menuItem['url'] = $this->originalUrl;
          $menuItem['blocks'] = array_merge($menuItem['blocks'], array('blockHeader', 'blockFooter'));
          $this->request->setMenuItem($menuItem);
          $controller = $menuItem['controller'];
          break;
        }
      }

      if($controller == '' && count($urlArr)>1) {
        array_pop($urlArr);
        $subUrl = implode('/', $urlArr);
        return $this->getControllerNameByUrl($subUrl);
      }
    }
    
    return $controller;
  }
  
  private function matchedMenu($url, $menuUrl) { 
    //if('/' == substr($url, -1)) $url = rtrim($url, '/');
    
    $urlArr = explode('/', $url);
    $menuUrlArr = explode('/', $menuUrl);

    if (count($menuUrlArr) != count($urlArr)) return FALSE;

    for ($i=0; $i<count($urlArr); $i++) {
      if ($urlArr[$i] != $menuUrlArr[$i] && $menuUrlArr[$i] != '%')  {
        return FALSE;
      }
    }
    
    return TRUE;
  }

  /**
    * Go to controller, start mvc
    *
    */
  public function gotoController()
  {
    $controllerName = $this->controllerName;
    $controller = new $controllerName($this->request);
    $controller->start();
  }
}