<?php
// Titre de la page
$pageTitle = 'Modifier Enquête - ' . htmlspecialchars($enquete['titre']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-edit"></i> Modifier Enquête</h2>
    <div class="d-flex gap-2">
        <a href="?controller=Agent&action=viewEnquete&id=<?= $enquete['id'] ?>" class="btn btn-outline-info">
            <i class="fas fa-eye"></i> Aperçu
        </a>
        <a href="?controller=Agent&action=enquetes" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<!-- Messages flash -->
<?php if (!empty($flash_messages)): ?>
    <?php foreach ($flash_messages as $type => $messages): ?>
        <?php foreach ($messages as $message): ?>
            <div class="alert alert-<?= $type === 'success' ? 'success' : ($type === 'error' ? 'danger' : 'info') ?> alert-dismissible fade show">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Indicateur de statut -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><?= htmlspecialchars($enquete['titre']) ?></h5>
                        <small class="text-muted">
                            Créée le <?= date('d/m/Y à H:i', strtotime($enquete['date_creation'])) ?>
                            <?php if ($enquete['date_modification'] !== $enquete['date_creation']): ?>
                                • Modifiée le <?= date('d/m/Y à H:i', strtotime($enquete['date_modification'])) ?>
                            <?php endif; ?>
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <?php
                        $badges = [
                            'brouillon' => ['secondary', 'draft'],
                            'active' => ['success', 'play'],
                            'terminee' => ['info', 'stop'],
                            'archivee' => ['dark', 'archive']
                        ];
                        $badge = $badges[$enquete['statut']] ?? ['secondary', 'question'];
                        ?>
                        <span class="badge bg-<?= $badge[0] ?> fs-6">
                            <i class="fas fa-<?= $badge[1] ?>"></i> <?= ucfirst($enquete['statut']) ?>
                        </span>
                        
                        <?php if ($enquete['statut'] === 'brouillon' && !empty($questions)): ?>
                            <form method="POST" action="?controller=Agent&action=activateEnquete" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                <input type="hidden" name="id" value="<?= $enquete['id'] ?>">
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Activer cette enquête ?')">
                                    <i class="fas fa-play"></i> Activer
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informations générales -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations générales</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="?controller=Agent&action=updateEnquete">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= $enquete['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre de l'enquête</label>
                        <input type="text" 
                               class="form-control" 
                               id="titre" 
                               name="titre" 
                               value="<?= htmlspecialchars($enquete['titre']) ?>"
                               <?= $enquete['statut'] === 'active' ? 'readonly' : '' ?>>
                        <?php if ($enquete['statut'] === 'active'): ?>
                            <div class="form-text text-info">Le titre ne peut pas être modifié pour une enquête active.</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="4"><?= htmlspecialchars($enquete['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Sauvegarder les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Questions -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-question-circle"></i> Questions (<?= count($questions) ?>)</h5>
                <?php if ($enquete['statut'] === 'brouillon'): ?>
                    <a href="?controller=Agent&action=questions&enquete_id=<?= $enquete['id'] ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Ajouter une question
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($questions)): ?>
                    <div class="list-group">
                        <?php foreach ($questions as $index => $question): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <span class="badge bg-primary me-2"><?= $index + 1 ?></span>
                                            <?= htmlspecialchars($question['texte']) ?>
                                        </h6>
                                        <p class="mb-1 text-muted small">
                                            Type: <strong><?= ucfirst($question['type_question']) ?></strong>
                                            <?php if ($question['obligatoire']): ?>
                                                • <span class="text-danger">Obligatoire</span>
                                            <?php endif; ?>
                                        </p>
                                        <?php if (!empty($question['options'])): ?>
                                            <div class="small text-muted">
                                                Options: <?= htmlspecialchars(implode(', ', array_slice(json_decode($question['options'], true) ?: [], 0, 3))) ?>
                                                <?php if (count(json_decode($question['options'], true) ?: []) > 3): ?>...<?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($enquete['statut'] === 'brouillon'): ?>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="editQuestion(<?= $question['id'] ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteQuestion(<?= $question['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($enquete['statut'] === 'brouillon'): ?>
                        <div class="mt-3 text-center">
                            <a href="?controller=Agent&action=questions&enquete_id=<?= $enquete['id'] ?>" class="btn btn-outline-success">
                                <i class="fas fa-plus"></i> Ajouter une autre question
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                        <h5>Aucune question ajoutée</h5>
                        <p class="text-muted">Commencez par ajouter des questions à votre enquête.</p>
                        <?php if ($enquete['statut'] === 'brouillon'): ?>
                            <a href="?controller=Agent&action=questions&enquete_id=<?= $enquete['id'] ?>" class="btn btn-success">
                                <i class="fas fa-plus"></i> Créer ma première question
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Panneau latéral -->
    <div class="col-lg-4">
        <!-- Statistiques -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Statistiques</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-0"><?= count($questions) ?></h4>
                            <small class="text-muted">Questions</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info mb-0"><?= count($assigned_clients) ?></h4>
                        <small class="text-muted">Clients assignés</small>
                    </div>
                </div>
                
                <?php if ($enquete['statut'] !== 'brouillon'): ?>
                    <hr>
                    <div class="text-center">
                        <a href="?controller=Agent&action=results&id=<?= $enquete['id'] ?>" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-chart-line"></i> Voir les résultats
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Clients assignés -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-users"></i> Clients assignés</h6>
                <?php if ($enquete['statut'] === 'brouillon'): ?>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#assignClientsModal">
                        <i class="fas fa-plus"></i> Assigner
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($assigned_clients)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($assigned_clients, 0, 5) as $client): ?>
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="small"><?= htmlspecialchars($client['nom'] . ' ' . $client['prenom']) ?></strong>
                                        <br><small class="text-muted"><?= htmlspecialchars($client['email']) ?></small>
                                    </div>
                                    <?php if (isset($client['participation_statut'])): ?>
                                        <span class="badge bg-<?= $client['participation_statut'] === 'complete' ? 'success' : 'warning' ?>">
                                            <?= $client['participation_statut'] ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($assigned_clients) > 5): ?>
                        <div class="text-center mt-2">
                            <small class="text-muted">Et <?= count($assigned_clients) - 5 ?> autre(s)...</small>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-user-plus fa-2x mb-2"></i>
                        <p class="small mb-0">Aucun client assigné</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt"></i> Actions rapides</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if ($enquete['statut'] === 'brouillon'): ?>
                        <?php if (!empty($questions) && !empty($assigned_clients)): ?>
                            <form method="POST" action="?controller=Agent&action=activateEnquete">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                <input type="hidden" name="id" value="<?= $enquete['id'] ?>">
                                <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('Activer cette enquête ?')">
                                    <i class="fas fa-play"></i> Activer l'enquête
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-outline-success btn-sm w-100" disabled title="Ajoutez des questions et assignez des clients">
                                <i class="fas fa-play"></i> Activer l'enquête
                            </button>
                            <small class="text-muted text-center">
                                <?php if (empty($questions)): ?>Ajoutez des questions<br><?php endif; ?>
                                <?php if (empty($assigned_clients)): ?>Assignez des clients<?php endif; ?>
                            </small>
                        <?php endif; ?>
                        
                        <button class="btn btn-outline-danger btn-sm w-100" onclick="deleteEnquete(<?= $enquete['id'] ?>)">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    <?php elseif ($enquete['statut'] === 'active'): ?>
                        <a href="?controller=Agent&action=results&id=<?= $enquete['id'] ?>" class="btn btn-outline-info btn-sm w-100">
                            <i class="fas fa-chart-bar"></i> Voir résultats
                        </a>
                        <button class="btn btn-outline-warning btn-sm w-100" onclick="archiveEnquete(<?= $enquete['id'] ?>)">
                            <i class="fas fa-archive"></i> Archiver
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'assignation des clients -->
<?php if ($enquete['statut'] === 'brouillon'): ?>
<div class="modal fade" id="assignClientsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assigner des clients</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?controller=Agent&action=assignClients">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="enquete_id" value="<?= $enquete['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Sélectionner les clients :</label>
                        <div class="row">
                            <?php foreach ($clients as $client): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="client_ids[]" 
                                               value="<?= $client['id'] ?>"
                                               id="client_<?= $client['id'] ?>"
                                               <?= in_array($client['id'], array_column($assigned_clients, 'id')) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="client_<?= $client['id'] ?>">
                                            <strong><?= htmlspecialchars($client['nom'] . ' ' . $client['prenom']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($client['email']) ?></small>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Assigner les clients</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function deleteEnquete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette enquête ? Cette action est irréversible.')) {
        window.location.href = `?controller=Agent&action=deleteEnquete&id=${id}&csrf_token=<?= $csrf_token ?>`;
    }
}

function archiveEnquete(id) {
    if (confirm('Êtes-vous sûr de vouloir archiver cette enquête ?')) {
        window.location.href = `?controller=Agent&action=archiveEnquete&id=${id}&csrf_token=<?= $csrf_token ?>`;
    }
}

function editQuestion(id) {
    // TODO: Implémenter l'édition de question
    alert('Fonctionnalité d\'édition en cours de développement');
}

function deleteQuestion(id) {
    if (confirm('Supprimer cette question ?')) {
        // TODO: Implémenter la suppression de question
        alert('Fonctionnalité de suppression en cours de développement');
    }
}
</script>
