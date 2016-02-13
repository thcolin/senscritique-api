<?php

	use thcolin\SensCritiqueAPI\Client;

	class ClientTest extends PHPUnit_Framework_TestCase{

		public function setUp(){
      $this -> client = new Client();
      error_reporting(E_ALL^E_WARNING);
		}

		public function testGetUserSuccess(){
			$user = $this -> client -> getUser('Thomas_Colin');
			$this -> assertInstanceOf('thcolin\SensCritiqueAPI\User\User', $user);
		}

		public function testGetUserError(){
			$this -> setExpectedException('thcolin\SensCritiqueAPI\Exceptions\RedirectException');
			$user = $this -> client -> getUser('Error_Example');
		}

		public function testGetUserCollectionSuccess(){
      $collection = $this -> client -> getUser('Thomas_Colin') -> getCollection();
			$this -> assertGreaterThanOrEqual(552, $collection -> length());

			$movie = $collection[$collection -> length() - 1];

      $this -> assertEquals(420119, $movie -> getId());
      $this -> assertEquals('Gravity', $movie -> getTitle());
      $this -> assertEquals(2013, $movie -> getYear());
      $this -> assertEquals('Alfonso Cuarón', $movie -> getDirectors());
      $this -> assertEquals('Sandra Bullock, George Clooney, Ed Harris, Orto Ignatiussen, Phaldut Sharma, Amy Warren, Basher Savage', $movie -> getActors());
      $this -> assertEquals('Drame, Thriller', $movie -> getGenres());
      $this -> assertEquals('1 h 30 min', $movie -> getDuration());
      $this -> assertEquals('États-unis, Royaume-uni', $movie -> getCountries());
      $this -> assertEquals('Une ingénieur médicale et un astronaute tentent de retourner sur Terre après la destruction de leur navette. Ils se retrouvent seuls dans l’espace.', $movie -> getStoryline());
		}

		public function testGetUserCollectionEmptySuccess(){
      $collection = $this -> client -> getUser('kevingrillo1') -> getCollection();
			$this -> assertEquals(0, $collection -> length());
		}

		public function testGetUserListsSuccess(){
      $lists = $this -> client -> getUser('Thomas_Colin') -> getLists();
			$this -> assertGreaterThanOrEqual(1, $lists -> length());

			$movie = $lists['Animes'][0];

      $this -> assertEquals(392900, $movie -> getId());
      $this -> assertEquals('Anastasia', $movie -> getTitle());
      $this -> assertEquals(1997, $movie -> getYear());
      $this -> assertEquals('Don Bluth, Gary Goldman', $movie -> getDirectors());
      $this -> assertEquals('Jean-Michel Farcy, Lucienne Chiaroni, Barbara Tissier, Patrick Guillemin, Meg Ryan, John Cusack, Christopher Lloyd, Kelsey Grammer', $movie -> getActors());
      $this -> assertEquals('Animation, Aventure, Drame, Fantastique', $movie -> getGenres());
      $this -> assertEquals('1 h 34 min', $movie -> getDuration());
      $this -> assertEquals('États-unis', $movie -> getCountries());
      $this -> assertEquals('Saint-Pétersbourg, 1917. L\'impératrice Marie et sa petite fille Anastasia survivent au massacre de la famille royale. Mais le destin les sépare...', $movie -> getStoryline());
		}

		public function testGetUserListsEmptySuccess(){
      $lists = $this -> client -> getUser('kevingrillo1') -> getLists();
			$this -> assertEquals(0, $lists -> length());
		}

		public function testGetArtworkMovieSuccess(){
      $movie = $this -> client -> getArtwork(8193726);

      $this -> assertEquals(8193726, $movie -> getId());
      $this -> assertEquals('007 Spectre', $movie -> getTitle());
      $this -> assertEquals(2015, $movie -> getYear());
      $this -> assertEquals('Sam Mendes', $movie -> getDirectors());
      $this -> assertEquals('Daniel Craig, Christoph Waltz, Léa Seydoux, Ralph Fiennes, Ben Whishaw, Naomie Harris, Dave Bautista, Andrew Scott', $movie -> getActors());
      $this -> assertEquals('Action, Aventure, Policier, Thriller', $movie -> getGenres());
      $this -> assertEquals('2 h 28 min', $movie -> getDuration());
      $this -> assertEquals('États-unis, Royaume-uni', $movie -> getCountries());
      $this -> assertEquals('Un message énigmatique surgi du passé entraîne James Bond dans une mission très personnelle à Mexico City puis à Rome, où il rencontre Lucia Sciarra, la magnifique et inaccessible veuve d’un célèbre criminel. Bond réussit à infiltrer une réunion secrète révélant l’existence d’une redoutable organisation baptisée SPECTRE.', $movie -> getStoryline());
		}

		public function testGetArtworkError(){
			$this -> setExpectedException('thcolin\SensCritiqueAPI\Exceptions\URIException');
			$movie = $this -> client -> getArtwork(1);
		}

		public function testGetArtworkMovieSerializedSuccess(){
      $movie = $this -> client -> getArtwork(8193726);
			$serialized = $movie -> serialize();

			$this -> assertArrayHasKey('id', $serialized);
			$this -> assertArrayHasKey('url', $serialized);
			$this -> assertArrayHasKey('title', $serialized);
			$this -> assertArrayHasKey('year', $serialized);
			$this -> assertArrayHasKey('directors', $serialized);
			$this -> assertArrayHasKey('actors', $serialized);
			$this -> assertArrayHasKey('genres', $serialized);
			$this -> assertArrayHasKey('duration', $serialized);
			$this -> assertArrayHasKey('countries', $serialized);
			$this -> assertArrayHasKey('storyline', $serialized);
		}

		public function testGetArtworkTVShowSuccess(){
      $movie = $this -> client -> getArtwork(438579);

      $this -> assertEquals(438579, $movie -> getId());
      $this -> assertEquals('Black Mirror', $movie -> getTitle());
      $this -> assertEquals(2011, $movie -> getYear());
      $this -> assertEquals('Charlie Brooker', $movie -> getDirectors());
      $this -> assertEquals('Rasmus Hardiker, Tobias Menzies, Janet Montgomery, Ian Bonar, Tuppence Middleton, Jason Flemyng, Claire Keelan, Chloe Pirrie', $movie -> getActors());
      $this -> assertEquals('Thriller, Drame', $movie -> getGenres());
      $this -> assertEquals('1 h', $movie -> getDuration());
      $this -> assertEquals('Royaume-uni', $movie -> getCountries());
      $this -> assertEquals('Série britannique présentant à chaque épisode une réalité et un casting différents, reflétant la façon dont l\'homme vit de nos jours ainsi que ses conséquences.', $movie -> getStoryline());
		}

	}

?>
