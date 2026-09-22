# Activation des espaces E-Vitrine

La démo est opérationnelle avec ?demo=1. Elle utilise uniquement des données fictives dans le navigateur (clé evitrine.workspace.demo.v1). Ce n'est pas un espace privé. Les messages du formulaire public ne sont PAS synchronisés avec la démo.

La page d'entrée du gestionnaire est pages/demandes-clients.html. Sans configuration elle affiche une explication, sans données. La page de connexion dirige les administrateurs vers les demandes, et les autres comptes vers leurs projets.

## Pour activer les vrais comptes
1. Créer un projet Supabase, sélectionner une région adaptée, et exécuter schema.sql dans un nouveau projet. Ce script n'a pas encore été exécuté ni testé sur un serveur.
2. Créer les utilisateurs depuis Supabase Auth et attribuer le compte du gestionnaire dans ev_admins depuis l'éditeur SQL uniquement. Associer chaque client à son auth_user_id dans ev_clients.
3. Renseigner l'URL et la clé PUBLIQUE dans client-config.js. Jamais la clé secrète/service_role.
4. Vérifier avec deux comptes clients et un administrateur que les accès croisés sont refusés avant toute donnée réelle. Les notes/tâches sont réservées à leur propriétaire administrateur.
5. Pour recevoir les demandes du formulaire public, ajouter un endpoint serveur avec validation, limitation de débit et protection anti-abus, puis relier le formulaire. Il n'est volontairement pas ouvert en écriture anonyme. Le formulaire actuel prépare toujours un e-mail.
6. Finaliser les informations RGPD (identité légale du responsable, hébergeur, durées de conservation, sous-traitants) avant mise en ligne. Le lieu Bruxelles seul ne suffit pas à compléter ces informations.

Source des règles d'accès : https://supabase.com/docs/guides/database/postgres/row-level-security
