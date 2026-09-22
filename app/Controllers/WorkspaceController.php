<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http,Security};
use App\Models\Workspace;
final class WorkspaceController {
    public static function read(): never {Http::json(Workspace::read(Security::requireUser()));}
    public static function save(): never {$user=Security::requireUser();$input=Http::input();Security::csrf($input);Security::rate('write',$user['id'],120,60);$kind=Http::text($input,'kind',16);if(!is_array($input['values']??null))throw new \InvalidArgumentException('Données invalides.');Http::json(['id'=>Workspace::save($kind,$input['values'],$user)]);}
}
