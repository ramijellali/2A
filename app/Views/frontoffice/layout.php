<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enquêtes de Satisfaction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/frontoffice.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-primary">
        <div class="container">
            <a class="navbar-brand text-white" href="#">
                <i class="fas fa-poll"></i> Enquêtes Satisfaction
            </a>
            
            <?php if (isset($currentUser)): ?>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> 
                        <?= htmlspecialchars($currentUser['prenom'] . ' ' . $currentUser['nom']) ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?controller=Client&action=dashboard">
                            <i class="fas fa-tachometer-alt"></i> Mon espace
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="?controller=Auth&action=logout">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a></li>
                    </ul>
                </div>
            </div>
            <?php else: ?>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="?controller=Auth&action=login">
                    <i class="fas fa-sign-in-alt"></i> Connexion
                </a>
                <a class="nav-link text-white" href="?controller=Auth&action=register">
                    <i class="fas fa-user-plus"></i> Inscription
                </a>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container mt-4">
        <?php if (!empty($flash_messages)): ?>
            <?php foreach ($flash_messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : $message['type'] ?> alert-dismissible fade show">
                    <?= htmlspecialchars($message['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?= $content ?>
    </main>

    <footer class="bg-light mt-5 py-4">
        <div class="container text-center">
            <p class="text-muted">&copy; 2025 Système de Gestion des Enquêtes de Satisfaction</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/validation.js"></script>
    <script src="js/frontoffice.js"></script>
</body>
</html>
