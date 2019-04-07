<?php

namespace App\Service\Tools\RandomProfile;

use App\Service\Tools\API\CurlCobain;

/**
 * Class AvatarFinder
 * @package App\Service\NameFinder
 */
class AvatarFinder implements FinderInterface
{
    /**
     * Size of generated avatar
     */
    const AV_SIZE = 128;
    /**
     * Default hexadecimal color code for avatar's text
     */
    const AV_DEFAULT_TEXT_COLOR = 'efefef';

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
     * @var array
     */
    protected $params;

    protected $letters = [
        'a' => ['ใ','ไ','ำ','ะ','า', 'ใั'],
        'b' => ['บ'],
        'c' => ['จ', 'ฉ', 'ช', 'ฌ'],
        'd' => ['ฎ', 'ด'],
        'e' => ['แ', 'เ'],
        'f' => ['ฝ', 'ฟ'],
        'g' => ['ฆ'],
        'h' => ['ฅ'],
        'i' => ['ิ', 'ี'],
        'j' => ['ึ', ''],
        'k' => ['ก', 'ข', 'ฃ', 'ค', 'ฆ', 'ฅ'],
        'l' => ['ล', 'ฬ'],
        'm' => ['ม'],
        'n' => ['ณ', 'ง'],
        'o' => ['โ'],
        'p' => ['ป', 'ผ', 'พ', 'ภ'],
        'q' => ['ก', 'ข', 'ฃ', 'ค', 'ฆ', 'ฅ'],
        'r' => ['ร'],
        's' => ['ส', 'ศ', 'ษ', 'ซ'],
        't' => ['ฏ', 'ต', 'ถ', 'ฐ', 'ฑ', 'ท', 'ธ', 'ฒ'],
        'u' => ['ุุ', 'ู'],
        'v' => ['ฯ', 'ล'],
        'w' => ['ว'],
        'x' => ['๚', 'ๆ'],
        'y' => ['ย', 'ญ'],
        'z' => ['อ'],
    ];

    /**
     * NicknameFinder constructor.
     * @param CurlCobain $curlCobain
     * @param string $host
     */
    public function __construct(CurlCobain $curlCobain, string $host)
    {
        $this->curlCobain = $curlCobain;
        $this->host = $host;
        $this->params = $this->configureParams();
    }

    /**
     * @return array
     */
    public function configureParams()
    {
        $background = $this->getRandomBackgroundColor();
        return [
            'background' => $background,
            'color'      => ($this->isDarkBackground($background)) ? 'ffffff' : self::AV_DEFAULT_TEXT_COLOR,
            'font-size'  => 0.95,
            'length'     => 1,
            'name'       => null,
            'rounded'    => true,
            'size'       => self::AV_SIZE,
        ];
    }

    /**
     * @return string
     */
    protected function getRandomBackgroundColor()
    {
        return str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    /**
     * @param string $background
     * @return bool
     */
    protected function isDarkBackground(string $background)
    {
        // TODO: Find a way to say whether it's dark or not
        return false;
    }

    /**
     * @param array|null $params
     * @return bool|mixed|string
     */
    public function find(array $params = null)
    {
        if (empty($params['username'])) {
            throw new \InvalidArgumentException('You must provide a username to get avatar');
        }

        $this->setParam('name', $this->getAvatarName($params['username']));

        $this->curlCobain->init($this->getFullUrl());
        $this->curlCobain->setOption(CURLOPT_RETURNTRANSFER, true);
        $content = $this->curlCobain->exec();

        if ($content === false) {
            // TODO log error
            throw new \UnexpectedValueException('Error while getting a nickname');
        }

        return $content;
    }

    /**
     * @param string $username
     * @return mixed
     */
    protected function getAvatarName(string $username)
    {
        $firstLetter = substr($username, 0, 1);
        $letters = $this->letters[strtolower($firstLetter)];
        $index = rand(0, count($letters) - 1);

        return $letters[$index];
    }

    /**
     * @param string $param
     * @param $value
     */
    public function setParam(string $param, $value)
    {
        if (!array_key_exists($param, $this->params)) {
            throw new \InvalidArgumentException(
                'This option does not exist. Available options: '
                . implode(', ', array_keys($this->params))
            );
        }

        $this->params[$param] = $value;
    }

    /**
     * @return string
     */
    protected function getFullUrl(): string
    {
        return sprintf("%s?%s", $this->host, http_build_query($this->params));
    }
}
