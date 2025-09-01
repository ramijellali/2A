<?php
// Titre de la page
$pageTitle = 'Gestion des enquêtes';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-poll"></i> Gestion des enquêtes</h2>
</div>

<?php if (!empty($flash_messages)): ?>
    <?php foreach ($flash_messages as $flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Liste des enquêtes</h5>
    </div>
    <div class="card-body">
        <?php if (empty($enquetes)): ?>
            <div class="text-center py-4">
                <i class="fas fa-poll fa-3x text-muted"></i>
                <h5 class="mt-3 text-muted">Aucune enquête trouvée</h5>
                <p class="text-muted">Aucune enquête n'a encore été créée dans le système.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Campagne</th>
                            <th>Agent</th>
                            <th>Statut</th>
                            <th>Date création</th>
                            <th>Participants</th>
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
                                                <?= htmlspecialchars(substr($enquete['description'], 0, 80)) ?>
                                                <?= strlen($enquete['description']) > 80 ? '...' : '' ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($enquete['campagne_nom'])): ?>
                                        <span class="badge bg-info">
                                            <?= htmlspecialchars($enquete['campagne_nom']) ?>
                                        </span>
                                        <br><small class="text-muted">
                                            <span class="badge bg-<?= 
                                                ($enquete['campagne_statut'] ?? '') === 'active' ? 'success' : 
                                                (($enquete['campagne_statut'] ?? '') === 'en_preparation' ? 'secondary' : 
                                                (($enquete['campagne_statut'] ?? '') === 'terminee' ? 'warning' : 'danger'))
                                            ?> mt-1">
                                                <?= 
                                                    ($enquete['campagne_statut'] ?? '') === 'en_preparation' ? 'En préparation' :
                                                    (($enquete['campagne_statut'] ?? '') === 'active' ? 'Active' :
                                                    (($enquete['campagne_statut'] ?? '') === 'terminee' ? 'Terminée' :
                                                    (($enquete['campagne_statut'] ?? '') === 'suspendue' ? 'Suspendue' : ucfirst($enquete['campagne_statut'] ?? 'Inconnue'))))
                                                ?>
                                            </span>
                                        </small>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Aucune campagne</span>
                                    <?php endif; ?>
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
                                    <small><?= date('d/m/Y H:i', strtotime($enquete['date_creation'])) ?></small>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-primary mb-1">
                                            <?= $enquete['total_clients'] ?? 0 ?> ciblé(s)
                                        </span>
                                        <span class="badge bg-success">
                                            <?= $enquete['reponses_completes'] ?? 0 ?> réponse(s)
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?controller=Admin&action=viewEnquete&id=<?= $enquete['id'] ?>" 
                                           class="btn btn-outline-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?controller=Agent&action=viewEnquete&id=<?= $enquete['id'] ?>" 
                                           class="btn btn-outline-primary" title="Voir en tant qu'agent">
                                            <i class="fas fa-user-cog"></i>
                                        </a>
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

<!-- Statistiques globales -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="text-primary"><?= count($enquetes) ?></h4>
                <small class="text-muted">Total enquêtes</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <?php $actives = array_filter($enquetes, function($e) { return $e['statut'] === 'active'; }); ?>
                <h4 class="text-success"><?= count($actives) ?></h4>
                <small class="text-muted">Enquêtes actives</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <?php $totalReponses = array_sum(array_column($enquetes, 'reponses_completes')); ?>
                <h4 class="text-info"><?= $totalReponses ?></h4>
                <small class="text-muted">Réponses totales</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <?php $totalClients = array_sum(array_column($enquetes, 'total_clients')); ?>
                <h4 class="text-warning"><?= $totalClients ?></h4>
                <small class="text-muted">Clients ciblés</small>
            </div>
        </div>
    </div>
</div>
