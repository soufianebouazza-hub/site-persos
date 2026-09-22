<?php
declare(strict_types=1);
namespace App\Core;
final class Security {
    public static function start(): void {
        $secure=($_SERVER['HTTPS']??'')==='on';
        if(Config::get('env')!=='local'&&!$secure){http_response_code(503);exit('HTTPS doit être configuré sur le serveur avant utilisation.');}
        header('X-Content-Type-Options: nosniff');header('X-Frame-Options: DENY');header('Referrer-Policy: strict-origin-when-cross-origin');header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; connect-src 'self'; frame-src https://www.openstreetmap.org; object-src 'none'; base-uri 'none'; form-action 'self'; frame-ancestors 'none'");
        if($secure)header('Strict-Transport-Security: max-age=31536000');
        header('Cache-Control: no-store');
        ini_set('session.use_strict_mode','1');ini_set('session.use_only_cookies','1');ini_set('session.use_trans_sid','0');
        session_name('EVSESSION');session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax']);session_start();
        if(isset($_SESSION['user_id'])&&(time()-($_SESSION['last_seen']??0)>1800||time()-($_SESSION['signed_at']??0)>28800)){$_SESSION=[];session_regenerate_id(true);}
        if(isset($_SESSION['user_id']))$_SESSION['last_seen']=time();
        $_SESSION['csrf']??=bin2hex(random_bytes(32));
    }
    public static function csrf(array $data): void { $token=$_SERVER['HTTP_X_CSRF_TOKEN']??$data['_csrf']??'';if(!is_string($token)||!hash_equals($_SESSION['csrf'],$token))Http::json(['error'=>'Session expirée ou formulaire invalide. Rechargez la page.'],419);if(($_SERVER['HTTP_SEC_FETCH_SITE']??'')==='cross-site')Http::json(['error'=>'Origine refusée.'],403); }
    public static function user(): ?array {
        $id=$_SESSION['user_id']??null;if(!$id)return null;
        $user=Database::query('SELECT id,email,role,password_hash FROM users WHERE id=? AND active=1',[$id])->fetch();
        if(!$user||!hash_equals(hash('sha256',$user['password_hash']),$_SESSION['credential_version']??'')){unset($_SESSION['user_id']);return null;}
        unset($user['password_hash']);return $user;
    }
    public static function requireUser(bool $admin=false): array { $user=self::user();if(!$user)Http::json(['error'=>'Connexion requise.'],401);if($admin&&$user['role']!=='admin')Http::json(['error'=>'Accès réservé au gestionnaire.'],403);return $user; }
    public static function rate(string $scope,string $identity,int $max,int $window): void {
        $key=hash('sha256',$scope.'|'.$identity);$now=time();$db=Database::get();$driver=$db->getAttribute(\PDO::ATTR_DRIVER_NAME);
        if($driver==='sqlite')Database::query('INSERT INTO rate_limits (bucket,hits,expires) VALUES (?,1,?) ON CONFLICT(bucket) DO UPDATE SET hits=CASE WHEN expires<=? THEN 1 ELSE hits+1 END, expires=CASE WHEN expires<=? THEN excluded.expires ELSE expires END',[$key,$now+$window,$now,$now]);
        else Database::query('INSERT INTO rate_limits (bucket,hits,expires) VALUES (?,1,?) ON DUPLICATE KEY UPDATE hits=IF(expires<=?,1,hits+1),expires=IF(expires<=?,VALUES(expires),expires)',[$key,$now+$window,$now,$now]);
        $row=Database::query('SELECT hits,expires FROM rate_limits WHERE bucket=?',[$key])->fetch();
        Database::query('DELETE FROM rate_limits WHERE expires<?',[$now-86400]);
        if((int)$row['hits']>$max){header('Retry-After: '.max(1,(int)$row['expires']-$now));Http::json(['error'=>'Trop de tentatives. Réessayez plus tard.'],429);}
    }
}
