// JavaScript pour le back office
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation
    initializeBackoffice();
    
    // Gestion des formulaires
    initializeForms();
    
    // Gestion des tableaux
    initializeTables();
    
    // Gestion des modales
    initializeModals();
});

function initializeBackoffice() {
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
    
    // Activation des tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function initializeForms() {
    // Validation côté client pour tous les formulaires
    const forms = document.querySelectorAll('form[data-validate="true"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showAlert('Veuillez corriger les erreurs dans le formulaire', 'danger');
            }
        });
    });
    
    // Confirmation de suppression
    const deleteButtons = document.querySelectorAll('[data-action="delete"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const message = this.getAttribute('data-message') || 'Êtes-vous sûr de vouloir supprimer cet élément ?';
            if (confirm(message)) {
                // Procéder à la suppression
                window.location.href = this.href;
            }
        });
    });
}

function initializeTables() {
    // Tri des tableaux
    const sortableHeaders = document.querySelectorAll('th[data-sortable]');
    sortableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            sortTable(this);
        });
    });
    
    // Recherche dans les tableaux
    const searchInputs = document.querySelectorAll('[data-table-search]');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            filterTable(this.getAttribute('data-table-search'), this.value);
        });
    });
}

function initializeModals() {
    // Chargement du contenu des modales via AJAX
    const modalTriggers = document.querySelectorAll('[data-modal-url]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-modal-url');
            const modalId = this.getAttribute('data-bs-target');
            loadModalContent(url, modalId);
        });
    });
}

// Fonctions de validation
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            markFieldAsInvalid(input, 'Ce champ est obligatoire');
            isValid = false;
        } else {
            markFieldAsValid(input);
        }
    });
    
    // Validation email
    const emailInputs = form.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        if (input.value && !isValidEmail(input.value)) {
            markFieldAsInvalid(input, 'Format email invalide');
            isValid = false;
        }
    });
    
    return isValid;
}

function markFieldAsInvalid(field, message) {
    field.classList.add('is-invalid');
    field.classList.remove('is-valid');
    
    let feedback = field.parentNode.querySelector('.invalid-feedback');
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        field.parentNode.appendChild(feedback);
    }
    feedback.textContent = message;
}

function markFieldAsValid(field) {
    field.classList.remove('is-invalid');
    field.classList.add('is-valid');
    
    const feedback = field.parentNode.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.remove();
    }
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Fonctions de tri et filtrage
function sortTable(header) {
    const table = header.closest('table');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const column = Array.from(header.parentNode.children).indexOf(header);
    const isAscending = header.classList.contains('sort-asc');
    
    // Supprimer les classes de tri existantes
    header.parentNode.querySelectorAll('th').forEach(th => {
        th.classList.remove('sort-asc', 'sort-desc');
    });
    
    // Ajouter la nouvelle classe de tri
    header.classList.add(isAscending ? 'sort-desc' : 'sort-asc');
    
    rows.sort((a, b) => {
        const aText = a.children[column].textContent.trim();
        const bText = b.children[column].textContent.trim();
        
        if (isAscending) {
            return bText.localeCompare(aText);
        } else {
            return aText.localeCompare(bText);
        }
    });
    
    // Réorganiser les lignes
    rows.forEach(row => tbody.appendChild(row));
}

function filterTable(tableId, searchTerm) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    const term = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
}

// Fonctions de gestion des modales
function loadModalContent(url, modalId) {
    const modal = document.querySelector(modalId);
    const modalBody = modal.querySelector('.modal-body');
    
    modalBody.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
    
    fetch(url)
        .then(response => response.text())
        .then(html => {
            modalBody.innerHTML = html;
        })
        .catch(error => {
            modalBody.innerHTML = '<div class="alert alert-danger">Erreur de chargement</div>';
        });
}

// Utilitaires
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alert-container') || document.body;
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.insertBefore(alert, alertContainer.firstChild);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 150);
    }, 5000);
}

function showLoader() {
    const loader = document.createElement('div');
    loader.className = 'spinner-overlay';
    loader.innerHTML = '<div class="spinner-border text-primary" role="status"></div>';
    document.body.appendChild(loader);
}

function hideLoader() {
    const loader = document.querySelector('.spinner-overlay');
    if (loader) {
        loader.remove();
    }
}

// Fonctions spécifiques aux utilisateurs
function confirmDelete(userId, userName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${userName}" ?`)) {
        window.location.href = `?controller=Admin&action=deleteUser&id=${userId}`;
    }
}

// Fonctions spécifiques aux campagnes
function confirmDeleteCampagne(campagneId, campagneName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer la campagne "${campagneName}" ?`)) {
        window.location.href = `?controller=Admin&action=deleteCampagne&id=${campagneId}`;
    }
}

// Gestion des questions (pour les enquêtes)
function initializeQuestionManagement() {
    // Drag & Drop pour réorganiser les questions
    const questionsList = document.getElementById('questions-list');
    if (questionsList) {
        // Implémentation du drag & drop si nécessaire
    }
    
    // Ajout dynamique de questions
    const addQuestionBtn = document.getElementById('add-question');
    if (addQuestionBtn) {
        addQuestionBtn.addEventListener('click', addQuestion);
    }
}

function addQuestion() {
    // Implémentation de l'ajout de questions
    console.log('Ajout d\'une nouvelle question');
}

// Export des fonctions pour utilisation globale
window.backoffice = {
    showAlert,
    showLoader,
    hideLoader,
    confirmDelete,
    confirmDeleteCampagne
};
