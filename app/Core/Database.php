<?php
declare(strict_types=1);
namespace App\Core;
use PDO;
final class Database {
    private static ?PDO $db = null;
    public static function get(): PDO {
        if (self::$db === null) {
            self::$db = new PDO(Config::get('dsn'), Config::get('db_user'), Config::get('db_password'), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
            if (self::$db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') { self::$db->exec('PRAGMA foreign_keys=ON'); self::$db->exec('PRAGMA busy_timeout=5000'); }
            else { self::$db->exec("SET SESSION sql_mode='STRICT_ALL_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ZERO_DATE,NO_ZERO_IN_DATE'"); }
        }
        return self::$db;
    }
    public static function query(string $sql, array $params=[]): \PDOStatement { $stmt=self::get()->prepare($sql);$stmt->execute($params);return $stmt; }
}
