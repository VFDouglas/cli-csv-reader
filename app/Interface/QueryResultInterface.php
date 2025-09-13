<?php

namespace App\Interface;

interface QueryResultInterface
{
    public function isSuccess(): bool;

    public function getAffectedRows(): ?int;

    public function getErrors(): array;
}
