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
        $result = new InsertResultDTO();

        return $result
            ->setErrors($stmt->errorInfo())
            ->setLastInsertId($this->connection->lastInsertId())
            ->setSuccess($success)
            ->setAffectedRows($stmt->rowCount());
    }

    public function executeMultiple(string $sql, array $paramsList = []): InsertResultDTO
    {
        $stmt = $this->connection->prepare($sql);
        foreach ($paramsList as $params) {
            $this->bindMultipleParams($stmt, $params);
        }

        $success = $stmt->execute();
        $result = new InsertResultDTO();

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

    private function bindMultipleParams(PDOStatement $statement, array $params): void
    {
        foreach ($params as $paramKey => $paramValue) {
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
    }
}
