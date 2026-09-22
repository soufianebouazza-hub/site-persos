<?php
return [
    'env' => getenv('APP_ENV') ?: 'production',
    'dsn' => getenv('DB_DSN') ?: 'sqlite:' . dirname(__DIR__) . '/storage/evitrine.sqlite',
    'db_user' => getenv('DB_USER') ?: '',
    'db_password' => getenv('DB_PASSWORD') ?: '',
    'storage_path' => dirname(__DIR__) . '/storage',
];
