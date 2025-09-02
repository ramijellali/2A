<?php
/**
 * Serveur simple pour tester l'application
 */

// Configuration minimale
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Démarrer la session avant tout output
session_start();

// Simuler un utilisateur connecté pour les tests
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'admin';
    $_SESSION['user_email'] = 'admin@enquetes.com';
    $_SESSION['user_nom'] = 'Admin';
    $_SESSION['user_prenom'] = 'Système';
}

// Inclure les fichiers nécessaires
require_once __DIR__ . '/../config/database.php';

// Routage simple
$controller = $_GET['controller'] ?? 'Home';
$action = $_GET['action'] ?? 'index';

// Page d'accueil simple
if ($controller === 'Home') {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Enquêtes de Satisfaction - Test</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="?">
                    <i class="fas fa-poll"></i> Enquêtes de Satisfaction
                </a>
                <div class="navbar-nav ms-auto">
                    <span class="navbar-text text-white">
                        <i class="fas fa-user"></i> <?= $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'] ?>
                        (<?= ucfirst($_SESSION['user_role']) ?>)
                    </span>
                    <a class="nav-link text-white" href="?controller=Auth&action=logout">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </div>
            </div>
        </nav>

        <div class="container mt-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> 
                        <strong>Application lancée avec succès !</strong>
                        Votre système d'enquêtes de satisfaction est opérationnel.
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-cog"></i> Administration
                        </div>
                        <div class="card-body">
                            <p>Gérer le système complet</p>
                            <a href="?controller=Admin&action=dashboard" class="btn btn-primary">
                                <i class="fas fa-tachometer-alt"></i> Dashboard Admin
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-user-tie"></i> Espace Agent
                        </div>
                        <div class="card-body">
                            <p>Créer et gérer des enquêtes</p>
                            <a href="?controller=Agent&action=dashboard" class="btn btn-success">
                                <i class="fas fa-poll"></i> Dashboard Agent
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-user"></i> Espace Client
                        </div>
                        <div class="card-body">
                            <p>Répondre aux enquêtes</p>
                            <a href="?controller=Client&action=dashboard" class="btn btn-info">
                                <i class="fas fa-comments"></i> Dashboard Client
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-database"></i> État de la base de données
                        </div>
                        <div class="card-body">
                            <?php
                            try {
                                $pdo = new PDO("mysql:host=localhost;dbname=enquetes_satisfaction;charset=utf8mb4", "root", "");
                                echo '<div class="alert alert-success"><i class="fas fa-check"></i> Connexion à la base de données : <strong>OK</strong></div>';
                                
                                // Statistiques rapides
                                $stats = [];
                                $stats['users'] = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
                                $stats['campaigns'] = $pdo->query("SELECT COUNT(*) FROM campagnes")->fetchColumn();
                                $stats['surveys'] = $pdo->query("SELECT COUNT(*) FROM enquetes")->fetchColumn();
                                $stats['questions'] = $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();
                                
                                echo '<div class="row text-center">';
                                echo '<div class="col-md-3"><h5>' . $stats['users'] . '</h5><small>Utilisateurs</small></div>';
                                echo '<div class="col-md-3"><h5>' . $stats['campaigns'] . '</h5><small>Campagnes</small></div>';
                                echo '<div class="col-md-3"><h5>' . $stats['surveys'] . '</h5><small>Enquêtes</small></div>';
                                echo '<div class="col-md-3"><h5>' . $stats['questions'] . '</h5><small>Questions</small></div>';
                                echo '</div>';
                                
                            } catch (Exception $e) {
                                echo '<div class="alert alert-danger"><i class="fas fa-times"></i> Erreur de connexion : ' . $e->getMessage() . '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit;
}

// Pour les autres contrôleurs, incluez l'application normale
require_once __DIR__ . '/../config/bootstrap.php';
Application::run();
?>
