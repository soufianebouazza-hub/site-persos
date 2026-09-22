window.EVPortal=(()=>{
 const stages=['À cadrer','En création','À valider','Livré'],requestStages=['Nouvelle','À recontacter','Devis envoyé','Gagnée','Classée'];
 async function request(url,options={}){const r=await fetch(url,{credentials:'same-origin',...options});const data=await r.json();if(!r.ok)throw new Error(data.error||'Service indisponible.');return data;}
 async function connect(){const session=await request('/api/session');if(!session.user){location.replace('/connexion');return null;}return {demo:false,admin:session.user.role==='admin',user:session.user,read:()=>request('/api/data'),save:(kind,values)=>request('/api/save',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':session.csrf},body:JSON.stringify({kind,values})}),logout:async()=>{await request('/api/logout',{method:'POST',headers:{'X-CSRF-Token':session.csrf}});location.replace('/connexion');}};}
 return {connect,stages,requestStages};
})();
