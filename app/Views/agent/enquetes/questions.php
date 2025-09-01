<?php
// Titre de la page
$pageTitle = 'Questions - ' . htmlspecialchars($enquete['titre']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-question-circle"></i> Gestion des questions</h2>
    <div class="d-flex gap-2">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
            <i class="fas fa-plus"></i> Ajouter une question
        </button>
        <a href="?controller=Agent&action=editEnquete&id=<?= $enquete['id'] ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour à l'enquête
        </a>
    </div>
</div>

<!-- Messages flash -->
<?php if (!empty($flash_messages)): ?>
    <?php foreach ($flash_messages as $type => $messages): ?>
        <?php foreach ($messages as $message): ?>
            <div class="alert alert-<?= $type === 'success' ? 'success' : ($type === 'error' ? 'danger' : 'info') ?> alert-dismissible fade show">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>

<!-- En-tête de l'enquête -->
<div class="card mb-4">
    <div class="card-body">
        <h4><?= htmlspecialchars($enquete['titre']) ?></h4>
        <?php if (!empty($enquete['description'])): ?>
            <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($enquete['description'])) ?></p>
        <?php endif; ?>
        <div class="mt-2">
            <span class="badge bg-<?= $enquete['statut'] === 'brouillon' ? 'secondary' : 'success' ?>">
                <?= ucfirst($enquete['statut']) ?>
            </span>
            <span class="badge bg-primary ms-2"><?= count($questions) ?> question(s)</span>
        </div>
    </div>
</div>

