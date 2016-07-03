<?php

namespace thcolin\SensCritiqueAPI;

use ArrayAccess;
use Exception;

class DynamicArray implements ArrayAccess{

  private $container = [];
  private $objects = [];

  public function length(){
    return count($this->container);
  }

  public function offsetSet($key, $element){
    if(!isset($element['class']) && !isset($element['uri'])){
      throw new CollectionElementException('A collection element must contain "class" and "uri" keys');
    } else if(is_null($key)){
      $this->container[] = $element;
    } else{
      $this->container[$key] = $element;
    }
  }

  public function offsetExists($key){
    return isset($this->container[$key]);
  }

  public function offsetUnset($key){
    unset($this->container[$key]);
    unset($this->objects[$key]);
  }

  public function offsetGet($key){
    if(isset($this->objects[$key])){
      return $this->objects[$key];
    } else if(isset($this->container[$key])){
      return new $this->container[$key]['class']($this->container[$key]['uri']);
    } else{
      throw new Exception('Unknwon offset : '.$key);
    }
  }

}

?>
