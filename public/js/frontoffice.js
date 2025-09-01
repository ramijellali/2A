// JavaScript pour le front office
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation
    initializeFrontoffice();
    
    // Gestion des enquêtes
    initializeSurveys();
    
    // Gestion des formulaires
    initializeForms();
});

function initializeFron// Notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;   // Auto-fermeture des alertes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 150);
        }, 5000);
    });
    
    // Animation d'entrée pour les cartes
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('slide-in');
    });
}

function initializeSurveys() {
    // Gestion des questions à choix multiples
    initializeMultipleChoice();
    
    // Gestion des questions de notation
    initializeRating();
    
    // Gestion de la progression
    initializeProgress();
    
    // Sauvegarde automatique
    initializeAutoSave();
}

function initializeMultipleChoice() {
    const multipleChoiceQuestions = document.querySelectorAll('.multiple-choice-question');
    
    multipleChoiceQuestions.forEach(question => {
        const options = question.querySelectorAll('.multiple-choice-option');
        
        options.forEach(option => {
            option.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                const checkbox = this.querySelector('input[type="checkbox"]');
                
                if (radio) {
                    // Pour les boutons radio, désélectionner les autres options
                    options.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    radio.checked = true;
                } else if (checkbox) {
                    // Pour les cases à cocher, basculer la sélection
                    this.classList.toggle('selected');
                    checkbox.checked = !checkbox.checked;
                }
                
                updateProgress();
            });
        });
    });
}

function initializeRating() {
    const ratingQuestions = document.querySelectorAll('.rating-question');
    
    ratingQuestions.forEach(question => {
        const options = question.querySelectorAll('.rating-option');
        const hiddenInput = question.querySelector('input[type="hidden"]');
        
        options.forEach((option, index) => {
            option.addEventListener('click', function() {
                // Désélectionner toutes les options
                options.forEach(opt => opt.classList.remove('selected'));
                
                // Sélectionner l'option cliquée et toutes celles avant
                for (let i = 0; i <= index; i++) {
                    options[i].classList.add('selected');
                }
                
                // Mettre à jour la valeur
                const value = this.dataset.value;
                hiddenInput.value = value;
                
                updateProgress();
            });
            
            option.addEventListener('mouseenter', function() {
                // Effet de survol
                options.forEach((opt, i) => {
                    if (i <= index) {
                        opt.style.backgroundColor = '#e3f2fd';
                        opt.style.borderColor = '#2196f3';
                    } else {
                        opt.style.backgroundColor = '';
                        opt.style.borderColor = '#dee2e6';
                    }
                });
            });
        });
        
        question.addEventListener('mouseleave', function() {
            // Restaurer l'état sélectionné
            options.forEach(opt => {
                if (opt.classList.contains('selected')) {
                    opt.style.backgroundColor = '#007bff';
                    opt.style.borderColor = '#007bff';
                    opt.style.color = 'white';
                } else {
                    opt.style.backgroundColor = '';
                    opt.style.borderColor = '#dee2e6';
                    opt.style.color = '';
                }
            });
        });
    });
}

function initializeProgress() {
    updateProgress();
}

function updateProgress() {
    const progressBar = document.querySelector('.progress-bar-survey');
    if (!progressBar) return;
    
    const questions = document.querySelectorAll('.question-container');
    let answeredQuestions = 0;
    
    questions.forEach(question => {
        const inputs = question.querySelectorAll('input, textarea, select');
        let hasAnswer = false;
        
        inputs.forEach(input => {
            if (input.type === 'radio' || input.type === 'checkbox') {
                if (input.checked) hasAnswer = true;
            } else if (input.value.trim() !== '') {
                hasAnswer = true;
            }
        });
        
        if (hasAnswer) answeredQuestions++;
    });
    
    const progressPercentage = (answeredQuestions / questions.length) * 100;
    progressBar.style.width = progressPercentage + '%';
    progressBar.textContent = Math.round(progressPercentage) + '%';
}

