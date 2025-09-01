<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur serveur - 500</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white text-center">
                        <h1><i class="fas fa-exclamation-triangle"></i> 500</h1>
                        <h4>Erreur interne du serveur</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-server fa-5x text-danger"></i>
                        </div>
                        <h5 class="card-title">Une erreur s'est produite sur le serveur</h5>
                        <p class="card-text text-muted">
                            Nous nous excusons pour ce problème technique. 
                            Veuillez réessayer dans quelques instants.
                        </p>
                        
                        <div class="d-grid gap-2 mt-4">
                            <a href="/" class="btn btn-primary">
                                <i class="fas fa-home"></i> Retour à l'accueil
                            </a>
                            <button onclick="window.location.reload()" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i> Réessayer
                            </button>
                        </div>
                    </div>
                    <div class="card-footer text-muted text-center">
                        <small>Si le problème persiste, contactez l'administrateur.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
