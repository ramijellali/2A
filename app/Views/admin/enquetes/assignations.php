<?php
/**
 * Vue : Gestion des assignations d'une enquête
 * Permet d'assigner/retirer des clients d'une enquête
 */
?>

<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Assignations - <?= htmlspecialchars($enquete['titre']) ?></h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="/admin/enquetes">Enquêtes</a></li>
                    <li class="breadcrumb-item active">Assignations</li>
                </ol>
            </nav>
        </div>
        <a href="/admin/enquetes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux enquêtes
        </a>
    </div>

    <!-- Messages flash -->
    <?php if (!empty($flash_messages)): ?>
        <?php foreach ($flash_messages as $message): ?>
            <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : $message['type'] ?> alert-dismissible fade show">
                <?= htmlspecialchars($message['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="row">
        <!-- Statistiques -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-bar"></i> Statistiques des assignations</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="stat-item">
                                <div class="stat-number text-primary"><?= $stats['total_assigne'] ?? 0 ?></div>
                                <div class="stat-label">Total assigné</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-item">
                                <div class="stat-number text-info"><?= $stats['envoye'] ?? 0 ?></div>
                                <div class="stat-label">Envoyé</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-item">
                                <div class="stat-number text-warning"><?= $stats['en_cours'] ?? 0 ?></div>
                                <div class="stat-label">En cours</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-item">
                                <div class="stat-number text-success"><?= $stats['complete'] ?? 0 ?></div>
                                <div class="stat-label">Complété</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-item">
                                <div class="stat-number text-danger"><?= $stats['expire'] ?? 0 ?></div>
                                <div class="stat-label">Expiré</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-item">
                                <div class="stat-number text-muted"><?= count($clients) - ($stats['total_assigne'] ?? 0) ?></div>
                                <div class="stat-label">Non assigné</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire d'assignation -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user-plus"></i> Assigner des clients</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/enquetes/assigner">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="enquete_id" value="<?= $enquete['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Clients disponibles</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="select-all">
                                <label class="form-check-label fw-bold" for="select-all">
                                    Sélectionner tout
                                </label>
                            </div>
                            <div style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.75rem;">
                                <?php 
                                $assignedClientIds = array_column($assignations, 'client_id');
                                foreach ($clients as $client): 
                                    $isAssigned = in_array($client['id'], $assignedClientIds);
                                ?>
                                    <div class="form-check">
                                        <input class="form-check-input client-checkbox" 
                                               type="checkbox" 
                                               name="client_ids[]" 
                                               value="<?= $client['id'] ?>"
                                               id="client_<?= $client['id'] ?>"
                                               <?= $isAssigned ? 'disabled' : '' ?>>
                                        <label class="form-check-label <?= $isAssigned ? 'text-muted' : '' ?>" 
                                               for="client_<?= $client['id'] ?>">
                                            <?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?>
                                            <small class="text-muted d-block"><?= htmlspecialchars($client['email']) ?></small>
                                            <?php if ($isAssigned): ?>
                                                <small class="text-success"><i class="fas fa-check"></i> Déjà assigné</small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane"></i> Assigner aux clients sélectionnés
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste des assignations actuelles -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-users"></i> Clients assignés (<?= count($assignations) ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($assignations)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>Aucun client assigné à cette enquête</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Email</th>
                                        <th>Statut</th>
                                        <th>Date envoi</th>
                                        <th>Date début</th>
                                        <th>Date fin</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignations as $assignation): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($assignation['client_prenom'] . ' ' . $assignation['client_nom']) ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($assignation['client_email']) ?></td>
                                            <td>
                                                <?php
                                                $statusClass = match($assignation['statut']) {
                                                    'envoye' => 'info',
                                                    'en_cours' => 'warning',
                                                    'complete' => 'success',
                                                    'expire' => 'danger',
                                                    default => 'secondary'
                                                };
                                                $statusText = match($assignation['statut']) {
                                                    'envoye' => 'Envoyé',
                                                    'en_cours' => 'En cours',
                                                    'complete' => 'Complété',
                                                    'expire' => 'Expiré',
                                                    default => $assignation['statut']
                                                };
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                            <td>
                                                <?= $assignation['date_envoi'] ? date('d/m/Y H:i', strtotime($assignation['date_envoi'])) : '-' ?>
                                            </td>
                                            <td>
                                                <?= $assignation['date_debut'] ? date('d/m/Y H:i', strtotime($assignation['date_debut'])) : '-' ?>
                                            </td>
                                            <td>
                                                <?= $assignation['date_fin'] ? date('d/m/Y H:i', strtotime($assignation['date_fin'])) : '-' ?>
                                            </td>
                                            <td>
                                                <form method="POST" action="/admin/enquetes/retirer-assignation" class="d-inline" 
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir retirer cette assignation ?');">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                                    <input type="hidden" name="enquete_id" value="<?= $enquete['id'] ?>">
                                                    <input type="hidden" name="client_id" value="<?= $assignation['client_id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Retirer l'assignation">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-item {
    padding: 1rem 0;
}
.stat-number {
    font-size: 2rem;
    font-weight: bold;
    line-height: 1;
}
.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du "Sélectionner tout"
    const selectAllCheckbox = document.getElementById('select-all');
    const clientCheckboxes = document.querySelectorAll('.client-checkbox:not([disabled])');
    
    selectAllCheckbox.addEventListener('change', function() {
        clientCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Mise à jour du "Sélectionner tout" quand les cases individuelles changent
    clientCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(clientCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(clientCheckboxes).some(cb => cb.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        });
    });
});
</script>
