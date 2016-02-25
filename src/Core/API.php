<?php

  namespace thcolin\SensCritiqueAPI\Core;

  use DiDom\Document;
  use jyggen\Curl\Dispatcher;
  use jyggen\Curl\Request;
  use thcolin\SensCritiqueAPI\Exceptions\URIException;
  use thcolin\SensCritiqueAPI\Exceptions\RedirectException;
  use thcolin\SensCritiqueAPI\Exceptions\JSONUnvalidException;

  class API{

    const DOMAIN = 'www.senscritique.com';

    public function getDocumentByURI($args){
      $uris = (is_array($args) ? $args:[$args]);
      $dp = new Dispatcher();
      $requests = $documents = [];

      foreach($uris as $uri){
        $requests[] = new Request('http://'.self::DOMAIN.'/'.$uri);
        $dp -> add(end($requests));
      }

      $dp -> execute();

      foreach($requests as $request){
        $raw = substr($request -> getRawResponse(), $request -> getInfo(CURLINFO_HEADER_SIZE));

        if($request -> getResponse() -> headers -> get('location')){
          throw new RedirectException();
        } else if(!$raw){
          throw new URIException();
        }

        $documents[] = new Document($raw);
      }

      return (is_array($args) ? $documents:$documents[0]);
    }

    public function getJSONByURI($uri){
      $request = new Request('http://'.self::DOMAIN.'/sc/'.$uri.'.json');
      $request -> setOption(CURLOPT_HTTPHEADER, ['X-Requested-With: XMLHttpRequest']);
      $request -> execute();

      $raw = substr($request -> getRawResponse(), $request -> getInfo(CURLINFO_HEADER_SIZE));
      $json = json_decode($raw, true);

      if(!$json OR !$json['json']['success']){
        throw new JSONUnvalidException();
      }

      return $json;
    }

    public function getLocation($uri){
      $request = new Request('http://'.self::DOMAIN.'/'.$uri);
      $request -> execute();

      $location = $request -> getResponse() -> headers -> get('location');

      if(!$location){
        throw new URIException('No "Location" header found');
      } else if($location == '/'){
        throw new URIException();
      } else{
        return $location;
      }
    }

  }

?>
