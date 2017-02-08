# SensCritique API


[![Build Status](https://travis-ci.org/thcolin/senscritique-api.svg?branch=master)](https://travis-ci.org/thcolin/senscritique-api)
[![Code Climate](https://codeclimate.com/github/thcolin/senscritique-api/badges/gpa.svg)](https://codeclimate.com/github/thcolin/senscritique-api)
[![Test Coverage](https://codeclimate.com/github/thcolin/senscritique-api/badges/coverage.svg)](https://codeclimate.com/github/thcolin/senscritique-api/coverage)

PHP Library to call basics usage of SensCritique website by curl and dom parsing

## Installation
Install with composer :
```
composer require thcolin/senscritique-api
```

## Example :
Create a new ```Client``` object :
```php
use thcolin\SensCritiqueAPI\Client;

$client = new Client();

$user = $client -> getUser('Plug_In_Papa');
$collection = $user -> getCollection();

$movie = $collection[0];
echo $movie -> getTitle();
print_r($movie -> serialize());

$tvshow = $client -> getArtwork(438579);
echo $tvshow -> getStoryline();
print_r($tvshow -> serialize());

$lists = $user -> getLists();
$bestMovies = $lists['bestMovies'];
$movie = $bestMovies[0];

$list = $client -> getList(455329);
$best2016Movie = $list[0];
```

Check ```tests/ClientTests``` for more

## Cool
* Beautiful ```DynamicArray``` class which load page dynamically
* Gorgeous ```Selection``` class which get all the pages asynchronously (much faster :heart: )

## TODO
* Implement ```Client``` method ```searchArtwork```, but only "next page" available, not latest
* Add log (and levels) on ```API``` and other ```Core``` or ```src``` class
* Add stress tests (how much ```Artwork``` can the API handle ?)
