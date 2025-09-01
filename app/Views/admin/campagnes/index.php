<?php
// Titre de la page
$pageTitle = 'Gestion des campagnes';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-bullhorn"></i> Gestion des campagnes</h2>
    <a href="?controller=Admin&action=createCampagne" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouvelle campagne
    </a>
</div>

<?php if (!empty($flash_messages)): ?>
    <?php foreach ($flash_messages as $flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Liste des campagnes</h5>
    </div>
    <div class="card-body">
        <?php if (empty($campagnes)): ?>
            <div class="text-center py-4">
                <i class="fas fa-bullhorn fa-3x text-muted"></i>
                <h5 class="mt-3 text-muted">Aucune campagne trouvée</h5>
                <p class="text-muted">Commencez par créer une nouvelle campagne d'enquête.</p>
                <a href="?controller=Admin&action=createCampagne" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Créer une campagne
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Période</th>
                            <th>Statut</th>
                            <th>Créateur</th>
                            <th>Enquêtes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($campagnes as $campagne): ?>
                            <tr>
                                <td><?= $campagne['id'] ?></td>
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars($campagne['nom']) ?></strong>
                                        <?php if (!empty($campagne['description'])): ?>
                                            <br><small class="text-muted">
                                                <?= htmlspecialchars(substr($campagne['description'], 0, 100)) ?>
                                                <?= strlen($campagne['description']) > 100 ? '...' : '' ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-start"></i> <?= date('d/m/Y', strtotime($campagne['date_debut'])) ?><br>
                                        <i class="fas fa-calendar-end"></i> <?= date('d/m/Y', strtotime($campagne['date_fin'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $campagne['statut'] === 'active' ? 'success' : 
                                        ($campagne['statut'] === 'en_preparation' ? 'secondary' : 
                                        ($campagne['statut'] === 'terminee' ? 'warning' : 'danger'))
                                    ?>">
                                        <?= 
                                            $campagne['statut'] === 'en_preparation' ? 'En préparation' :
                                            ($campagne['statut'] === 'active' ? 'Active' :
                                            ($campagne['statut'] === 'terminee' ? 'Terminée' :
                                            ($campagne['statut'] === 'suspendue' ? 'Suspendue' : ucfirst($campagne['statut']))))
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        <?= htmlspecialchars($campagne['createur_prenom'] . ' ' . $campagne['createur_nom']) ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= $campagne['nb_enquetes'] ?? 0 ?> enquête(s)
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?controller=Admin&action=viewCampagne&id=<?= $campagne['id'] ?>" 
                                           class="btn btn-outline-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?controller=Admin&action=editCampagne&id=<?= $campagne['id'] ?>" 
                                           class="btn btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirmDeleteCampagne(<?= $campagne['id'] ?>, <?= json_encode($campagne['nom']) ?>)" 
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteCampagneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la campagne <strong id="deleteCampagneName"></strong> ?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cette action supprimera également toutes les enquêtes associées et est irréversible.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteCampagneForm" method="POST" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDeleteCampagne(campagneId, campagneTitre) {
    document.getElementById('deleteCampagneName').textContent = campagneTitre;
    document.getElementById('deleteCampagneForm').action = '?controller=Admin&action=deleteCampagne&id=' + campagneId;
    new bootstrap.Modal(document.getElementById('deleteCampagneModal')).show();
}
</script>
