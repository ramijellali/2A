<?php
// Titre de la page
$pageTitle = 'Mes Enquêtes';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-poll"></i> Mes Enquêtes</h2>
    <div class="d-flex gap-2">
        <a href="?controller=Agent&action=newEnquete" class="btn btn-success">
            <i class="fas fa-plus"></i> Nouvelle Enquête
        </a>
        <a href="?controller=Agent&action=templates" class="btn btn-outline-primary">
            <i class="fas fa-copy"></i> Modèles
        </a>
    </div>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <input type="hidden" name="controller" value="Agent">
            <input type="hidden" name="action" value="enquetes">
            
            <div class="col-md-3">
                <label for="statut" class="form-label">Statut</label>
                <select class="form-select" id="statut" name="statut">
                    <option value="">Tous les statuts</option>
                    <option value="active" <?= ($filters['statut'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="brouillon" <?= ($filters['statut'] ?? '') === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                    <option value="terminee" <?= ($filters['statut'] ?? '') === 'terminee' ? 'selected' : '' ?>>Terminée</option>
                    <option value="archivee" <?= ($filters['statut'] ?? '') === 'archivee' ? 'selected' : '' ?>>Archivée</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="date_debut" class="form-label">Date début</label>
                <input type="date" class="form-control" id="date_debut" name="date_debut" 
                       value="<?= htmlspecialchars($filters['date_debut'] ?? '') ?>">
            </div>
            
            <div class="col-md-3">
                <label for="date_fin" class="form-label">Date fin</label>
                <input type="date" class="form-control" id="date_fin" name="date_fin" 
                       value="<?= htmlspecialchars($filters['date_fin'] ?? '') ?>">
            </div>
            
            <div class="col-md-3">
                <label for="search" class="form-label">Recherche</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Titre, description..." 
                           value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
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

<!-- Liste des enquêtes -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list"></i> Liste des enquêtes 
            <span class="badge bg-primary ms-2"><?= count($enquetes) ?></span>
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($enquetes)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Statut</th>
                            <th>Questions</th>
                            <th>Réponses</th>
                            <th>Date création</th>
                            <th>Date modification</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enquetes as $enquete): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($enquete['titre']) ?></strong>
                                    <?php if ($enquete['statut'] === 'brouillon'): ?>
                                        <i class="fas fa-edit text-muted ms-1" title="Brouillon"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= htmlspecialchars(substr($enquete['description'] ?? '', 0, 100)) ?>
                                        <?= strlen($enquete['description'] ?? '') > 100 ? '...' : '' ?>
                                    </small>
                                </td>
                                <td>
                                    <?php
                                    $badges = [
                                        'active' => 'success',
                                        'brouillon' => 'secondary',
                                        'terminee' => 'info',
                                        'archivee' => 'dark'
                                    ];
                                    $badgeClass = $badges[$enquete['statut']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <?= ucfirst($enquete['statut']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= $enquete['nb_questions'] ?? 0 ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= $enquete['nb_reponses'] ?? 0 ?></span>
                                    <?php if (($enquete['nb_questions'] ?? 0) > 0): ?>
                                        <small class="text-muted d-block">
                                            <?= round((($enquete['nb_reponses'] ?? 0) / ($enquete['nb_questions'] ?? 1)) * 100, 1) ?>% de taux de réponse
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?= date('d/m/Y H:i', strtotime($enquete['date_creation'])) ?></small>
                                </td>
                                <td>
                                    <small><?= date('d/m/Y H:i', strtotime($enquete['date_modification'])) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?controller=Agent&action=viewEnquete&id=<?= $enquete['id'] ?>" 
                                           class="btn btn-outline-primary" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <?php if ($enquete['statut'] !== 'archivee'): ?>
                                            <a href="?controller=Agent&action=editEnquete&id=<?= $enquete['id'] ?>" 
                                               class="btn btn-outline-secondary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="?controller=Agent&action=questions&enquete_id=<?= $enquete['id'] ?>" 
                                           class="btn btn-outline-info" title="Questions">
                                            <i class="fas fa-question-circle"></i>
                                        </a>
                                        
                                        <?php if (($enquete['nb_reponses'] ?? 0) > 0): ?>
                                            <a href="?controller=Agent&action=results&id=<?= $enquete['id'] ?>" 
                                               class="btn btn-outline-success" title="Résultats">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                            
                                            <a href="?controller=Agent&action=exportResults&id=<?= $enquete['id'] ?>" 
                                               class="btn btn-outline-warning" title="Exporter">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($enquete['statut'] === 'brouillon'): ?>
                                            <button onclick="deleteEnquete(<?= $enquete['id'] ?>)" 
                                                    class="btn btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php elseif ($enquete['statut'] === 'active'): ?>
                                            <button onclick="archiveEnquete(<?= $enquete['id'] ?>)" 
                                                    class="btn btn-outline-warning" title="Archiver">
                                                <i class="fas fa-archive"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (!empty($pagination)): ?>
                <nav aria-label="Navigation des enquêtes" class="mt-3">
                    <ul class="pagination justify-content-center">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?controller=Agent&action=enquetes&page=<?= $pagination['current_page'] - 1 ?>">
                                    <i class="fas fa-chevron-left"></i> Précédent
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?controller=Agent&action=enquetes&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?controller=Agent&action=enquetes&page=<?= $pagination['current_page'] + 1 ?>">
                                    Suivant <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-poll fa-4x text-muted mb-3"></i>
                <h4>Aucune enquête trouvée</h4>
                <p class="text-muted">
                    <?php if (!empty($filters['search']) || !empty($filters['statut'])): ?>
                        Aucune enquête ne correspond à vos critères de recherche.
                        <br>
                        <a href="?controller=Agent&action=enquetes" class="btn btn-link">
                            <i class="fas fa-times"></i> Effacer les filtres
                        </a>
                    <?php else: ?>
                        Vous n'avez pas encore créé d'enquête.
                    <?php endif; ?>
                </p>
                <a href="?controller=Agent&action=newEnquete" class="btn btn-success">
                    <i class="fas fa-plus"></i> Créer ma première enquête
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modales de confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette enquête ?</p>
                <p class="text-danger"><small>Cette action est irréversible.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer l'archivage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir archiver cette enquête ?</p>
                <p class="text-info"><small>L'enquête sera déplacée dans les archives et ne sera plus accessible aux clients.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="archiveForm" method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <button type="submit" class="btn btn-warning">Archiver</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteEnquete(id) {
    const form = document.getElementById('deleteForm');
    form.action = `?controller=Agent&action=deleteEnquete&id=${id}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function archiveEnquete(id) {
    const form = document.getElementById('archiveForm');
    form.action = `?controller=Agent&action=archiveEnquete&id=${id}`;
    const modal = new bootstrap.Modal(document.getElementById('archiveModal'));
    modal.show();
}

// Auto-submit des filtres avec délai
document.getElementById('search').addEventListener('input', function() {
    clearTimeout(this.searchTimeout);
    this.searchTimeout = setTimeout(() => {
        this.form.submit();
    }, 500);
});

// Sauvegarde des filtres dans le localStorage
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    
    // Restaurer les filtres depuis localStorage si disponible
    const savedFilters = localStorage.getItem('agent_enquetes_filters');
    if (savedFilters && !window.location.search.includes('page=')) {
        try {
            const filters = JSON.parse(savedFilters);
            Object.keys(filters).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input && !input.value) {
                    input.value = filters[key];
                }
            });
        } catch (e) {
            console.error('Erreur lors de la restauration des filtres:', e);
        }
    }
    
    // Sauvegarder les filtres lors du submit
    form.addEventListener('submit', function() {
        const formData = new FormData(this);
        const filters = {};
        for (let [key, value] of formData.entries()) {
            if (!['controller', 'action', 'csrf_token'].includes(key) && value) {
                filters[key] = value;
            }
        }
        localStorage.setItem('agent_enquetes_filters', JSON.stringify(filters));
    });
});
</script>
