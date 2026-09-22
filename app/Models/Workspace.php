<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database as DB;
use App\Core\Http;
final class Workspace {
    public const KINDS=['clients','projects','requests','tasks','notes','messages'];
    public const STAGES=['À cadrer','En création','À valider','Livré'];
    public const STATUSES=['Nouvelle','À recontacter','Devis envoyé','Gagnée','Classée'];
    public static function read(array $user): array {
        $data=array_fill_keys(self::KINDS,[]);
        if($user['role']==='admin')$rows=DB::query("SELECT * FROM records WHERE kind NOT IN ('tasks','notes') OR owner_id=? ORDER BY created_at,id",[$user['id']])->fetchAll();
        else $rows=DB::query("SELECT * FROM records WHERE (kind='clients' AND assigned_user_id=?) OR (kind='projects' AND client_id IN (SELECT id FROM records WHERE kind='clients' AND assigned_user_id=?)) OR (kind='messages' AND project_id IN (SELECT id FROM records WHERE kind='projects' AND client_id IN (SELECT id FROM records WHERE kind='clients' AND assigned_user_id=?))) ORDER BY created_at,id",[$user['id'],$user['id'],$user['id']])->fetchAll();
        foreach($rows as $row){$v=json_decode($row['payload'],true,512,JSON_THROW_ON_ERROR);$v['id']=$row['id'];$v['created_at']=$row['created_at'];if($row['kind']==='messages')$v['sender_id']=$row['owner_id'];$data[$row['kind']][]=$v;}
        return $data;
    }
    public static function save(string $kind,array $input,array $user): string {
        if(!in_array($kind,self::KINDS,true))throw new \InvalidArgumentException('Rubrique inconnue.');
        $id=Http::text($input,'id',32,false);$existing=null;
        if($id){$existing=DB::query('SELECT * FROM records WHERE id=? AND kind=?',[$id,$kind])->fetch();if(!$existing)Http::json(['error'=>'Élément introuvable.'],404);}
        if($user['role']!=='admin'&&($kind!=='messages'||$existing))Http::json(['error'=>'Action refusée.'],403);
        if($existing&&in_array($kind,['tasks','notes'],true)&&$existing['owner_id']!==$user['id'])Http::json(['error'=>'Action refusée.'],403);
        $fields=match($kind){'clients'=>['name','email','phone'],'projects'=>['title','client_id','stage','due_date','next_step','summary'],'requests'=>['name','email','phone','need','status'],'tasks'=>['title','due_date','done'],'notes'=>['title','body'],'messages'=>['project_id','body']};
        foreach(array_keys($input) as $key)if($key!=='id'&&!in_array($key,$fields,true))throw new \InvalidArgumentException('Champ non autorisé.');
        $v=$existing?json_decode($existing['payload'],true,512,JSON_THROW_ON_ERROR):[];
        foreach($fields as $field){if(array_key_exists($field,$input))$v[$field]=$input[$field];}
        foreach($fields as $field){if($field==='done'){if(isset($v[$field])&&!is_bool($v[$field]))throw new \InvalidArgumentException('État invalide.');$v[$field]=$v[$field]??false;continue;}$v[$field]=Http::text($v,$field,in_array($field,['body','need','summary'],true)?6000:250,!in_array($field,['phone','due_date','next_step','summary'],true));}
        if(isset($v['email']))$v['email']=Http::email($v);
        if(isset($v['stage'])&&!in_array($v['stage'],self::STAGES,true))throw new \InvalidArgumentException('Étape invalide.');
        if(isset($v['status'])&&!in_array($v['status'],self::STATUSES,true))throw new \InvalidArgumentException('Statut invalide.');
        if(!empty($v['due_date'])){$date=\DateTimeImmutable::createFromFormat('!Y-m-d',$v['due_date']);if(!$date||$date->format('Y-m-d')!==$v['due_date'])throw new \InvalidArgumentException('Date invalide.');}
        if($kind==='projects'&&!DB::query("SELECT id FROM records WHERE id=? AND kind='clients'",[$v['client_id']])->fetch())throw new \InvalidArgumentException('Client inconnu.');
        if($kind==='messages'){$project=DB::query("SELECT p.id,c.assigned_user_id FROM records p JOIN records c ON c.id=p.client_id WHERE p.id=? AND p.kind='projects' AND c.kind='clients'",[$v['project_id']])->fetch();if(!$project||($user['role']!=='admin'&&$project['assigned_user_id']!==$user['id']))Http::json(['error'=>'Projet non accessible.'],403);}
        $json=json_encode($v,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
        if($existing)DB::query('UPDATE records SET payload=?,client_id=?,project_id=? WHERE id=?',[$json,$v['client_id']??null,$v['project_id']??null,$id]);
        else {$id=bin2hex(random_bytes(16));DB::query('INSERT INTO records (id,kind,owner_id,client_id,project_id,payload,created_at) VALUES (?,?,?,?,?,?,?)',[$id,$kind,$user['id']??null,$v['client_id']??null,$v['project_id']??null,$json,gmdate('c')]);}
        return $id;
    }
}
