<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    const PUBLIC_DIR_PATH = '/var/www/html/muchopeacho/public/';
    const REL_PATH_TO_PUBLIC_PATTERN = '/^(\.\.\/)*public\//';

    public function getFilters()
    {
        return [
            new TwigFilter('assetPath', [$this, 'filterAssetPath']),
        ];
    }

    /**
     * Clean path to make it work with twig's asset() function
     * @param string $path
     * @return mixed|string|string[]|null
     */
    public function filterAssetPath(string $path)
    {
        if (strpos($path, self::PUBLIC_DIR_PATH) === 0) {
            // case when absolute path is given
            $path = str_replace(self::PUBLIC_DIR_PATH, '', $path);
        } elseif (preg_match(self::REL_PATH_TO_PUBLIC_PATTERN, $path)) {
            // case relative path to public is given
            $path = preg_replace(self::REL_PATH_TO_PUBLIC_PATTERN, '', $path);
        }

        return $path;
    }
}