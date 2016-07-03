<?php

namespace thcolin\SensCritiqueAPI\Exceptions;

use Exception;

class DocumentParsingException extends Exception{
  public function __construct($attribute){
    parent::__construct('Error on parsing the document searching : '.$attribute);
  }
}

?>
