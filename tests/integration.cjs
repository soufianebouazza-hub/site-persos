const fs=require('node:fs'),path=require('node:path'),assert=require('node:assert/strict'),crypto=require('node:crypto'),{spawn,execFileSync}=require('node:child_process');
const root=path.resolve(__dirname,'..'),php=path.join(root,'.runtime/php/php.exe');
const state=path.join(__dirname,'.state',Date.now().toString());fs.mkdirSync(state,{recursive:true});
const mysql=true,dbName='evitrine_test_'+crypto.randomBytes(6).toString('hex');
if(mysql)execFileSync(php,['tests/mysql.php','create',dbName],{cwd:root});
const env={...process.env,APP_ENV:'local',DB_DSN:mysql?'mysql:host=127.0.0.1;port=3306;dbname='+dbName+';charset=utf8mb4':'sqlite:'+path.join(state,'test.sqlite'),DB_USER:mysql?'root':'',DB_PASSWORD:'',EV_STORAGE_PATH:state};
execFileSync('C:/wamp64/bin/mysql/mysql8.4.7/bin/mysql.exe',['-u','root','-h','127.0.0.1'],{cwd:root,input:'USE '+dbName+'; SET SESSION default_storage_engine=InnoDB;\n'+fs.readFileSync(path.join(root,'database/schema.sql'),'utf8')});
const password=crypto.randomBytes(20).toString('hex');
const sql=value=>value==null?'NULL':"CONVERT(0x"+Buffer.from(String(value),'utf8').toString('hex')+" USING utf8mb4) COLLATE utf8mb4_unicode_ci";
const runSql=statement=>execFileSync('C:/wamp64/bin/mysql/mysql8.4.7/bin/mysql.exe',['-u','root','-h','127.0.0.1',dbName],{input:statement,cwd:root});
function seed(){
 const hash=execFileSync(php,['-r','echo password_hash(stream_get_contents(STDIN), PASSWORD_BCRYPT, ["cost"=>12]);'],{input:password,encoding:'utf8'});
 const id=()=>crypto.randomBytes(16).toString('hex');const a=id(),b=id(),u=id(),v=id(),c=id(),d=id(),p=id(),q=id(),n=id();
 let statement='';
 for(const [key,email,role] of [[a,'admin@example.test','admin'],[b,'second@example.test','admin'],[u,'client@example.test','client'],[v,'other@example.test','client']])statement+='INSERT INTO users(id,email,password_hash,role,created_at) VALUES('+[key,email,hash,role,new Date().toISOString()].map(sql).join(',')+');';
 const rows=[
 [c,'clients',a,u,null,null,{name:'Client A',email:'client@example.test',phone:''}],
 [d,'clients',a,v,null,null,{name:'Client B',email:'other@example.test',phone:''}],
 [p,'projects',a,null,c,null,{title:'Projet A',client_id:c,stage:'À cadrer',due_date:'',next_step:'Texte',summary:'Test'}],
 [q,'projects',a,null,d,null,{title:'Projet B',client_id:d,stage:'À cadrer',due_date:'',next_step:'Texte',summary:'Test'}],
 [n,'notes',b,null,null,null,{title:'Privé',body:'Secret second admin'}]];
 for(const row of rows){row[6]=JSON.stringify(row[6]);statement+='INSERT INTO records(id,kind,owner_id,assigned_user_id,client_id,project_id,payload,created_at) VALUES('+[...row,new Date().toISOString()].map(sql).join(',')+');';}
 runSql(statement);return {project:p,otherProject:q,otherNote:n};
}
const activation=crypto.randomBytes(24).toString('hex');fs.writeFileSync(path.join(state,'activation.json'),JSON.stringify({hash:crypto.createHash('sha256').update(activation).digest('hex')}));
let fixture;
const server=spawn(php,['-S','127.0.0.1:4180','-t','public','public/router.php'],{cwd:root,env,stdio:'ignore'});
const base='http://127.0.0.1:4180';let checks=0;
function client(){let cookie='';return {get cookie(){return cookie},async call(url,body,csrf,form=false){const h={};if(cookie)h.Cookie=cookie;if(body!==undefined)h['Content-Type']=form?'application/x-www-form-urlencoded':'application/json';if(csrf)h['X-CSRF-Token']=csrf;const r=await fetch(base+url,{method:body===undefined?'GET':'POST',headers:h,body:body===undefined?undefined:form?new URLSearchParams(body):JSON.stringify(body),redirect:'manual'});const set=r.headers.get('set-cookie');if(set)cookie=set.split(';')[0];const text=await r.text();let data;try{data=JSON.parse(text)}catch{}return {status:r.status,headers:r.headers,text,data};}}}
function ok(value,label){assert.ok(value,label);checks++;console.log('OK '+label)}
async function login(c,email){const s=await c.call('/api/session');const before=c.cookie;const r=await c.call('/api/login',{email,password},s.data.csrf);ok(r.status===200,'connexion '+email);ok(c.cookie!==before,'rotation session');return (await c.call('/api/session')).data.csrf;}
(async()=>{
 try{
  for(let n=0;n<60;n++){try{await fetch(base);break}catch{await new Promise(r=>setTimeout(r,100));}}
  const anon=client(),admin=client(),customer=client();
  const setup=await anon.call('/activation'),setupCsrf=setup.text.match(/name="_csrf" value="([^"]+)"/)[1];
  ok((await anon.call('/activation',{_csrf:setupCsrf,token:'wrong',email:'owner@example.test',password,confirmation:password},undefined,true)).text.includes('incorrect'),'activation protégée par code');
  ok((await anon.call('/activation',{_csrf:setupCsrf,token:activation,email:'owner@example.test',password,confirmation:password},undefined,true)).status===303,'activation du premier gestionnaire');
  ok((await anon.call('/activation')).status===404&&!fs.existsSync(path.join(state,'activation.json')),'activation à usage unique');
  fixture=seed();
  ok((await anon.call('/api/data')).status===401,'données privées refusées sans session');
  ok((await anon.call('/demandes?demo=1')).status===303,'aucun contournement par le mode démo');
  for(const p of ['/config/local.php','/storage/activation.json','/app/bootstrap.php','/.git/config','/assets/../../config/local.php'])ok((await anon.call(p)).status===404,'fichier privé inaccessible '+p);
  const ac=await login(admin,'admin@example.test'),cc=await login(customer,'client@example.test');
  const ad=(await admin.call('/api/data')).data,cd=(await customer.call('/api/data')).data;
  ok(ad.projects.length===2,'gestionnaire voit ses projets');ok(!ad.notes.some(n=>n.id===fixture.otherNote),'notes autre administrateur isolées');
  ok(cd.projects.length===1&&cd.projects[0].id===fixture.project&&cd.clients.length===1&&cd.requests.length===0,'client limité à ses données');
  ok((await customer.call('/api/save',{kind:'messages',values:{project_id:fixture.otherProject,body:'intrusion'}},cc)).status===403,'message sur projet tiers refusé');
  ok((await customer.call('/api/save',{kind:'requests',values:{name:'intrusion'}},cc)).status===403,'client ne modifie pas les demandes');
  ok((await admin.call('/api/save',{kind:'notes',values:{id:fixture.otherNote,title:'intrusion',body:'x'}},ac)).status===403,'modification note autre admin refusée');
  ok((await admin.call('/api/save',{kind:'tasks',values:{title:'test'}})).status===419,'protection CSRF');
  ok((await admin.call('/api/save',{kind:'tasks',values:{title:'test',owner_id:'x'}},ac)).status===422,'propriétaire forgé refusé');
  const payload='<img src=x onerror=alert(1)>\' OR 1=1 --';
  ok((await customer.call('/api/save',{kind:'messages',values:{project_id:fixture.project,body:payload}},cc)).status===200,'message valide stocké comme texte');
  ok((await customer.call('/api/data')).data.messages[0].body===payload,'contenu conservé sans interprétation SQL');
  const contact=await anon.call('/contact');const csrf=contact.text.match(/name="_csrf" value="([^"]+)"/)[1],nonce=contact.text.match(/name="nonce" value="([^"]+)"/)[1];
  const form={_csrf:csrf,nonce,company_name:'Test visiteur',email:'visitor@example.test',phone:'',offer:'essentiel',message:payload,website:''};
  ok((await anon.call('/contact',form,undefined,true)).status===303,'formulaire contact accepté');
  ok((await anon.call('/contact')).text.includes('bien été enregistrée'),'confirmation après enregistrement');
  await anon.call('/contact',form,undefined,true);
  const requests=(await admin.call('/api/data')).data.requests;ok(requests.length===1&&requests[0].need.includes(payload),'demande reçue une seule fois dans espace admin');
  const out=await admin.call('/api/logout',{},ac);ok(out.status===200&&(await admin.call('/api/data')).status===401,'déconnexion effective');
  const changedHash=execFileSync(php,['-r','echo password_hash(stream_get_contents(STDIN), PASSWORD_BCRYPT, ["cost"=>12]);'],{input:crypto.randomBytes(20).toString('hex'),encoding:'utf8'});runSql('UPDATE users SET password_hash='+sql(changedHash)+' WHERE email='+sql('client@example.test')+';');ok((await customer.call('/api/data')).status===401,'changement mot de passe révoque les sessions');
  let rate;for(let i=0;i<8;i++)rate=await anon.call('/contact',form,undefined,true);ok(rate.status===429,'limitation anti-abus formulaire');
  const fail=client();const sc=(await fail.call('/api/session')).data.csrf;for(let i=0;i<11;i++)rate=await fail.call('/api/login',{email:'unknown@example.test',password:'invalid-password'},sc);ok(rate.status===429,'limitation tentatives connexion');
  const headers=(await anon.call('/')).headers;ok(headers.get('content-security-policy').includes("script-src 'self'")&&headers.get('x-content-type-options')==='nosniff','en-têtes de protection');
  console.log(`${checks} vérifications réussies.`);
 }finally{server.kill();if(mysql)execFileSync(php,['tests/mysql.php','drop',dbName],{cwd:root});}
})().catch(e=>{console.error(e);process.exitCode=1});
