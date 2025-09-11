<?php

namespace App\Database;

use PDO;

class Connection
{
    private static ?Connection $instance = null;

    private PDO $connection;

    private function __construct()
    {
        $this->connection = new PDO(
            "mysql:host=" . env('DB_HOST') . ";dbname=" . env('DB_NAME'),
            env('DB_USERNAME'),
            env('DB_PASSWORD')
        );
    }

    public static function getInstance(): ?Connection
    {
        if (self::$instance === null) {
            self::$instance = new Connection();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
