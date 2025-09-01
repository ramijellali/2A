<?php
// Titre de la page
$pageTitle = 'Gestion des questions';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-question-circle"></i> Gestion des questions</h2>
</div>

<?php if (!empty($flash_messages)): ?>
    <?php foreach ($flash_messages as $flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title"><?= $stats['total'] ?></h4>
                        <p class="card-text">Questions totales</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-question-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Répartition par type</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($stats['par_type'] as $type => $count): ?>
                        <div class="col-md-3 mb-2">
                            <div class="d-flex justify-content-between">
                                <span><?= ucfirst(str_replace('_', ' ', $type)) ?></span>
                                <span class="badge bg-secondary"><?= $count ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des questions -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Liste des questions</h5>
    </div>
    <div class="card-body">
        <?php if (empty($questions)): ?>
            <div class="text-center py-4">
                <i class="fas fa-question-circle fa-3x text-muted"></i>
                <h6 class="mt-3 text-muted">Aucune question trouvée</h6>
                <p class="text-muted">Il n'y a pas encore de questions dans le système.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Type</th>
                            <th>Enquête</th>
                            <th>Campagne</th>
                            <th>Obligatoire</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questions as $question): ?>
                            <tr>
                                <td><?= $question['id'] ?></td>
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars(substr($question['texte'], 0, 60)) ?></strong>
                                        <?= strlen($question['texte']) > 60 ? '...' : '' ?>
                                        <?php if ($question['obligatoire']): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">Ordre: <?= $question['ordre_affichage'] ?? 'Non défini' ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $question['type_question'] === 'texte_libre' ? 'info' : 
                                        ($question['type_question'] === 'choix_unique' ? 'primary' : 
                                        ($question['type_question'] === 'choix_multiple' ? 'success' : 
                                        ($question['type_question'] === 'echelle' ? 'warning' : 'secondary')))
                                    ?>">
                                        <?= ucfirst(str_replace('_', ' ', $question['type_question'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <?= htmlspecialchars($question['enquete_titre'] ?? 'Non définie') ?>
                                    </div>
                                    <small class="text-muted">
                                        <span class="badge bg-<?= 
                                            ($question['enquete_statut'] ?? '') === 'active' ? 'success' : 
                                            (($question['enquete_statut'] ?? '') === 'brouillon' ? 'secondary' : 'warning')
                                        ?>">
                                            <?= ucfirst($question['enquete_statut'] ?? 'Inconnue') ?>
                                        </span>
                                    </small>
                                </td>
                                <td>
                                    <?php if (!empty($question['campagne_nom'])): ?>
                                        <small><?= htmlspecialchars($question['campagne_nom']) ?></small>
                                    <?php else: ?>
                                        <small class="text-muted">Non définie</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($question['obligatoire']): ?>
                                        <span class="badge bg-danger">Obligatoire</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Optionnelle</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?controller=Admin&action=viewQuestion&id=<?= $question['id'] ?>" 
                                           class="btn btn-outline-info" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?controller=Agent&action=viewEnquete&id=<?= $question['enquete_id'] ?>" 
                                           class="btn btn-outline-primary" title="Voir enquête">
                                            <i class="fas fa-poll"></i>
                                        </a>
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
