<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http,Security};
use App\Models\User;
final class AuthController {
    public static function session(): never {Http::json(['csrf'=>$_SESSION['csrf'],'user'=>Security::user()]);}
    public static function login(): never {
        $data=Http::input();Security::csrf($data);
        Security::rate('login-ip',$_SERVER['REMOTE_ADDR']??'',20,900);
        $email=Http::email($data);Security::rate('login-email',$email,10,900);
        $password=$data['password']??'';
        if(!is_string($password)||strlen($password)>72||str_contains($password,"\0"))Http::json(['error'=>'Identifiants incorrects.'],401);
        $user=User::authenticate($email,$password);
        if(!$user)Http::json(['error'=>'Identifiants incorrects.'],401);
        $_SESSION=[];session_regenerate_id(true);$_SESSION=['user_id'=>$user['id'],'csrf'=>bin2hex(random_bytes(32)),'signed_at'=>time(),'last_seen'=>time(),'credential_version'=>hash('sha256',$user['password_hash'])];
        Http::json(['redirect'=>$user['role']==='admin'?'/demandes':'/espace-client']);
    }
    public static function logout(): never {Security::csrf(Http::input());$_SESSION=[];session_regenerate_id(true);Http::json(['ok'=>true]);}
}
