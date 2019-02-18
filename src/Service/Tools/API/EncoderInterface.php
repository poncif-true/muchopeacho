<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 06/02/19
 * Time: 00:29
 */

namespace App\Service\Tools\API;


interface EncoderInterface
{
    public function encodeData($data, string $output);
    public function decodeData($data, string $format);
}