# 📚 Git Cheat Sheet

## 🔗 Première connexion au projet

```bash
git clone git@github.com:USERNAME/nom-du-projet.git
cd nom-du-projet
```

## 📊 Vérifier l'état

```bash
git status
```

## ➕ Ajouter les fichiers

```bash
git add .
```

## 💾 Créer un commit

```bash
git commit -m "message"
```

## ⬆️ Envoyer sur GitHub

```bash
git push
```

Premier push d'une nouvelle branche :

```bash
git push -u origin nom-de-la-branche
```

## ⬇️ Récupérer les modifications

```bash
git pull
```

## 🌿 Branches

Créer une branche :

```bash
git switch -c nom-de-la-branche
```

Changer de branche :

```bash
git switch nom-de-la-branche
```

Voir les branches :

```bash
git branch
```

Supprimer une branche locale :

```bash
git branch -d nom-de-la-branche
```

## 🌐 Remote GitHub

Voir le dépôt GitHub connecté :

```bash
git remote -v
```

## ↩️ Annuler

Annuler les modifications d'un fichier non ajouté :

```bash
git restore nom-du-fichier
```

Retirer un fichier de `git add` :

```bash
git restore --staged nom-du-fichier
```

## 🔄 Workflow quotidien

```bash
git pull
git status
git add .
git commit -m "description de la modification"
git push
```

## ⚡ Raccourcis à retenir

```text
pull    = récupérer
add     = préparer
commit  = sauvegarder
push    = envoyer
branch  = branche
switch  = changer de branche
clone   = copier un projet
status  = vérifier
```

