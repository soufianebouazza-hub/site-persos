<?php
declare(strict_types=1);
spl_autoload_register(function(string $class): void {
    if (str_starts_with($class, 'App\\')) {
        $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($file)) require $file;
    }
});
$config = require dirname(__DIR__) . '/config/app.php';
if (is_file(dirname(__DIR__) . '/config/local.php')) $config = array_replace($config, require dirname(__DIR__) . '/config/local.php');
foreach (['env'=>'APP_ENV','dsn'=>'DB_DSN','db_user'=>'DB_USER','db_password'=>'DB_PASSWORD','storage_path'=>'EV_STORAGE_PATH'] as $key=>$variable) { if (getenv($variable)!==false) $config[$key]=getenv($variable); }
App\Core\Config::$values = $config;
date_default_timezone_set('Europe/Brussels');
if (PHP_SAPI !== 'cli') {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    App\Core\Security::start();
}
