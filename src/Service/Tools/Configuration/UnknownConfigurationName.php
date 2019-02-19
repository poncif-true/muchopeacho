<?php

namespace App\Service\Tools\Configuration;


use Throwable;

/**
 * Class UnknownConfigurationName
 * @package App\Service\Tools\Configuration
 */
class UnknownConfigurationName extends \RuntimeException
{
    /**
     * UnknownConfigurationName constructor.
     * @param $name
     * @param Throwable|null $previous
     */
    public function __construct($name, Throwable $previous = null)
    {
        parent::__construct('Unknown configuration with name: ' . $name, 0, $previous);
    }
}