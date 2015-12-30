<?php

/*
 * This is the "page not found"(404) controller
 * @Author: Elevenfox
 */

Class pageNotFoundController extends ControllerCore {
  
  /*
   * This run fucntion is a core function for a controller, it will call model
   * class to get data, and then call view class to render html with templates.
   */
  public function start() {
    ZDebug::my_echo("I'm pageNotFound controller, I'm gonna call model and view.");
  }
}
?>
