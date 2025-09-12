<?php

namespace App\Factories;

use App\DTO\FileReaderConfigDTO;
use App\Exceptions\UnsupportedFileFormatException;
use App\FileReaders\CsvReader;
use App\Interfaces\FileReaderInterface;

class FileReaderFactory
{
    /**
     * @throws UnsupportedFileFormatException
     */
    public static function create(FileReaderConfigDTO $config): FileReaderInterface
    {
        $ext = pathinfo($config->getFilePath(), PATHINFO_EXTENSION);

        return match ($ext) {
            'csv'   => new CsvReader($config),
            default => throw new UnsupportedFileFormatException("Unsupported file format: $ext"),
        };
    }
}
