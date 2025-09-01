# Système de Gestion d'Enquêtes de Satisfaction

Une application web complète développée en PHP 8 pour la gestion d'enquêtes de satisfaction client avec architecture MVC, système multi-rôles et interface responsive.

##  Démarrage Rapide

### Installation en 5 minutes

1. **Cloner le projet** (ou télécharger les fichiers)
2. **Configurer la base de données** :
   `ash
   mysql -u root -p -e "CREATE DATABASE enquetes_satisfaction;"
   mysql -u root -p enquetes_satisfaction < sql/database.sql
   `
3. **Modifier config/database.php** avec vos paramètres MySQL
4. **Démarrer le serveur** :
   `ash
   cd public && php -S localhost:8000
   `
5. **Accéder à l'application** : http://localhost:8000

### Comptes de démonstration
- **Admin** : admin@enquetes.com / password
- **Agent** : marie.dupont@enquetes.com / password  
- **Client** : sophie.moreau@client.com / password

##  Fonctionnalités

###  Système d'authentification
- Connexion sécurisée multi-rôles
- Gestion des sessions avec protection CSRF
- Hash sécurisé des mots de passe (bcrypt)

###  Gestion des utilisateurs (Admin)
- CRUD complet des utilisateurs
- Attribution des rôles (Admin, Agent, Client)
- Tableau de bord avec statistiques

###  Gestion des campagnes (Admin)
- Création et suivi des campagnes
- Gestion des périodes d'activité
- Statistiques de participation

###  Gestion des enquêtes (Agent)
- Création d'enquêtes dans les campagnes
- 4 types de questions supportés :
  - Questions ouvertes (texte libre)
  - Questions à choix multiples (QCM)
  - Questions de notation (1-5 étoiles)
  - Questions Oui/Non
- Assignation de clients cibles
- Suivi des réponses en temps réel

###  Interface client
- Consultation des enquêtes assignées
- Réponse intuitive aux questionnaires
- Historique des participations

###  Tableaux de bord
- Statistiques visuelles avec Chart.js
- Métriques de performance
- Rapports d'activité

##  Architecture Technique

### MVC Pattern
`
app/
 Controllers/     # Logique métier et gestion des requêtes
 Models/          # Accès aux données avec PDO
 Views/           # Templates et interface utilisateur
`

### Stack technique
- **Backend** : PHP 8 avec PDO (MySQL)
- **Frontend** : Bootstrap 5 + JavaScript ES6
- **Base de données** : MySQL 5.7+
- **Graphiques** : Chart.js
- **Icônes** : Font Awesome

### Sécurité implémentée
-  Protection CSRF sur tous les formulaires
-  Validation et sanitisation des données
-  Contrôle d'accès basé sur les rôles
-  Headers de sécurité HTTP
-  Préparation des requêtes SQL

##  Structure du Projet

`
enquetes-satisfaction/
  app/
     Controllers/          # Contrôleurs MVC
       BaseController.php   # Contrôleur de base
       AuthController.php   # Authentification
       AdminController.php  # Interface administrateur
       AgentController.php  # Interface agent
       ClientController.php # Interface client
     Models/              # Modèles de données
       BaseModel.php       # Modèle de base avec PDO
       Utilisateur.php     # Gestion des utilisateurs
       Campagne.php        # Gestion des campagnes
       Enquete.php         # Gestion des enquêtes
       Question.php        # Gestion des questions
       Reponse.php         # Gestion des réponses
     Views/               # Vues et templates
         auth/            # Pages d'authentification
         admin/           # Interface administrateur
         agent/           # Interface agent
         client/          # Interface client
         backoffice/      # Template back-office
         frontoffice/     # Template front-office
         errors/          # Pages d'erreur
  config/                  # Configuration
    database.php            # Configuration base de données
    config.php             # Configuration générale
    utils.php              # Fonctions utilitaires
    bootstrap.php          # Initialisation application
  public/                  # Fichiers publics
     css/                # Feuilles de style
     js/                 # Scripts JavaScript
    index.php              # Point d'entrée
    .htaccess              # Configuration Apache
  sql/                     # Scripts base de données
    database.sql           # Schéma et données de test
 INSTALL.md                 # Guide d'installation détaillé
 README.md                  # Documentation
`

