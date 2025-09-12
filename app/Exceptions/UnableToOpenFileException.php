<?php

namespace App\Exceptions;

use Exception;

class UnableToOpenFileException extends Exception
{
    public function __construct(
        string $message = 'File could not be opened.',
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
