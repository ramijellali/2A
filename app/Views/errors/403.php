<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé - 403</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white text-center">
                        <h1><i class="fas fa-ban"></i> 403</h1>
                        <h4>Accès refusé</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-user-slash fa-5x text-danger"></i>
                        </div>
                        <h5 class="card-title">Vous n'avez pas l'autorisation d'accéder à cette page</h5>
                        <p class="card-text text-muted">
                            Cette section est réservée aux utilisateurs autorisés. 
                            Veuillez vous connecter avec un compte approprié.
                        </p>
                        
                        <div class="d-grid gap-2 mt-4">
                            <a href="/?controller=Auth&action=login" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Se connecter
                            </a>
                            <a href="/" class="btn btn-outline-secondary">
                                <i class="fas fa-home"></i> Retour à l'accueil
                            </a>
                        </div>
                    </div>
                    <div class="card-footer text-muted text-center">
                        <small>Si vous pensez qu'il s'agit d'une erreur, contactez l'administrateur.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
