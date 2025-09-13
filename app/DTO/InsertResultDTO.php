<?php

namespace App\DTO;

use App\Interface\QueryResultInterface;

class InsertResultDTO implements QueryResultInterface
{
    private array $errors = [];

    private ?int $lastInsertId = null;

    private bool $success = false;

    private int $affectedRows = 0;

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): InsertResultDTO
    {
        $this->errors = $errors;

        return $this;
    }

    public function getLastInsertId(): ?int
    {
        return $this->lastInsertId;
    }

    public function setLastInsertId(?int $lastInsertId): InsertResultDTO
    {
        $this->lastInsertId = $lastInsertId;

        return $this;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): InsertResultDTO
    {
        $this->success = $success;

        return $this;
    }

    public function getAffectedRows(): int
    {
        return $this->affectedRows;
    }

    public function setAffectedRows(int $rowCount): InsertResultDTO
    {
        $this->affectedRows = $rowCount;

        return $this;
    }
}
