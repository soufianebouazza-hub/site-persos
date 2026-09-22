// Migration ponctuelle des anciens modèles statiques ; les vues PHP deviennent la source.
const fs=require('node:fs'),path=require('node:path');
const root=path.resolve(__dirname,'..');
const read=p=>fs.readFileSync(path.join(root,p),'utf8');
const write=(p,s)=>{fs.mkdirSync(path.dirname(path.join(root,p)),{recursive:true});fs.writeFileSync(path.join(root,p),s)};
const routes={'index':'/','produits':'/offres','services':'/services','contact':'/contact','connexion':'/connexion','rgpd':'/rgpd','espace-personnel':'/espace','espace-client':'/espace-client','demandes-clients':'/demandes'};
function convert(s,base){return s.replace(/\s*<script[^>]+(?:cdn\.jsdelivr|client-config\.js)[^>]*><\/script>/g,'').replace(/\s*<link[^>]+https:\/\/[^>]*>/g,'').replace(/(href|src)="([^"]+)"/g,(all,attr,url)=>{if(/^(?:https?:|mailto:|tel:|#|\?)/.test(url))return all;const u=new URL(url,'http://local/'+base),p=u.pathname;let target=p;if(p.startsWith('/concepts/'))target=p.replace(/\.html$/,'.php');else if(p.endsWith('.html'))target=routes[path.basename(p,'.html')]||p;else if(/\.(css|js)$/.test(p))target='/assets/'+path.basename(p);else if(p.startsWith('/img/'))target='/assets'+p;return `${attr}="${target}${u.search.replace(/\?demo=1/g,'')}${u.hash}"`;});}
for(const name of Object.keys(routes)){
 let s=convert(read(name==='index'?'index.html':`pages/${name}.html`),name==='index'?'index.html':`pages/${name}.html`);
 if(name==='connexion'){
 s=s.replace(/<section class="spaces-preview">[\s\S]*?<\/section>/,'<section class="spaces-preview"><h2>Vos espaces privés</h2><p>Gestion des demandes, projets clients et organisation personnelle : connectez-vous avec vos accès.</p><?php if (App\\Core\\Config::get("env")==="local" && !App\\Core\\Database::query("SELECT id FROM users WHERE role=\'admin\' LIMIT 1")->fetch()): ?><p><a href="/activation">Créer mon compte gestionnaire →</a></p><?php endif; ?></section>');
 s=s.replace('id="login-form"','id="login-form" method="post" action="/api/login"').replace('<h2>Accéder à mon espace</h2>','<input type="hidden" name="_csrf" value="<?= App\\Core\\Http::escape($_SESSION[\'csrf\']) ?>"><h2>Accéder à mon espace</h2><noscript>Activez JavaScript pour accéder à votre espace.</noscript>');
 }
 if(name==='contact'){
 s=s.replace('id="project-form"','id="project-form" method="post" action="/contact"');
 s=s.replace('<div class="form-group"><label for="offer">',`<?php $_SESSION['contact_nonce'] ??= bin2hex(random_bytes(24)); $old=$flash['old']??[]; ?>
 <input type="hidden" name="_csrf" value="<?= App\\Core\\Http::escape($_SESSION['csrf']) ?>"><input type="hidden" name="nonce" value="<?= App\\Core\\Http::escape($_SESSION['contact_nonce']) ?>">
 <div hidden aria-hidden="true"><label>Ne pas remplir<input name="website" tabindex="-1" autocomplete="off"></label></div>
 <?php if($flash): ?><p role="status" class="portal-status"><?= App\\Core\\Http::escape($flash['text']) ?></p><?php endif; ?>
 <div class="form-group"><label for="offer">`);
 for(const field of ['company_name','email','phone'])s=s.replace(`name="${field}"`,`name="${field}" maxlength="${field==='email'?254:250}" value="<?= App\\Core\\Http::escape($old['${field}']??'') ?>"`);
 s=s.replace('name="message" rows="6"','name="message" maxlength="5500" rows="6"').replace('required></textarea>',"required><?= App\\Core\\Http::escape($old['message']??'') ?></textarea>");
 s=s.replace('Le message est préparé ici puis envoyé par votre messagerie.','Votre demande est enregistrée dans notre base et accessible au gestionnaire.').replace('Préparer mon message','Envoyer ma demande').replace('Préparez votre message, puis ouvrez votre messagerie pour l’envoyer.','Votre demande sera transmise directement à E-Vitrine.').replace(/<div id="message-result"[\s\S]*?<\/div><\/form>/,'</form>').replace('Préparez votre demande via le formulaire, puis envoyez-la avec votre messagerie.','Envoyez votre demande directement via le formulaire.');
 }
 s=s.replace(/<a class="button" href="\?demo=1">[^<]*<\/a>/g,'');
 write(`app/Views/site/${name}.php`,s);
}
for(const dir of fs.readdirSync(path.join(root,'concepts'))){
 const folder=path.join(root,'concepts',dir);if(!fs.statSync(folder).isDirectory())continue;
 for(const file of fs.readdirSync(folder)){
  if(file.endsWith('.html'))write(`app/Views/concepts/${dir}/${file.replace('.html','.php')}`,convert(read(`concepts/${dir}/${file}`),`concepts/${dir}/${file}`));
  else if(file==='img'||/\.(css|js)$/.test(file))fs.cpSync(path.join(folder,file),path.join(root,'public/concepts',dir,file),{recursive:true});
 }
}
for(const file of ['style.css','espace.css','espace.js','script.js'])write('public/assets/'+file,read(file));
fs.cpSync(path.join(root,'img'),path.join(root,'public/assets/img'),{recursive:true});
let js=read('public/assets/script.js');js=js.slice(0,js.indexOf("    form.addEventListener('submit', event => {"))+'});\n';write('public/assets/script.js',js);
js=read('public/assets/espace.js').replace("demo=new URLSearchParams(location.search).get('demo')==='1'","demo=false").replace('La réception automatique nécessite de connecter le formulaire public.','Les demandes du formulaire public apparaissent automatiquement ici.');write('public/assets/espace.js',js);
console.log('Vues PHP et ressources créées.');
