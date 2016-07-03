<?php

namespace thcolin\SensCritiqueAPI;

use thcolin\SensCritiqueAPI\Core\API;
use thcolin\SensCritiqueAPI\Core\Connection;

use DiDom\Document as DiDomDocument;
use DiDom\Element as DiDomElement;

trait Document{

  use Connection;

  protected $uri;
  protected $document;

  public function __construct($uri){
    $this->__document($uri);
  }

  public function __document($uri){
    $this->__connection();
    $this->uri = $uri;
    $this->document = $this->api->getDocumentByURI($this->uri);
  }

  public function __toString(){
    return 'http://'.API::DOMAIN.'/'.$this->uri;
  }

  public function has($expression){
    return $this->document->has($expression);
  }

  public function find($expression){
    return $this->document->find($expression);
  }

  public function findNextNodeByText(DiDomElement $element, $expression, $grep = false){
    $childNodes = $element->getNode()->childNodes;
    $length = $childNodes->length;
    $stop = false;

    for($i = 0; $i < $length; $i++){
      $node = $childNodes->item($i);
      if($stop && trim($node->textContent)){
        $document = new DiDomDocument();
        $document->appendChild($node);
        return $document;
      } else if(!$grep && $expression == $node->textContent){
        $stop = true;
      } else if($grep && preg_match($expression, $node->textContent)){
        $stop = true;
      }
    }
  }

}

?>
