<?php

namespace App\Repositories;

use App\Database\Database;
use App\DTO\InsertResultDTO;
use App\Interfaces\RepositoryInterface;

class ProductRepository implements RepositoryInterface
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function save(array $data): InsertResultDTO
    {
        $sql = '
            INSERT INTO products (
                gtin, language, title, picture, description, price, stock
            ) VALUES (
                :gtin, :language, :title, :picture, :description, :price, :stock
            ) ON DUPLICATE KEY UPDATE
            title = :title, picture = :picture, description = :description, price = :price, stock = :stock
        ';

        return match (count($data)) {
            0       => (new InsertResultDTO())->setSuccess(false)->setErrors(['Nothing to insert']),
            1       => $this->database->execute($sql, $data),
            default => $this->database->executeMultiple($sql, $data),
        };
    }
}
