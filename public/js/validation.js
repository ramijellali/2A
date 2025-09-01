/**
 * Validation JavaScript pour les formulaires
 * Conforme aux exigences du module Technologies Web
 */

class FormValidator {
    constructor() {
        this.initValidations();
    }

    initValidations() {
        // Validation en temps réel
        document.addEventListener('DOMContentLoaded', () => {
            this.setupFormValidations();
        });
    }

    setupFormValidations() {
        // Validation des formulaires utilisateur
        const userForms = document.querySelectorAll('form[data-validate="user"]');
        userForms.forEach(form => this.setupUserValidation(form));

        // Validation des formulaires de campagne
        const campagneForms = document.querySelectorAll('form[data-validate="campagne"]');
        campagneForms.forEach(form => this.setupCampagneValidation(form));

        // Validation des formulaires d'enquête
        const enqueteForms = document.querySelectorAll('form[data-validate="enquete"]');
        enqueteForms.forEach(form => this.setupEnqueteValidation(form));

        // Validation des formulaires de questions
        const questionForms = document.querySelectorAll('form[data-validate="question"]');
        questionForms.forEach(form => this.setupQuestionValidation(form));
    }

    setupUserValidation(form) {
        const nom = form.querySelector('[name="nom"]');
        const prenom = form.querySelector('[name="prenom"]');
        const email = form.querySelector('[name="email"]');
        const password = form.querySelector('[name="password"]');
        const passwordConfirm = form.querySelector('[name="password_confirmation"]');

        if (nom) {
            nom.addEventListener('input', () => {
                this.validateName(nom, 'Nom');
            });
        }

        if (prenom) {
            prenom.addEventListener('input', () => {
                this.validateName(prenom, 'Prénom');
            });
        }

        if (email) {
            email.addEventListener('input', () => {
                this.validateEmail(email);
            });
        }

        if (password) {
            password.addEventListener('input', () => {
                this.validatePassword(password);
                if (passwordConfirm) {
                    this.validatePasswordConfirmation(password, passwordConfirm);
                }
            });
        }

        if (passwordConfirm) {
            passwordConfirm.addEventListener('input', () => {
                this.validatePasswordConfirmation(password, passwordConfirm);
            });
        }

        form.addEventListener('submit', (e) => {
            if (!this.validateUserForm(form)) {
                e.preventDefault();
            }
        });
    }

    setupCampagneValidation(form) {
        const titre = form.querySelector('[name="titre"]');
        const description = form.querySelector('[name="description"]');
        const dateDebut = form.querySelector('[name="date_debut"]');
        const dateFin = form.querySelector('[name="date_fin"]');

        if (titre) {
            titre.addEventListener('input', () => {
                this.validateRequired(titre, 'Le titre est obligatoire');
                this.validateLength(titre, 3, 100, 'Le titre doit contenir entre 3 et 100 caractères');
            });
        }

        if (description) {
            description.addEventListener('input', () => {
                this.validateRequired(description, 'La description est obligatoire');
            });
        }

        if (dateDebut && dateFin) {
            dateDebut.addEventListener('change', () => {
                this.validateDateRange(dateDebut, dateFin);
            });
            dateFin.addEventListener('change', () => {
                this.validateDateRange(dateDebut, dateFin);
            });
        }

        form.addEventListener('submit', (e) => {
            if (!this.validateCampagneForm(form)) {
                e.preventDefault();
            }
        });
    }

    setupEnqueteValidation(form) {
        const titre = form.querySelector('[name="titre"]');
        const description = form.querySelector('[name="description"]');

        if (titre) {
            titre.addEventListener('input', () => {
                this.validateRequired(titre, 'Le titre est obligatoire');
                this.validateLength(titre, 5, 200, 'Le titre doit contenir entre 5 et 200 caractères');
            });
        }

        if (description) {
            description.addEventListener('input', () => {
                this.validateRequired(description, 'La description est obligatoire');
            });
        }

        form.addEventListener('submit', (e) => {
            if (!this.validateEnqueteForm(form)) {
                e.preventDefault();
            }
        });
    }

