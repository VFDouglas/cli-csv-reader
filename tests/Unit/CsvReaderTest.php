<?php

namespace Tests\Unit;

use App\DTO\FileReaderConfigDTO;
use App\Exceptions\MissingFileFieldException;
use App\Exceptions\UnableToOpenFileException;
use App\FileReaders\CsvReader;
use PHPUnit\Framework\TestCase;

class CsvReaderTest extends TestCase
{
    private string $file;

    protected function setUp(): void
    {
        $this->file = tempnam(sys_get_temp_dir(), 'csv');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    /**
     * @throws UnableToOpenFileException
     * @throws MissingFileFieldException
     */
    public function testReadCsvWithDefaultSeparator(): void
    {
        file_put_contents($this->file, "id,name\n1,Foo\n2,Bar");

        $config = new FileReaderConfigDTO($this->file);
        $reader = new CsvReader($config);

        $rows = iterator_to_array($reader->read());

        $this->assertCount(2, $rows);
        $this->assertSame(['id' => '1', 'name' => 'Foo'], $rows[0]);
    }

    /**
     * @throws MissingFileFieldException
     */
    public function testThrowsWhenFileMissing(): void
    {
        $this->expectException(UnableToOpenFileException::class);

        $config = new FileReaderConfigDTO('/invalid/path.csv');
        $reader = new CsvReader($config);
        iterator_to_array($reader->read());
    }

    /**
     * @throws UnableToOpenFileException
     */
    public function testThrowsOnMismatchedRowLength(): void
    {
        file_put_contents($this->file, "id,name\n1");

        $config = new FileReaderConfigDTO($this->file);
        $reader = new CsvReader($config);

        $this->expectException(MissingFileFieldException::class);
        iterator_to_array($reader->read());
    }
}
