<?php

require_once __DIR__ . '/vendor/autoload.php';


$host = env('DB_HOST');   // or 'mysql' if using Docker Compose service name
var_dump($host);
$db   = env('DB_NAME');
$user = env('DB_USER');
$pass = env('DB_PASSWORD');
$charset = env('DB_CHARSET');

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch results as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // use native prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "✅ Database connection successful!";
} catch (Throwable $e) {
    echo "❌ Connection failed: " . $e->getTraceAsString() . "\n";
}
