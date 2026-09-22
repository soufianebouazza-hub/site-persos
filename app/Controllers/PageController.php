<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http,Security};
use App\Models\Workspace;
final class PageController {
    public static function show(string $view): void {
        if(in_array($view,['espace-personnel','demandes-clients','espace-client'],true)){$user=Security::user();if(!$user)Http::redirect('/connexion');if($view!=='espace-client'&&$user['role']!=='admin')Http::redirect('/espace-client');}
        $flash=$_SESSION['flash']??null;unset($_SESSION['flash']);
        require dirname(__DIR__).'/Views/site/'.$view.'.php';
    }
    public static function contact(): never {
        $input=Http::input();Security::csrf($input);
        Security::rate('contact',$_SERVER['REMOTE_ADDR']??'',5,900);
        try {
            if(Http::text($input,'website',100,false)!=='')throw new \InvalidArgumentException('Demande refusée.');
            $nonce=Http::text($input,'nonce',64);
            if(!hash_equals($_SESSION['contact_nonce']??'', $nonce))throw new \InvalidArgumentException('Ce formulaire a déjà été envoyé ou a expiré.');
            $offer=Http::text($input,'offer',32);if(!in_array($offer,['a-definir','essentiel','professionnel','sur-mesure'],true))throw new \InvalidArgumentException('Offre invalide.');
            Workspace::save('requests',['name'=>Http::text($input,'company_name'),'email'=>Http::email($input),'phone'=>Http::text($input,'phone',80,false),'need'=>'Offre : '.$offer."\n".Http::text($input,'message',5500),'status'=>'Nouvelle'],['role'=>'admin','id'=>null]);
            unset($_SESSION['contact_nonce']);$_SESSION['flash']=['ok'=>true,'text'=>'Votre demande a bien été enregistrée. E-Vitrine peut maintenant la consulter dans son espace privé.'];
        } catch(\InvalidArgumentException $e){$_SESSION['flash']=['ok'=>false,'text'=>$e->getMessage(),'old'=>$input];}
        Http::redirect('/contact#demande');
    }
}