function initializeAutoSave() {
    // Sauvegarde automatique toutes les 30 secondes
    const form = document.querySelector('#survey-form');
    if (form) {
        setInterval(() => {
            autoSave(form);
        }, 30000);
        
        // Sauvegarde avant fermeture de la page
        window.addEventListener('beforeunload', function(e) {
            autoSave(form);
        });
    }
}

function autoSave(form) {
    const formData = new FormData(form);
    formData.append('auto_save', '1');
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    }).catch(error => {
        console.log('Sauvegarde automatique échouée:', error);
    });
}

function initializeForms() {
    // Validation en temps réel
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateField(this);
                }
            });
        });
        
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showNotification('Veuillez corriger les erreurs dans le formulaire', 'error');
            } else {
                showLoader();
            }
        });
    });
}

function validateField(field) {
    let isValid = true;
    const value = field.value.trim();
    
    // Champ requis
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        showFieldError(field, 'Ce champ est obligatoire');
    }
    
    // Email
    if (field.type === 'email' && value && !isValidEmail(value)) {
        isValid = false;
        showFieldError(field, 'Format d\'email invalide');
    }
    
    // Mot de passe
    if (field.type === 'password' && value && value.length < 6) {
        isValid = false;
        showFieldError(field, 'Le mot de passe doit contenir au moins 6 caractères');
    }
    
    // Confirmation de mot de passe
    if (field.name === 'password_confirmation') {
        const passwordField = document.querySelector('input[name="password"]');
        if (passwordField && value !== passwordField.value) {
            isValid = false;
            showFieldError(field, 'Les mots de passe ne correspondent pas');
        }
    }
    
    if (isValid) {
        clearFieldError(field);
    }
    
    return isValid;
}

function validateForm(form) {
    let isValid = true;
    const fields = form.querySelectorAll('input, textarea, select');
    
    fields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function showFieldError(field, message) {
    field.classList.add('is-invalid');
    
    let feedback = field.parentNode.querySelector('.invalid-feedback');
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        field.parentNode.appendChild(feedback);
    }
    feedback.textContent = message;
}

function clearFieldError(field) {
    field.classList.remove('is-invalid');
    const feedback = field.parentNode.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.remove();
    }
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = lert alert-{type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed;
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    
    notification.innerHTML = 
        {message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    ;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 150);
    }, 5000);
}

function showLoader() {
    const loader = document.createElement('div');
    loader.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center';
    loader.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    loader.style.zIndex = '9999';
    loader.innerHTML = '<div class="spinner-border text-light" role="status"></div>';
    document.body.appendChild(loader);
}

function hideLoader() {
    const loader = document.querySelector('.position-fixed.top-0');
    if (loader) {
        loader.remove();
    }
}

// Gestion des enquêtes - navigation entre questions
function initializeQuestionNavigation() {
    const nextButtons = document.querySelectorAll('.btn-next-question');
    const prevButtons = document.querySelectorAll('.btn-prev-question');
    
    nextButtons.forEach(button => {
        button.addEventListener('click', function() {
            const currentQuestion = this.closest('.question-container');
            const nextQuestion = currentQuestion.nextElementSibling;
            
            if (nextQuestion && nextQuestion.classList.contains('question-container')) {
                currentQuestion.style.display = 'none';
                nextQuestion.style.display = 'block';
                nextQuestion.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            const currentQuestion = this.closest('.question-container');
            const prevQuestion = currentQuestion.previousElementSibling;
            
            if (prevQuestion && prevQuestion.classList.contains('question-container')) {
                currentQuestion.style.display = 'none';
                prevQuestion.style.display = 'block';
                prevQuestion.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

// Statistiques et graphiques côté client
function initializeClientStats() {
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach(card => {
        const number = card.querySelector('.stat-number');
        if (number) {
            animateNumber(number);
        }
    });
}

function animateNumber(element) {
    const target = parseInt(element.textContent);
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;
    
    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}
