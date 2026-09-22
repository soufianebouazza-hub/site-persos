<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Config,Database,Http,Security};
use App\Models\User;
final class SetupController {
    public static function handle(): void {
        if(Config::get('env')!=='local'||!in_array($_SERVER['REMOTE_ADDR']??'',['127.0.0.1','::1'],true))Http::json(['error'=>'Page introuvable.'],404);
        $file=Config::get('storage_path').'/activation.json';
        if(!is_file($file))Http::json(['error'=>'Activation indisponible ou déjà terminée.'],404);
        $lock=fopen(Config::get('storage_path').'/activation.lock','c');flock($lock,LOCK_EX);
        try {
            if(Database::query("SELECT id FROM users WHERE role='admin' LIMIT 1")->fetch())Http::redirect('/connexion');
            $error='';
            if($_SERVER['REQUEST_METHOD']==='POST'){
                $input=Http::input();Security::csrf($input);Security::rate('activation',$_SERVER['REMOTE_ADDR'],5,900);
                try {
                    $setup=json_decode(file_get_contents($file),true,512,JSON_THROW_ON_ERROR);
                    if(!hash_equals($setup['hash'],hash('sha256',Http::text($input,'token',100))))throw new \InvalidArgumentException('Code d’activation incorrect.');
                    $password=$input['password']??'';if(!is_string($password)||$password!==($input['confirmation']??null))throw new \InvalidArgumentException('Les mots de passe ne correspondent pas.');
                    User::create(Http::email($input),$password,'admin');
                    unlink($file);$note=Config::get('storage_path').'/ACTIVATION-LOCALE.txt';if(is_file($note))unlink($note);
                    Http::redirect('/connexion');
                }catch(\InvalidArgumentException $e){$error=$e->getMessage();}
            }
            require dirname(__DIR__).'/Views/site/activation.php';
        } finally {flock($lock,LOCK_UN);fclose($lock);}
    }
}
