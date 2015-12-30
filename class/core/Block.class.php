<?php

/*
 * Handle Block
 * @Author: Elevenfox
 */

Class Block {
  
  static $blocks = array();
  
  public static function setBlock(array $blockItems, $modelName) {
    if(!empty($blockItems) && self::blockItemValid($blockItems)) {
      foreach ($blockItems as $blockName => $blockItem) {
        $blockItem['model'] = $modelName;
        self::$blocks[$blockName] = $blockItem;
      }
    }
  }
  
  public static function getBlock($name) {
    return self::$blocks[$name];
  }
  
  public static function blockItemValid(array $blockItems) {
    return TRUE;
  }


  /*
   * init function is to load all model to generate the menus to do router.
   */
  public static function genBlocks() {
    // Todo: add cache to avoid loading all model every time.
    $modelNames = self::getAllModelNames();
    $request = new Request();
    foreach ($modelNames as $modelName) {
      // Create controller objects to run __construct to register all menus.
      import('model', $modelName);
      new $modelName($request);    
    }
  }
  
  /*
   * Get all modelNames via looking up the model folder
   * @Return an array of model names
   */
  private static function getAllModelNames() {
    $modelNames = array();
    $char_pattern = '/^[_a-zA-Z]{1}[_a-zA-Z0-9]*.class.php$/';
    $handle = opendir(MODEL_PATH);
    while (false !== ($classFileName = readdir($handle))) {
      $classFile= MODEL_PATH. $classFileName;
      if('file' == filetype($classFile) && preg_match($char_pattern, $classFileName)) {
        if (file_exists($classFile)) {
          $className = str_replace('.class.php', '', $classFileName);
          $modelNames[] = $className;
        }
      }
    }
    return $modelNames;
  }
}
?>
