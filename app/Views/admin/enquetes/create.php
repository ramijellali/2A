<?php
/**
 * Vue de création d'enquête - Admin
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Créer une nouvelle enquête
                    </h4>
                    <a href="?controller=Admin&action=enquetes" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour
                    </a>
                </div>

                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-1"></i> Erreurs de validation :</h6>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="?controller=Admin&action=storeEnquete" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="titre" class="form-label">
                                        <i class="fas fa-heading me-1"></i>
                                        Titre de l'enquête *
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= isset($errors['titre']) ? 'is-invalid' : '' ?>" 
                                           id="titre" 
                                           name="titre" 
                                           value="<?= htmlspecialchars($old_data['titre'] ?? '') ?>"
                                           required>
                                    <?php if (isset($errors['titre'])): ?>
                                        <div class="invalid-feedback"><?= $errors['titre'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left me-1"></i>
                                        Description
                                    </label>
                                    <textarea class="form-control" 
                                              id="description" 
                                              name="description" 
                                              rows="4"><?= htmlspecialchars($old_data['description'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="campagne_id" class="form-label">
                                        <i class="fas fa-bullhorn me-1"></i>
                                        Campagne *
                                    </label>
                                    <select class="form-select <?= isset($errors['campagne_id']) ? 'is-invalid' : '' ?>" 
                                            id="campagne_id" 
                                            name="campagne_id" 
                                            required>
                                        <option value="">-- Sélectionner une campagne --</option>
                                        <?php foreach ($campagnes as $campagne): ?>
                                            <option value="<?= $campagne['id'] ?>" 
                                                    <?= ($old_data['campagne_id'] ?? '') == $campagne['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($campagne['nom']) ?>
                                                (<?= htmlspecialchars($campagne['statut']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['campagne_id'])): ?>
                                        <div class="invalid-feedback"><?= $errors['campagne_id'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="created_by" class="form-label">
                                        <i class="fas fa-user-tie me-1"></i>
                                        Agent responsable *
                                    </label>
                                    <select class="form-select <?= isset($errors['created_by']) ? 'is-invalid' : '' ?>" 
                                            id="created_by" 
                                            name="created_by" 
                                            required>
                                        <option value="">-- Sélectionner un agent --</option>
                                        <?php foreach ($agents as $agent): ?>
                                            <option value="<?= $agent['id'] ?>" 
                                                    <?= ($old_data['created_by'] ?? '') == $agent['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($agent['prenom'] . ' ' . $agent['nom']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['created_by'])): ?>
                                        <div class="invalid-feedback"><?= $errors['created_by'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="statut" class="form-label">
                                        <i class="fas fa-flag me-1"></i>
                                        Statut
                                    </label>
                                    <select class="form-select" id="statut" name="statut">
                                        <option value="brouillon" <?= ($old_data['statut'] ?? 'brouillon') == 'brouillon' ? 'selected' : '' ?>>
                                            Brouillon
                                        </option>
                                        <option value="active" <?= ($old_data['statut'] ?? '') == 'active' ? 'selected' : '' ?>>
                                            Active
                                        </option>
                                        <option value="fermee" <?= ($old_data['statut'] ?? '') == 'fermee' ? 'selected' : '' ?>>
                                            Fermée
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="?controller=Admin&action=enquetes" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Créer l'enquête
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validation du formulaire
(function() {
    'use strict';
    
    const form = document.querySelector('.needs-validation');
    
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        form.classList.add('was-validated');
    }, false);
})();
</script>
