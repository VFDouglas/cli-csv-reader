<?php

namespace App\Interfaces;

use App\DTO\InsertResultDTO;

interface RepositoryInterface
{
    public function save(array $data): InsertResultDTO;
}
