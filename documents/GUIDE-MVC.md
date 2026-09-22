# E-Vitrine — Guide PHP MVC et Wamp

## Démarrer et créer son accès

1. Démarrer **WampServer** depuis le raccourci sur le Bureau.
2. Ouvrir **http://127.0.0.1:4173/**. Live Server de VS Code ne doit plus servir le projet : il n’exécute pas PHP.
3. Pour la première connexion, ouvrir `storage/ACTIVATION-LOCALE.txt` dans le projet.
4. Aller sur **http://127.0.0.1:4173/activation**, copier le code à usage unique et choisir son e-mail et son mot de passe (14 caractères minimum, 72 octets maximum).
5. Se connecter sur `/connexion` pour arriver dans `/demandes`.

L’activation fonctionne seulement depuis cet ordinateur en environnement local, tant qu’aucun gestionnaire n’existe. Le code est supprimé après création du compte. Aucun mot de passe administrateur par défaut n’est fourni.

## Comprendre le MVC

Le navigateur demande une page à `public/index.php`. Ce fichier choisit une route. Le **contrôleur** vérifie les droits et traite l’action. Le **modèle** lit ou enregistre les données. La **vue** affiche le résultat.

| Dossier | Fonction |
| --- | --- |
| `app/Models` | Données et règles métier : utilisateurs, clients, demandes, projets |
| `app/Controllers` | Traitement des formulaires, connexion et vérification des accès |
| `app/Views/site` | Pages PHP E-Vitrine conservant le design |
| `app/Views/concepts` | Les quatre concepts, un dossier par site |
| `app/Core` | Base PDO, sessions, validation et protections |
| `public` | Seul dossier accessible à Apache : point d’entrée, scripts, styles, images |
| `config/local.php` | Configuration MySQL privée, exclue de Git |
| `storage` | Fichiers privés nécessaires à l’application |
| `database` | Schéma SQL des tables users, records et rate_limits |
| `bin` | Outils de ligne de commande pour Wamp et les comptes |
| `tests` | Vérifications avec des données fictives isolées |
| `archive-static` | Ancien HTML et prototype Supabase conservés, non utilisés |

Exemple concret : `/contact` appelle `PageController`, qui valide la saisie et le jeton CSRF, puis appelle `Workspace` pour enregistrer une demande. `/demandes` vérifie le rôle gestionnaire avant d’afficher la vue ; son API ne retourne les données qu’après une nouvelle vérification de session.

Modifier les vues dans `app/Views` et les ressources dans `public`. Ne pas modifier l’archive ni relancer ses outils de migration. Les anciennes URL `/pages/*.html` redirigent vers les nouvelles routes PHP.

## Les trois espaces

- **Demandes** : réception directe des formulaires, recherche, filtres, statut et lien pour répondre avec votre messagerie. Aucun e-mail automatique n’est envoyé par Wamp.
- **Activité et organisation** : fiches clients, projets, avancement, tâches et notes. Les notes et tâches appartiennent à chaque gestionnaire.
- **Espace client** : uniquement les projets attribués au client et leurs échanges. Chaque compte doit être associé à sa fiche.

Pour créer un compte client, créer d’abord sa fiche dans le carnet. Sa référence apparaît sous ses coordonnées. Dans PowerShell, depuis le dossier du projet :

```powershell
.\bin\compte.ps1 -Action create -Email 'client@exemple.be' -Role client -ClientId 'REFERENCE_DE_LA_FICHE'
```

Le mot de passe est demandé avec une saisie masquée et ne figure pas dans la commande. Transmettre les accès au client par un canal approprié. Pour modifier le mot de passe ou désactiver un accès :

```powershell
.\bin\compte.ps1 -Action password -Email 'client@exemple.be'
.\bin\compte.ps1 -Action disable -Email 'client@exemple.be'
```

Ces actions invalident les sessions existantes au prochain accès. Il n’y a pas encore de récupération automatique par e-mail.

