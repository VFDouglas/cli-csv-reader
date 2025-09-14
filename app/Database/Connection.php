<?php

namespace App\Database;

use App\Exception\DatabaseConnectionFailedException;
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
        $dbName = defined('PHPUNIT_COMPOSER_INSTALL') ? env('DB_NAME_TESTS') : env('DB_NAME');
        $dsn    = sprintf(
            "mysql:host=%s;dbname=%s;port=%s;charset=%s",
            env('DB_HOST'),
            $dbName,
            env('DB_PORT'),
            env('DB_CHARSET')
        );

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
