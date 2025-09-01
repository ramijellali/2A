<?php
// Titre de la page
$pageTitle = 'Gestion des réponses';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-chart-bar"></i> Gestion des réponses</h2>
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
                        <p class="card-text">Réponses totales</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-bar fa-2x"></i>
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
                        <h4 class="card-title"><?= $stats['derniere_semaine'] ?></h4>
                        <p class="card-text">Cette semaine</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-week fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Répartition par type de question</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($stats['par_type'] as $type => $count): ?>
                        <div class="col-md-6 mb-2">
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

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <input type="hidden" name="controller" value="Admin">
            <input type="hidden" name="action" value="reponses">
            
            <div class="col-md-3">
                <label for="type_question" class="form-label">Type de question</label>
                <select class="form-select" name="type_question" id="type_question">
                    <option value="">Tous les types</option>
                    <option value="texte_libre">Texte libre</option>
                    <option value="choix_multiple">Choix multiple</option>
                    <option value="notation">Notation</option>
                    <option value="oui_non">Oui/Non</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="date_debut" class="form-label">Date début</label>
                <input type="date" class="form-control" name="date_debut" id="date_debut">
            </div>
            
            <div class="col-md-3">
                <label for="date_fin" class="form-label">Date fin</label>
                <input type="date" class="form-control" name="date_fin" id="date_fin">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="?controller=Admin&action=reponses" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Liste des réponses -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Liste des réponses</h5>
    </div>
    <div class="card-body">
        <?php if (empty($reponses)): ?>
            <div class="text-center py-4">
                <i class="fas fa-chart-bar fa-3x text-muted"></i>
                <h6 class="mt-3 text-muted">Aucune réponse trouvée</h6>
                <p class="text-muted">Il n'y a pas encore de réponses dans le système.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Type</th>
                            <th>Réponse</th>
                            <th>Client</th>
                            <th>Enquête</th>
                            <th>Campagne</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reponses as $reponse): ?>
                            <tr>
                                <td><?= $reponse['id'] ?></td>
                                <td>
                                    <div>
                                        <small><?= htmlspecialchars($reponse['question_texte'] ?? 'Non définie') ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        ($reponse['question_type'] ?? '') === 'texte_libre' ? 'info' : 
                                        (($reponse['question_type'] ?? '') === 'choix_unique' ? 'primary' : 
                                        (($reponse['question_type'] ?? '') === 'choix_multiple' ? 'success' : 
                                        (($reponse['question_type'] ?? '') === 'notation' ? 'warning' : 'secondary')))
                                    ?>">
                                        <?= ucfirst(str_replace('_', ' ', $reponse['question_type'] ?? 'inconnu')) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="max-width: 150px;">
                                        <?php 
                                        $valeur = $reponse['valeur'] ?? '';
                                        if (($reponse['question_type'] ?? '') === 'choix_multiple') {
                                            $choix = json_decode($valeur ?: '[]', true);
                                            $affichage = is_array($choix) ? implode(', ', $choix) : $valeur;
                                        } else {
                                            $affichage = $valeur;
                                        }
                                        
                                        $texte = htmlspecialchars(substr($affichage ?: '', 0, 50));
                                        echo $texte;
                                        if (strlen($affichage ?: '') > 50) echo '...';
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($reponse['client_nom'] ?? 'Client inconnu') ?></small>
                                </td>
                                <td>
                                    <div>
                                        <small><?= htmlspecialchars($reponse['enquete_titre'] ?? 'Non définie') ?></small>
                                    </div>
                                    <small class="text-muted">
                                        <span class="badge bg-<?= 
                                            ($reponse['enquete_statut'] ?? '') === 'active' ? 'success' : 
                                            (($reponse['enquete_statut'] ?? '') === 'brouillon' ? 'secondary' : 'warning')
                                        ?>">
                                            <?= ucfirst($reponse['enquete_statut'] ?? 'Inconnue') ?>
                                        </span>
                                    </small>
                                </td>
                                <td>
                                    <?php if (!empty($reponse['campagne_nom'])): ?>
                                        <small><?= htmlspecialchars($reponse['campagne_nom']) ?></small>
                                    <?php else: ?>
                                        <small class="text-muted">Non définie</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?= date('d/m/Y', strtotime($reponse['date_reponse'])) ?></small>
                                    <br>
                                    <small class="text-muted"><?= date('H:i', strtotime($reponse['date_reponse'])) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?controller=Admin&action=viewReponse&id=<?= $reponse['id'] ?>" 
                                           class="btn btn-outline-info" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (!empty($reponse['enquete_id'])): ?>
                                        <a href="?controller=Agent&action=viewEnquete&id=<?= $reponse['enquete_id'] ?>" 
                                           class="btn btn-outline-primary" title="Voir enquête">
                                            <i class="fas fa-poll"></i>
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
