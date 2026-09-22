<?php
declare(strict_types=1);
require dirname(__DIR__).'/app/bootstrap.php';
use App\Core\Http;
use App\Controllers\{PageController,AuthController,WorkspaceController};
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);$method=$_SERVER['REQUEST_METHOD'];
$pages=['/'=>'index','/offres'=>'produits','/services'=>'services','/contact'=>'contact','/connexion'=>'connexion','/rgpd'=>'rgpd','/espace'=>'espace-personnel','/demandes'=>'demandes-clients','/espace-client'=>'espace-client'];
try {
    if($path==='/activation'&&in_array($method,['GET','POST'],true)){App\Controllers\SetupController::handle();exit;}
    if($method==='GET'){
        foreach($pages as $route=>$name){$legacy=$name==='index'?'/index.html':'/pages/'.$name.'.html';if($path===$legacy)Http::redirect($route.(empty($_SERVER['QUERY_STRING'])?'':'?'.$_SERVER['QUERY_STRING']));}
        if(isset($pages[$path])){PageController::show($pages[$path]);exit;}
        if($path==='/api/session')AuthController::session();
        if($path==='/api/data')WorkspaceController::read();
        if(preg_match('~^/concepts/(atelier-bois|table-saison|studio-eclat|snack-pause)/(index|services|a-propos|contact)\.php$~D',$path,$m)){$file=dirname(__DIR__).'/app/Views/concepts/'.$m[1].'/'.$m[2].'.php';if(is_file($file)){require $file;exit;}}
        if(preg_match('~^/concepts/(atelier-bois|table-saison|studio-eclat|snack-pause)/(index|services|a-propos|contact)\.html$~D',$path))Http::redirect(str_replace('.html','.php',$path));
    }
    if($method==='POST'){
        if($path==='/contact')PageController::contact();
        if($path==='/api/login')AuthController::login();
        if($path==='/api/logout')AuthController::logout();
        if($path==='/api/save')WorkspaceController::save();
    }
    Http::json(['error'=>'Page introuvable.'],404);
}catch(\InvalidArgumentException $e){Http::json(['error'=>$e->getMessage()],422);}catch(\Throwable $e){error_log('E-Vitrine: '.get_class($e).' code '.$e->getCode());Http::json(['error'=>'Service temporairement indisponible. Réessayez plus tard.'],500);}
