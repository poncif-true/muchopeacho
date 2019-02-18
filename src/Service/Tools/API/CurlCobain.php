<?php

namespace App\Service\Tools\API;

/**
 *
 */
class CurlCobain implements CurlCallerInterface, EncoderInterface
{
    /**
     * cURL handler returned by curl_init() function
     * $curl
     */
    protected $curlHandler;

    /**
     * CurlCobain constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * @param string $url
     * @return bool|mixed|string
     */
    public function get(string $url)
    {
        return $this->callApi($url);
    }

    /**
     * @param string $url
     * @param string $data
     * @return bool|mixed|string
     */
    public function post(string $url, string $data = '')
    {
        $this->setOption(CURLOPT_POST, 1);
        if (!empty($data)) {
            $this->setOption(CURLOPT_POSTFIELDS, $data);
        }

        return $this->callApi($url);
    }

    /**
     * @param string $url
     * @param string $data
     * @return bool|mixed|string
     */
    public function patch(string $url, string $data = '')
    {
        if (!empty($data)) {
            $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        return $this->callApi($url);
    }

    /**
     * @param string $url
     * @param string $data
     * @return bool|mixed|string
     */
    public function put(string $url, string $data = '')
    {
        $this->setOption(CURLOPT_PUT, 1);

        return $this->callApi($url);
    }

    /**
     * @param string $url
     * @param string $data
     * @return bool|mixed|string
     */
    public function delete(string $url, string $data = '')
    {
        if (!empty($data)) {
            $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        $this->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');

        return $this->callApi($url);
    }

    /**
     * Init curl handler
     * @param string $url
     * @return mixed|void
     */
    public function init(string $url = '')
    {
        $this->curlHandler = curl_init($url);
    }

    /**
     * Executes curl request
     * @return bool|string
     */
    public function exec()
    {
        return curl_exec($this->curlHandler);
    }

    /*
     * Close and clear curl handle
     */
    protected function close()
    {
        $this->curlHandler = curl_close($this->curlHandler);
    }

    /**
     * @param string $url
     * @return mixed
     */
    protected function callApi(string $url)
    {
        if (is_null($this->curlHandler)) {
            $this->init();
        }

        $this->setOption(CURLOPT_URL, $url);
        // get the return value instead of true in case of success
        $this->setOption(CURLOPT_RETURNTRANSFER, 1);

        $response = $this->exec();
        $this->close();

        if ($response === false) {
            throw new \RuntimeException('Failed to make API call');
        }

        return $response;
    }

    /**
     * @param int $option one of the CURLOPT_ native options
     * @param mixed $value
     */
    public function setOption(int $option, $value)
    {
        curl_setopt($this->curlHandler, $option, $value);
    }

    /**
     * @param $data
     * @param string $output
     * @return bool|false|string
     */
    public function encodeData($data, string $output)
    {
        switch ($output) {
            case 'serialize':
                return serialize($data);

            case 'json':
                return json_encode($data);

            case 'url':
                if (is_array($data)) {
                    $string = '';
                    foreach ($data as $key => $value) {
                        $string .= $key . '=' . $value . '&';
                    }
                    $data = substr($string, 0, -1);
                }
                return $data;

            default:
                throw new \LogicException('Trying to encode data in a non-handled response type');
        }
    }

    /**
     * @param $data
     * @param string $format
     * @return mixed
     */
    public function decodeData($data, string $format)
    {
        switch ($format) {
            case 'serialize':
                return unserialize($data);

            case 'json':
                return json_decode($data);

            default:
                throw new \LogicException('Trying to decode a data in a non-handled response type');
        }
    }
}
