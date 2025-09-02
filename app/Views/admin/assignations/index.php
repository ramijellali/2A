<?php
/**
 * Vue : Toutes les assignations
 * Vue d'ensemble de toutes les assignations d'enquêtes
 */
?>

<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Toutes les assignations</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin">Tableau de bord</a></li>
                    <li class="breadcrumb-item active">Assignations</li>
                </ol>
            </nav>
        </div>
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

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Campagne</label>
                    <select name="campagne" class="form-select" id="filter-campagne">
                        <option value="">Toutes les campagnes</option>
                        <?php
                        $campagnes = array_unique(array_column($assignations, 'campagne_nom'));
                        foreach ($campagnes as $campagne):
                        ?>
                            <option value="<?= htmlspecialchars($campagne) ?>" 
                                    <?= ($_GET['campagne'] ?? '') === $campagne ? 'selected' : '' ?>>
                                <?= htmlspecialchars($campagne) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select" id="filter-statut">
                        <option value="">Tous les statuts</option>
                        <option value="envoye" <?= ($_GET['statut'] ?? '') === 'envoye' ? 'selected' : '' ?>>Envoyé</option>
                        <option value="en_cours" <?= ($_GET['statut'] ?? '') === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                        <option value="complete" <?= ($_GET['statut'] ?? '') === 'complete' ? 'selected' : '' ?>>Complété</option>
                        <option value="expire" <?= ($_GET['statut'] ?? '') === 'expire' ? 'selected' : '' ?>>Expiré</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Client</label>
                    <input type="text" name="client" class="form-control" placeholder="Nom du client..." 
                           value="<?= htmlspecialchars($_GET['client'] ?? '') ?>" id="filter-client">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>
                        <a href="/admin/assignations" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Effacer
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des assignations -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-list"></i> Liste des assignations (<?= count($assignations) ?>)</h5>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" onclick="exportTable()">
                        <i class="fas fa-download"></i> Exporter
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($assignations)): ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                    <h5>Aucune assignation trouvée</h5>
                    <p>Il n'y a actuellement aucune assignation correspondant aux critères.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="assignations-table">
                        <thead>
                            <tr>
                                <th>Campagne</th>
                                <th>Enquête</th>
                                <th>Client</th>
                                <th>Email</th>
                                <th>Statut</th>
                                <th>Date envoi</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                <th>Durée</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assignations as $assignation): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-primary"><?= htmlspecialchars($assignation['campagne_nom']) ?></span>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($assignation['enquete_titre']) ?></strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($assignation['client_prenom'] . ' ' . $assignation['client_nom']) ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= htmlspecialchars($assignation['client_email']) ?></small>
                                    </td>
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
                                        <?php if ($assignation['date_debut'] && $assignation['date_fin']): ?>
                                            <?php
                                            $debut = new DateTime($assignation['date_debut']);
                                            $fin = new DateTime($assignation['date_fin']);
                                            $duree = $debut->diff($fin);
                                            ?>
                                            <small class="text-muted">
                                                <?php if ($duree->days > 0): ?>
                                                    <?= $duree->days ?>j 
                                                <?php endif; ?>
                                                <?= $duree->h ?>h <?= $duree->i ?>m
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="/admin/enquetes/assignations?enquete_id=<?= $assignation['enquete_id'] ?>" 
                                               class="btn btn-outline-primary" title="Voir les assignations de cette enquête">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST" action="/admin/enquetes/retirer-assignation" class="d-inline" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir retirer cette assignation ?');">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                                <input type="hidden" name="enquete_id" value="<?= $assignation['enquete_id'] ?>">
                                                <input type="hidden" name="client_id" value="<?= $assignation['client_id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger" title="Retirer l'assignation">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
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

<script>
// Filtrage côté client
document.addEventListener('DOMContentLoaded', function() {
    const campagneFilter = document.getElementById('filter-campagne');
    const statutFilter = document.getElementById('filter-statut');
    const clientFilter = document.getElementById('filter-client');
    const table = document.getElementById('assignations-table');
    
    function filterTable() {
        if (!table) return;
        
        const campagne = campagneFilter.value.toLowerCase();
        const statut = statutFilter.value.toLowerCase();
        const client = clientFilter.value.toLowerCase();
        
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const campagneText = cells[0].textContent.toLowerCase();
            const statutText = cells[4].textContent.toLowerCase();
            const clientText = cells[2].textContent.toLowerCase();
            
            const campagneMatch = !campagne || campagneText.includes(campagne);
            const statutMatch = !statut || statutText.includes(statut);
            const clientMatch = !client || clientText.includes(client);
            
            row.style.display = (campagneMatch && statutMatch && clientMatch) ? '' : 'none';
        });
    }
    
    // Filtrage en temps réel pour le champ client
    clientFilter.addEventListener('input', filterTable);
});

// Export des données
function exportTable() {
    const table = document.getElementById('assignations-table');
    if (!table) return;
    
    let csv = 'Campagne,Enquête,Client,Email,Statut,Date envoi,Date début,Date fin,Durée\n';
    
    const rows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const rowData = [
            cells[0].textContent.trim(),
            cells[1].textContent.trim(),
            cells[2].textContent.trim(),
            cells[3].textContent.trim(),
            cells[4].textContent.trim(),
            cells[5].textContent.trim(),
            cells[6].textContent.trim(),
            cells[7].textContent.trim(),
            cells[8].textContent.trim()
        ];
        csv += rowData.map(cell => `"${cell.replace(/"/g, '""')}"`).join(',') + '\n';
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'assignations_' + new Date().toISOString().split('T')[0] + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
