<?php

/*
 * Basic controller class
 * @Author: Elevenfox
 */

Class ControllerCore {
  
  public $request;
  public $model;
  public $view;

  /*
   * This is the construnt of controller, Resquest came from router
   * InitMenu is to register menu for router
   */
  public function __construct(Request $request) {
    $this->request = $request;
    $this->initMenu();
  }
  
  /*
   * Set menu here, a menu is a url request
   */
  private function initMenu() {
    $items = array();
    Menu::setMenu($items, __CLASS__);
  }
  
  public function preStart() {
    // Sub class can override this function if needed
  }
  
  public function afterStart() {
    // Sub class can override this function if needed
  }
  
  public function preStartModelMake() {
    // Sub class can override this function if needed
  }
  
  public function preStartViewDisplay() {
    // Sub class can override this function if needed
  }

  /*
   * This run fucntion is a core function for a controller, it will call model
   * class to get data, and then call view class to render html with templates.
   */
  public function start() {
    
    $this->preStart();
    
    $this->getModel();    
    $this->preStartModelMake();
    $this->model->make();
    
    $this->buildBlockData();
    
    $this->getView();
    $this->preStartViewDisplay();
    $this->view->display();
    
    $this->afterStart();
  }
  
  private function getModel() {
    $menuItem = $this->request->getMenuItem();
    
    // Get Model class name
    $modelClassName = str_replace('Controller', 'Model', $menuItem['controller']);
    
    // Call model class and call menu callback function
    if(import('model', $modelClassName, TRUE)) {
      $model = new $modelClassName($this->request);
    }    
    else {
      $model = new ModelCore($this->request);
    }
    $this->model = $model;
  }
  
  private function getView() {
    $menuItem = $this->request->getMenuItem();
    
    // Get view class name
    $viewClassName = str_replace('Controller', 'View', $menuItem['controller']);
    
    // Call view class, pass request and model data to the view
    if(import('view', $viewClassName, TRUE)) {
      $view = new $viewClassName($this->request, $this->model->data);
    }
    else {
      $view = new ViewCore($this->request, $this->model->data);
    }
    
    $this->view = $view;
  }
  
  private function buildBlockData() {
    
    $menuItem = $this->request->getMenuItem();
    
    // Load blocks and get blocks data here, based on menu's block settings
    $blocks = array();
    
    foreach ($menuItem['blocks'] as $blockName) {
      $block = Block::getBlock($blockName);
      if(import('model', $block['model'], TRUE)) {
        $theModel = new $block['model']($this->request);
      }
      else {
        $theModel = new ModelCore($this->request);
      }
      
      $block['data'] = $theModel->$block['callBack']();
      $blocks[$blockName] = $block;
    }
    $this->model->data['blocks'] = $blocks;
  }
  
}
?>
