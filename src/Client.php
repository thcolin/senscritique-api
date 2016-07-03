<?php

namespace thcolin\SensCritiqueAPI;

use thcolin\SensCritiqueAPI\Core\Connection;
use thcolin\SensCritiqueAPI\User\User;
use thcolin\SensCritiqueAPI\User\Listing;
use thcolin\SensCritiqueAPI\Models\Artwork;

class Client{

  use Connection;

  public function getUser($username){
    $uri = $username;
    return new User($uri);
  }

  public function getList($id){
    $uri = substr($this->api->getLocation('liste/Unkown/'.$id), 1);
    return new Listing($uri);
  }

  public function getArtwork($id){
    $uri = substr($this->api->getLocation('film/Unkown/'.$id), 1);
    return Artwork::constructObjectByURI($uri);
  }

}

?>