    setupQuestionValidation(form) {
        const questions = form.querySelectorAll('.question-item');
        
        questions.forEach((questionDiv, index) => {
            const questionInput = questionDiv.querySelector('[name*="[question]"]');
            const typeSelect = questionDiv.querySelector('[name*="[type]"]');
            
            if (questionInput) {
                questionInput.addEventListener('input', () => {
                    this.validateRequired(questionInput, `La question ${index + 1} est obligatoire`);
                });
            }

            if (typeSelect) {
                typeSelect.addEventListener('change', () => {
                    this.handleQuestionTypeChange(questionDiv, typeSelect.value);
                });
            }
        });

        form.addEventListener('submit', (e) => {
            if (!this.validateQuestionsForm(form)) {
                e.preventDefault();
            }
        });
    }

    // Validations spécifiques
    validateName(input, fieldName) {
        const value = input.value.trim();
        const nameRegex = /^[a-zA-ZÀ-ÿ\s-']{2,50}$/;
        
        if (value === '') {
            this.showError(input, `${fieldName} est obligatoire`);
            return false;
        } else if (!nameRegex.test(value)) {
            this.showError(input, `${fieldName} invalide (2-50 caractères, lettres uniquement)`);
            return false;
        } else {
            this.showSuccess(input);
            return true;
        }
    }

    validateEmail(input) {
        const value = input.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (value === '') {
            this.showError(input, 'Email est obligatoire');
            return false;
        } else if (!emailRegex.test(value)) {
            this.showError(input, 'Format d\'email invalide');
            return false;
        } else {
            this.showSuccess(input);
            return true;
        }
    }

    validatePassword(input) {
        const value = input.value;
        
        if (value === '') {
            this.showError(input, 'Mot de passe obligatoire');
            return false;
        } else if (value.length < 6) {
            this.showError(input, 'Minimum 6 caractères');
            return false;
        } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(value)) {
            this.showError(input, 'Doit contenir: minuscule, majuscule, chiffre');
            return false;
        } else {
            this.showSuccess(input);
            return true;
        }
    }

    validatePasswordConfirmation(passwordInput, confirmInput) {
        const password = passwordInput.value;
        const confirm = confirmInput.value;
        
        if (confirm === '') {
            this.showError(confirmInput, 'Confirmation obligatoire');
            return false;
        } else if (password !== confirm) {
            this.showError(confirmInput, 'Les mots de passe ne correspondent pas');
            return false;
        } else {
            this.showSuccess(confirmInput);
            return true;
        }
    }

    validateRequired(input, message) {
        const value = input.value.trim();
        
        if (value === '') {
            this.showError(input, message);
            return false;
        } else {
            this.showSuccess(input);
            return true;
        }
    }

    validateLength(input, min, max, message) {
        const value = input.value.trim();
        
        if (value.length < min || value.length > max) {
            this.showError(input, message);
            return false;
        } else {
            this.showSuccess(input);
            return true;
        }
    }

