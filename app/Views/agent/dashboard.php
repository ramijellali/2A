<?php
// Titre de la page
$pageTitle = 'Tableau de bord Agent';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-tachometer-alt"></i> Tableau de bord Agent</h2>
        <p class="text-muted">Bienvenue, <?= htmlspecialchars($currentUser['prenom'] . ' ' . $currentUser['nom']) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="?controller=Agent&action=enquetes" class="btn btn-primary">
            <i class="fas fa-poll"></i> Mes Enquêtes
        </a>
        <a href="?controller=Agent&action=newEnquete" class="btn btn-success">
            <i class="fas fa-plus"></i> Nouvelle Enquête
        </a>
    </div>
</div>

<!-- Cartes de statistiques -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['enquetes']['total'] ?? 0 ?></h4>
                        <small>Enquêtes Créées</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-poll fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['enquetes']['active'] ?? 0 ?></h4>
                        <small>Enquêtes Actives</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['questions']['total'] ?? 0 ?></h4>
                        <small>Questions</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-question-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $stats['reponses']['total'] ?? 0 ?></h4>
                        <small>Réponses</small>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-comments fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enquêtes récentes -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock"></i> Enquêtes Récentes</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recentEnquetes)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Statut</th>
                                    <th>Questions</th>
                                    <th>Réponses</th>
                                    <th>Date création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentEnquetes as $enquete): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($enquete['titre']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($enquete['description']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $enquete['statut'] === 'active' ? 'success' : 'secondary' ?>">
                                                <?= ucfirst($enquete['statut']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= $enquete['nb_questions'] ?? 0 ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?= $enquete['nb_reponses'] ?? 0 ?></span>
                                        </td>
                                        <td>
                                            <small><?= date('d/m/Y', strtotime($enquete['date_creation'])) ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="?controller=Agent&action=viewEnquete&id=<?= $enquete['id'] ?>" 
                                                   class="btn btn-outline-primary" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="?controller=Agent&action=editEnquete&id=<?= $enquete['id'] ?>" 
                                                   class="btn btn-outline-secondary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?controller=Agent&action=results&id=<?= $enquete['id'] ?>" 
                                                   class="btn btn-outline-success" title="Résultats">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-poll fa-3x text-muted mb-3"></i>
                        <h5>Aucune enquête créée</h5>
                        <p class="text-muted">Commencez par créer votre première enquête</p>
                        <a href="?controller=Agent&action=newEnquete" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Créer une enquête
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie"></i> Aperçu Activité</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Enquêtes Actives</span>
                        <span class="fw-bold"><?= $stats['enquetes']['active'] ?? 0 ?></span>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-success" style="width: <?= ($stats['enquetes']['total'] > 0) ? (($stats['enquetes']['active'] / $stats['enquetes']['total']) * 100) : 0 ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Taux de réponse moyen</span>
                        <span class="fw-bold"><?= $stats['taux_reponse'] ?? '0' ?>%</span>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-info" style="width: <?= $stats['taux_reponse'] ?? 0 ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Questions créées</span>
                        <span class="fw-bold"><?= $stats['questions']['total'] ?? 0 ?></span>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="?controller=Agent&action=statistics" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-chart-line"></i> Voir statistiques détaillées
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Raccourcis -->
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-bolt"></i> Actions Rapides</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="?controller=Agent&action=newEnquete" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nouvelle Enquête
                    </a>
                    <a href="?controller=Agent&action=questions" class="btn btn-info btn-sm">
                        <i class="fas fa-question-circle"></i> Gérer Questions
                    </a>
                    <a href="?controller=Agent&action=templates" class="btn btn-secondary btn-sm">
                        <i class="fas fa-copy"></i> Modèles d'enquête
                    </a>
                    <a href="?controller=Agent&action=help" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-question"></i> Aide
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notifications récentes -->
<?php if (!empty($notifications)): ?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bell"></i> Notifications Récentes</h5>
            </div>
            <div class="card-body">
                <?php foreach ($notifications as $notification): ?>
                    <div class="alert alert-<?= $notification['type'] === 'info' ? 'info' : 'warning' ?> alert-dismissible fade show">
                        <i class="fas fa-<?= $notification['type'] === 'info' ? 'info-circle' : 'exclamation-triangle' ?>"></i>
                        <?= htmlspecialchars($notification['message']) ?>
                        <small class="text-muted d-block"><?= date('d/m/Y H:i', strtotime($notification['date'])) ?></small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Rafraîchissement automatique des statistiques toutes les 5 minutes
setInterval(function() {
    // Code pour rafraîchir les stats si nécessaire
}, 300000);
</script>
