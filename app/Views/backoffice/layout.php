<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Enquêtes - Back Office</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/backoffice.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-poll"></i> Enquêtes Satisfaction
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> 
                        <?= htmlspecialchars(trim(($currentUser['prenom'] ?? '') . ' ' . ($currentUser['nom'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                        <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($currentUser['role'] ?? 'utilisateur'), ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?controller=Auth&action=logout">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="?controller=<?= ucfirst($currentUser['role'] ?? 'Admin') ?>&action=dashboard">
                                <i class="fas fa-tachometer-alt"></i> Tableau de bord
                            </a>
                        </li>
                        
                        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?controller=Admin&action=users">
                                <i class="fas fa-users"></i> Utilisateurs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?controller=Admin&action=campagnes">
                                <i class="fas fa-bullhorn"></i> Campagnes
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (in_array($currentUser['role'] ?? '', ['admin', 'agent'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?controller=<?= ucfirst($currentUser['role'] ?? 'Admin') ?>&action=enquetes">
                                <i class="fas fa-clipboard-list"></i> Enquêtes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?controller=<?= ucfirst($currentUser['role']) ?>&action=questions">
                                <i class="fas fa-question-circle"></i> Questions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?controller=<?= ucfirst($currentUser['role']) ?>&action=reponses">
                                <i class="fas fa-chart-bar"></i> Réponses
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="pt-3">
                    <?php if (!empty($flash_messages)): ?>
                        <?php foreach ($flash_messages as $message): ?>
                            <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : $message['type'] ?> alert-dismissible fade show">
                                <?= htmlspecialchars($message['message']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?= $content ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/validation.js"></script>
    <script src="js/backoffice.js"></script>
</body>
</html>
