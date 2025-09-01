<?php
session_start();

// Vérifier si l'utilisateur est connecté et a le bon rôle
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
    header('Location: /?controller=Auth&action=login');
    exit;
}

$user = $_SESSION['user'];

// Chargement des dépendances nécessaires
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Enquete.php';

try {
    $enqueteModel = new Enquete();
    $enquetes = $enqueteModel->getForClient($user['id']);
} catch (Exception $e) {
    $enquetes = [];
    $error = "Erreur lors du chargement des enquêtes: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Client - Enquêtes de Satisfaction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-poll"></i> Enquêtes Satisfaction
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Bonjour, <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                </span>
                <a class="nav-link text-white" href="/?controller=Auth&action=logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard Client</h1>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-clipboard-list"></i> Mes Enquêtes</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($enquetes)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Aucune enquête disponible pour le moment.
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($enquetes as $enquete): ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= htmlspecialchars($enquete['titre']) ?></h5>
                                                <p class="card-text"><?= htmlspecialchars($enquete['description']) ?></p>
                                                <a href="/?controller=Client&action=enquete&id=<?= $enquete['id'] ?>" class="btn btn-primary">
                                                    <i class="fas fa-play"></i> Répondre
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="/?controller=Auth&action=login" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
