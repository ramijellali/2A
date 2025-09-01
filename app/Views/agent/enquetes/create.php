<?php
// Titre de la page
$pageTitle = 'Nouvelle Enquête';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus"></i> Nouvelle Enquête</h2>
    <a href="?controller=Agent&action=enquetes" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
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
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Informations de l'enquête</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="?controller=Agent&action=storeEnquete">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre de l'enquête <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= !empty($errors['titre']) ? 'is-invalid' : '' ?>" 
                               id="titre" 
                               name="titre" 
                               value="<?= htmlspecialchars($old_data['titre'] ?? '') ?>"
                               placeholder="Ex: Satisfaction service client Q4 2024">
                        <?php if (!empty($errors['titre'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['titre']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="4"
                                  placeholder="Décrivez l'objectif et le contexte de cette enquête..."><?= htmlspecialchars($old_data['description'] ?? '') ?></textarea>
                        <div class="form-text">Cette description sera visible par les clients lors de leur participation à l'enquête.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="campagne_id" class="form-label">Campagne associée <span class="text-danger">*</span></label>
                        <select class="form-select <?= !empty($errors['campagne_id']) ? 'is-invalid' : '' ?>" 
                                id="campagne_id" 
                                name="campagne_id">
                            <option value="">-- Sélectionner une campagne --</option>
                            <?php foreach ($campagnes as $campagne): ?>
                                <option value="<?= $campagne['id'] ?>" 
                                        <?= ($old_data['campagne_id'] ?? '') == $campagne['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($campagne['nom']) ?>
                                    <?php if (!empty($campagne['description'])): ?>
                                        - <?= htmlspecialchars(substr($campagne['description'], 0, 50)) ?>...
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['campagne_id'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['campagne_id']) ?></div>
                        <?php endif; ?>
                        <?php if (empty($campagnes)): ?>
                            <div class="form-text text-warning">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Aucune campagne active disponible. 
                                <a href="?controller=Admin&action=campagnes">Créer une campagne</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut initial</label>
                        <select class="form-select" id="statut" name="statut">
                            <option value="brouillon" selected>Brouillon</option>
                        </select>
                        <div class="form-text">L'enquête sera créée en mode brouillon. Vous pourrez l'activer après avoir ajouté les questions.</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="?controller=Agent&action=enquetes" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-success" <?= empty($campagnes) ? 'disabled' : '' ?>>
                            <i class="fas fa-save"></i> Créer l'enquête
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Guide de création</h6>
            </div>
            <div class="card-body">
                <div class="small">
                    <h6>Étapes de création :</h6>
                    <ol class="ps-3">
                        <li class="mb-2">
                            <strong>Informations générales</strong><br>
                            <small class="text-muted">Titre et description de l'enquête</small>
                        </li>
                        <li class="mb-2">
                            <strong>Ajout des questions</strong><br>
                            <small class="text-muted">Créer les questions après la sauvegarde</small>
                        </li>
                        <li class="mb-2">
                            <strong>Assignment des clients</strong><br>
                            <small class="text-muted">Sélectionner les participants</small>
                        </li>
                        <li class="mb-2">
                            <strong>Activation</strong><br>
                            <small class="text-muted">Rendre l'enquête disponible</small>
                        </li>
                    </ol>
                </div>
                
                <hr>
                
                <h6><i class="fas fa-lightbulb"></i> Conseils</h6>
                <ul class="small text-muted ps-3">
                    <li class="mb-1">Choisissez un titre clair et descriptif</li>
                    <li class="mb-1">La description aide les clients à comprendre l'objectif</li>
                    <li class="mb-1">Associez l'enquête à une campagne active</li>
                    <li>Vous pourrez modifier ces informations plus tard</li>
                </ul>
            </div>
        </div>
        
        <?php if (!empty($campagnes)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-tags"></i> Campagnes disponibles</h6>
            </div>
            <div class="card-body">
                <?php foreach (array_slice($campagnes, 0, 3) as $campagne): ?>
                    <div class="mb-2 p-2 bg-light rounded">
                        <strong class="small"><?= htmlspecialchars($campagne['nom']) ?></strong>
                        <?php if (!empty($campagne['description'])): ?>
                            <br><small class="text-muted"><?= htmlspecialchars(substr($campagne['description'], 0, 80)) ?>...</small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <?php if (count($campagnes) > 3): ?>
                    <small class="text-muted">Et <?= count($campagnes) - 3 ?> autre(s)...</small>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Validation côté client
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const titreInput = document.getElementById('titre');
    const campagneSelect = document.getElementById('campagne_id');
    
    // Validation en temps réel
    titreInput.addEventListener('blur', function() {
        if (this.value.trim().length < 3) {
            this.classList.add('is-invalid');
            showFieldError(this, 'Le titre doit contenir au moins 3 caractères');
        } else {
            this.classList.remove('is-invalid');
            removeFieldError(this);
        }
    });
    
    campagneSelect.addEventListener('change', function() {
        if (!this.value) {
            this.classList.add('is-invalid');
            showFieldError(this, 'Veuillez sélectionner une campagne');
        } else {
            this.classList.remove('is-invalid');
            removeFieldError(this);
        }
    });
    
    // Validation à la soumission
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Vérifier le titre
        if (titreInput.value.trim().length < 3) {
            titreInput.classList.add('is-invalid');
            showFieldError(titreInput, 'Le titre est obligatoire (min. 3 caractères)');
            isValid = false;
        }
        
        // Vérifier la campagne
        if (!campagneSelect.value) {
            campagneSelect.classList.add('is-invalid');
            showFieldError(campagneSelect, 'Veuillez sélectionner une campagne');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            // Scroll vers le premier champ en erreur
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });
    
    function showFieldError(field, message) {
        removeFieldError(field);
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;
        field.parentNode.appendChild(feedback);
    }
    
    function removeFieldError(field) {
        const existingFeedback = field.parentNode.querySelector('.invalid-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
    }
});
</script>
