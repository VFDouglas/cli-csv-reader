<?php

namespace App\FileReader;

use App\DTO\FileReaderConfigDTO;
use App\Exception\MissingFileFieldException;
use App\Exception\FileDoesNotExistException;
use App\Interface\FileReaderInterface;
use App\Interface\QueryResultInterface;
use App\Interface\RepositoryInterface;
use Generator;

class CsvReader implements FileReaderInterface
{
    private const string PHP_SAPI_CLI = 'cli';

    public function __construct(private readonly FileReaderConfigDTO $config)
    {
    }

    /**
     * @throws FileDoesNotExistException
     * @throws MissingFileFieldException
     */
    public function read(): Generator
    {
        if (!file_exists($this->config->getFilePath())) {
            throw new FileDoesNotExistException('File does not exist: ' . $this->config->getFilePath());
        }
        $handle = fopen($this->config->getFilePath(), 'r');

        $headers = fgetcsv(
            $handle,
            0,
            $this->config->getSeparator(),
            $this->config->getEnclosure(),
            $this->config->getEscape()
        );
        if ($headers === false) {
            fclose($handle);

            throw new MissingFileFieldException("File " . basename($this->config->getFilePath()) . " has no headers");
        }

        while (
            ($row = fgetcsv(
                $handle,
                0,
                $this->config->getSeparator(),
                $this->config->getEnclosure(),
                $this->config->getEscape()
            )) !== false
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
     * @throws FileDoesNotExistException|MissingFileFieldException
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
