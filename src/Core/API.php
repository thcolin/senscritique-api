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

    public function __construct(){
      $this -> cookie = tempnam('/tmp', 'CURL_COOKIE_SENSCRITIQUE_');
      $this -> ch = curl_init();
      curl_setopt($this -> ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($this -> ch, CURLOPT_COOKIEJAR, $this -> cookie);
      curl_setopt($this -> ch, CURLOPT_COOKIEFILE, $this -> cookie);
      curl_setopt($this -> ch, CURLOPT_HEADER, true);
    }

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

    private function curl_exec($url, $args = []){
      curl_setopt($this -> ch, CURLOPT_URL, $url);
      foreach($args as $opt => $value){
        curl_setopt($this -> ch, $opt, $value);
      }
      $exec = curl_exec($this -> ch);
      $info = curl_getinfo($this -> ch);

      $this -> headers = substr($exec, 0, $info['header_size']);
      $this -> raw = substr($exec, $info['header_size']);
      $this -> error = curl_error($this -> ch);

      return $this -> raw;
    }

    private function curl_headers(){
      return $this -> headers;
    }

    private function curl_error(){
      return $this -> error;
    }

  }

?>