    validateDateRange(startInput, endInput) {
        const startDate = new Date(startInput.value);
        const endDate = new Date(endInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (startDate < today) {
            this.showError(startInput, 'La date de début ne peut pas être dans le passé');
            return false;
        }
        
        if (endDate <= startDate) {
            this.showError(endInput, 'La date de fin doit être après la date de début');
            return false;
        }
        
        this.showSuccess(startInput);
        this.showSuccess(endInput);
        return true;
    }

    // Validation complète des formulaires
    validateUserForm(form) {
        let isValid = true;
        
        const nom = form.querySelector('[name="nom"]');
        const prenom = form.querySelector('[name="prenom"]');
        const email = form.querySelector('[name="email"]');
        const password = form.querySelector('[name="password"]');
        const passwordConfirm = form.querySelector('[name="password_confirmation"]');
        
        if (nom && !this.validateName(nom, 'Nom')) isValid = false;
        if (prenom && !this.validateName(prenom, 'Prénom')) isValid = false;
        if (email && !this.validateEmail(email)) isValid = false;
        if (password && !this.validatePassword(password)) isValid = false;
        if (passwordConfirm && !this.validatePasswordConfirmation(password, passwordConfirm)) isValid = false;
        
        return isValid;
    }

    validateCampagneForm(form) {
        let isValid = true;
        
        const titre = form.querySelector('[name="titre"]');
        const description = form.querySelector('[name="description"]');
        const dateDebut = form.querySelector('[name="date_debut"]');
        const dateFin = form.querySelector('[name="date_fin"]');
        
        if (titre) {
            if (!this.validateRequired(titre, 'Le titre est obligatoire')) isValid = false;
            if (!this.validateLength(titre, 3, 100, 'Le titre doit contenir entre 3 et 100 caractères')) isValid = false;
        }
        
        if (description && !this.validateRequired(description, 'La description est obligatoire')) isValid = false;
        if (dateDebut && dateFin && !this.validateDateRange(dateDebut, dateFin)) isValid = false;
        
        return isValid;
    }

    validateEnqueteForm(form) {
        let isValid = true;
        
        const titre = form.querySelector('[name="titre"]');
        const description = form.querySelector('[name="description"]');
        
        if (titre) {
            if (!this.validateRequired(titre, 'Le titre est obligatoire')) isValid = false;
            if (!this.validateLength(titre, 5, 200, 'Le titre doit contenir entre 5 et 200 caractères')) isValid = false;
        }
        
        if (description && !this.validateRequired(description, 'La description est obligatoire')) isValid = false;
        
        return isValid;
    }

    validateQuestionsForm(form) {
        let isValid = true;
        const questions = form.querySelectorAll('.question-item');
        
        if (questions.length === 0) {
            alert('Au moins une question est requise');
            return false;
        }
        
        questions.forEach((questionDiv, index) => {
            const questionInput = questionDiv.querySelector('[name*="[question]"]');
            
            if (questionInput && !this.validateRequired(questionInput, `La question ${index + 1} est obligatoire`)) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    // Gestion des types de questions dynamiques
    handleQuestionTypeChange(questionDiv, type) {
        const optionsContainer = questionDiv.querySelector('.options-container');
        
        if (type === 'radio' || type === 'checkbox') {
            if (!optionsContainer) {
                this.addOptionsContainer(questionDiv);
            }
        } else {
            if (optionsContainer) {
                optionsContainer.remove();
            }
        }
    }

    addOptionsContainer(questionDiv) {
        const optionsHtml = `
            <div class="options-container mt-2">
                <label class="form-label">Options de réponse:</label>
                <div class="options-list">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="options[]" placeholder="Option 1" required>
                        <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="options[]" placeholder="Option 2" required>
                        <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="this.addOption(this)">
                    <i class="fas fa-plus"></i> Ajouter une option
                </button>
            </div>
        `;
        questionDiv.insertAdjacentHTML('beforeend', optionsHtml);
    }

    // Affichage des erreurs et succès
    showError(input, message) {
        this.clearFeedback(input);
        
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;
        
        input.parentNode.appendChild(feedback);
    }

    showSuccess(input) {
        this.clearFeedback(input);
        
        input.classList.add('is-valid');
        input.classList.remove('is-invalid');
    }

    clearFeedback(input) {
        const existingFeedback = input.parentNode.querySelector('.invalid-feedback, .valid-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
    }
}

// Fonctions utilitaires globales
function addOption(button) {
    const optionsList = button.parentElement.querySelector('.options-list');
    const optionCount = optionsList.children.length + 1;
    
    const optionHtml = `
        <div class="input-group mb-2">
            <input type="text" class="form-control" name="options[]" placeholder="Option ${optionCount}" required>
            <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    optionsList.insertAdjacentHTML('beforeend', optionHtml);
}

function removeQuestion(button) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette question ?')) {
        button.closest('.question-item').remove();
        updateQuestionNumbers();
    }
}

function updateQuestionNumbers() {
    const questions = document.querySelectorAll('.question-item');
    questions.forEach((question, index) => {
        const title = question.querySelector('.question-title');
        if (title) {
            title.textContent = `Question ${index + 1}`;
        }
    });
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    new FormValidator();
});
