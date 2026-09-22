<?php
// Outil CLI pour créer/réinitialiser/désactiver les accès, jamais accessible par URL.
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/app/bootstrap.php';
use App\Core\{Database as DB,Http};
use App\Models\User;
try {
    $action=$argv[1]??'';$email=Http::email(['email'=>$argv[2]??'']);
    if($action==='create'){
        $role=$argv[3]??'client';$clientId=$argv[4]??null;
        if($role==='client'&&(!$clientId||!DB::query("SELECT id FROM records WHERE id=? AND kind='clients' AND assigned_user_id IS NULL",[$clientId])->fetch()))throw new InvalidArgumentException('Indiquez une fiche client existante sans compte.');
        $password=rtrim(stream_get_contents(STDIN,1024),"\r\n");
        DB::get()->beginTransaction();$id=User::create($email,$password,$role);
        if($role==='client')DB::query('UPDATE records SET assigned_user_id=? WHERE id=?',[$id,$clientId]);
        DB::get()->commit();echo "Compte créé.\n";
    }elseif($action==='disable'){
        DB::query('UPDATE users SET active=0 WHERE email=?',[$email]);echo "Compte désactivé si existant ; sessions refusées immédiatement.\n";
    }elseif($action==='password'){
        $password=rtrim(stream_get_contents(STDIN,1024),"\r\n");if(strlen($password)<14||strlen($password)>72||str_contains($password,"\0"))throw new InvalidArgumentException('Mot de passe : 14 à 72 octets.');
        DB::query('UPDATE users SET password_hash=? WHERE email=?',[password_hash($password,PASSWORD_BCRYPT,['cost'=>12]),$email]);echo "Mot de passe actualisé si compte existant.\n";
    }else throw new InvalidArgumentException('Actions : create, disable, password.');
}catch(Throwable $e){if(DB::get()->inTransaction())DB::get()->rollBack();fwrite(STDERR,"Opération refusée. Vérifiez les arguments et les doublons.\n");exit(1);}
