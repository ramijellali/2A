<?php
// Titre de la page
$pageTitle = 'Modifier une campagne';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-edit"></i> Modifier la campagne</h2>
    <div>
        <a href="?controller=Admin&action=viewCampagne&id=<?= $campagne['id'] ?>" class="btn btn-outline-info me-2">
            <i class="fas fa-eye"></i> Voir
        </a>
        <a href="?controller=Admin&action=campagnes" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux campagnes
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

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Modifier les informations</h5>
            </div>
            <div class="card-body">
                <form action="?controller=Admin&action=updateCampagne" method="POST" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="id" value="<?= $campagne['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= !empty($errors['nom']) ? 'is-invalid' : '' ?>" 
                               id="nom" 
                               name="nom" 
                               value="<?= htmlspecialchars($old_data['nom'] ?? $campagne['nom']) ?>"
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
                                  placeholder="Décrivez l'objectif et le contexte de cette campagne..."><?= htmlspecialchars($old_data['description'] ?? $campagne['description']) ?></textarea>
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
                                       value="<?= htmlspecialchars($old_data['date_debut'] ?? $campagne['date_debut']) ?>">
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
                                       value="<?= htmlspecialchars($old_data['date_fin'] ?? $campagne['date_fin']) ?>">
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
                            <option value="en_preparation" <?= ($old_data['statut'] ?? $campagne['statut']) === 'en_preparation' ? 'selected' : '' ?>>
                                En préparation
                            </option>
                            <option value="active" <?= ($old_data['statut'] ?? $campagne['statut']) === 'active' ? 'selected' : '' ?>>
                                Active
                            </option>
                            <option value="terminee" <?= ($old_data['statut'] ?? $campagne['statut']) === 'terminee' ? 'selected' : '' ?>>
                                Terminée
                            </option>
                            <option value="suspendue" <?= ($old_data['statut'] ?? $campagne['statut']) === 'suspendue' ? 'selected' : '' ?>>
                                Suspendue
                            </option>
                        </select>
                        <?php if (!empty($errors['statut'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['statut']) ?></div>
                        <?php endif; ?>
                        <div class="form-text">
                            <?php if ($campagne['statut'] === 'active'): ?>
                                <span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Cette campagne est actuellement active.</span>
                            <?php elseif ($campagne['statut'] === 'terminee'): ?>
                                <span class="text-info"><i class="fas fa-info-circle"></i> Cette campagne est terminée.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="?controller=Admin&action=viewCampagne&id=<?= $campagne['id'] ?>" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Sauvegarder les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informations</h6>
            </div>
            <div class="card-body">
                <div class="small">
                    <dl class="row">
                        <dt class="col-5">ID :</dt>
                        <dd class="col-7"><?= $campagne['id'] ?></dd>
                        
                        <dt class="col-5">Créée le :</dt>
                        <dd class="col-7"><?= date('d/m/Y à H:i', strtotime($campagne['date_creation'])) ?></dd>
                        
                        <?php if (!empty($campagne['date_modification'])): ?>
                        <dt class="col-5">Modifiée le :</dt>
                        <dd class="col-7"><?= date('d/m/Y à H:i', strtotime($campagne['date_modification'])) ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Attention</h6>
            </div>
            <div class="card-body">
                <div class="small">
                    <ul class="ps-3">
                        <li class="mb-2">Modifier une campagne active peut affecter les enquêtes en cours</li>
                        <li class="mb-2">Les dates ne peuvent pas être antérieures à aujourd'hui</li>
                        <li class="mb-2">Le passage en "Terminée" empêchera toute nouvelle participation</li>
                        <li>Vérifiez l'impact sur les enquêtes associées</li>
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
    const statutSelect = document.getElementById('statut');
    
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
    
    // Avertissement pour changement de statut
    statutSelect.addEventListener('change', function() {
        const originalStatut = '<?= $campagne['statut'] ?>';
        
        if (originalStatut === 'active' && this.value === 'terminee') {
            if (!confirm('Êtes-vous sûr de vouloir terminer cette campagne ? Cette action empêchera toute nouvelle participation.')) {
                this.value = originalStatut;
                return;
            }
        }
        
        if (originalStatut === 'terminee' && this.value === 'active') {
            if (!confirm('Êtes-vous sûr de vouloir réactiver cette campagne terminée ?')) {
                this.value = originalStatut;
                return;
            }
        }
    });
    
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
