<?php

namespace App\Interfaces;

interface FileReaderInterface
{
    public function read(string $path);
    public function save(string $path, RepositoryInterface $repository): bool;
}
