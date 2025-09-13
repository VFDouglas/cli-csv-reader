<?php

namespace App\Exception;

use Exception;

class DatabaseConnectionFailedException extends Exception
{
    public function __construct(
        string $message = 'Failed to connect to the database.',
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
