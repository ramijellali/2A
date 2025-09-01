<?php
// Titre de la page
$pageTitle = 'Détails de la question';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-question-circle"></i> Détails de la question</h2>
    <div>
        <a href="?controller=Agent&action=viewEnquete&id=<?= $question['enquete_id'] ?>" class="btn btn-outline-primary me-2">
            <i class="fas fa-poll"></i> Voir l'enquête
        </a>
        <a href="?controller=Admin&action=questions" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux questions
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

<!-- Informations de la question -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informations de la question</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-4">ID :</dt>
                            <dd class="col-8"><?= $question['id'] ?></dd>
                            
                            <dt class="col-4">Type :</dt>
                            <dd class="col-8">
                                <span class="badge bg-<?= 
                                    $question['type_question'] === 'texte_libre' ? 'info' : 
                                    ($question['type_question'] === 'choix_unique' ? 'primary' : 
                                    ($question['type_question'] === 'choix_multiple' ? 'success' : 
                                    ($question['type_question'] === 'echelle' ? 'warning' : 'secondary')))
                                ?>">
                                    <?= ucfirst(str_replace('_', ' ', $question['type_question'])) ?>
                                </span>
                            </dd>
                            
                            <dt class="col-4">Ordre :</dt>
                            <dd class="col-8"><?= $question['ordre_affichage'] ?? 'Non défini' ?></dd>
                            
                            <dt class="col-4">Obligatoire :</dt>
                            <dd class="col-8">
                                <?php if ($question['obligatoire']): ?>
                                    <span class="badge bg-danger">Oui</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Non</span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-4">Enquête :</dt>
                            <dd class="col-8">
                                <a href="?controller=Agent&action=viewEnquete&id=<?= $question['enquete_id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($question['enquete_titre'] ?? 'Non définie') ?>
                                </a>
                            </dd>
                            
                            <dt class="col-4">Date création :</dt>
                            <dd class="col-8">
                                <?php if (!empty($question['date_creation'])): ?>
                                    <?= date('d/m/Y à H:i', strtotime($question['date_creation'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">Non définie</span>
                                <?php endif; ?>
                            </dd>
                            
                            <dt class="col-4">Date modification :</dt>
                            <dd class="col-8">
                                <?php if (!empty($question['date_modification'])): ?>
                                    <?= date('d/m/Y à H:i', strtotime($question['date_modification'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">Jamais modifiée</span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                </div>
                
                <hr>
                
                <div>
                    <h6>Texte de la question :</h6>
                    <div class="p-3 bg-light rounded">
                        <?= nl2br(htmlspecialchars($question['texte'])) ?>
                    </div>
                </div>
                
                <?php if (!empty($question['options_json'])): ?>
                    <hr>
                    <div>
                        <h6>Options disponibles :</h6>
                        <?php 
                        $options = json_decode($question['options_json'], true);
                        if ($options): ?>
                            <ul class="list-group">
                                <?php foreach ($options as $index => $option): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($option) ?>
                                        <span class="badge bg-primary rounded-pill"><?= $index + 1 ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Statistiques des réponses</h6>
            </div>
            <div class="card-body">
                <?php if (empty($reponses)): ?>
                    <div class="text-center py-3">
                        <i class="fas fa-chart-bar fa-2x text-muted"></i>
                        <p class="mt-2 text-muted mb-0">Aucune réponse encore</p>
                    </div>
                <?php else: ?>
                    <div class="text-center mb-3">
                        <h4 class="text-primary mb-0"><?= count($reponses) ?></h4>
                        <small class="text-muted">Réponses reçues</small>
                    </div>
                    
                    <?php if (in_array($question['type_question'], ['choix_unique', 'choix_multiple']) && !empty($question['options_json'])): ?>
                        <?php 
                        $options = json_decode($question['options_json'], true);
                        $choixCounts = [];
                        
                        // Compter les réponses pour chaque option
                        foreach ($reponses as $reponse) {
                            $valeur = $reponse['valeur'];
                            if ($question['type_question'] === 'choix_multiple') {
                                $choixMultiples = json_decode($valeur, true);
                                if (is_array($choixMultiples)) {
                                    foreach ($choixMultiples as $choix) {
                                        $choixCounts[$choix] = ($choixCounts[$choix] ?? 0) + 1;
                                    }
                                }
                            } else {
                                $choixCounts[$valeur] = ($choixCounts[$valeur] ?? 0) + 1;
                            }
                        }
                        ?>
                        
                        <hr>
                        <h6>Répartition des choix :</h6>
                        <?php foreach ($options as $index => $option): ?>
                            <?php 
                            $count = $choixCounts[$option] ?? 0;
                            $pourcentage = count($reponses) > 0 ? round(($count / count($reponses)) * 100) : 0;
                            ?>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small">
                                    <span><?= htmlspecialchars(substr($option, 0, 20)) ?><?= strlen($option) > 20 ? '...' : '' ?></span>
                                    <span><?= $count ?> (<?= $pourcentage ?>%)</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar" style="width: <?= $pourcentage ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Dernières réponses -->
<?php if (!empty($reponses)): ?>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-comments"></i> Dernières réponses (<?= count($reponses) ?>)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Réponse</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Limiter aux 10 dernières réponses
                    $dernieresReponses = array_slice($reponses, -10);
                    foreach (array_reverse($dernieresReponses) as $reponse): 
                    ?>
                        <tr>
                            <td>
                                <small>
                                    <?= htmlspecialchars($reponse['client_nom'] ?? 'Client #' . ($reponse['client_id'] ?? 'inconnu')) ?>
                                </small>
                            </td>
                            <td>
                                <small>
                                    <?php 
                                    $valeur = $reponse['valeur'] ?? '';
                                    if ($question['type_question'] === 'choix_multiple') {
                                        $choix = json_decode($valeur ?: '[]', true);
                                        echo htmlspecialchars(is_array($choix) ? implode(', ', $choix) : $valeur);
                                    } else {
                                        echo htmlspecialchars(substr($valeur ?: '', 0, 50));
                                        echo strlen($valeur ?: '') > 50 ? '...' : '';
                                    }
                                    ?>
                                </small>
                            </td>
                            <td>
                                <small><?= date('d/m/Y H:i', strtotime($reponse['date_reponse'])) ?></small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
