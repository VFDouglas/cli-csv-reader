<?php

namespace App\Factory;

use App\DTO\FileReaderConfigDTO;
use App\Exception\UnsupportedFileFormatException;
use App\FileReader\CsvReader;
use App\Interface\FileReaderInterface;

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
