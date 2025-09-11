<?php

namespace App\FileReaders;

use App\Exceptions\UnableToOpenFileException;
use App\Interfaces\FileReaderInterface;
use App\Interfaces\QueryResultInterface;
use App\Interfaces\RepositoryInterface;
use Generator;

class CsvReader implements FileReaderInterface
{
    private const string PHP_SAPI_CLI = 'cli';

    /**
     * @throws UnableToOpenFileException
     */
    public function read(string $path): Generator
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new UnableToOpenFileException("Unable to open file: $path");
        }

        $headers = fgetcsv($handle);
        if ($headers === false) {
            fclose($handle);

            return;
        }

        while (($row = fgetcsv($handle)) !== false) {
            yield array_combine($headers, $row);
        }
        fclose($handle);
    }

    /**
     * @throws UnableToOpenFileException
     */
    public function save(string $path, RepositoryInterface $repository): bool
    {
        $batch     = [];
        $batchSize = 1000;
        $offset    = 0;
        $result    = false;
        foreach ($this->read($path) as $key => $row) {
            $batch[] = $row;
            if (count($batch) === $batchSize) {
                $result = $repository->save($batch);
                $offset = $key * $batchSize;
                $this->logMessage($result, $offset, $offset + $batchSize);
                $batch = [];
            }
        }
        if (!empty($batch)) {
            $result = $repository->save($batch);
            $this->logMessage($result, $offset, ($offset + count($batch)));
        }

        return $result->isSuccess();
    }

    private function logMessage(QueryResultInterface $queryResult, int $offset, int $limit): void
    {
        if (PHP_SAPI !== self::PHP_SAPI_CLI) {
            return;
        }
        if ($queryResult->isSuccess()) {
            echo "Success importing from lines " . ($offset + 1) . " to " . ($offset + $limit) . ".\n";
        } else {
            echo "Error importing from lines " . ($offset + 1) . " to " . ($offset + $limit) . ".\n";
        }
    }
}
