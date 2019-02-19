<?php
namespace App\Service\Tools\Configuration;

/**
 * Interface ConfigurationInterface
 * @package App\Service\Tools\Configuration
 */
interface ConfigurationInterface
{
    /**
     * @param string $key
     * @return array
     */
    public function get(string $key): array;
}
