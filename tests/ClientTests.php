<?php

use thcolin\SensCritiqueAPI\Client;
use thcolin\SensCritiqueAPI\Models\Artwork;

class ClientTest extends PHPUnit_Framework_TestCase{

  public function setUp(){
    $this->client = new Client();
    error_reporting(E_ALL^E_WARNING);
  }

  public function testGetUserSuccess(){
    $user = $this->client->getUser('Thomas_Colin');
    $this->assertInstanceOf('thcolin\SensCritiqueAPI\User\User', $user);
  }

  public function testGetUserError(){
    $this->setExpectedException('thcolin\SensCritiqueAPI\Exceptions\RedirectException');
    $user = $this->client->getUser('Error_Example');
  }

  public function testGetUserCollectionSuccess(){
    $collection = $this->client->getUser('Thomas_Colin')->getCollection();
    $this->assertGreaterThanOrEqual(552, $collection->length());

    $movie = $collection[$collection->length() - 1];

    $this->assertEquals(420119, $movie->getId());
    $this->assertEquals('Gravity', $movie->getTitle());
    $this->assertEquals(2013, $movie->getYear());
    $this->assertEquals('Alfonso Cuarón', $movie->getDirectors());
    $this->assertEquals('Sandra Bullock, George Clooney, Ed Harris, Orto Ignatiussen, Phaldut Sharma, Amy Warren, Basher Savage', $movie->getActors());
    $this->assertEquals('Drame, Thriller', $movie->getGenres());
    $this->assertEquals('1 h 30 min', $movie->getDuration());
    $this->assertEquals('États-unis, Royaume-uni', $movie->getCountries());
    $this->assertEquals('Une ingénieur médicale et un astronaute tentent de retourner sur Terre après la destruction de leur navette. Ils se retrouvent seuls dans l’espace.', $movie->getStoryline());
  }

  public function testGetUserCollectionEmptySuccess(){
    $collection = $this->client->getUser('kevingrillo1')->getCollection();
    $this->assertEquals(0, $collection->length());
  }

  public function testGetUserListsSuccess(){
    $lists = $this->client->getUser('Thomas_Colin')->getLists();
    $this->assertGreaterThanOrEqual(1, $lists->length());

    $movie = $lists['Animes'][0];

    $this->assertEquals(392900, $movie->getId());
    $this->assertEquals('Anastasia', $movie->getTitle());
    $this->assertEquals(1997, $movie->getYear());
    $this->assertEquals('Don Bluth, Gary Goldman', $movie->getDirectors());
    $this->assertEquals('Jean-Michel Farcy, Lucienne Chiaroni, Barbara Tissier, Patrick Guillemin, Meg Ryan, John Cusack, Christopher Lloyd (1), Kelsey Grammer', $movie->getActors());
    $this->assertEquals('Animation, Aventure, Drame, Fantastique', $movie->getGenres());
    $this->assertEquals('1 h 34 min', $movie->getDuration());
    $this->assertEquals('États-unis', $movie->getCountries());
    $this->assertEquals('Saint-Pétersbourg, 1917. L\'impératrice Marie et sa petite fille Anastasia survivent au massacre de la famille royale. Mais le destin les sépare...', $movie->getStoryline());
  }

  public function testGetUserListsEmptySuccess(){
    $lists = $this->client->getUser('kevingrillo1')->getLists();
    $this->assertEquals(0, $lists->length());
  }

  public function testGetArtworkMovieSuccess(){
    $movie = $this->client->getArtwork(408059);

    $this->assertEquals(408059, $movie->getId());
    $this->assertEquals('C\'est la fin', $movie->getTitle());
    $this->assertEquals('This Is the End', $movie->getTitle(Artwork::TITLE_ORIGINAL));
    $this->assertEquals(2012, $movie->getYear());
    $this->assertEquals('Seth Rogen, Evan Goldberg', $movie->getDirectors());
    $this->assertEquals('James Franco, Jonah Hill, Seth Rogen, Jay Baruchel, Danny McBride, Craig Robinson, Michael Cera, Emma Watson', $movie->getActors());
    $this->assertEquals('Comédie', $movie->getGenres());
    $this->assertEquals('1 h 47 min', $movie->getDuration());
    $this->assertEquals('États-unis', $movie->getCountries());
    $this->assertEquals('Des amis sont obligés de se terrer dans leur maison à la suite d\'événements étranges et catastrophiques qui ravagent Los Angeles.', $movie->getStoryline());
  }

