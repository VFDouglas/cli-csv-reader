<?php

namespace App\Interface;

interface FileReaderInterface
{
    public function read();

    public function save(RepositoryInterface $repository): bool;
}
