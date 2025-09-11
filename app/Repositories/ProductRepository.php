<?php

namespace App\Repositories;

use App\Database\Database;
use App\DTO\InsertResultDTO;
use App\Interfaces\RepositoryInterface;

class ProductRepository implements RepositoryInterface
{
    private const string TABLE = 'products';

    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function save(array $data): InsertResultDTO
    {
        if (empty($data)) {
            return (new InsertResultDTO())->setErrors(['No data provided.']);
        }

        $errors = [];
        foreach ($data as $key => $line) {
            if (empty($line['gtin'])) {
                $errors[] = "GTIN not informed at line " . ($key + 1) . ".";
                continue;
            }
            if (empty($line['title'])) {
                $errors[] = "Title not informed at line " . ($key + 1) . ".";
                continue;
            }
            if (empty($line['picture'])) {
                $errors[] = "Picture not informed at line " . ($key + 1) . ".";
                continue;
            }
            if (empty($line['description'])) {
                $errors[] = "Description not informed at line " . ($key + 1) . ".";
                continue;
            }
            if (empty($line['price'])) {
                $errors[] = "Price not informed at line " . ($key + 1) . ".";
                continue;
            }
            if (empty($line['stock'])) {
                $errors[] = "Stock not informed at line " . ($key + 1) . ".";
            }
        }
        if (!empty($errors)) {
            return (new InsertResultDTO())
                ->setErrors($errors);
        }

        return $this->database->batchInsert(
            self::TABLE,
            $data,
            ['title', 'picture', 'description', 'price', 'stock', 'updated_at']
        );
    }
}
