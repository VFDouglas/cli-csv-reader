<?php

namespace Tests\Unit\FileReader;

use App\DTO\FileReaderConfigDTO;
use App\Exception\FileDoesNotExistException;
use App\Exception\MissingFileFieldException;
use App\FileReader\CsvReader;
use PHPUnit\Framework\TestCase;

class CsvReaderTest extends TestCase
{
    private string $separator = ',';

    private string $enclosure = '"';

    private string $escape = '\\';

    private FileReaderConfigDTO $config;

    protected function setUp(): void
    {
        $this->config = (new FileReaderConfigDTO(tempnam(sys_get_temp_dir(), 'csv')))
            ->setSeparator($this->separator)
            ->setEnclosure($this->enclosure)
            ->setEscape($this->escape);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->config->getFilePath())) {
            unlink($this->config->getFilePath());
        }
    }

    /**
     * @throws FileDoesNotExistException
     * @throws MissingFileFieldException
     */
    public function testReadCsvWithDefaultSeparator(): void
    {
        file_put_contents($this->config->getFilePath(), "id,name\n1,Foo\n2,Bar");

        $reader = new CsvReader($this->config);

        $rows = iterator_to_array($reader->read());

        $this->assertCount(2, $rows);
        $this->assertSame(['id' => '1', 'name' => 'Foo'], $rows[0]);
    }

    /**
     * @throws MissingFileFieldException
     */
    public function testThrowsWhenFileMissing(): void
    {
        $this->expectException(FileDoesNotExistException::class);

        $this->config->setFilePath('/invalid/path.csv');
        $reader = new CsvReader($this->config);
        iterator_to_array($reader->read());
    }

    /**
     * @throws FileDoesNotExistException
     */
    public function testThrowsOnMismatchedRowLength(): void
    {
        file_put_contents($this->config->getFilePath(), "id,name\n1");

        $reader = new CsvReader($this->config);

        $this->expectException(MissingFileFieldException::class);
        iterator_to_array($reader->read());
    }
}