  public function testGetArtworkMovieNoActorsSuccess(){
    $movie = $this->client->getArtwork(13330620);

    $this->assertEquals(13330620, $movie->getId());
    $this->assertEquals('Demain', $movie->getTitle());
    $this->assertEquals(2015, $movie->getYear());
    $this->assertEquals('Cyril Dion, Mélanie Laurent', $movie->getDirectors());
    $this->assertEquals(null, $movie->getActors());
    $this->assertEquals('Film', $movie->getGenres());
    $this->assertEquals('1 h 58 min', $movie->getDuration());
    $this->assertEquals('France', $movie->getCountries());
    $this->assertEquals("Et si montrer des solutions, raconter une histoire qui fait du bien, était la meilleure façon de résoudre les crises écologiques, économiques et sociales, que traversent nos pays ? Suite à la publication d’une étude qui annonce la possible disparition d’une partie de l’humanité d’ici 2100, Cyril Dion et Mélanie Laurent sont partis avec une équipe de quatre personnes enquêter dans dix pays pour comprendre ce qui pourrait provoquer cette catastrophe et surtout comment l'éviter. Durant leur voyage, ils ont rencontré les pionniers qui réinventent l’agriculture, l’énergie, l’économie, la démocratie et l’éducation. En mettant bout à bout ces initiatives positives et concrètes qui fonctionnent déjà, ils commencent à voir émerger ce que pourrait être le monde de demain…", $movie->getStoryline());
  }

  public function testGetArtworkError(){
    $this->setExpectedException('thcolin\SensCritiqueAPI\Exceptions\URIException');
    $movie = $this->client->getArtwork(1);
  }

  public function testGetArtworkMovieSerializedSuccess(){
    $movie = $this->client->getArtwork(8193726);
    $serialized = $movie->serialize();

    $this->assertArrayHasKey('id', $serialized);
    $this->assertArrayHasKey('url', $serialized);
    $this->assertArrayHasKey('title', $serialized);
    $this->assertArrayHasKey('year', $serialized);
    $this->assertArrayHasKey('directors', $serialized);
    $this->assertArrayHasKey('actors', $serialized);
    $this->assertArrayHasKey('genres', $serialized);
    $this->assertArrayHasKey('duration', $serialized);
    $this->assertArrayHasKey('countries', $serialized);
    $this->assertArrayHasKey('storyline', $serialized);
  }

  public function testGetArtworkTVShowSuccess(){
    $movie = $this->client->getArtwork(438579);

    $this->assertEquals(438579, $movie->getId());
    $this->assertEquals('Black Mirror', $movie->getTitle());
    $this->assertEquals(null, $movie->getTitle(Artwork::TITLE_ORIGINAL));
    $this->assertEquals(2011, $movie->getYear());
    $this->assertEquals('Charlie Brooker', $movie->getDirectors());
    $this->assertEquals('Rasmus Hardiker, Tobias Menzies, Janet Montgomery, Ian Bonar, Tuppence Middleton, Jason Flemyng, Claire Keelan, Chloe Pirrie', $movie->getActors());
    $this->assertEquals('Thriller, Drame', $movie->getGenres());
    $this->assertEquals('1 h', $movie->getDuration());
    $this->assertEquals('Royaume-uni', $movie->getCountries());
    $this->assertEquals('Série britannique présentant à chaque épisode une réalité et un casting différents, reflétant la façon dont l\'homme vit de nos jours ainsi que ses conséquences.', $movie->getStoryline());
  }

}

?>
