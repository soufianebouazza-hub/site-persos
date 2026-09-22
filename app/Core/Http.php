<?php
declare(strict_types=1);
namespace App\Core;
final class Http {
    public static function json(array $data, int $status=200): never { http_response_code($status);header('Content-Type: application/json; charset=utf-8');echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);exit; }
    public static function redirect(string $path): never { header('Location: '.$path,true,303);exit; }
    public static function input(): array { if ((int)($_SERVER['CONTENT_LENGTH']??0)>20000)self::json(['error'=>'Requête trop volumineuse.'],413); if(str_contains($_SERVER['CONTENT_TYPE']??'','application/json')) { $body=file_get_contents('php://input',false,null,0,20001);if(strlen($body)>20000)self::json(['error'=>'Requête trop volumineuse.'],413);$data=json_decode($body,true);if(!is_array($data))self::json(['error'=>'Format invalide.'],400);return $data;} return $_POST; }
    public static function text(array $data,string $key,int $max=250,bool $required=true): string { $v=$data[$key]??'';if(!is_string($v))throw new \InvalidArgumentException('Champ invalide : '.$key);$v=trim($v);if(($required&&$v==='')||strlen($v)>$max||!preg_match('//u',$v))throw new \InvalidArgumentException('Vérifiez le champ : '.$key);return $v; }
    public static function email(array $data,string $key='email'): string { $v=strtolower(self::text($data,$key,254));if(!filter_var($v,FILTER_VALIDATE_EMAIL))throw new \InvalidArgumentException('Adresse e-mail invalide.');return $v; }
    public static function escape(mixed $v): string { return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }
}
