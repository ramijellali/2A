<?php
// Titre de la page
$pageTitle = 'Créer un utilisateur';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-plus"></i> Créer un utilisateur</h2>
    <a href="?controller=Admin&action=users" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informations utilisateur</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="?controller=Admin&action=storeUser" data-validate="user">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">
                                    <i class="fas fa-user"></i> Nom *
                                </label>
                                <input type="text" 
                                       class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>" 
                                       id="nom" 
                                       name="nom" 
                                       value="<?= htmlspecialchars($old_data['nom'] ?? '') ?>" 
                                       required
                                       minlength="2"
                                       maxlength="50"
                                       pattern="[a-zA-ZÀ-ÿ\s\-']{2,50}">
                                <?php if (isset($errors['nom'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['nom']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prenom" class="form-label">
                                    <i class="fas fa-user"></i> Prénom *
                                </label>
                                <input type="text" 
                                       class="form-control <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>" 
                                       id="prenom" 
                                       name="prenom" 
                                       value="<?= htmlspecialchars($old_data['prenom'] ?? '') ?>" 
                                       required
                                       minlength="2"
                                       maxlength="50"
                                       pattern="[a-zA-ZÀ-ÿ\s\-']{2,50}">
                                <?php if (isset($errors['prenom'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['prenom']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email *
                        </label>
                        <input type="email" 
                               class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                               id="email" 
                               name="email" 
                               value="<?= htmlspecialchars($old_data['email'] ?? '') ?>" 
                               required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">
                            <i class="fas fa-user-tag"></i> Rôle *
                        </label>
                        <select class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" 
                                id="role" 
                                name="role" 
                                required>
                            <option value="">Sélectionner un rôle</option>
                            <option value="admin" <?= ($old_data['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
                                Administrateur
                            </option>
                            <option value="agent" <?= ($old_data['role'] ?? '') === 'agent' ? 'selected' : '' ?>>
                                Agent
                            </option>
                            <option value="client" <?= ($old_data['role'] ?? '') === 'client' ? 'selected' : '' ?>>
                                Client
                            </option>
                        </select>
                        <?php if (isset($errors['role'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['role']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">
                            <i class="fas fa-lock"></i> Mot de passe *
                        </label>
                        <input type="password" 
                               class="form-control <?= isset($errors['mot_de_passe']) ? 'is-invalid' : '' ?>" 
                               id="mot_de_passe" 
                               name="mot_de_passe" 
                               required
                               minlength="6">
                        <div class="form-text">
                            Le mot de passe doit contenir au moins 6 caractères avec une minuscule, une majuscule et un chiffre.
                        </div>
                        <?php if (isset($errors['mot_de_passe'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['mot_de_passe']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-lock"></i> Confirmer le mot de passe *
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="statut" class="form-label">
                            <i class="fas fa-toggle-on"></i> Statut
                        </label>
                        <select class="form-select" id="statut" name="statut">
                            <option value="actif" <?= ($old_data['statut'] ?? 'actif') === 'actif' ? 'selected' : '' ?>>
                                Actif
                            </option>
                            <option value="inactif" <?= ($old_data['statut'] ?? '') === 'inactif' ? 'selected' : '' ?>>
                                Inactif
                            </option>
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="?controller=Admin&action=users" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Créer l'utilisateur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
