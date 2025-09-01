<?php
// Titre de la page
$pageTitle = 'Modifier l\'utilisateur';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-edit"></i> Modifier l'utilisateur</h2>
    <a href="?controller=Admin&action=users" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    Modification de : <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                    <span class="badge bg-<?= 
                        $user['role'] === 'admin' ? 'danger' : 
                        ($user['role'] === 'agent' ? 'warning' : 'primary') 
                    ?> ms-2">
                        <?= ucfirst($user['role']) ?>
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="?controller=Admin&action=updateUser" data-validate="user">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    
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
                                       value="<?= htmlspecialchars($old_data['nom'] ?? $user['nom']) ?>" 
                                       required
                                       minlength="2"
                                       maxlength="50"
                                       pattern="[a-zA-ZÀ-ÿ\s\-']{2,50}">
                                <?php if (isset($errors['nom'])): ?>
                                    <div class="invalid-feedback"><?= $errors['nom'] ?></div>
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
                                       value="<?= htmlspecialchars($old_data['prenom'] ?? $user['prenom']) ?>" 
                                       required
                                       minlength="2"
                                       maxlength="50"
                                       pattern="[a-zA-ZÀ-ÿ\s\-']{2,50}">
                                <?php if (isset($errors['prenom'])): ?>
                                    <div class="invalid-feedback"><?= $errors['prenom'] ?></div>
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
                               value="<?= htmlspecialchars($old_data['email'] ?? $user['email']) ?>" 
                               required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= $errors['email'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">
                            <i class="fas fa-user-tag"></i> Rôle *
                        </label>
                        <select class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" 
                                id="role" 
                                name="role" 
                                required
                                <?= $user['id'] === $currentUser['id'] ? 'disabled' : '' ?>>
                            <option value="">Sélectionner un rôle</option>
                            <option value="admin" <?= ($old_data['role'] ?? $user['role']) === 'admin' ? 'selected' : '' ?>>
                                Administrateur
                            </option>
                            <option value="agent" <?= ($old_data['role'] ?? $user['role']) === 'agent' ? 'selected' : '' ?>>
                                Agent
                            </option>
                            <option value="client" <?= ($old_data['role'] ?? $user['role']) === 'client' ? 'selected' : '' ?>>
                                Client
                            </option>
                        </select>
                        <?php if ($user['id'] === $currentUser['id']): ?>
                            <input type="hidden" name="role" value="<?= $user['role'] ?>">
                            <div class="form-text text-warning">
                                <i class="fas fa-info-circle"></i> Vous ne pouvez pas modifier votre propre rôle.
                            </div>
                        <?php endif; ?>
                        <?php if (isset($errors['role'])): ?>
                            <div class="invalid-feedback"><?= $errors['role'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">
                            <i class="fas fa-lock"></i> Nouveau mot de passe
                        </label>
                        <input type="password" 
                               class="form-control <?= isset($errors['mot_de_passe']) ? 'is-invalid' : '' ?>" 
                               id="mot_de_passe" 
                               name="mot_de_passe" 
                               minlength="6">
                        <div class="form-text">
                            Laissez vide pour conserver le mot de passe actuel. 
                            Si modifié, doit contenir au moins 6 caractères avec une minuscule, une majuscule et un chiffre.
                        </div>
                        <?php if (isset($errors['mot_de_passe'])): ?>
                            <div class="invalid-feedback"><?= $errors['mot_de_passe'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-lock"></i> Confirmer le nouveau mot de passe
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation">
                    </div>
                    
                    <div class="mb-3">
                        <label for="statut" class="form-label">
                            <i class="fas fa-toggle-on"></i> Statut
                        </label>
                        <select class="form-select" 
                                id="statut" 
                                name="statut"
                                <?= $user['id'] === $currentUser['id'] ? 'disabled' : '' ?>>
                            <option value="actif" <?= ($old_data['statut'] ?? $user['statut']) === 'actif' ? 'selected' : '' ?>>
                                Actif
                            </option>
                            <option value="inactif" <?= ($old_data['statut'] ?? $user['statut']) === 'inactif' ? 'selected' : '' ?>>
                                Inactif
                            </option>
                        </select>
                        <?php if ($user['id'] === $currentUser['id']): ?>
                            <input type="hidden" name="statut" value="<?= $user['statut'] ?>">
                            <div class="form-text text-warning">
                                <i class="fas fa-info-circle"></i> Vous ne pouvez pas modifier votre propre statut.
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Informations supplémentaires</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> Créé le : 
                                            <?= date('d/m/Y à H:i', strtotime($user['date_creation'])) ?>
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="fas fa-edit"></i> Dernière modification : 
                                            <?= $user['date_modification'] ? date('d/m/Y à H:i', strtotime($user['date_modification'])) : 'Jamais' ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="?controller=Admin&action=users" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
