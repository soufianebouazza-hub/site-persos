document.addEventListener('DOMContentLoaded',()=>{
 const form=document.querySelector('#login-form');if(!form)return;
 const status=document.querySelector('#login-status'),button=form.querySelector('[type=submit]');
 form.addEventListener('submit',async event=>{event.preventDefault();if(!EVPortal.configured())return;button.disabled=true;status.textContent='Connexion en cours…';try{const db=EVPortal.createClient();const {error}=await db.auth.signInWithPassword({email:form.elements.email.value.trim(),password:form.elements.password.value});if(error)throw new Error('Connexion impossible. Vérifiez vos identifiants.');const role=await db.rpc('ev_is_admin');if(role.error)throw new Error('Les droits du compte ne sont pas encore configurés.');location.href=role.data?'demandes-clients.html':'espace-client.html';}catch(error){status.textContent=error.message;button.disabled=false;}});
 if(!EVPortal.configured()){button.disabled=true;status.textContent='Les vrais comptes ne sont pas encore activés. Explorez les démonstrations ci-dessous.';}
});
