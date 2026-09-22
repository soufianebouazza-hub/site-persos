<?php
declare(strict_types=1);
namespace App\Core;
final class Config { public static array $values = []; public static function get(string $key): mixed { return self::$values[$key] ?? null; } }
