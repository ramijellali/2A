<?php
// Titre de la page
$pageTitle = 'Détails de l\'enquête';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-poll"></i> <?= htmlspecialchars($enquete['titre']) ?></h2>
    <div>
        <a href="?controller=Agent&action=viewEnquete&id=<?= $enquete['id'] ?>" class="btn btn-outline-primary me-2">
            <i class="fas fa-user-cog"></i> Vue agent
        </a>
        <a href="?controller=Admin&action=enquetes" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux enquêtes
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
                            <dd class="col-8"><?= $enquete['id'] ?></dd>
                            
                            <dt class="col-4">Titre :</dt>
                            <dd class="col-8"><?= htmlspecialchars($enquete['titre']) ?></dd>
                            
                            <dt class="col-4">Statut :</dt>
                            <dd class="col-8">
                                <span class="badge bg-<?= 
                                    $enquete['statut'] === 'active' ? 'success' : 
                                    ($enquete['statut'] === 'brouillon' ? 'secondary' : 
                                    ($enquete['statut'] === 'terminee' ? 'warning' : 'info'))
                                ?>">
                                    <?= ucfirst($enquete['statut']) ?>
                                </span>
                            </dd>
                            
                            <dt class="col-4">Agent :</dt>
                            <dd class="col-8">
                                <?= htmlspecialchars($enquete['agent_prenom'] . ' ' . $enquete['agent_nom']) ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-4">Campagne :</dt>
                            <dd class="col-8">
                                <?php if (!empty($enquete['campagne_nom'])): ?>
                                    <a href="?controller=Admin&action=viewCampagne&id=<?= $enquete['campagne_id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($enquete['campagne_nom']) ?>
                                    </a>
                                    <br><span class="badge bg-<?= 
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
                                <?php else: ?>
                                    <span class="text-muted">Aucune campagne assignée</span>
                                <?php endif; ?>
                            </dd>
                            
                            <dt class="col-4">Créée le :</dt>
                            <dd class="col-8"><?= date('d/m/Y à H:i', strtotime($enquete['date_creation'])) ?></dd>
                            
                            <?php if (!empty($enquete['date_modification'])): ?>
                            <dt class="col-4">Modifiée le :</dt>
                            <dd class="col-8"><?= date('d/m/Y à H:i', strtotime($enquete['date_modification'])) ?></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>
                
                <?php if (!empty($enquete['description'])): ?>
                <hr>
                <div>
                    <h6>Description :</h6>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($enquete['description'])) ?></p>
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
                            <h4 class="text-primary mb-0"><?= count($questions) ?></h4>
                            <small class="text-muted">Questions</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info mb-0"><?= count($assigned_clients) ?></h4>
                        <small class="text-muted">Clients<br>ciblés</small>
                    </div>
                </div>
                
                <hr class="my-3">
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-success mb-0"><?= $stats['total_reponses'] ?? 0 ?></h4>
                            <small class="text-muted">Réponses<br>complètes</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning mb-0"><?= $stats['en_cours'] ?? 0 ?></h4>
                        <small class="text-muted">En cours</small>
                    </div>
                </div>
                
                <?php if (count($assigned_clients) > 0): ?>
                <hr class="my-3">
                <div class="text-center">
                    <div class="progress" style="height: 8px;">
                        <?php 
                        $taux = count($assigned_clients) > 0 ? round((($stats['total_reponses'] ?? 0) / count($assigned_clients)) * 100) : 0;
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

<!-- Questions -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-question-circle"></i> Questions (<?= count($questions) ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($questions)): ?>
            <div class="text-center py-4">
                <i class="fas fa-question-circle fa-3x text-muted"></i>
                <h6 class="mt-3 text-muted">Aucune question ajoutée</h6>
                <p class="text-muted">Cette enquête ne contient pas encore de questions.</p>
            </div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($questions as $index => $question): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <span class="badge bg-primary me-2"><?= $index + 1 ?></span>
                                    <?= htmlspecialchars($question['texte']) ?>
                                    <?php if ($question['obligatoire']): ?>
                                        <span class="text-danger">*</span>
                                    <?php endif; ?>
                                </h6>
                                <p class="mb-1 text-muted small">
                                    Type: <strong><?= ucfirst($question['type_question']) ?></strong>
                                    <?php if ($question['obligatoire']): ?>
                                        • <span class="text-danger">Obligatoire</span>
                                    <?php endif; ?>
                                </p>
                                <?php if (!empty($question['options'])): ?>
                                    <div class="small text-muted">
                                        Options: <?= htmlspecialchars(implode(', ', array_slice(json_decode($question['options_json'], true) ?: [], 0, 3))) ?>
                                        <?php if (count(json_decode($question['options_json'], true) ?: []) > 3): ?>...<?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Clients assignés -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-users"></i> Clients assignés (<?= count($assigned_clients) ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($assigned_clients)): ?>
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted"></i>
                <h6 class="mt-3 text-muted">Aucun client assigné</h6>
                <p class="text-muted">Cette enquête n'a pas encore été envoyée à des clients.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Email</th>
                            <th>Statut</th>
                            <th>Date envoi</th>
                            <th>Date réponse</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assigned_clients as $client): ?>
                            <tr>
                                <td><?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?></td>
                                <td><?= htmlspecialchars($client['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $client['statut'] === 'complete' ? 'success' : 
                                        ($client['statut'] === 'en_cours' ? 'warning' : 'secondary')
                                    ?>">
                                        <?= 
                                            $client['statut'] === 'complete' ? 'Complète' :
                                            ($client['statut'] === 'en_cours' ? 'En cours' :
                                            ($client['statut'] === 'envoye' ? 'Envoyée' : ucfirst($client['statut'])))
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($client['date_envoi'])): ?>
                                        <small><?= date('d/m/Y H:i', strtotime($client['date_envoi'])) ?></small>
                                    <?php else: ?>
                                        <small class="text-muted">-</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($client['date_reponse'])): ?>
                                        <small><?= date('d/m/Y H:i', strtotime($client['date_reponse'])) ?></small>
                                    <?php else: ?>
                                        <small class="text-muted">-</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
