<?php
// Titre de la page
$pageTitle = 'Gestion des utilisateurs';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users"></i> Gestion des utilisateurs</h2>
    <a href="?controller=Admin&action=createUser" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Nouvel utilisateur
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
        <h5 class="mb-0">Liste des utilisateurs</h5>
    </div>
    <div class="card-body">
        <?php if (empty($users)): ?>
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted"></i>
                <h5 class="mt-3 text-muted">Aucun utilisateur trouvé</h5>
                <p class="text-muted">Commencez par créer un nouvel utilisateur.</p>
                <a href="?controller=Admin&action=createUser" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Créer un utilisateur
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Date création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-2">
                                            <i class="fas fa-user-circle fa-2x text-<?= 
                                                $user['role'] === 'admin' ? 'danger' : 
                                                ($user['role'] === 'agent' ? 'warning' : 'primary') 
                                            ?>"></i>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $user['role'] === 'admin' ? 'danger' : 
                                        ($user['role'] === 'agent' ? 'warning' : 'primary') 
                                    ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $user['statut'] === 'actif' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($user['statut']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($user['date_creation'])) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?controller=Admin&action=editUser&id=<?= $user['id'] ?>" 
                                           class="btn btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($user['id'] !== $currentUser['id']): ?>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmDelete(<?= $user['id'] ?>, <?= json_encode($user['prenom'] . ' ' . $user['nom']) ?>)" 
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
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
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="deleteUserName"></strong> ?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cette action est irréversible.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" class="d-inline">
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
function confirmDelete(userId, userName) {
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteForm').action = '?controller=Admin&action=deleteUser&id=' + userId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
