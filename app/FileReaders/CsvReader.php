<?php

namespace App\FileReaders;

use App\DTO\FileReaderConfigDTO;
use App\Exceptions\MissingFileFieldException;
use App\Exceptions\UnableToOpenFileException;
use App\Interfaces\FileReaderInterface;
use App\Interfaces\QueryResultInterface;
use App\Interfaces\RepositoryInterface;
use Generator;

class CsvReader implements FileReaderInterface
{
    private const string PHP_SAPI_CLI = 'cli';

    public function __construct(private readonly FileReaderConfigDTO $config)
    {
    }

    public function getFilePath(): string
    {
        return realpath($this->config->getFilePath());
    }

    public function getSeparator(): string
    {
        return $this->config->getSeparator();
    }

    public function getEnclosure(): string
    {
        return $this->config->getEnclosure();
    }

    public function getEscape(): string
    {
        return $this->config->getEscape();
    }

    /**
     * @throws UnableToOpenFileException
     * @throws MissingFileFieldException
     */
    public function read(): Generator
    {
        $handle = fopen($this->getFilePath(), 'r');
        if ($handle === false) {
            throw new UnableToOpenFileException('Unable to open file: ' . $this->getFilePath());
        }

        $headers = fgetcsv($handle, 0, $this->getSeparator(), $this->getEnclosure(), $this->getEscape());
        if ($headers === false) {
            fclose($handle);

            throw new MissingFileFieldException("File " . $this->getFilePath() . " has no headers");
        }

        while (
            ($row = fgetcsv($handle, 0, $this->getSeparator(), $this->getEnclosure(), $this->getEscape())) !== false
        ) {
            if (count($headers) !== count($row)) {
                throw new MissingFileFieldException(
                    "The header and rows are not compatible. Check file to see if something is wrong, " .
                    "like the separator, enclosure or escape."
                );
            }
            yield array_combine($headers, $row);
        }
        fclose($handle);
    }

    /**
     * @throws UnableToOpenFileException|MissingFileFieldException
     */
    public function save(RepositoryInterface $repository): bool
    {
        $batch     = [];
        $batchSize = 1000;
        $offset    = 0;
        $result    = null;
        foreach ($this->read() as $key => $row) {
            $batch[] = $row;
            if (count($batch) === $batchSize) {
                $result = $repository->save($batch);
                $offset = $key + 1 - $batchSize;
                $batch  = [];
                $this->logMessage($result, $offset, $batchSize);
            }
        }
        if (!empty($batch)) {
            $result = $repository->save($batch);
            $this->logMessage($result, $offset, $offset + count($batch));
        }

        return $result->isSuccess();
    }

    private function logMessage(QueryResultInterface $queryResult, int $offset, int $limit): void
    {
        if (PHP_SAPI !== self::PHP_SAPI_CLI) {
            return;
        }
        if ($queryResult->isSuccess()) {
            echo "Success importing lines " . ($offset + 1) . " to " . ($offset + $limit) . ".\n";
        } else {
            echo "Error importing lines " . ($offset + 1) . " to " . ($offset + $limit) .
                ":\n" . implode("\n", $queryResult->getErrors()) . "\n";
        }
    }
}
