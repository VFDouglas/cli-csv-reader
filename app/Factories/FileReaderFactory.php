<?php

namespace App\Factories;

use App\Exceptions\UnsupportedFileFormatException;
use App\FileReaders\CsvReader;
use App\Interfaces\FileReaderInterface;

class FileReaderFactory
{
    /**
     * @throws UnsupportedFileFormatException
     */
    public static function create(string $file): FileReaderInterface
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        return match ($ext) {
            'csv'     => new CsvReader(),
            'default' => throw new UnsupportedFileFormatException("Unsupported file format: $ext"),
        };
    }
}
