<?php

namespace App\Exceptions;

use Exception;

class UnsupportedFileFormatException extends Exception
{
    public function __construct(
        string $message = 'File format not supported.',
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
