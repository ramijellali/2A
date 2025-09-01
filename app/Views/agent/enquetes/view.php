<?php
// Titre de la page
$pageTitle = 'Enquête - ' . htmlspecialchars($enquete['titre']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-eye"></i> Aperçu de l'enquête</h2>
    <div class="d-flex gap-2">
        <a href="?controller=Agent&action=editEnquete&id=<?= $enquete['id'] ?>" class="btn btn-outline-primary">
            <i class="fas fa-edit"></i> Modifier
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

<div class="row">
    <div class="col-lg-8">
        <!-- En-tête de l'enquête -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h3 class="mb-2"><?= htmlspecialchars($enquete['titre']) ?></h3>
                        <?php if (!empty($enquete['description'])): ?>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($enquete['description'])) ?></p>
                        <?php endif; ?>
                    </div>
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
                </div>
                
                <div class="row text-center">
                    <div class="col-3">
                        <div class="border-end">
                            <h4 class="text-primary mb-0"><?= count($questions) ?></h4>
                            <small class="text-muted">Questions</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border-end">
                            <h4 class="text-info mb-0"><?= count($assigned_clients) ?></h4>
                            <small class="text-muted">Clients</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border-end">
                            <h4 class="text-success mb-0"><?= $stats['clients_ayant_repondu'] ?? 0 ?></h4>
                            <small class="text-muted">Réponses</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <h4 class="text-warning mb-0">
                            <?= count($assigned_clients) > 0 ? round((($stats['clients_ayant_repondu'] ?? 0) / count($assigned_clients)) * 100, 1) : 0 ?>%
                        </h4>
                        <small class="text-muted">Taux</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-question-circle"></i> Questions de l'enquête</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($questions)): ?>
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="mb-4 <?= $index < count($questions) - 1 ? 'border-bottom pb-4' : '' ?>">
                            <div class="d-flex align-items-start mb-3">
                                <span class="badge bg-primary me-3 mt-1"><?= $index + 1 ?></span>
                                <div class="flex-grow-1">
                                    <h6 class="mb-2">
                                        <?= htmlspecialchars($question['texte']) ?>
                                        <?php if ($question['obligatoire']): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif; ?>
                                    </h6>
                                    
                                    <div class="text-muted small mb-2">
                                        Type: <strong><?= ucfirst($question['type_question']) ?></strong>
                                        <?php if ($question['obligatoire']): ?>
                                            • <span class="text-danger">Obligatoire</span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Aperçu selon le type de question -->
                                    <div class="question-preview bg-light p-3 rounded">
                                        <?php if ($question['type_question'] === 'text'): ?>
                                            <input type="text" class="form-control" placeholder="Zone de saisie de texte..." disabled>
                                            
                                        <?php elseif ($question['type_question'] === 'textarea'): ?>
                                            <textarea class="form-control" rows="3" placeholder="Zone de saisie de texte long..." disabled></textarea>
                                            
                                        <?php elseif ($question['type_question'] === 'multiple_choice'): ?>
                                            <?php 
                                            $options = json_decode($question['options'] ?? '[]', true) ?: [];
                                            foreach ($options as $option):
                                            ?>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" disabled>
                                                    <label class="form-check-label"><?= htmlspecialchars($option) ?></label>
                                                </div>
                                            <?php endforeach; ?>
                                            
                                        <?php elseif ($question['type_question'] === 'rating'): ?>
                                            <?php 
                                            $maxRating = json_decode($question['options'] ?? '{"max": 5}', true)['max'] ?? 5;
                                            ?>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="small text-muted">1</span>
                                                <?php for ($i = 1; $i <= $maxRating; $i++): ?>
                                                    <i class="fas fa-star text-muted" style="cursor: not-allowed;"></i>
                                                <?php endfor; ?>
                                                <span class="small text-muted"><?= $maxRating ?></span>
                                            </div>
                                            
                                        <?php elseif ($question['type_question'] === 'yes_no'): ?>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" disabled>
                                                <label class="form-check-label">Oui</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" disabled>
                                                <label class="form-check-label">Non</label>
                                            </div>
                                            
                                        <?php else: ?>
                                            <div class="text-muted fst-italic">Type de question non reconnu</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-question-circle fa-4x text-muted mb-3"></i>
                        <h5>Aucune question ajoutée</h5>
                        <p class="text-muted">Cette enquête ne contient pas encore de questions.</p>
                        <?php if ($enquete['statut'] === 'brouillon'): ?>
                            <a href="?controller=Agent&action=editEnquete&id=<?= $enquete['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Ajouter des questions
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Informations -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informations</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Campagne:</strong></td>
                        <td><?= htmlspecialchars($enquete['campagne_nom'] ?? 'Non définie') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Créée le:</strong></td>
                        <td><?= date('d/m/Y à H:i', strtotime($enquete['date_creation'])) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Modifiée le:</strong></td>
                        <td><?= date('d/m/Y à H:i', strtotime($enquete['date_modification'])) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Agent:</strong></td>
                        <td><?= htmlspecialchars(($enquete['agent_nom'] ?? '') . ' ' . ($enquete['agent_prenom'] ?? '')) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Clients assignés -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-users"></i> Clients assignés (<?= count($assigned_clients) ?>)</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($assigned_clients)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($assigned_clients as $client): ?>
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="small"><?= htmlspecialchars($client['nom'] . ' ' . $client['prenom']) ?></strong>
                                        <br><small class="text-muted"><?= htmlspecialchars($client['email']) ?></small>
                                    </div>
                                    <?php if (isset($client['participation_statut'])): ?>
                                        <?php
                                        $statusBadges = [
                                            'envoye' => 'warning',
                                            'en_cours' => 'info',
                                            'complete' => 'success',
                                            'expire' => 'danger'
                                        ];
                                        $statusBadge = $statusBadges[$client['participation_statut']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $statusBadge ?>">
                                            <?= ucfirst(str_replace('_', ' ', $client['participation_statut'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-user-plus fa-2x mb-2"></i>
                        <p class="small mb-0">Aucun client assigné</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-cog"></i> Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="?controller=Agent&action=editEnquete&id=<?= $enquete['id'] ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Modifier l'enquête
                    </a>
                    
                    <?php if ($enquete['statut'] !== 'brouillon' && !empty($stats['clients_ayant_repondu'])): ?>
                        <a href="?controller=Agent&action=results&id=<?= $enquete['id'] ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-chart-bar"></i> Voir les résultats
                        </a>
                        
                        <a href="?controller=Agent&action=exportResults&id=<?= $enquete['id'] ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-download"></i> Exporter CSV
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($enquete['statut'] === 'brouillon'): ?>
                        <hr>
                        <button class="btn btn-danger btn-sm" onclick="deleteEnquete(<?= $enquete['id'] ?>)">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    <?php elseif ($enquete['statut'] === 'active'): ?>
                        <hr>
                        <button class="btn btn-warning btn-sm" onclick="archiveEnquete(<?= $enquete['id'] ?>)">
                            <i class="fas fa-archive"></i> Archiver
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteEnquete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette enquête ? Cette action est irréversible.')) {
        // Créer un formulaire pour soumettre avec CSRF token
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `?controller=Agent&action=deleteEnquete&id=${id}`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = '<?= $csrf_token ?? "" ?>';
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function archiveEnquete(id) {
    if (confirm('Êtes-vous sûr de vouloir archiver cette enquête ?')) {
        // Créer un formulaire pour soumettre avec CSRF token
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `?controller=Agent&action=archiveEnquete&id=${id}`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = '<?= $csrf_token ?? "" ?>';
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
