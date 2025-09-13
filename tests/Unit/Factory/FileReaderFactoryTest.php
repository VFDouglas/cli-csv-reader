<?php

namespace Tests\Unit\Factory;

use App\DTO\FileReaderConfigDTO;
use App\Factory\FileReaderFactory;
use App\FileReader\CsvReader;
use App\Exception\UnsupportedFileFormatException;
use PHPUnit\Framework\TestCase;

class FileReaderFactoryTest extends TestCase
{
    /**
     * @throws UnsupportedFileFormatException
     */
    public function testCreatesCsvReader(): void
    {
        $config = new FileReaderConfigDTO('file.csv');

        $this->assertInstanceOf(CsvReader::class, FileReaderFactory::create($config));
    }

    public function testThrowsOnUnsupportedFormat(): void
    {
        $config = new FileReaderConfigDTO('file.jsx');

        $this->expectException(UnsupportedFileFormatException::class);
        FileReaderFactory::create($config);
    }
}
