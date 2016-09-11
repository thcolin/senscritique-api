<?php

namespace thcolin\SensCritiqueAPI\User;

use thcolin\SensCritiqueAPI\Selection;
use thcolin\SensCritiqueAPI\Exceptions\DocumentParsingException;

class Listing extends Selection{
  public function getName(){
    if($elements = $this->find('.elme-listTitle')){
      return trim($elements[0]->text());
    } else{
      throw new ns\DocumentParsingException('listing name');
    }
  }

  public function getDescription(){
    if($elements = $this->find('.d-rubric-description div')){
      return (count($elements) > 2 ? trim($elements[1]->text()):null);
    } else{
      return null;
    }
  }
}

?>
