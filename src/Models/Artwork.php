<?php

namespace thcolin\SensCritiqueAPI\Models;

use JsonSerializable;

use thcolin\SensCritiqueAPI\Document;
use thcolin\SensCritiqueAPI\Exceptions\URIException;
use thcolin\SensCritiqueAPI\Exceptions\DocumentParsingException;
use thcolin\SensCritiqueAPI\Exceptions\JSONUnvalidException;

abstract class Artwork{

  use Document;

  const TITLE_DEFAULT = 0;
  const TITLE_ORIGINAL = 1;

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
        $array[lcfirst(substr($method, 3))] = $this->$method();
     }
   }

   return $array;
  }

  public function getId(){
    if($elements = $this->find('body')){
      return intval($elements[0]->attr('data-sc-page-object-id'));
    } else{
      throw new DocumentParsingException('id');
    }
  }

  public function getUrl(){
    return $this->__toString();
  }

  public function getTitle($type = self::TITLE_DEFAULT){
    if($type == self::TITLE_DEFAULT && $elements = $this->find('.pco-cover-title')){
      return trim($elements[0]->text());
    } else if($type == self::TITLE_ORIGINAL && $elements = $this->find('.d-grid-aside')){
      $element = $this->findNextNodeByText($elements[0], 'Titre original');
      return ($element ? $element->text():null);
    } else{
      throw new DocumentParsingException('title');
    }
  }

  public function getYear(){
    if($elements = $this->find('.d-grid-aside')){
      if($document = $this->findNextNodeByText($elements[0], 'Année de production')){
        return intval($document->text());
      } else if($document = $this->findNextNodeByText($elements[0], '#Première diffusion#', true)){
        return intval(substr($document->text(), -4));
      } else{
        throw new DocumentParsingException('year');
      }
    } else{
      throw new DocumentParsingException('year');
    }
  }

  public function getDirectors($array = false){
    $directors = [];

    if($elements = $this->find('.d-grid-main')){
      if($element = $this->findNextNodeByText($elements[0], '#Réalisateur|Créateurs#', true)){
        foreach($element->find('.ecot-contact-label') as $element){
          $directors[] = trim($element->text());
        }
      }
    } else{
      throw new DocumentParsingException('directors');
    }

    return ($array ? $directors:implode(', ', $directors));
  }

  public function getActors($array = false){
    $actors = [];

    if($elements = $this->find('.d-grid-main .d-rubric')){
      if($element = $this->findNextNodeByText($elements[0], '#Acteurs#', true)){
        foreach($element->find('.ecot-contact-label') as $element){
          $actors[] = trim($element->text());
        }
      }
    } else{
      throw new DocumentParsingException('actors');
    }

    return ($array ? $actors:implode(', ', $actors));
  }

  public function getGenres($array = false){
    $genres = [];

    if($elements = $this->find('.d-grid-aside')){
      if($element = $this->findNextNodeByText($elements[0], 'Genre')){
        foreach($element->find('a') as $element){
          $genres[] = $element->text();
        }
      } else{
        $genres[] = 'Film';
      }
    } else{
      throw new DocumentParsingException('genres');
    }

    return ($array ? $genres:implode(', ', $genres));
  }

  public function getDuration(){
    if($elements = $this->find('.d-grid-aside')){
      return $this->findNextNodeByText($elements[0], 'Durée')->text();
    } else{
      throw new DocumentParsingException('duration');
    }
  }

  public function getCountries($array = false){
    $countries = [];

    if($elements = $this->find('.d-grid-aside')){
      foreach($this->findNextNodeByText($elements[0], 'Pays d\'origine')->find('li') as $element){
        $countries[] = $element->text();
      }
    } else{
      throw new DocumentParsingException('countries');
    }

    return ($array ? $countries:implode(', ', $countries));
  }

  public function getStoryline(){
    if(isset($this->storyline)){
      return $this->storyline;
    }

    try{
      $json = $this->api->getJSONByURI('product/storyline/'.$this->getID());
      $this->storyline = $json['json']['data'];
    } catch(JSONUnvalidException $e){
      $explode = explode('/', $this->uri);

      if(end($explode) == 'details'){
        unset($explode[count($explode) - 1]);
      }

      $document = $this->api->getDocumentByURI(implode('/', $explode));
      $elements = $document->find('.pvi-productDetails-resume');

      $this->storyline = trim($elements[0]->text());
    }

    return $this->storyline;
  }

}

?>
