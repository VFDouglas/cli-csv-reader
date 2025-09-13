<?php

namespace App\Interface;

interface RepositoryInterface
{
    public function save(array $data): QueryResultInterface;
}