##  Flux d'utilisation

### 1. Workflow Administrateur
1. Connexion  Tableau de bord admin
2. Créer une campagne avec période d'activité
3. Gérer les utilisateurs (agents et clients)
4. Consulter les statistiques globales

### 2. Workflow Agent
1. Connexion  Tableau de bord agent
2. Créer une enquête dans une campagne
3. Ajouter différents types de questions
4. Assigner des clients à l'enquête
5. Activer l'enquête et suivre les réponses

### 3. Workflow Client
1. Connexion  Espace client
2. Consulter les enquêtes disponibles
3. Répondre aux questionnaires
4. Visualiser l'historique des participations

##  Configuration

### Base de données
Modifier config/database.php :
`php
private const HOST = 'localhost';
private const DB_NAME = 'enquetes_satisfaction';
private const USERNAME = 'votre_username';
private const PASSWORD = 'votre_password';
`

### Application
Modifier config/config.php :
`php
define('APP_NAME', 'Enquêtes Satisfaction');
define('APP_URL', 'http://localhost:8000');
define('APP_DEBUG', true);
`

##  Schéma de Base de Données

- **utilisateurs** : Gestion des comptes (admin, agents, clients)
- **campagnes** : Campagnes d'enquêtes avec périodes
- **enquetes** : Questionnaires liés aux campagnes
- **questions** : Questions avec types variés
- **reponses** : Réponses des clients aux questions
- **enquete_clients** : Association enquêtes-clients

##  Interface Utilisateur

### Templates responsives
- **Back-office** : Interface administrative avec sidebar
- **Front-office** : Interface client épurée
- **Responsive** : Compatible mobile et desktop

### Composants UI
- Tableaux interactifs avec tri et recherche
- Graphiques dynamiques (Chart.js)
- Formulaires avec validation en temps réel
- Messages flash pour les notifications

##  Installation de Production

### 1. Serveur Web Apache
`pache
<VirtualHost *:80>
    DocumentRoot "/var/www/enquetes-satisfaction/public"
    ServerName enquetes.votredomaine.com
    
    <Directory "/var/www/enquetes-satisfaction/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
`

### 2. Configuration PHP
`ini
; php.ini recommandé
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 10M
post_max_size = 10M
`

### 3. Permissions
`ash
chown -R www-data:www-data /var/www/enquetes-satisfaction
chmod -R 755 /var/www/enquetes-satisfaction
`

##  Tests et Validation

### Tests manuels recommandés
1.  Authentification avec chaque type de rôle
2.  Création de campagne  enquête  questions
3.  Assignation de clients et collecte de réponses
4.  Affichage des statistiques et graphiques
5.  Navigation entre les interfaces

### Points de validation
- Sécurité : CSRF, sanitisation, accès par rôle
- Performance : Requêtes optimisées, cache session
- UX : Messages d'erreur, navigation intuitive

##  Dépannage

### Problèmes courants

**Erreur de base de données**
`ash
# Vérifier la connexion
php -r "new PDO('mysql:host=localhost;dbname=enquetes_satisfaction', 'user', 'pass');"
`

**Page blanche**
`php
// Activer le débogage dans config/config.php
define('APP_DEBUG', true);
`

**Erreur 404**
`pache
# Vérifier mod_rewrite
a2enmod rewrite
service apache2 restart
`

##  Fonctionnalités Avancées

- **Dashboard Analytics** : Graphiques interactifs des performances
- **Export de données** : Possibilité d'extension pour export Excel/PDF
- **Notifications** : Système de messages flash contextuels
- **Responsive Design** : Interface adaptative tous écrans
- **Architecture modulaire** : Facilement extensible

##  Contexte Académique

Ce projet a été développé dans le cadre du module **"Projet Technologies Web"** en utilisant :
- Architecture MVC stricte
- Bonnes pratiques de développement PHP
- Sécurisation des applications web
- Interface utilisateur moderne et accessible

---

**Version** : 1.0.0  
**Développé avec** : PHP 8, MySQL, Bootstrap 5  
**Licence** : Projet académique  
**Contact** : Équipe de développement
