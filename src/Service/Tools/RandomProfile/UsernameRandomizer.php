<?php

namespace App\Service\Tools\RandomProfile;


/**
 * Class UsernameRandomizer
 * @package App\Service\Tools\Randomizer
 */
class UsernameRandomizer implements RandomizerInterface
{
    /**
     * @var NicknameFinder
     */
    protected $nicknameFinder;

    /**
     * UsernameRandomizer constructor.
     * @param NicknameFinder $finder
     */
    public function __construct(NicknameFinder $finder)
    {
        $this->nicknameFinder = $finder;
    }

    /**
     * Generates a random username
     */
    public function generate(): string
    {
        $username = $this->nicknameFinder->find();

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
