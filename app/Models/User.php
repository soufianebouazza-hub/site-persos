<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
use App\Core\Http;
final class User {
    public static function create(string $email,string $password,string $role): string {
        $email=Http::email(['email'=>$email]);
        if(!in_array($role,['admin','client'],true)||strlen($password)<14||strlen($password)>72||str_contains($password,"\0"))throw new \InvalidArgumentException('Mot de passe : 14 à 72 octets ; rôle admin ou client.');
        $id=bin2hex(random_bytes(16));
        Database::query('INSERT INTO users (id,email,password_hash,role,created_at) VALUES (?,?,?,?,?)',[$id,$email,password_hash($password,PASSWORD_BCRYPT,['cost'=>12]),$role,gmdate('c')]);
        return $id;
    }
    public static function authenticate(string $email,string $password): ?array {
        $user=Database::query('SELECT * FROM users WHERE email=? AND active=1',[$email])->fetch();
        $hash=$user['password_hash']??'$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        $ok=password_verify($password,$hash);
        return $ok&&$user?$user:null;
    }
}
