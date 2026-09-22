<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
final class Schema {
    public static function migrate(): void {
        if(PHP_SAPI!=='cli')throw new \RuntimeException('Installation uniquement en ligne de commande.');
        $engine=Database::get()->getAttribute(\PDO::ATTR_DRIVER_NAME)==='mysql'?' ENGINE=InnoDB':'';
        Database::query('CREATE TABLE IF NOT EXISTS users (id VARCHAR(32) PRIMARY KEY,email VARCHAR(254) NOT NULL UNIQUE,password_hash VARCHAR(255) NOT NULL,role VARCHAR(12) NOT NULL,active INTEGER NOT NULL DEFAULT 1,created_at VARCHAR(30) NOT NULL)'.$engine);
        Database::query('CREATE TABLE IF NOT EXISTS records (id VARCHAR(32) PRIMARY KEY,kind VARCHAR(16) NOT NULL,owner_id VARCHAR(32),assigned_user_id VARCHAR(32),client_id VARCHAR(32),project_id VARCHAR(32),payload TEXT NOT NULL,created_at VARCHAR(30) NOT NULL,FOREIGN KEY(owner_id) REFERENCES users(id),FOREIGN KEY(assigned_user_id) REFERENCES users(id),FOREIGN KEY(client_id) REFERENCES records(id),FOREIGN KEY(project_id) REFERENCES records(id))'.$engine);
        Database::query('CREATE TABLE IF NOT EXISTS rate_limits (bucket VARCHAR(64) PRIMARY KEY,hits INTEGER NOT NULL,expires BIGINT NOT NULL)'.$engine);
    }
}
