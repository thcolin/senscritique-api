<?php

  namespace thcolin\SensCritiqueAPI\Models;

  use JsonSerializable;

  use thcolin\SensCritiqueAPI\Document;
  use thcolin\SensCritiqueAPI\Exceptions\URIException;
  use thcolin\SensCritiqueAPI\Exceptions\DocumentParsingException;

  abstract class Artwork{

    use Document;

    static function constructObjectByURI($uri){
      $explode = explode('/', $uri);

      if(!in_array('details', $explode)){
        $explode[] = 'details';
      }

      $uri = implode('/', $explode);
      $class = self::getClassByURI($uri);

      if(!$class){
        throw new URIException();
      }

      return new $class($uri);
    }

    static function getClassByURI($uri){
      $explode = explode('/', $uri);

      switch($explode[0]){
        case 'film':
          return 'thcolin\SensCritiqueAPI\Models\Movie';
        break;
        case 'serie':
          return 'thcolin\SensCritiqueAPI\Models\TVShow';
        break;
        default:
          return null;
        break;
      }
    }

    public function serialize(){
     $array = [];

     foreach(get_class_methods(__CLASS__) as $method){
       if(substr($method, 0, 3) == 'get' && !in_array($method, ['getClassByURI'])){
           $array[lcfirst(substr($method, 3))] = $this -> $method();
       }
     }

     return $array;
    }

    public function getId(){
      if($elements = $this -> find('body')){
        return intval($elements[0] -> attr('data-sc-page-object-id'));
      } else{
        throw new DocumentParsingException();
      }
    }

    public function getUrl(){
      return $this -> __toString();
    }

    public function getTitle(){
      if($elements = $this -> find('.pco-cover-title')){
        return trim($elements[0] -> text());
      } else{
        throw new DocumentParsingException();
      }
    }

    public function getYear(){
      if($elements = $this -> find('.d-grid-aside')){
        if($document = $this -> findNextNodeByText($elements[0], 'Année de production')){
          return intval($document -> text());
        } else if($document = $this -> findNextNodeByText($elements[0], 'Première diffusion')){
          return intval(substr($document -> text(), -4));
        } else{
          throw new DocumentParsingException();
        }
      } else{
        throw new DocumentParsingException();
      }
    }

    public function getDirectors($array = false){
      $directors = [];

      if($elements = $this -> find('.d-rubric')){
        foreach($elements[1] -> find('.ecot-contact-label') as $element){
          $directors[] = trim($element -> text());
        }
      } else{
        throw new DocumentParsingException();
      }

      return ($array ? $directors:implode(', ', $directors));
    }

    public function getActors($array = false){
      $actors = [];

      if($elements = $this -> find('.d-rubric')){
        foreach($elements[0] -> find('.ecot-contact-label') as $element){
          $actors[] = trim($element -> text());
        }
      } else{
        throw new DocumentParsingException();
      }

      return ($array ? $actors:implode(', ', $actors));
    }

    public function getGenres($array = false){
      $genres = [];

      if($elements = $this -> find('.d-grid-aside')){
        foreach($this -> findNextNodeByText($elements[0], 'Genre') -> find('a') as $element){
          $genres[] = $element -> text();
        }
      } else{
        throw new DocumentParsingException();
      }

      return ($array ? $genres:implode(', ', $genres));
    }

    public function getDuration(){
      if($elements = $this -> find('.d-grid-aside')){
        return $this -> findNextNodeByText($elements[0], 'Durée') -> text();
      } else{
        throw new DocumentParsingException();
      }
    }

    public function getCountries($array = false){
      $countries = [];

      if($elements = $this -> find('.d-grid-aside')){
        foreach($this -> findNextNodeByText($elements[0], 'Pays d\'origine') -> find('li') as $element){
          $countries[] = $element -> text();
        }
      } else{
        throw new DocumentParsingException();
      }

      return ($array ? $countries:implode(', ', $countries));
    }

    public function getStoryline(){
      if(!isset($this -> storyline)){
        $json = $this -> api -> getJSONByURI('products/storyline/'.$this -> getID());
        $this -> storyline = $json['json']['data'];
      }

      return $this -> storyline;
    }

  }

?>