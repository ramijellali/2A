<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-tachometer-alt"></i> Mon Espace Client</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="?controller=Client&action=historique" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-history"></i> Historique
            </a>
            <a href="?controller=Client&action=mesStats" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-chart-line"></i> Mes statistiques
            </a>
        </div>
    </div>
</div>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Enquêtes Reçues
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number" data-target="<?= $stats['enquetes_recues'] ?>">
                            0
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-inbox fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Enquêtes Complétées
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number" data-target="<?= $stats['enquetes_completees'] ?>">
                            0
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            En Attente
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number" data-target="<?= $stats['enquetes_en_cours'] ?>">
                            0
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enquêtes disponibles -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clipboard-list"></i> Mes Enquêtes
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($enquetes)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-600">Aucune enquête disponible</h5>
                        <p class="text-gray-500">Vous n'avez pas encore reçu d'enquêtes.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($enquetes as $enquete): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card enquete-card h-100 position-relative">
                                    <!-- Badge de statut -->
                                    <div class="enquete-status">
                                        <?php
                                        $badgeClass = '';
                                        $statutText = '';
                                        switch ($enquete['participation_statut']) {
                                            case 'envoye':
                                                $badgeClass = 'bg-warning';
                                                $statutText = 'Nouveau';
                                                break;
                                            case 'vu':
                                                $badgeClass = 'bg-info';
                                                $statutText = 'En cours';
                                                break;
                                            case 'complete':
                                                $badgeClass = 'bg-success';
                                                $statutText = 'Terminé';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= $statutText ?></span>
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title text-primary">
                                            <?= htmlspecialchars($enquete['titre']) ?>
                                        </h5>
                                        
                                        <p class="card-text text-muted mb-2">
                                            <small><i class="fas fa-bullhorn"></i> <?= htmlspecialchars($enquete['campagne_nom']) ?></small>
                                        </p>
                                        
                                        <?php if (!empty($enquete['description'])): ?>
                                            <p class="card-text flex-grow-1">
                                                <?= htmlspecialchars(substr($enquete['description'], 0, 100)) ?>
                                                <?= strlen($enquete['description']) > 100 ? '...' : '' ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar"></i> 
                                                    Reçu le <?= isset($enquete['date_envoi']) && $enquete['date_envoi'] ? date('d/m/Y', strtotime($enquete['date_envoi'])) : 'Date inconnue' ?>
                                                </small>
                                            </div>
                                            
                                            <?php if ($enquete['participation_statut'] === 'complete'): ?>
                                                <a href="?controller=Client&action=viewEnquete&id=<?= $enquete['id'] ?>" 
                                                   class="btn btn-outline-success btn-sm w-100">
                                                    <i class="fas fa-eye"></i> Voir mes réponses
                                                </a>
                                            <?php else: ?>
                                                <a href="?controller=Client&action=viewEnquete&id=<?= $enquete['id'] ?>" 
                                                   class="btn btn-primary btn-sm w-100">
                                                    <i class="fas fa-edit"></i> 
                                                    <?= $enquete['participation_statut'] === 'vu' ? 'Continuer' : 'Commencer' ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Conseils et aide -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h6 class="m-0"><i class="fas fa-lightbulb"></i> Conseils</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        Répondez honnêtement pour nous aider à améliorer nos services
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        Vos réponses sont confidentielles et anonymisées
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        Vous pouvez interrompre et reprendre une enquête
                    </li>
                    <li>
                        <i class="fas fa-check text-success"></i>
                        Consultez votre historique dans l'onglet dédié
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0"><i class="fas fa-question-circle"></i> Aide</h6>
            </div>
            <div class="card-body">
                <p class="card-text">
                    Besoin d'aide pour répondre aux enquêtes ? Consultez notre guide ou contactez le support.
                </p>
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-book"></i> Guide d'utilisation
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-envelope"></i> Contacter le support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des statistiques
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(element => {
        const target = parseInt(element.dataset.target);
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 20);
    });
    
    // Animation d'entrée pour les cartes d'enquêtes
    const enqueteCards = document.querySelectorAll('.enquete-card');
    enqueteCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}
.text-gray-600 {
    color: #858796 !important;
}
.text-gray-500 {
    color: #b7b9cc !important;
}
.text-xs {
    font-size: 0.7rem;
}
.enquete-card {
    transition: transform 0.2s ease-in-out;
}
.enquete-card:hover {
    transform: translateY(-5px);
}
</style>
