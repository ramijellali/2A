<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - 404</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 text-center">
                <div class="error-template">
                    <h1 class="display-1 text-primary">
                        <i class="fas fa-exclamation-triangle"></i>
                    </h1>
                    <h2>404 - Page non trouvée</h2>
                    <div class="error-details">
                        <p class="text-muted">
                            Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
                        </p>
                    </div>
                    <div class="error-actions">
                        <a href="?controller=Auth&action=login" class="btn btn-primary">
                            <i class="fas fa-home"></i> Retour à l'accueil
                        </a>
                        <a href="javascript:history.back()" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Page précédente
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
