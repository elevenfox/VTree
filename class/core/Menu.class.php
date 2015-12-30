<?php

/*
 * Handle menu
 * @Author: Elevenfox
 */

Class Menu {
  
  static $menus = array();
  
  public static function setMenu(array $menuItems, $controllerName) {
    if(!empty($menuItems) && self::menuItemValid($menuItems)) {
      foreach ($menuItems as $menuName => $menuItem) {
        $theMenuItem['title'] = isset($menuItem['title']) ? $menuItem['title'] : '';
        $theMenuItem['accessCallback'] = isset($menuItem['accessCallback']) ? $menuItem['accessCallback'] : '';
        $theMenuItem['modelMethod'] = isset($menuItem['modelMethod']) ? $menuItem['modelMethod'] : '';
        $theMenuItem['template'] = isset($menuItem['template']) ? $menuItem['template'] : '';
        $theMenuItem['blocks'] = isset($menuItem['blocks']) ? $menuItem['blocks'] : array();
        $theMenuItem['controller'] = $controllerName;
        self::$menus[$menuName] = $theMenuItem;
      }
    }
  }
  
  public static function getMenu($name) {
    return self::$menus[$name];
  }
  
  public static function menuItemValid(array $menuItem) {
    return TRUE;
  }


  /*
   * init function is to load all controller to generate the menus to do router.
   */
  public static function genMenus() {
    // Todo: add cache to avoid loading all controller every time
    $controllerNames = self::getAllControllerNames();
    $request = new Request();
    foreach ($controllerNames as $controllerName) {
      // Create controller objects to run __construct to register all menus.
      import('controller', $controllerName);
      new $controllerName($request);    
    }
  }
  
  /*
   * Get all controllerNames via looking up the controller folder
   * @Return an array of controller names
   */
  private static function getAllControllerNames() {
    $controllerNames = array();
    $char_pattern = '/^[_a-zA-Z]{1}[_a-zA-Z0-9]*.class.php$/';
    $handle = opendir(CONTROLLER_PATH);
    while (false !== ($classFileName = readdir($handle))) {
      $classFile= CONTROLLER_PATH. $classFileName;
      if('file' == filetype($classFile) && preg_match($char_pattern, $classFileName)) {
        if (file_exists($classFile)) {
          $className = str_replace('.class.php', '', $classFileName);
          $controllerNames[] = $className;
        }
      }
    }
    return $controllerNames;
  }
}
?>
