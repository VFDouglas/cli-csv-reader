<?php

namespace App\Database;

use App\DTO\InsertResultDTO;
use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private PDO $connection;

    public function __construct(PDO $connection = null)
    {
        $this->connection = $connection ?? Connection::getInstance()->getConnection();
    }

    public function execute(string $sql, array $params = []): InsertResultDTO
    {
        $result = new InsertResultDTO();

        try {
            $stmt = $this->connection->prepare($sql);
            $this->bindMultipleParams($stmt, $params);

            $success = $stmt->execute();

            return $result
                ->setErrors($stmt->errorInfo())
                ->setLastInsertId($this->connection->lastInsertId())
                ->setSuccess($success)
                ->setAffectedRows($stmt->rowCount());
        } catch (PDOException $e) {
            return $result->setErrors([$e->getCode(), $e->getMessage(), 'PDOException'])->setSuccess(false);
        }
    }

    public function batchInsert(string $table, array $rows, array $updateColumns = []): InsertResultDTO
    {
        $result = new InsertResultDTO();

        if (empty($rows)) {
            return $result->setErrors([null, 'No rows to insert', null])->setSuccess(false);
        }

        $placeholders = [];
        $params       = [];
        $columns      = array_keys($rows[0]);

        foreach ($rows as $rowKey => $row) {
            $rowPlaceholders = [];
            foreach ($columns as $column) {
                $paramKey          = "{$column}_$rowKey";
                $rowPlaceholders[] = ":$paramKey";
                $params[$paramKey] = $row[$column];
            }
            $placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
        }

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES %s",
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders),
        );
        if (!empty($updateColumns)) {
            $updates = implode(', ', array_map(fn($col) => "`$col` = VALUES(`$col`)", $updateColumns));
            $sql     .= " ON DUPLICATE KEY UPDATE $updates";
        }

        try {
            $stmt = $this->connection->prepare($sql);
            foreach ($params as $paramKey => $paramValue) {
                $this->bindParamType($stmt, $paramKey, $paramValue);
            }
            $success = $stmt->execute();

            return $result
                ->setErrors($stmt->errorInfo())
                ->setLastInsertId($this->connection->lastInsertId())
                ->setSuccess($success)
                ->setAffectedRows($stmt->rowCount());
        } catch (PDOException $e) {
            return $result->setErrors([$e->getCode(), $e->getMessage(), 'PDOException'])->setSuccess(false);
        }
    }

    public function fetch(string $sql, array $params = []): array
    {
        $stmt = $this->connection->prepare($sql);
        $this->bindMultipleParams($stmt, $params);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function bindParamType(PDOStatement $statement, string $paramKey, mixed $paramValue): void
    {
        switch (gettype($paramValue)) {
            case 'integer':
                $statement->bindValue(":$paramKey", $paramValue, PDO::PARAM_INT);
                break;
            case 'boolean':
                $statement->bindValue(":$paramKey", $paramValue, PDO::PARAM_BOOL);
                break;
            case 'NULL':
                $statement->bindValue(":$paramKey", $paramValue, PDO::PARAM_NULL);
                break;
            default:
                $statement->bindValue(":$paramKey", $paramValue);
                break;
        }
    }

    private function bindMultipleParams(PDOStatement $statement, array $params): void
    {
        foreach ($params as $paramKey => $paramValue) {
            $this->bindParamType($statement, $paramKey, $paramValue);
        }
    }
}
