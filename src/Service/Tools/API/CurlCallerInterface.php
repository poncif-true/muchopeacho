<?php

namespace App\Service\Tools\API;

/**
 * Describe how we should make single cUrl calls
 * data should be encoded as a string first
 *
 * Interface CurlCallerInterface
 * @package App\Service\Tools\API
 */
interface CurlCallerInterface
{
    /**
     * @param int $option one of the CURLOPT_ native options
     * @param mixed $value
     */
    public function setOption(int $option, $value);

    /**
     * Initializes a curl handler
     * @param string $url
     * @return mixed
     */
    public function init(string $url = '');

    /**
     * Executes a curl request
     *
     * @return string|bool
     */
    public function exec();

    /**
     * Executes a get request
     *
     * @param string $url If some params have to be added, it should be in url
     * @return string|bool
     */
    public function get(string $url);

    /**
     * Executes a post request
     *
     * @param string $url
     * @param string $data
     * @return string|bool
     */
    public function post(string $url, string $data = '');

    /**
     * Executes a patch request
     *
     * @param string $url
     * @param string $data
     * @return string|bool
     */
    public function patch(string $url, string $data = '');

    /**
     * Executes a put request
     *
     * @param string $url
     * @param string $data
     * @return string|bool
     */
    public function put(string $url, string $data = '');

    /**
     * Executes a delete request
     *
     * @param string $url
     * @param string $data
     * @return string|bool
     */
    public function delete(string $url, string $data = '');
}
