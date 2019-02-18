<?php

namespace App\Service\NameFinder;

use App\Service\Tools\API\CurlCobain;

/**
 * Class NicknameFinder
 * @package App\Service\NameFinder
 */
class NicknameFinder implements NameFinderInterface
{
    const NAME_SIZE_MIN = 5;
    const NAME_SIZE_MAX = 12;

    /**
     * @var CurlCobain
     */
    protected $curlCobain;

    /**
     * Host name (UZBY nickname generator)
     * @var string
     */
    protected $host;

    /**
     * NicknameFinder constructor.
     * @param CurlCobain $curlCobain
     * @param string $host
     */
    public function __construct(CurlCobain $curlCobain, string $host)
    {
        $this->curlCobain = $curlCobain;
        $this->host = $host;
    }

    /**
     * @return bool|string
     */
    public function findName()
    {
        $this->curlCobain->init($this->getUrl());
        $this->curlCobain->setOption(CURLOPT_RETURNTRANSFER, true);
        $html = $this->curlCobain->exec();

        if ($html === false) {
            throw new \UnexpectedValueException('Error while getting a nickname');
        }

        return $html;
    }

    /**
     * Returns options needed to call nicknames API
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            'min' => self::NAME_SIZE_MIN,
            'max' => self::NAME_SIZE_MAX,
        ];
    }

    /**
     * @return string
     */
    protected function getUrl(): string
    {
        return sprintf("%s?%s", $this->host, http_build_query($this->getOptions()));
    }
}
