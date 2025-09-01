<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4><i class="fas fa-user-plus"></i> Inscription Client</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="?controller=Auth&action=store" id="registerForm" data-validate="user">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">
                                    <i class="fas fa-user"></i> Nom *
                                </label>
                                <input type="text" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>" 
                                       id="nom" name="nom" value="<?= htmlspecialchars($old_data['nom'] ?? '') ?>" required>
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
                                <input type="text" class="form-control <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>" 
                                       id="prenom" name="prenom" value="<?= htmlspecialchars($old_data['prenom'] ?? '') ?>" required>
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
                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                               id="email" name="email" value="<?= htmlspecialchars($old_data['email'] ?? '') ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= $errors['email'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Mot de passe *
                                </label>
                                <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                       id="password" name="password" required>
                                <div class="form-text">Au moins 6 caractères</div>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?= $errors['password'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock"></i> Confirmer le mot de passe *
                                </label>
                                <input type="password" class="form-control <?= isset($errors['password_confirmation']) ? 'is-invalid' : '' ?>" 
                                       id="password_confirmation" name="password_confirmation" required>
                                <?php if (isset($errors['password_confirmation'])): ?>
                                    <div class="invalid-feedback"><?= $errors['password_confirmation'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> S'inscrire
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">
                    Déjà un compte ? 
                    <a href="?controller=Auth&action=login">Se connecter</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const nom = document.getElementById('nom').value.trim();
    const prenom = document.getElementById('prenom').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const passwordConfirmation = document.getElementById('password_confirmation').value;
    
    let errors = [];
    
    if (!nom) errors.push('Le nom est obligatoire');
    if (!prenom) errors.push('Le prénom est obligatoire');
    if (!email) errors.push('L\'email est obligatoire');
    if (!isValidEmail(email)) errors.push('Format d\'email invalide');
    if (!password) errors.push('Le mot de passe est obligatoire');
    if (password.length < 6) errors.push('Le mot de passe doit contenir au moins 6 caractères');
    if (password !== passwordConfirmation) errors.push('Les mots de passe ne correspondent pas');
    
    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join('\n'));
        return false;
    }
});

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Vérification en temps réel de la correspondance des mots de passe
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmation = this.value;
    
    if (confirmation && password !== confirmation) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});
</script>
