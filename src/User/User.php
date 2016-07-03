<?php

namespace thcolin\SensCritiqueAPI\User;

use thcolin\SensCritiqueAPI\DynamicArray;
use thcolin\SensCritiqueAPI\Document;

class User{

  use Document;

  public function getUsername(){
    if(!isset($this->username)){
      if($elements = $this->find('[data-rel="sc-scout-option-block-button"]')){
        $this->username = substr(trim($elements[0]->text()), 9);
      } else{
        throw new DocumentParsingException();
      }
    }

    return $this->username;
  }

  public function getCollection(){
    if(!isset($this->collection)){
      $this->collection = new Collection($this->getUsername().'/collection');
    }

    return $this->collection;
  }

  public function getLists(){
    if(!isset($this->lists)){
      $this->lists = new DynamicArray();
      $document = $this->api->getDocumentByURI($this->getUsername().'/listes');
      foreach($document->find('[data-rel="lists-content"] li a') as $element){
        $this->lists[trim($element->text())] = [
          'class' => 'thcolin\SensCritiqueAPI\User\Listing',
          'uri' => substr($element->attr('href'), 1)
        ];
      }
    }

    return $this->lists;
  }

}

?>
