<?php

namespace App\Exception;

use Exception;

class MissingFileFieldException extends Exception
{
    public function __construct(
        string $message = 'A field is missing in the file',
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
