<?php
// Titre de la page
$pageTitle = 'Détails de la campagne';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-bullhorn"></i> <?= htmlspecialchars($campagne['nom']) ?></h2>
    <div>
        <a href="?controller=Admin&action=editCampagne&id=<?= $campagne['id'] ?>" class="btn btn-outline-primary me-2">
            <i class="fas fa-edit"></i> Modifier
        </a>
        <a href="?controller=Admin&action=campagnes" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux campagnes
        </a>
    </div>
</div>

<?php if (!empty($flash_messages)): ?>
    <?php foreach ($flash_messages as $flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Informations générales -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informations générales</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-4">ID :</dt>
                            <dd class="col-8"><?= $campagne['id'] ?></dd>
                            
                            <dt class="col-4">Nom :</dt>
                            <dd class="col-8"><?= htmlspecialchars($campagne['nom']) ?></dd>
                            
                            <dt class="col-4">Statut :</dt>
                            <dd class="col-8">
                                <span class="badge bg-<?= 
                                    $campagne['statut'] === 'active' ? 'success' : 
                                    ($campagne['statut'] === 'en_preparation' ? 'secondary' : 
                                    ($campagne['statut'] === 'terminee' ? 'warning' : 'danger'))
                                ?>">
                                    <?= 
                                        $campagne['statut'] === 'en_preparation' ? 'En préparation' :
                                        ($campagne['statut'] === 'active' ? 'Active' :
                                        ($campagne['statut'] === 'terminee' ? 'Terminée' :
                                        ($campagne['statut'] === 'suspendue' ? 'Suspendue' : ucfirst($campagne['statut']))))
                                    ?>
                                </span>
                            </dd>
                            
                            <dt class="col-4">Créateur :</dt>
                            <dd class="col-8">
                                <?= htmlspecialchars($campagne['createur_prenom'] . ' ' . $campagne['createur_nom']) ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-4">Date début :</dt>
                            <dd class="col-8"><?= date('d/m/Y', strtotime($campagne['date_debut'])) ?></dd>
                            
                            <dt class="col-4">Date fin :</dt>
                            <dd class="col-8"><?= date('d/m/Y', strtotime($campagne['date_fin'])) ?></dd>
                            
                            <dt class="col-4">Créée le :</dt>
                            <dd class="col-8"><?= date('d/m/Y à H:i', strtotime($campagne['date_creation'])) ?></dd>
                            
                            <?php if (!empty($campagne['date_modification'])): ?>
                            <dt class="col-4">Modifiée le :</dt>
                            <dd class="col-8"><?= date('d/m/Y à H:i', strtotime($campagne['date_modification'])) ?></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>
                
                <?php if (!empty($campagne['description'])): ?>
                <hr>
                <div>
                    <h6>Description :</h6>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($campagne['description'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Statistiques</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-0"><?= $stats['total_enquetes'] ?? 0 ?></h4>
                            <small class="text-muted">Enquêtes<br>totales</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-0"><?= $stats['enquetes_actives'] ?? 0 ?></h4>
                        <small class="text-muted">Enquêtes<br>actives</small>
                    </div>
                </div>
                
                <hr class="my-3">
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-info mb-0"><?= $stats['total_clients_cibles'] ?? 0 ?></h4>
                            <small class="text-muted">Clients<br>ciblés</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning mb-0"><?= $stats['reponses_completes'] ?? 0 ?></h4>
                        <small class="text-muted">Réponses<br>complètes</small>
                    </div>
                </div>
                
                <?php if (($stats['total_clients_cibles'] ?? 0) > 0): ?>
                <hr class="my-3">
                <div class="text-center">
                    <div class="progress" style="height: 8px;">
                        <?php 
                        $taux = round((($stats['reponses_completes'] ?? 0) / $stats['total_clients_cibles']) * 100);
                        ?>
                        <div class="progress-bar bg-success" 
                             style="width: <?= $taux ?>%"></div>
                    </div>
                    <small class="text-muted">Taux de réponse : <?= $taux ?>%</small>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Enquêtes associées -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-poll"></i> Enquêtes associées</h5>
        <?php if ($campagne['statut'] === 'active'): ?>
        <a href="?controller=Agent&action=newEnquete" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Nouvelle enquête
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (empty($enquetes)): ?>
            <div class="text-center py-4">
                <i class="fas fa-poll fa-3x text-muted"></i>
                <h6 class="mt-3 text-muted">Aucune enquête trouvée</h6>
                <p class="text-muted">Cette campagne ne contient pas encore d'enquête.</p>
                <?php if ($campagne['statut'] === 'active'): ?>
                <a href="?controller=Agent&action=newEnquete" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Créer une enquête
                </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Agent</th>
                            <th>Statut</th>
                            <th>Date création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enquetes as $enquete): ?>
                            <tr>
                                <td><?= $enquete['id'] ?></td>
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars($enquete['titre']) ?></strong>
                                        <?php if (!empty($enquete['description'])): ?>
                                            <br><small class="text-muted">
                                                <?= htmlspecialchars(substr($enquete['description'], 0, 60)) ?>
                                                <?= strlen($enquete['description']) > 60 ? '...' : '' ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <small>
                                        <?= htmlspecialchars($enquete['agent_prenom'] . ' ' . $enquete['agent_nom']) ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $enquete['statut'] === 'active' ? 'success' : 
                                        ($enquete['statut'] === 'brouillon' ? 'secondary' : 
                                        ($enquete['statut'] === 'terminee' ? 'warning' : 'info'))
                                    ?>">
                                        <?= ucfirst($enquete['statut']) ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?= date('d/m/Y', strtotime($enquete['date_creation'])) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?controller=Agent&action=viewEnquete&id=<?= $enquete['id'] ?>" 
                                           class="btn btn-outline-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (in_array($enquete['statut'], ['brouillon', 'active'])): ?>
                                        <a href="?controller=Agent&action=editEnquete&id=<?= $enquete['id'] ?>" 
                                           class="btn btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($enquete['statut'] === 'active'): ?>
                                        <a href="?controller=Agent&action=results&id=<?= $enquete['id'] ?>" 
                                           class="btn btn-outline-success" title="Résultats">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Actions rapides -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-cogs"></i> Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2 d-md-flex">
                    <a href="?controller=Admin&action=editCampagne&id=<?= $campagne['id'] ?>" 
                       class="btn btn-outline-primary">
                        <i class="fas fa-edit"></i> Modifier la campagne
                    </a>
                    
                    <?php if ($campagne['statut'] === 'brouillon'): ?>
                    <form action="?controller=Admin&action=updateCampagne" method="POST" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="id" value="<?= $campagne['id'] ?>">
                        <input type="hidden" name="nom" value="<?= htmlspecialchars($campagne['nom'] ?? '') ?>">
                        <input type="hidden" name="description" value="<?= htmlspecialchars($campagne['description'] ?? '') ?>">
                        <input type="hidden" name="date_debut" value="<?= $campagne['date_debut'] ?? '' ?>">
                        <input type="hidden" name="date_fin" value="<?= $campagne['date_fin'] ?? '' ?>">
                        <input type="hidden" name="statut" value="active">
                        <button type="submit" class="btn btn-outline-success"
                                onclick="return confirm('Êtes-vous sûr de vouloir activer cette campagne ?')">
                            <i class="fas fa-play"></i> Activer la campagne
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <?php if ($campagne['statut'] === 'active'): ?>
                    <form action="?controller=Admin&action=updateCampagne" method="POST" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="id" value="<?= $campagne['id'] ?>">
                        <input type="hidden" name="nom" value="<?= htmlspecialchars($campagne['nom'] ?? '') ?>">
                        <input type="hidden" name="description" value="<?= htmlspecialchars($campagne['description'] ?? '') ?>">
                        <input type="hidden" name="date_debut" value="<?= $campagne['date_debut'] ?? '' ?>">
                        <input type="hidden" name="date_fin" value="<?= $campagne['date_fin'] ?? '' ?>">
                        <input type="hidden" name="statut" value="terminee">
                        <button type="submit" class="btn btn-outline-warning"
                                onclick="return confirm('Êtes-vous sûr de vouloir terminer cette campagne ? Cette action empêchera toute nouvelle participation.')">
                            <i class="fas fa-stop"></i> Terminer la campagne
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <?php if (empty($enquetes) || count(array_filter($enquetes, function($e) { return $e['statut'] === 'active'; })) === 0): ?>
                    <button type="button" class="btn btn-outline-danger ms-auto" 
                            onclick="confirmDeleteCampagne(<?= $campagne['id'] ?>, <?= json_encode($campagne['nom'] ?? '') ?>)">
                        <i class="fas fa-trash"></i> Supprimer la campagne
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteCampagneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la campagne <strong id="deleteCampagneName"></strong> ?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cette action supprimera également toutes les enquêtes associées et est irréversible.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteCampagneForm" method="GET" class="d-inline">
                    <input type="hidden" name="controller" value="Admin">
                    <input type="hidden" name="action" value="deleteCampagne">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDeleteCampagne(campagneId, campagneNom) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer la campagne "${campagneNom}" ?`)) {
        window.location.href = `?controller=Admin&action=deleteCampagne&id=${campagneId}`;
    }
}
</script>
