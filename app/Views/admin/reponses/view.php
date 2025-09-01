<?php
// Titre de la page
$pageTitle = 'Détails de la réponse';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-comment"></i> Détails de la réponse</h2>
    <div>
        <?php if (!empty($reponse['enquete_id'])): ?>
        <a href="?controller=Agent&action=viewEnquete&id=<?= $reponse['enquete_id'] ?>" class="btn btn-outline-primary me-2">
            <i class="fas fa-poll"></i> Voir l'enquête
        </a>
        <?php endif; ?>
        <a href="?controller=Admin&action=reponses" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux réponses
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

<!-- Informations de la réponse -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informations de la réponse</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-4">ID :</dt>
                            <dd class="col-8"><?= $reponse['id'] ?></dd>
                            
                            <dt class="col-4">Type :</dt>
                            <dd class="col-8">
                                <span class="badge bg-<?= 
                                    ($reponse['question_type'] ?? '') === 'texte_libre' ? 'info' : 
                                    (($reponse['question_type'] ?? '') === 'choix_unique' ? 'primary' : 
                                    (($reponse['question_type'] ?? '') === 'choix_multiple' ? 'success' : 
                                    (($reponse['question_type'] ?? '') === 'notation' ? 'warning' : 'secondary')))
                                ?>">
                                    <?= ucfirst(str_replace('_', ' ', $reponse['question_type'] ?? 'inconnu')) ?>
                                </span>
                            </dd>
                            
                            <dt class="col-4">Date :</dt>
                            <dd class="col-8"><?= date('d/m/Y à H:i', strtotime($reponse['date_reponse'])) ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-4">Client :</dt>
                            <dd class="col-8">
                                <div><?= htmlspecialchars($reponse['client_nom'] ?? 'Client inconnu') ?></div>
                                <small class="text-muted"><?= htmlspecialchars($reponse['client_email'] ?? 'Email inconnu') ?></small>
                            </dd>
                            
                            <dt class="col-4">Enquête :</dt>
                            <dd class="col-8">
                                <?php if (!empty($reponse['enquete_id'])): ?>
                                <a href="?controller=Agent&action=viewEnquete&id=<?= $reponse['enquete_id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($reponse['enquete_titre'] ?? 'Non définie') ?>
                                </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($reponse['enquete_titre'] ?? 'Non définie') ?>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                </div>
                
                <hr>
                
                <div>
                    <h6>Question posée :</h6>
                    <div class="p-3 bg-light rounded">
                        <?= nl2br(htmlspecialchars($reponse['question_texte'] ?? 'Question non définie')) ?>
                    </div>
                </div>
                
                <hr>
                
                <div>
                    <h6>Réponse donnée :</h6>
                    <div class="p-3 bg-primary bg-opacity-10 rounded">
                        <?php 
                        $valeur = $reponse['valeur'] ?? '';
                        
                        if (($reponse['question_type'] ?? '') === 'choix_multiple') {
                            $choix = json_decode($valeur ?: '[]', true);
                            if (is_array($choix) && !empty($choix)) {
                                echo '<ul class="mb-0">';
                                foreach ($choix as $option) {
                                    echo '<li>' . htmlspecialchars($option ?: '') . '</li>';
                                }
                                echo '</ul>';
                            } else {
                                echo htmlspecialchars($valeur ?: '');
                            }
                        } elseif (($reponse['question_type'] ?? '') === 'notation') {
                            $note = (int)$valeur;
                            echo '<div class="d-flex align-items-center">';
                            echo '<strong class="me-2">' . htmlspecialchars($valeur ?: '') . '/5</strong>';
                            
                            // Affichage des étoiles
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $note) {
                                    echo '<i class="fas fa-star text-warning"></i>';
                                } else {
                                    echo '<i class="far fa-star text-muted"></i>';
                                }
                            }
                            echo '</div>';
                        } elseif (($reponse['question_type'] ?? '') === 'oui_non') {
                            $class = strtolower($valeur ?: '') === 'oui' ? 'success' : 'danger';
                            echo '<span class="badge bg-' . $class . ' fs-6">' . htmlspecialchars($valeur ?: '') . '</span>';
                        } else {
                            echo nl2br(htmlspecialchars($valeur ?: ''));
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informations contextuelles</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted">Horodatage</h6>
                    <p class="mb-1"><strong>Date :</strong> <?= date('d/m/Y', strtotime($reponse['date_reponse'])) ?></p>
                    <p class="mb-1"><strong>Heure :</strong> <?= date('H:i:s', strtotime($reponse['date_reponse'])) ?></p>
                    <p class="mb-0"><strong>Jour :</strong> <?= date('l', strtotime($reponse['date_reponse'])) ?></p>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <h6 class="text-muted">Client</h6>
                    <p class="mb-1"><strong>Nom :</strong> <?= htmlspecialchars($reponse['client_nom'] ?? 'Inconnu') ?></p>
                    <p class="mb-0"><strong>Email :</strong> <?= htmlspecialchars($reponse['client_email'] ?? 'Inconnu') ?></p>
                </div>
                
                <?php if (!empty($reponse['enquete_titre'])): ?>
                <hr>
                
                <div class="mb-3">
                    <h6 class="text-muted">Enquête</h6>
                    <p class="mb-1"><strong>Titre :</strong> <?= htmlspecialchars($reponse['enquete_titre']) ?></p>
                    <?php if (!empty($reponse['enquete_id'])): ?>
                    <a href="?controller=Agent&action=viewEnquete&id=<?= $reponse['enquete_id'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> Voir l'enquête
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php 
                $dureeDepuis = time() - strtotime($reponse['date_reponse']);
                $jours = floor($dureeDepuis / (24 * 60 * 60));
                $heures = floor(($dureeDepuis % (24 * 60 * 60)) / (60 * 60));
                ?>
                
                <hr>
                
                <div>
                    <h6 class="text-muted">Ancienneté</h6>
                    <p class="mb-0">
                        <?php if ($jours > 0): ?>
                            Il y a <?= $jours ?> jour<?= $jours > 1 ? 's' : '' ?>
                            <?php if ($heures > 0): ?>
                                et <?= $heures ?> heure<?= $heures > 1 ? 's' : '' ?>
                            <?php endif; ?>
                        <?php elseif ($heures > 0): ?>
                            Il y a <?= $heures ?> heure<?= $heures > 1 ? 's' : '' ?>
                        <?php else: ?>
                            Il y a moins d'une heure
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
