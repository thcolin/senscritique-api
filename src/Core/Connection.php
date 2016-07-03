<?php

namespace thcolin\SensCritiqueAPI\Core;

trait Connection{

  protected $api;

  public function __construct(){
    $this->__connection();
  }

  public function __connection(){
    $this->api = new API();
  }

}

?>
