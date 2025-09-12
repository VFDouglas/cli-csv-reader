<?php

namespace App\Interfaces;

interface FileReaderInterface
{
    public function getFilePath(): string;

    public function read();

    public function save(RepositoryInterface $repository): bool;
}
