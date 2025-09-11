<?php

namespace App\Database;

use App\DTO\InsertResultDTO;
use PDO;
use PDOStatement;

class Database
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Connection::getInstance()->getConnection();
    }

    public function execute(string $sql, array $params = []): InsertResultDTO
    {
        $stmt = $this->connection->prepare($sql);
        $this->bindMultipleParams($stmt, $params);

        $success = $stmt->execute();
        $result  = new InsertResultDTO();

        return $result
            ->setErrors($stmt->errorInfo())
            ->setLastInsertId($this->connection->lastInsertId())
            ->setSuccess($success)
            ->setAffectedRows($stmt->rowCount());
    }

    public function batchInsert(string $table, array $rows, array $updateColumns = []): InsertResultDTO
    {
        $result = new InsertResultDTO();

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

        if (empty($updateColumns)) {
            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                $table,
                implode(', ', $rows),
                implode(', ', $placeholders),
            );
        } else {
            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES %s ON DUPLICATE KEY UPDATE %s",
                $table,
                implode(', ', $columns),
                implode(', ', $placeholders),
                implode(', ', array_map(fn($col) => "$col = VALUES($col)", $updateColumns)),
            );
        }
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
    }

    public function fetch(string $sql, array $params = []): array
    {
        $stmt = $this->connection->prepare($sql);
        $this->bindMultipleParams($stmt, $params);
        $stmt->execute($params);

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
