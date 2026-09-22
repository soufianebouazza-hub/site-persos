document.querySelector('#call-demo').addEventListener('click',()=>{document.querySelector('#call-status').textContent='Démonstration : aucun numéro réel n’est configuré. Sur votre site, ce bouton appellera directement votre snack.';});

document.querySelector('#load-map')?.addEventListener('click',event=>{const map=document.querySelector('iframe[data-src]');map.src=map.dataset.src;map.hidden=false;event.currentTarget.hidden=true;});