<!-- Liste des questions -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Questions de l'enquête</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($questions)): ?>
            <div id="questions-list" class="list-group">
                <?php foreach ($questions as $index => $question): ?>
                    <div class="list-group-item" data-question-id="<?= $question['id'] ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-primary me-3"><?= $index + 1 ?></span>
                                    <h6 class="mb-0">
                                        <?= htmlspecialchars($question['texte']) ?>
                                        <?php if ($question['obligatoire']): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif; ?>
                                    </h6>
                                </div>
                                
                                <div class="mb-2">
                                    <span class="badge bg-info"><?= ucfirst($question['type_question']) ?></span>
                                    <?php if ($question['obligatoire']): ?>
                                        <span class="badge bg-danger">Obligatoire</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Aperçu de la question -->
                                <div class="question-preview bg-light p-3 rounded mt-2">
                                    <?php if ($question['type_question'] === 'text'): ?>
                                        <input type="text" class="form-control" placeholder="Réponse textuelle..." disabled>
                                        
                                    <?php elseif ($question['type_question'] === 'textarea'): ?>
                                        <textarea class="form-control" rows="2" placeholder="Réponse longue..." disabled></textarea>
                                        
                                    <?php elseif ($question['type_question'] === 'multiple_choice'): ?>
                                        <?php 
                                        $options = json_decode($question['options'] ?? '[]', true) ?: [];
                                        foreach ($options as $option):
                                        ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" disabled>
                                                <label class="form-check-label"><?= htmlspecialchars($option) ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                        
                                    <?php elseif ($question['type_question'] === 'rating'): ?>
                                        <?php 
                                        $maxRating = json_decode($question['options'] ?? '{"max": 5}', true)['max'] ?? 5;
                                        ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="small">1</span>
                                            <?php for ($i = 1; $i <= $maxRating; $i++): ?>
                                                <i class="fas fa-star text-muted"></i>
                                            <?php endfor; ?>
                                            <span class="small"><?= $maxRating ?></span>
                                        </div>
                                        
                                    <?php elseif ($question['type_question'] === 'yes_no'): ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" disabled>
                                            <label class="form-check-label">Oui</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" disabled>
                                            <label class="form-check-label">Non</label>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if ($enquete['statut'] === 'brouillon'): ?>
                                <div class="btn-group btn-group-sm ms-3">
                                    <button class="btn btn-outline-primary" onclick="editQuestion(<?= $question['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="moveQuestionUp(<?= $question['id'] ?>)" <?= $index === 0 ? 'disabled' : '' ?>>
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="moveQuestionDown(<?= $question['id'] ?>)" <?= $index === count($questions) - 1 ? 'disabled' : '' ?>>
                                        <i class="fas fa-arrow-down"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteQuestion(<?= $question['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($enquete['statut'] === 'brouillon'): ?>
                <div class="mt-3 text-center">
                    <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                        <i class="fas fa-plus"></i> Ajouter une autre question
                    </button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-question-circle fa-4x text-muted mb-3"></i>
                <h5>Aucune question ajoutée</h5>
                <p class="text-muted">Commencez par ajouter des questions à votre enquête.</p>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                    <i class="fas fa-plus"></i> Créer ma première question
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal d'ajout de question -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addQuestionForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="enquete_id" value="<?= $enquete['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="question_text" class="form-label">Texte de la question <span class="text-danger">*</span></label>
                        <textarea class="form-control" 
                                  id="question_text" 
                                  name="texte" 
                                  rows="3" 
                                  placeholder="Posez votre question ici..."
                                  required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="question_type" class="form-label">Type de question <span class="text-danger">*</span></label>
                                <select class="form-select" id="question_type" name="type_question" required>
                                    <option value="">-- Sélectionner un type --</option>
                                    <option value="texte_libre">Texte libre</option>
                                    <option value="choix_multiple">Choix multiple</option>
                                    <option value="notation">Notation</option>
                                    <option value="oui_non">Oui/Non</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="question_required" name="obligatoire" value="1">
                                    <label class="form-check-label" for="question_required">
                                        Question obligatoire
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Options pour choix multiple -->
                    <div id="multiple_choice_options" class="mb-3" style="display: none;">
                        <label class="form-label">Options de réponse</label>
                        <div id="options_container">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control option-input" placeholder="Option 1">
                                <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)" disabled>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control option-input" placeholder="Option 2">
                                <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="addOption()">
                            <i class="fas fa-plus"></i> Ajouter une option
                        </button>
                    </div>
                    
                    <!-- Options pour notation -->
                    <div id="rating_options" class="mb-3" style="display: none;">
                        <label for="rating_max" class="form-label">Échelle de notation</label>
                        <select class="form-select" id="rating_max" name="rating_max">
                            <option value="3">1 à 3</option>
                            <option value="5" selected>1 à 5</option>
                            <option value="7">1 à 7</option>
                            <option value="10">1 à 10</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Ajouter la question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Gestion du type de question
document.getElementById('question_type').addEventListener('change', function() {
    const type = this.value;
    
    // Masquer tous les options
    document.getElementById('multiple_choice_options').style.display = 'none';
    document.getElementById('rating_options').style.display = 'none';
    
    // Afficher les options appropriées
    if (type === 'choix_multiple') {
        document.getElementById('multiple_choice_options').style.display = 'block';
    } else if (type === 'notation') {
        document.getElementById('rating_options').style.display = 'block';
    }
});

// Gestion des options pour choix multiple
function addOption() {
    const container = document.getElementById('options_container');
    const count = container.children.length + 1;
    
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control option-input" placeholder="Option ${count}">
        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    container.appendChild(div);
}

function removeOption(button) {
    const container = document.getElementById('options_container');
    if (container.children.length > 2) {
        button.parentElement.remove();
        
        // Renuméroter les placeholders
        const inputs = container.querySelectorAll('.option-input');
        inputs.forEach((input, index) => {
            input.placeholder = `Option ${index + 1}`;
        });
        
        // Désactiver le bouton de suppression si il n'y a que 2 options
        if (container.children.length === 2) {
            container.children[0].querySelector('.btn-outline-danger').disabled = true;
        }
    }
}

// Soumission du formulaire
document.getElementById('addQuestionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const type = formData.get('type_question');
    
    // Traitement des options selon le type
    if (type === 'choix_multiple') {
        const options = [];
        document.querySelectorAll('.option-input').forEach(input => {
            if (input.value.trim()) {
                options.push(input.value.trim());
            }
        });
        
        if (options.length < 2) {
            alert('Veuillez ajouter au moins 2 options pour une question à choix multiples');
            return;
        }
        
        formData.append('options', JSON.stringify(options));
    } else if (type === 'notation') {
        const max = document.getElementById('rating_max').value;
        formData.append('options', JSON.stringify({max: parseInt(max)}));
    }
    
    // Envoi AJAX
    fetch('?controller=Agent&action=addQuestion', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recharger la page pour voir la nouvelle question
        } else {
            alert('Erreur: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur de communication avec le serveur');
    });
});

// Fonctions de gestion des questions
function editQuestion(id) {
    // TODO: Implémenter l'édition
    alert('Fonctionnalité d\'édition en cours de développement');
}

function deleteQuestion(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette question ?')) {
        fetch(`?controller=Agent&action=deleteQuestion&id=${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `csrf_token=<?= $csrf_token ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur de communication');
        });
    }
}

function moveQuestionUp(id) {
    // TODO: Implémenter le déplacement
    alert('Fonctionnalité de réorganisation en cours de développement');
}

function moveQuestionDown(id) {
    // TODO: Implémenter le déplacement
    alert('Fonctionnalité de réorganisation en cours de développement');
}

// Réinitialiser le modal à la fermeture
document.getElementById('addQuestionModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('addQuestionForm').reset();
    document.getElementById('multiple_choice_options').style.display = 'none';
    document.getElementById('rating_options').style.display = 'none';
    
    // Réinitialiser les options
    const container = document.getElementById('options_container');
    container.innerHTML = `
        <div class="input-group mb-2">
            <input type="text" class="form-control option-input" placeholder="Option 1">
            <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)" disabled>
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="input-group mb-2">
            <input type="text" class="form-control option-input" placeholder="Option 2">
            <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
});
</script>
