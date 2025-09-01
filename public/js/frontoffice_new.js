// JavaScript pour le front office
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation
    initializeFrontoffice();
    
    // Gestion des enquêtes
    initializeSurveys();
    
    // Gestion des formulaires
    initializeForms();
    
    // Gestion des animations
    initializeAnimations();
});

function initializeFrontoffice() {
    // Auto-fermeture des alertes après 5 secondes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (!alert.classList.contains('alert-permanent')) {
            setTimeout(() => {
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 150);
            }, 5000);
        }
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
    initializeRatings();
    
    // Gestion de la progression
    initializeProgress();
}

function initializeMultipleChoice() {
    const radioGroups = document.querySelectorAll('input[type="radio"]');
    radioGroups.forEach(radio => {
        radio.addEventListener('change', function() {
            updateProgress();
            highlightSelectedOption(this);
        });
    });
    
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateProgress();
        });
    });
}

function initializeRatings() {
    const ratingContainers = document.querySelectorAll('.rating-container');
    ratingContainers.forEach(container => {
        const stars = container.querySelectorAll('.star');
        const input = container.querySelector('input[type="hidden"]');
        
        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                const rating = index + 1;
                input.value = rating;
                updateStars(stars, rating);
                updateProgress();
            });
            
            star.addEventListener('mouseover', function() {
                updateStars(stars, index + 1, true);
            });
        });
        
        container.addEventListener('mouseleave', function() {
            updateStars(stars, input.value || 0);
        });
    });
}

function updateStars(stars, rating, isHover = false) {
    stars.forEach((star, index) => {
        star.classList.remove('active', 'hover');
        if (index < rating) {
            star.classList.add(isHover ? 'hover' : 'active');
        }
    });
}

function highlightSelectedOption(radio) {
    // Retirer la classe active de toutes les options du même groupe
    const groupName = radio.name;
    const options = document.querySelectorAll(`input[name="${groupName}"]`);
    options.forEach(option => {
        const parent = option.closest('.form-check, .option');
        if (parent) {
            parent.classList.remove('selected');
        }
    });
    
    // Ajouter la classe active à l'option sélectionnée
    const selectedParent = radio.closest('.form-check, .option');
    if (selectedParent) {
        selectedParent.classList.add('selected');
    }
}

function initializeForms() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
                showNotification('Veuillez corriger les erreurs dans le formulaire', 'error');
            }
        });
    });
    
    // Validation en temps réel
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(input);
        });
        
        input.addEventListener('input', function() {
            clearFieldError(input);
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const fieldType = field.type;
    let isValid = true;
    let errorMessage = '';
    
    // Vérification obligatoire
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Ce champ est obligatoire';
    }
    
    // Vérification email
    if (fieldType === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Format d\'email invalide';
        }
    }
    
    // Vérification mot de passe
    if (fieldType === 'password' && value) {
        if (value.length < 6) {
            isValid = false;
            errorMessage = 'Le mot de passe doit contenir au moins 6 caractères';
        }
    }
    
    // Vérification confirmation mot de passe
    if (field.name === 'password_confirmation') {
        const passwordField = document.querySelector('input[name="mot_de_passe"]');
        if (passwordField && value !== passwordField.value) {
            isValid = false;
            errorMessage = 'Les mots de passe ne correspondent pas';
        }
    }
    
    if (!isValid) {
        showFieldError(field, errorMessage);
    } else {
        clearFieldError(field);
    }
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('is-invalid');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('is-invalid');
    
    const errorDiv = field.parentNode.querySelector('.invalid-feedback');
    if (errorDiv) {
        errorDiv.remove();
    }
}

function initializeProgress() {
    updateProgress();
}

function updateProgress() {
    const progressBar = document.querySelector('.progress-bar');
    if (!progressBar) return;
    
    const form = document.querySelector('form');
    if (!form) return;
    
    const totalQuestions = form.querySelectorAll('.question').length;
    const answeredQuestions = form.querySelectorAll('.question input:checked, .question input[value]:not([value=""])').length;
    
    const percentage = totalQuestions > 0 ? Math.round((answeredQuestions / totalQuestions) * 100) : 0;
    
    progressBar.style.width = `${percentage}%`;
    progressBar.setAttribute('aria-valuenow', percentage);
    progressBar.textContent = `${percentage}%`;
    
    // Changer la couleur selon le progrès
    progressBar.className = 'progress-bar';
    if (percentage < 30) {
        progressBar.classList.add('bg-danger');
    } else if (percentage < 70) {
        progressBar.classList.add('bg-warning');
    } else {
        progressBar.classList.add('bg-success');
    }
}

function initializeAnimations() {
    // Animation de fade in pour les éléments
    const animatedElements = document.querySelectorAll('.fade-in');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    });
    
    animatedElements.forEach(el => {
        observer.observe(el);
    });
    
    // Animation de comptage pour les statistiques
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.round(current);
        }, 16);
    });
}

// Notifications
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
    `;
    
    document.body.appendChild(notification);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Fonctions utilitaires
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

function loadContent(url, targetElement) {
    fetch(url)
        .then(response => response.text())
        .then(html => {
            document.querySelector(targetElement).innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur de chargement:', error);
            showNotification('Erreur de chargement du contenu', 'error');
        });
}

// Gestion des modales
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) {
            bsModal.hide();
        }
    }
}

// Gestion du scroll smooth
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Gestion responsive du menu
function toggleMobileMenu() {
    const mobileMenu = document.querySelector('.mobile-menu');
    if (mobileMenu) {
        mobileMenu.classList.toggle('active');
    }
}

// Auto-save pour les formulaires longs
function initializeAutoSave() {
    const form = document.querySelector('form[data-autosave="true"]');
    if (!form) return;
    
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            saveFormData(form);
        });
    });
    
    // Restaurer les données sauvegardées
    restoreFormData(form);
}

function saveFormData(form) {
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    localStorage.setItem(`form_${form.id}_data`, JSON.stringify(data));
}

function restoreFormData(form) {
    const savedData = localStorage.getItem(`form_${form.id}_data`);
    if (!savedData) return;
    
    try {
        const data = JSON.parse(savedData);
        Object.keys(data).forEach(key => {
            const field = form.querySelector(`[name="${key}"]`);
            if (field) {
                field.value = data[key];
            }
        });
    } catch (error) {
        console.error('Erreur lors de la restauration des données:', error);
    }
}

// Initialisation de l'auto-save
document.addEventListener('DOMContentLoaded', function() {
    initializeAutoSave();
});
