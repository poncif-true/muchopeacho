<?php

namespace App\Service\Tools\API;


use App\Service\Tools\Configuration\ConfigurationInterface;
use App\Service\Tools\Configuration\UnknownConfigurationName;

class ApiConfiguration implements ConfigurationInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * ApiConfiguration constructor.
     */
    public function __construct()
    {
        $this->config = [
            getenv('CODETUNNEL_RANDOM_NICK_API') => [
                'method' => 'post',
                'encode_request' => 'json',
                'decode_response' => 'json',
                'theme' => 'default',
                'sizeLimit' => 59,
            ],
            getenv('UZBY_RANDOM_NAME_API') => [
                'method' => 'get',
                'encode_request' => 'url',
                'decode_response' => 'json',
                'min' => 7,
                'max' => 27,
            ],
        ];
        // TODO get config from file
    }

    /**
     * @param string $key
     * @return array
     * @throws UnknownConfigurationName
     */
    public function get(string $key): array
    {
        if ($this->isConfigured($key)) {
            throw new UnknownConfigurationName($key);
        }

        return $this->getOptions($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isConfigured(string $key): bool
    {
        return array_key_exists($key, $this->config);
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function getOptions(string $key)
    {
        return $this->config[$key];
    }
}