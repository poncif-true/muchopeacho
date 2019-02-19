<?php

namespace App\Service\NameFinder;

use App\Service\Tools\API\CurlCallerInterface;

/**
 *
 */
class MovieCharactersFinder implements NameFinderInterface
{
    protected $curl;

    public function __construct(CurlCallerInterface $curl)
    {
        $this->curl = $curl;
    }
    
    public function findName()
    {
        $usernames = $this->findNames();

        return $this->getRandomUsername($usernames);
    }
    
    public function findNames()
    {
        $movies = $this->findMovies();
        $movie = $this->getRandomMovie($movies);
        $movie = $this->getMovie($movie->getId());
        $characters = $movie->getCharacters();

        return $this->getCharactersNames($characters);
    }

    protected function getMovieFullCast(int $movieId): array
    {
        // $id = $movieSearch->getMovieId();
        $result = $this->curl->get(
            'https://api.themoviedb.org/3/movie/' . $movieId . '/credits',
            ['api_key' => getenv('TMDB_API_KEY')]
        );
        // $this->getCharacters($result, $movieSearch->getCharFilters);
        // TODO SearchResult entity that contain a getMovie method, Movie is also an entity w/ getCharacters method

        return $result;
    }

    protected function getMovie(int $movieId): array
    {
        $result = $this->curl->get(
            'https://api.themoviedb.org/3/movie/' . $movieId,
            ['api_key' => getenv('TMDB_API_KEY')]
        );
        // $this->getCharacters($result, $movieSearch->getCharFilters);
        // TODO SearchResult entity that contain a getMovie method, Movie is also an entity w/ getCharacters method

        return $result;
    }

    protected function findMovies(string $query)
    {
        $result = $this->curl->callApi(
            'get',
            'https://api.themoviedb.org/3/search/movie',
            ['api_key' => getenv('TMDB_API_KEY'), 'query' => urlencode($query)]
        );
        // TODO SearchResult entity that contain a getMovie method, Movie is also an entity w/ getCharacters method

        return $result;
    }
}
