/* Un adaptateur commun : démo locale explicite ou données protégées par Supabase. */
window.EVPortal = (() => {
    const storageKey = 'evitrine.workspace.demo.v1';
    const tables = {clients:'ev_clients',projects:'ev_projects',requests:'ev_requests',tasks:'ev_tasks',notes:'ev_notes',messages:'ev_messages'};
    const stages = ['À cadrer','En création','À valider','Livré'];
    const requestStages = ['Nouvelle','À recontacter','Devis envoyé','Gagnée','Classée'];
    const seed = () => ({
        clients:[{id:'demo-client',name:'Camille — entreprise exemple',email:'camille@example.com',phone:'',auth_user_id:null}],
        projects:[{id:'demo-project',client_id:'demo-client',title:'Site vitrine — projet exemple',stage:'En création',due_date:'',next_step:'Valider les textes de la page d’accueil',summary:'Présenter les services et faciliter la prise de contact.'}],
        requests:[{id:'demo-request',name:'Alex — demande exemple',email:'alex@example.com',need:'Un site une page pour présenter mon activité.',status:'Nouvelle'}],
        tasks:[{id:'demo-task',owner_id:'demo-admin',title:'Préparer les textes du projet exemple',due_date:'',done:false}],
        notes:[{id:'demo-note',owner_id:'demo-admin',title:'Mes idées pour E-Vitrine',body:'Tester le parcours client, puis organiser les prochains projets.'}],
        messages:[{id:'demo-message',project_id:'demo-project',sender_id:'demo-admin',body:'Bienvenue ! Les premiers éléments du projet sont en préparation.',created_at:'2026-09-21T09:00:00Z'}]
    });
    const configured = () => Boolean(window.CLIENT_PORTAL_CONFIG?.supabaseUrl && window.CLIENT_PORTAL_CONFIG?.supabasePublishableKey);
    const createClient = () => {
        if (!window.supabase) throw new Error('Le service de connexion est indisponible. Réessayez plus tard.');
        return window.supabase.createClient(window.CLIENT_PORTAL_CONFIG.supabaseUrl, window.CLIENT_PORTAL_CONFIG.supabasePublishableKey);
    };
    async function connect(demo, clientView) {
        if (demo) {
            let data;
            try {
                const raw = localStorage.getItem(storageKey);
                data = raw ? JSON.parse(raw) : seed();
                if (!data || !Object.keys(tables).every(key => Array.isArray(data[key]))) throw new Error('invalid');
            } catch {
                throw new Error('La sauvegarde de démonstration est inaccessible ou invalide. Vérifiez que le stockage du navigateur est autorisé. Aucune donnée n’a été effacée.');
            }
            const refreshLocal = () => {
                const raw = localStorage.getItem(storageKey);
                if (!raw) return;
                const saved = JSON.parse(raw);
                if (!saved || !Object.keys(tables).every(key => Array.isArray(saved[key]))) throw new Error("Sauvegarde locale invalide. Aucune donnée modifiée.");
                data = saved;
            };
            return {
                demo:true, admin:!clientView, user:{id:clientView?'demo-client-user':'demo-admin',email:clientView?'client de démonstration':'gestionnaire de démonstration'},
                async read() {
                    refreshLocal();
                    if (!clientView) return structuredClone(data);
                    const projects = data.projects.filter(p => p.client_id === 'demo-client');
                    return {clients:data.clients.filter(c=>c.id==='demo-client'),projects,requests:[],tasks:[],notes:[],messages:data.messages.filter(m=>projects.some(p=>p.id===m.project_id))};
                },
                async save(kind, values) {
                    if (!(kind in tables)) throw new Error('Rubrique inconnue.');
                    if (clientView && (kind!=='messages' || !data.projects.some(p=>p.id===values.project_id&&p.client_id==='demo-client'))) throw new Error('Action non autorisée dans cette vue.');
                    refreshLocal();
                    const draft = structuredClone(data);
                    const record = {...values,id:values.id || crypto.randomUUID()};
                    if (['tasks','notes'].includes(kind)) record.owner_id='demo-admin';
                    if (kind==='messages') {record.sender_id=clientView?'demo-client-user':'demo-admin';record.created_at=new Date().toISOString();}
                    const index=draft[kind].findIndex(row=>row.id===record.id);
                    if (index<0) { record.created_at = new Date().toISOString(); draft[kind].push(record); } else draft[kind][index]={...draft[kind][index],...record};
                    try {localStorage.setItem(storageKey,JSON.stringify(draft));} catch {throw new Error('Enregistrement impossible : le navigateur refuse le stockage ou manque de place. Votre saisie reste disponible.');}
                    data=draft;
                },
                async logout() {location.href='connexion.html';}
            };
        }
        if (!configured()) throw new Error('Les comptes ne sont pas encore activés. Utilisez la démonstration pour découvrir les espaces.');
        const db=createClient();
        const {data:auth,error:authError}=await db.auth.getUser();
        if (authError || !auth.user) {location.replace('connexion.html');return null;}
        const {data:admin,error:roleError}=await db.rpc('ev_is_admin');
        if (roleError) throw new Error('Les droits d’accès ne sont pas configurés. Contactez le gestionnaire.');
        if (!clientView && !admin) {location.replace('espace-client.html');return null;}
        db.auth.onAuthStateChange((event)=>{if(event==='SIGNED_OUT')location.replace('connexion.html');});
        return {
            demo:false,admin:Boolean(admin),user:auth.user,
            async read() {
                const data={};
                await Promise.all(Object.entries(tables).map(async([kind,table])=>{
                    if (clientView && ['requests','tasks','notes'].includes(kind)) {data[kind]=[];return;}
                    const {data:rows,error}=await db.from(table).select('*').order('created_at',{ascending:true});
                    if(error)throw new Error('Impossible de charger les données. Vérifiez la connexion et la configuration des accès.');
                    data[kind]=rows;
                }));
                return data;
            },
            async save(kind, values) {
                if (!(kind in tables))throw new Error('Rubrique inconnue.');
                const record={...values};
                if (['tasks','notes'].includes(kind))record.owner_id=auth.user.id;
                if (kind==='messages')record.sender_id=auth.user.id;
                for (const key of ['due_date','auth_user_id']) if (record[key]==='')record[key]=null;
                const id=record.id;delete record.id;delete record.created_at;
                const query=id?db.from(tables[kind]).update(record).eq('id',id):db.from(tables[kind]).insert(record);
                const {data:saved,error}=await query.select('id');
                if(error || !saved?.length)throw new Error('Enregistrement refusé ou connexion interrompue. Vérifiez vos droits et réessayez.');
            },
            async logout() {const {error}=await db.auth.signOut();if(error)throw new Error('Déconnexion impossible. Réessayez.');location.replace('connexion.html');}
        };
    }
    return {connect,configured,createClient,stages,requestStages};
})();
