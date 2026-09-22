document.addEventListener('DOMContentLoaded',()=>{
 const form=document.querySelector('#login-form');if(!form)return;
 const status=document.querySelector('#login-status'),button=form.querySelector('[type=submit]');
 form.addEventListener('submit',async event=>{event.preventDefault();button.disabled=true;status.textContent='Connexion en cours…';try{const response=await fetch('/api/login',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.fromEntries(new FormData(form)))});const data=await response.json();if(!response.ok)throw new Error(data.error||'Connexion impossible.');location.assign(data.redirect);}catch(error){status.textContent=error.message;button.disabled=false;}});
});
