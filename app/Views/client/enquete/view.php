<?php
/**
 * Vue d'affichage d'une enquête pour un client
 */
?>

<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?controller=Client&action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Enquête</li>
        </ol>
    </nav>

    <!-- En-tête de l'enquête -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h2 class="card-title mb-0">
                <i class="fas fa-clipboard-list"></i> <?= htmlspecialchars($enquete['titre']) ?>
            </h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Campagne :</strong> <?= htmlspecialchars($enquete['campagne_nom']) ?></p>
                    <p><strong>Description :</strong> <?= htmlspecialchars($enquete['description'] ?? 'Aucune description') ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Statut :</strong> 
                        <span class="badge bg-<?= $enquete['participation_statut'] === 'complete' ? 'success' : ($enquete['participation_statut'] === 'en_cours' ? 'warning' : 'info') ?>">
                            <?= ucfirst(str_replace('_', ' ', $enquete['participation_statut'])) ?>
                        </span>
                    </p>
                    <p><strong>Date d'envoi :</strong> <?= date('d/m/Y H:i', strtotime($enquete['date_envoi'] ?? 'now')) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire de l'enquête -->
    <form id="enquete-form" method="POST" action="?controller=Client&action=saveReponses">
        <input type="hidden" name="enquete_id" value="<?= $enquete['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        
        <?php if (empty($questions)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Aucune question n'est disponible pour cette enquête.
            </div>
        <?php else: ?>
            
            <?php foreach ($questions as $index => $question): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Question <?= $index + 1 ?>
                            <?php if ($question['obligatoire']): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?= nl2br(htmlspecialchars($question['texte'])) ?></p>
                        
                        <?php 
                        $reponseValue = $reponses[$question['id']] ?? '';
                        $questionName = "reponse_" . $question['id'];
                        ?>
                        
                        <?php if ($question['type_question'] === 'texte_libre'): ?>
                            <textarea 
                                class="form-control" 
                                name="<?= $questionName ?>" 
                                id="question_<?= $question['id'] ?>"
                                rows="4" 
                                placeholder="Votre réponse..."
                                <?= $question['obligatoire'] ? 'required' : '' ?>
                            ><?= htmlspecialchars($reponseValue) ?></textarea>
                            
                        <?php elseif ($question['type_question'] === 'choix_multiple'): 
                            $options = json_decode($question['options_json'] ?? '[]', true);
                            if (!is_array($options)) $options = [];
                            ?>
                            <?php foreach ($options as $optionIndex => $option): ?>
                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="radio" 
                                        name="<?= $questionName ?>" 
                                        id="question_<?= $question['id'] ?>_<?= $optionIndex ?>"
                                        value="<?= htmlspecialchars($option) ?>"
                                        <?= $reponseValue === $option ? 'checked' : '' ?>
                                        <?= $question['obligatoire'] ? 'required' : '' ?>
                                    >
                                    <label class="form-check-label" for="question_<?= $question['id'] ?>_<?= $optionIndex ?>">
                                        <?= htmlspecialchars($option) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                            
                        <?php elseif ($question['type_question'] === 'notation'): ?>
                            <div class="d-flex align-items-center">
                                <span class="me-2">1</span>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="form-check form-check-inline">
                                        <input 
                                            class="form-check-input" 
                                            type="radio" 
                                            name="<?= $questionName ?>" 
                                            id="question_<?= $question['id'] ?>_<?= $i ?>"
                                            value="<?= $i ?>"
                                            <?= $reponseValue == $i ? 'checked' : '' ?>
                                            <?= $question['obligatoire'] ? 'required' : '' ?>
                                        >
                                        <label class="form-check-label" for="question_<?= $question['id'] ?>_<?= $i ?>">
                                            <?= $i ?>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                                <span class="ms-2">5</span>
                            </div>
                            <small class="text-muted">1 = Très insatisfait, 5 = Très satisfait</small>
                            
                        <?php elseif ($question['type_question'] === 'oui_non'): ?>
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="radio" 
                                    name="<?= $questionName ?>" 
                                    id="question_<?= $question['id'] ?>_oui"
                                    value="Oui"
                                    <?= $reponseValue === 'Oui' ? 'checked' : '' ?>
                                    <?= $question['obligatoire'] ? 'required' : '' ?>
                                >
                                <label class="form-check-label" for="question_<?= $question['id'] ?>_oui">
                                    Oui
                                </label>
                            </div>
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="radio" 
                                    name="<?= $questionName ?>" 
                                    id="question_<?= $question['id'] ?>_non"
                                    value="Non"
                                    <?= $reponseValue === 'Non' ? 'checked' : '' ?>
                                    <?= $question['obligatoire'] ? 'required' : '' ?>
                                >
                                <label class="form-check-label" for="question_<?= $question['id'] ?>_non">
                                    Non
                                </label>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($question['obligatoire']): ?>
                            <small class="text-danger">* Champ obligatoire</small>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Boutons d'action -->
            <div class="card">
                <div class="card-body text-center">
                    <button type="button" class="btn btn-secondary me-2" onclick="autoSave()">
                        <i class="fas fa-save"></i> Sauvegarder le brouillon
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Soumettre l'enquête
                    </button>
                    <a href="?controller=Client&action=dashboard" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-arrow-left"></i> Retour au dashboard
                    </a>
                </div>
            </div>
            
        <?php endif; ?>
    </form>
</div>

<script>
// Auto-sauvegarde
function autoSave() {
    const formData = new FormData(document.getElementById('enquete-form'));
    formData.set('action', 'autoSave');
    
    fetch('?controller=Client&action=autoSave', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Afficher un message de succès temporaire
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                <i class="fas fa-check"></i> Brouillon sauvegardé
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Erreur lors de la sauvegarde:', error);
    });
}

// Validation du formulaire
document.getElementById('enquete-form').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let hasErrors = false;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            hasErrors = true;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (hasErrors) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs obligatoires');
    }
});
</script>
