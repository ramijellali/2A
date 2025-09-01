<?php
// Titre de la page
$pageTitle = 'Créer une campagne';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus"></i> Créer une campagne</h2>
    <a href="?controller=Admin&action=campagnes" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Retour aux campagnes
    </a>
</div>

<?php if (!empty($flash_messages)): ?>
    <?php foreach ($flash_messages as $flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informations de la campagne</h5>
            </div>
            <div class="card-body">
                <form action="?controller=Admin&action=storeCampagne" method="POST" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= !empty($errors['nom']) ? 'is-invalid' : '' ?>" 
                               id="nom" 
                               name="nom" 
                               value="<?= htmlspecialchars($old_data['nom'] ?? '') ?>"
                               placeholder="Ex: Campagne satisfaction Q4 2024">
                        <?php if (!empty($errors['nom'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['nom']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control <?= !empty($errors['description']) ? 'is-invalid' : '' ?>" 
                                  id="description" 
                                  name="description" 
                                  rows="4"
                                  placeholder="Décrivez l'objectif et le contexte de cette campagne..."><?= htmlspecialchars($old_data['description'] ?? '') ?></textarea>
                        <?php if (!empty($errors['description'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div>
                        <?php endif; ?>
                        <div class="form-text">Cette description sera visible dans les rapports et l'interface d'administration.</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_debut" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control <?= !empty($errors['date_debut']) ? 'is-invalid' : '' ?>" 
                                       id="date_debut" 
                                       name="date_debut" 
                                       value="<?= htmlspecialchars($old_data['date_debut'] ?? date('Y-m-d')) ?>"
                                       min="<?= date('Y-m-d') ?>">
                                <?php if (!empty($errors['date_debut'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['date_debut']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_fin" class="form-label">Date de fin <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control <?= !empty($errors['date_fin']) ? 'is-invalid' : '' ?>" 
                                       id="date_fin" 
                                       name="date_fin" 
                                       value="<?= htmlspecialchars($old_data['date_fin'] ?? '') ?>"
                                       min="<?= date('Y-m-d') ?>">
                                <?php if (!empty($errors['date_fin'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['date_fin']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                        <select class="form-select <?= !empty($errors['statut']) ? 'is-invalid' : '' ?>" 
                                id="statut" 
                                name="statut">
                            <option value="en_preparation" <?= ($old_data['statut'] ?? 'en_preparation') === 'en_preparation' ? 'selected' : '' ?>>
                                En préparation
                            </option>
                            <option value="active" <?= ($old_data['statut'] ?? '') === 'active' ? 'selected' : '' ?>>
                                Active
                            </option>
                        </select>
                        <?php if (!empty($errors['statut'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['statut']) ?></div>
                        <?php endif; ?>
                        <div class="form-text">Les campagnes en préparation ne sont pas visibles aux agents.</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="?controller=Admin&action=campagnes" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Créer la campagne
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
                            <strong>Définir le titre :</strong> Choisissez un nom clair et descriptif
                        </li>
                        <li class="mb-2">
                            <strong>Rédiger la description :</strong> Expliquez l'objectif et le contexte
                        </li>
                        <li class="mb-2">
                            <strong>Définir la période :</strong> Spécifiez les dates de début et fin
                        </li>
                        <li class="mb-2">
                            <strong>Choisir le statut :</strong> Brouillon pour préparer, Active pour lancer
                        </li>
                    </ol>
                    
                    <hr class="my-3">
                    
                    <h6>Bonnes pratiques :</h6>
                    <ul class="ps-3">
                        <li class="mb-1">Utilisez des titres informatifs</li>
                        <li class="mb-1">Prévoyez une période suffisante</li>
                        <li class="mb-1">Commencez en brouillon pour tester</li>
                        <li>Activez quand tout est prêt</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validation côté client
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const nomInput = document.getElementById('nom');
    const descriptionInput = document.getElementById('description');
    const dateDebutInput = document.getElementById('date_debut');
    const dateFinInput = document.getElementById('date_fin');
    
    // Validation en temps réel
    nomInput.addEventListener('blur', function() {
        if (this.value.trim().length < 3) {
            this.classList.add('is-invalid');
            showFieldError(this, 'Le nom doit contenir au moins 3 caractères');
        } else {
            this.classList.remove('is-invalid');
            removeFieldError(this);
        }
    });
    
    descriptionInput.addEventListener('blur', function() {
        if (this.value.trim().length === 0) {
            this.classList.add('is-invalid');
            showFieldError(this, 'La description est obligatoire');
        } else {
            this.classList.remove('is-invalid');
            removeFieldError(this);
        }
    });
    
    function validateDates() {
        const dateDebut = new Date(dateDebutInput.value);
        const dateFin = new Date(dateFinInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (dateDebut < today) {
            dateDebutInput.classList.add('is-invalid');
            showFieldError(dateDebutInput, 'La date de début ne peut pas être dans le passé');
            return false;
        }
        
        if (dateFin <= dateDebut) {
            dateFinInput.classList.add('is-invalid');
            showFieldError(dateFinInput, 'La date de fin doit être postérieure à la date de début');
            return false;
        }
        
        dateDebutInput.classList.remove('is-invalid');
        dateFinInput.classList.remove('is-invalid');
        removeFieldError(dateDebutInput);
        removeFieldError(dateFinInput);
        return true;
    }
    
    dateDebutInput.addEventListener('change', validateDates);
    dateFinInput.addEventListener('change', validateDates);
    
    // Validation à la soumission
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Vérifier le nom
        if (nomInput.value.trim().length < 3) {
            nomInput.classList.add('is-invalid');
            showFieldError(nomInput, 'Le nom est obligatoire (min. 3 caractères)');
            isValid = false;
        }
        
        // Vérifier la description
        if (descriptionInput.value.trim().length === 0) {
            descriptionInput.classList.add('is-invalid');
            showFieldError(descriptionInput, 'La description est obligatoire');
            isValid = false;
        }
        
        // Vérifier les dates
        if (!validateDates()) {
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
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