## Configuration Wamp installée

- Apache : **127.0.0.1:4173**, DocumentRoot dirigé vers `public`, accès local uniquement.
- MySQL 8.4.7 : **127.0.0.1:3306**, base `evitrine`, tables **InnoDB**.
- Compte applicatif `evitrine_app` : uniquement SELECT, INSERT, UPDATE et DELETE sur `evitrine`. Le site n’utilise pas root.
- MySQL et MariaDB écoutent seulement sur **127.0.0.1**. MariaDB est fourni avec Wamp ; E-Vitrine utilise MySQL.
- PHP Apache : version configurée dans Wamp, 8.3.28 lors de l’installation. PHP portable 8.4.25 dans `.runtime/php` pour les outils et les tests.
- Virtual host : `C:/wamp64/bin/apache/apache2.4.65/conf/extra/httpd-vhosts.conf`. Une sauvegarde `.before-evitrine.bak` a été conservée.
- Journaux : `C:/wamp64/logs/evitrine-error.log` et `evitrine-access.log`.

Si le dossier du projet change, modifier le DocumentRoot et redémarrer Apache. Ne jamais exposer la racine du projet ou publier `config/local.php`, `.runtime`, `storage` ou les archives.

## Protections et limites

Requêtes préparées, validation serveur, hachage bcrypt coût 12, cookies HttpOnly et SameSite=Lax, renouvellement de session à la connexion, jetons CSRF, droits par rôle, isolation des clients, limitation des tentatives, échappement HTML et CSP sont implémentés. La session expire après 30 minutes d’inactivité ou 8 heures au maximum.

L’environnement local fonctionne en HTTP sur cet ordinateur. En mode `production`, le code refuse de fonctionner sans HTTPS et utilise des cookies Secure. Un proxy HTTPS doit être configuré côté serveur ; un en-tête de proxy fourni par le visiteur n’est pas accepté comme preuve de HTTPS.

Ce travail ne constitue pas un audit indépendant ni une garantie absolue de sécurité. Wamp reste un environnement local. Avant publication : versions maintenues et mises à jour, hébergement HTTPS correctement configuré, identifiants dédiés, sauvegardes testées et permissions de fichiers adaptées. Ne pas publier Wamp ou phpMyAdmin sur Internet.

La page RGPD décrit les traitements actuels. Le nom légal derrière E-Vitrine, l’adresse professionnelle, l’hébergeur et les durées de conservation restent à compléter. Aucune purge automatique des demandes/projets n’est programmée. Confirmer aussi le fonctionnement de `info@e-vitrine.com`. La carte OpenStreetMap du snack ne se charge qu’après un clic.

## Tests et installation sur un autre environnement

```powershell
# Tests MySQL Wamp dans une base isolée créée puis supprimée
node tests/integration.cjs

# Vérifie que le mode production exige HTTPS
node tests/production.cjs

# Pages et ressources sous Apache, fichiers privés inaccessibles
node tests/routes.cjs
```

Les tests MySQL utilisent le compte root local initial de Wamp uniquement pour créer une base `evitrine_test_*`. Si son mot de passe change, adapter l’outil de test ; ne pas accorder de droits supplémentaires au compte applicatif.

Pour une nouvelle base, importer `database/schema.sql` avec le client MySQL ou phpMyAdmin, après avoir choisi InnoDB comme moteur de session. Utiliser un compte de migration disposant de CREATE ; le compte web installé n’a volontairement pas ce droit. `APP_ENV`, `DB_DSN`, `DB_USER` et `DB_PASSWORD` permettent une configuration par environnement. Le code d’activation existant est réservé au poste local actuel ; créer un administrateur via l’outil CLI pour un autre déploiement. Les tests utilisent directement le client MySQL fourni par Wamp pour importer le schéma.

Références : [hachage PHP](https://www.php.net/manual/en/function.password-hash.php), [virtual hosts Apache](https://httpd.apache.org/docs/2.4/vhosts/examples.html).
