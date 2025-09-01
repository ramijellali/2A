# Guide d'Installation - Système de Gestion d'Enquêtes

## Installation Rapide

### 1. Prérequis
- PHP 8.0+ avec extensions PDO et PDO_MySQL
- MySQL 5.7+ ou MariaDB 10.3+
- Serveur web Apache avec mod_rewrite

### 2. Installation de la base de données

1. Créer une base de données MySQL :
`sql
CREATE DATABASE enquetes_satisfaction CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
`

2. Importer le schéma de base de données :
`ash
mysql -u username -p enquetes_satisfaction < sql/database.sql
`

3. Modifier la configuration dans config/database.php :
`php
private const HOST = 'localhost';
private const DB_NAME = 'enquetes_satisfaction';
private const USERNAME = 'votre_username';
private const PASSWORD = 'votre_password';
`

### 3. Configuration du serveur web

#### Option A: Apache (Recommandé)
Configurer un VirtualHost :
`pache
<VirtualHost *:80>
    DocumentRoot "C:/chemin/vers/enquetes-satisfaction/public"
    ServerName enquetes.local
    
    <Directory "C:/chemin/vers/enquetes-satisfaction/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
`

Ajouter dans votre fichier hosts (C:\Windows\System32\drivers\etc\hosts) :
`
127.0.0.1 enquetes.local
`

#### Option B: Serveur de développement PHP
`ash
cd public
php -S localhost:8000
`

### 4. Test de l'installation

Accéder à : http://enquetes.local ou http://localhost:8000

## Comptes par défaut

### Administrateur
- Email: admin@enquetes.com
- Mot de passe: password

### Agent
- Email: marie.dupont@enquetes.com
- Mot de passe: password

### Client
- Email: sophie.moreau@client.com
- Mot de passe: password

## Première utilisation

### 1. Connexion administrateur
1. Se connecter avec le compte admin
2. Accéder au tableau de bord
3. Vérifier les statistiques

### 2. Créer une campagne (Admin)
1. Aller dans "Campagnes" > "Créer"
2. Remplir le formulaire :
   - Titre : "Test Satisfaction 2025"
   - Description : "Campagne de test"
   - Date début : Aujourd'hui
   - Date fin : Dans 1 mois
3. Sauvegarder

### 3. Créer une enquête (Agent)
1. Se connecter avec un compte agent
2. Aller dans "Enquêtes" > "Créer"
3. Sélectionner la campagne créée
4. Ajouter le titre et la description
5. Ajouter des questions :
   - Question texte libre
   - Question à choix multiples
   - Question de notation
   - Question Oui/Non

### 4. Assigner des clients
1. Dans l'enquête créée, onglet "Clients"
2. Sélectionner les clients cibles
3. Sauvegarder l'assignation
4. Activer l'enquête

### 5. Répondre à l'enquête (Client)
1. Se connecter avec un compte client
2. Visualiser les enquêtes disponibles
3. Répondre aux questions
4. Soumettre les réponses

## Structure du projet

`
enquetes-satisfaction/
 app/
    Controllers/          # Logique métier
       BaseController.php
       AuthController.php
       AdminController.php
       AgentController.php
       ClientController.php
    Models/              # Accès aux données
       BaseModel.php
       Utilisateur.php
       Campagne.php
       Enquete.php
       Question.php
       Reponse.php
    Views/               # Interface utilisateur
        auth/            # Authentification
        admin/           # Interface admin
        agent/           # Interface agent
        client/          # Interface client
        backoffice/      # Template back-office
        frontoffice/     # Template front-office
        errors/          # Pages d'erreur
 config/                  # Configuration
    database.php         # Configuration BDD
    config.php          # Configuration générale
    utils.php           # Fonctions utilitaires
    bootstrap.php       # Initialisation
 public/                  # Accessible publiquement
    css/                # Feuilles de style
    js/                 # Scripts JavaScript
    index.php           # Point d'entrée
 sql/                     # Scripts de base de données
    database.sql        # Schéma et données de test
 .htaccess               # Configuration Apache
 README.md               # Documentation
`

## Fonctionnalités principales

### Gestion des utilisateurs
- Authentification sécurisée
- Gestion des rôles (Admin, Agent, Client)
- CRUD complet des utilisateurs

### Gestion des campagnes
- Création de campagnes ciblées
- Suivi du statut des campagnes
- Statistiques détaillées

### Gestion des enquêtes
- Création d'enquêtes dans les campagnes
- Questions de différents types
- Assignation de clients cibles
- Suivi des réponses

### Interface utilisateur
- Templates responsives
- Interface différenciée par rôle
- Validation côté client et serveur
- Messages flash informatifs

## Sécurité

- Protection CSRF sur tous les formulaires
- Validation et sanitisation des données
- Hash sécurisé des mots de passe
- Contrôle d'accès basé sur les rôles
- Headers de sécurité HTTP

## Dépannage

### Erreur de connexion à la base de données
Vérifier :
- Les paramètres dans config/database.php
- Que MySQL est démarré
- Que la base de données existe

### Page blanche
Vérifier :
- Les logs PHP dans logs/php_errors.log
- Que APP_DEBUG = true dans config/config.php
- Les permissions sur les dossiers

### Erreur 404 sur les liens
Vérifier :
- Que mod_rewrite est activé
- Que le fichier .htaccess est présent
- La configuration du VirtualHost

### Problèmes de session
Vérifier :
- Les permissions du dossier de session PHP
- La configuration de session dans php.ini

## Support

Pour obtenir de l'aide :
1. Consulter la documentation complète dans README.md
2. Vérifier les logs d'erreur
3. Contacter l'équipe de développement

## Développement

Pour contribuer au projet :
1. Respecter l'architecture MVC
2. Suivre les standards PSR-12
3. Ajouter des commentaires PHPDoc
4. Tester les fonctionnalités avant commit

---
Système développé pour le module "Projet Technologies Web"
Version 1.0.0 - Septembre 2025
