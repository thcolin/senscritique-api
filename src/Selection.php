<?php

namespace thcolin\SensCritiqueAPI;

use thcolin\SensCritiqueAPI\Core\Connection;
use thcolin\SensCritiqueAPI\Models\Artwork;
use ArrayAccess;
use Exception;

abstract class Selection extends DynamicArray{

  use Document;

  public function __construct($uri){
    $this->__document($uri);

    $uris = [];

    for($i = 1; $i <= $this->getLastPage(); $i++){
      $uris[] = $this->uri.'/page-'.$i;
    }

    foreach($this->api->getDocumentByURI($uris) as $document){
      foreach($this->getArtworksByDocument($document) as $artwork){
        $this[] = $artwork;
      }
    }
  }

  private function getArtworksByDocument($document){
    $artworks = [];

    foreach($document->find('.elco-collection-list .elco-collection-item, .elli-list .elli-item') as $element){
      $uri = substr($element->find('.elco-title a')[0]->attr('href'), 1).'/details';

      if($class = Artwork::getClassByURI($uri)){
        $artworks[] = [
          'class' => $class,
          'uri' => $uri
        ];
      }
    }

    return $artworks;
  }

  private function getLastPage(){
    if($elements = $this->find('.eipa-pages .eipa-page')){
      return str_replace('.', '', trim(end($elements)->text()));
    } else{
      return 1;
    }
  }

  abstract public function getName();

  abstract public function getDescription();

}

?>
