<?php

namespace App\Service\Tools\Randomizer;

use App\Service\NameFinder\NameFinderInterface;
use App\Service\NameFinder\NicknameFinder;

/**
 * Class UsernameRandomizer
 * @package App\Service\Tools\Randomizer
 */
class UsernameRandomizer implements RandomizerInterface
{
    /**
     * @var NicknameFinder
     */
    protected $nameFinder;

    /**
     * UsernameRandomizer constructor.
     * @param NameFinderInterface $nameFinder
     */
    public function __construct(NameFinderInterface $nameFinder)
    {
        $this->nameFinder = $nameFinder;
    }

    /**
     * Generates a random username
     */
    public function generate(): string
    {
        $username = $this->nameFinder->findName();

        return $this->formatUsername($username);
    }

    /**
     * @param string $username
     * @return string
     */
    protected function formatUsername(string $username): string
    {
        return ucfirst(strtolower($username));
    }
}
