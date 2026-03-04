# 🎮 TFT Collection — Plateforme de gestion de jeux vidéo

Application web dynamique en PHP sur le thème de **Teamfight Tactics (TFT)**, permettant de gérer une collection de jeux vidéo avec un système d'authentification, de rôles et de succès.

## 🚀 Installation

### Prérequis
- PHP 8.1+
- SQLite3 (extension PHP `pdo_sqlite`)

### Étapes

1. **Cloner le projet**
   ```bash
   git clone <url-du-repo>
   cd PHP
   ```

2. **Configurer l'environnement**
   ```bash
   cp .env.example .env
   ```
   Remplissez le fichier `.env` avec vos identifiants.

3. **Initialiser la base de données**
   ```bash
   php database/seed.php
   ```

4. **Lancer le serveur**
   ```bash
   php -S localhost:8000
   ```

5. Ouvrir [http://localhost:8000](http://localhost:8000)

## 🔑 Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Admin | *(défini dans .env)* | *(défini dans .env)* |
| Joueur 1 | *(défini dans .env)* | *(défini dans .env)* |
| Joueur 2 | *(défini dans .env)* | *(défini dans .env)* |

## 📋 Fonctionnalités

- **Authentification** : inscription, connexion, déconnexion
- **Rôles** : utilisateur standard et administrateur
- **Catalogue de jeux** : liste, détail, ajout, modification, suppression (CRUD admin)
- **Collection personnelle** : ajouter/retirer des jeux, modifier le temps de jeu
- **Succès** : système de succès liés à chaque jeu avec raretés (common → legendary)
- **Niveaux** : niveaux associés à chaque jeu avec difficulté (easy → extreme)
- **Espace admin** : gestion des utilisateurs et des jeux
- **Profil utilisateur** : informations du compte, statistiques, suppression de compte
- **Sécurité** : mots de passe hashés (bcrypt), protection CSRF, échappement XSS, validation serveur

## 🛠️ Technologies

- **Backend** : PHP 8.1+ (natif, sans framework)
- **Base de données** : SQLite
- **Frontend** : Tailwind CSS (CDN), CSS custom, JavaScript vanilla
- **Polices** : Cinzel (titres), Inter (texte)
