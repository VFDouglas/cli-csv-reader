<?php

namespace App\Database;

use App\Exceptions\DatabaseConnectionFailedException;
use PDO;
use PDOException;

class Connection
{
    private static ?Connection $instance = null;

    private PDO $connection;

    /**
     * @throws DatabaseConnectionFailedException
     */
    private function __construct()
    {
        $dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8mb4", env('DB_HOST'), env('DB_NAME'));

        try {
            $this->connection = new PDO(
                $dsn,
                env('DB_USERNAME'),
                env('DB_PASSWORD'),
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            throw new DatabaseConnectionFailedException('Database connection failed: ' . $e->getMessage());
        }
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
