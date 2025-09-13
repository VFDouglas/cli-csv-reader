<?php

require_once __DIR__ . '/vendor/autoload.php';

return [
    'paths'         => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds'      => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments'  => [
        'default_migration_table' => 'phinxlog',
        'default_environment'     => 'development',
        'production'              => [
            'adapter' => env('DB_CONNECTION', 'mysql'),
            'host'    => env('DB_HOST', 'mysql'),
            'name'    => env('DB_NAME', 'store'),
            'user'    => env('DB_USERNAME', 'root'),
            'pass'    => env('DB_PASSWORD', 'root'),
            'port'    => env('DB_PORT', 3306),
            'charset' => env('DB_CHARSET', 'utf8'),
        ],
        'development'             => [
            'adapter' => env('DB_CONNECTION', 'mysql'),
            'host'    => env('DB_HOST', 'mysql'),
            'name'    => env('DB_NAME', 'store'),
            'user'    => env('DB_USERNAME', 'root'),
            'pass'    => env('DB_PASSWORD', 'root'),
            'port'    => env('DB_PORT', 3306),
            'charset' => env('DB_CHARSET', 'utf8'),
        ],
        'testing'                 => [
            'adapter' => env('DB_CONNECTION', 'mysql'),
            'host'    => env('DB_HOST', 'mysql'),
            'name'    => env('DB_NAME_TESTS'),
            'user'    => env('DB_USERNAME', 'root'),
            'pass'    => env('DB_PASSWORD', 'root'),
            'port'    => env('DB_PORT', 3306),
            'charset' => env('DB_CHARSET', 'utf8'),
        ]
    ],
    'version_order' => 'creation'
];
