<?php
// Titre de la page
$pageTitle = 'Résultats - ' . htmlspecialchars($enquete['titre']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-chart-bar"></i> Résultats de l'enquête</h2>
    <div class="d-flex gap-2">
        <a href="?controller=Agent&action=exportResults&id=<?= $enquete['id'] ?>" class="btn btn-success">
            <i class="fas fa-download"></i> Exporter CSV
        </a>
        <a href="?controller=Agent&action=viewEnquete&id=<?= $enquete['id'] ?>" class="btn btn-outline-info">
            <i class="fas fa-eye"></i> Voir enquête
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

<!-- En-tête de l'enquête -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4><?= htmlspecialchars($enquete['titre']) ?></h4>
                <?php if (!empty($enquete['description'])): ?>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($enquete['description'])) ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <?php
                $badges = [
                    'brouillon' => ['secondary', 'draft'],
                    'active' => ['success', 'play'],
                    'terminee' => ['info', 'stop'],
                    'archivee' => ['dark', 'archive']
                ];
                $badge = $badges[$enquete['statut']] ?? ['secondary', 'question'];
                ?>
                <span class="badge bg-<?= $badge[0] ?> fs-6 float-end">
                    <i class="fas fa-<?= $badge[1] ?>"></i> <?= ucfirst($enquete['statut']) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques globales -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-question-circle fa-2x text-primary mb-2"></i>
                <h3 class="text-primary mb-0"><?= count($questions) ?></h3>
                <small class="text-muted">Questions</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x text-info mb-2"></i>
                <h3 class="text-info mb-0"><?= $stats['clients_assignes'] ?? 0 ?></h3>
                <small class="text-muted">Clients invités</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h3 class="text-success mb-0"><?= $stats['clients_ayant_repondu'] ?? 0 ?></h3>
                <small class="text-muted">Ont répondu</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-percentage fa-2x text-warning mb-2"></i>
                <h3 class="text-warning mb-0">
                    <?= ($stats['clients_assignes'] ?? 0) > 0 ? round((($stats['clients_ayant_repondu'] ?? 0) / ($stats['clients_assignes'] ?? 1)) * 100, 1) : 0 ?>%
                </h3>
                <small class="text-muted">Taux de participation</small>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($stats['clients_ayant_repondu'])): ?>
    <!-- Analyse par question -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Analyse des réponses</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($questions)): ?>
                        <?php foreach ($questions as $index => $question): ?>
                            <div class="mb-4 <?= $index < count($questions) - 1 ? 'border-bottom pb-4' : '' ?>">
                                <h6 class="mb-3">
                                    <span class="badge bg-primary me-2"><?= $index + 1 ?></span>
                                    <?= htmlspecialchars($question['texte']) ?>
                                </h6>
                                
                                <?php
                                // Filtrer les réponses pour cette question
                                $questionReponses = array_filter($reponses, function($r) use ($question) {
                                    return $r['question_id'] == $question['id'];
                                });
                                ?>
                                
                                <?php if (!empty($questionReponses)): ?>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <?php if ($question['type_question'] === 'rating'): ?>
                                                <!-- Analyse pour questions de notation -->
                                                <?php
                                                $values = array_map(function($r) { return floatval($r['valeur']); }, $questionReponses);
                                                $moyenne = array_sum($values) / count($values);
                                                $min = min($values);
                                                $max = max($values);
                                                ?>
                                                <div class="bg-light p-3 rounded">
                                                    <div class="row text-center">
                                                        <div class="col-4">
                                                            <h4 class="text-success mb-0"><?= number_format($moyenne, 1) ?></h4>
                                                            <small class="text-muted">Moyenne</small>
                                                        </div>
                                                        <div class="col-4">
                                                            <h4 class="text-info mb-0"><?= $min ?></h4>
                                                            <small class="text-muted">Minimum</small>
                                                        </div>
                                                        <div class="col-4">
                                                            <h4 class="text-warning mb-0"><?= $max ?></h4>
                                                            <small class="text-muted">Maximum</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            <?php elseif ($question['type_question'] === 'multiple_choice' || $question['type_question'] === 'yes_no'): ?>
                                                <!-- Analyse pour questions à choix multiples -->
                                                <?php
                                                $choiceCount = [];
                                                foreach ($questionReponses as $rep) {
                                                    $val = $rep['valeur'];
                                                    $choiceCount[$val] = ($choiceCount[$val] ?? 0) + 1;
                                                }
                                                arsort($choiceCount);
                                                $total = count($questionReponses);
                                                ?>
                                                
                                                <div class="bg-light p-3 rounded">
                                                    <?php foreach ($choiceCount as $choice => $count): ?>
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span><?= htmlspecialchars($choice) ?></span>
                                                                <span class="text-muted"><?= $count ?> (<?= round(($count / $total) * 100, 1) ?>%)</span>
                                                            </div>
                                                            <div class="progress" style="height: 8px;">
                                                                <div class="progress-bar" 
                                                                     style="width: <?= ($count / $total) * 100 ?>%"></div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                
                                            <?php else: ?>
                                                <!-- Réponses textuelles -->
                                                <div class="bg-light p-3 rounded" style="max-height: 200px; overflow-y: auto;">
                                                    <?php foreach (array_slice($questionReponses, 0, 5) as $rep): ?>
                                                        <div class="mb-2 p-2 bg-white rounded">
                                                            <small class="text-muted"><?= htmlspecialchars($rep['client_nom'] . ' ' . $rep['client_prenom']) ?>:</small>
                                                            <div><?= nl2br(htmlspecialchars($rep['valeur'])) ?></div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <?php if (count($questionReponses) > 5): ?>
                                                        <small class="text-muted">Et <?= count($questionReponses) - 5 ?> autre(s) réponse(s)...</small>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h4 class="text-primary"><?= count($questionReponses) ?></h4>
                                                <small class="text-muted">Réponses</small>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted fst-italic">Aucune réponse pour cette question</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Répartition des participants -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-pie-chart"></i> Participation</h6>
                </div>
                <div class="card-body">
                    <?php
                    $totalInvites = $stats['clients_assignes'] ?? 0;
                    $totalRepondu = $stats['clients_ayant_repondu'] ?? 0;
                    $tauxParticipation = $totalInvites > 0 ? ($totalRepondu / $totalInvites) * 100 : 0;
                    ?>
                    
                    <div class="text-center mb-3">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" 
                                 style="width: <?= $tauxParticipation ?>%">
                                <?= round($tauxParticipation, 1) ?>%
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <?= $totalRepondu ?> sur <?= $totalInvites ?> clients ont participé
                        </small>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h5 class="text-success mb-0"><?= $totalRepondu ?></h5>
                                <small class="text-muted">Ont répondu</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="text-muted mb-0"><?= $totalInvites - $totalRepondu ?></h5>
                            <small class="text-muted">N'ont pas répondu</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Note moyenne globale -->
            <?php if (!empty($stats['note_moyenne'])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-star"></i> Note moyenne</h6>
                    </div>
                    <div class="card-body text-center">
                        <h2 class="text-warning mb-2"><?= number_format($stats['note_moyenne'], 1) ?>/5</h2>
                        <div class="mb-2">
                            <?php 
                            $fullStars = floor($stats['note_moyenne']);
                            $halfStar = $stats['note_moyenne'] - $fullStars >= 0.5;
                            ?>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $fullStars): ?>
                                    <i class="fas fa-star text-warning"></i>
                                <?php elseif ($i == $fullStars + 1 && $halfStar): ?>
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                <?php else: ?>
                                    <i class="far fa-star text-muted"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <small class="text-muted">Basé sur les questions de notation</small>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Actions rapides -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-tools"></i> Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="?controller=Agent&action=exportResults&id=<?= $enquete['id'] ?>" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-file-csv"></i> Exporter CSV
                        </a>
                        
                        <a href="?controller=Agent&action=viewEnquete&id=<?= $enquete['id'] ?>" 
                           class="btn btn-outline-info btn-sm">
                            <i class="fas fa-eye"></i> Voir l'enquête
                        </a>
                        
                        <a href="?controller=Agent&action=editEnquete&id=<?= $enquete['id'] ?>" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        
                        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Aucune réponse -->
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-chart-bar fa-4x text-muted mb-4"></i>
            <h4>Aucune réponse disponible</h4>
            <p class="text-muted">Cette enquête n'a pas encore reçu de réponses des clients.</p>
            
            <?php if ($enquete['statut'] === 'brouillon'): ?>
                <p class="text-info">
                    <i class="fas fa-info-circle"></i> 
                    L'enquête est encore en brouillon. Activez-la pour que les clients puissent y répondre.
                </p>
                <a href="?controller=Agent&action=editEnquete&id=<?= $enquete['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifier l'enquête
                </a>
            <?php elseif (($stats['clients_assignes'] ?? 0) === 0): ?>
                <p class="text-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Aucun client n'est assigné à cette enquête.
                </p>
                <a href="?controller=Agent&action=editEnquete&id=<?= $enquete['id'] ?>" class="btn btn-warning">
                    <i class="fas fa-users"></i> Assigner des clients
                </a>
            <?php else: ?>
                <p class="text-muted">Les clients n'ont pas encore répondu à cette enquête.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<style>
@media print {
    .btn, .card-header .fas, .alert, nav, .d-flex.justify-content-between { 
        display: none !important; 
    }
    .card { 
        border: 1px solid #ddd !important; 
        box-shadow: none !important; 
    }
}
</style>
