<?php


namespace App\Exception;

use Throwable;

/**
 * Class FileExtensionException
 *
 * When a file does not match expected extension
 *
 * @package App\Exception
 */
class FileExtensionException extends \OutOfBoundsException
{
    const EXTENSION_NOT_MATCHING = 1501;

    /**
     * FileExtensionException constructor.
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($code = 0, Throwable $previous = null)
    {
        switch ($code) {
            case self::EXTENSION_NOT_MATCHING:
                $message = 'File extension does not match with expected';
                break;
            default:
                $message = 'Error with file extension';
        }

        parent::__construct($message, $code, $previous);
    }
}
