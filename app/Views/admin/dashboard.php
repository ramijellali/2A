<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-tachometer-alt"></i> Tableau de bord - Administration</h1>
</div>

<div class="row">
    <!-- Statistiques des utilisateurs -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Utilisateurs Totaux
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php 
                            $totalUsers = 0;
                            foreach ($stats['users'] as $userStat) {
                                $totalUsers += $userStat['total'];
                            }
                            echo $totalUsers;
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des campagnes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Campagnes Actives
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $stats['campagnes']['active'] ?> / <?= $stats['campagnes']['total'] ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des enquêtes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Enquêtes Actives
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $stats['enquetes']['active'] ?> / <?= $stats['enquetes']['total'] ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Taux de participation -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Taux de Participation
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php 
                            $participation = $stats['enquetes']['total'] > 0 ? 
                                round(($stats['enquetes']['active'] / $stats['enquetes']['total']) * 100, 1) : 0;
                            echo $participation . '%';
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Répartition des utilisateurs par rôle -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie"></i> Répartition des Utilisateurs
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Rôle</th>
                                <th>Total</th>
                                <th>Actifs</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['users'] as $userStat): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-<?= $userStat['role'] === 'admin' ? 'danger' : ($userStat['role'] === 'agent' ? 'warning' : 'info') ?>">
                                        <?= ucfirst($userStat['role']) ?>
                                    </span>
                                </td>
                                <td><?= $userStat['total'] ?></td>
                                <td><?= $userStat['actifs'] ?></td>
                                <td><?= $totalUsers > 0 ? round(($userStat['total'] / $totalUsers) * 100, 1) : 0 ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt"></i> Actions Rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="?controller=Admin&action=createUser" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Créer un utilisateur
                    </a>
                    <a href="?controller=Admin&action=createCampagne" class="btn btn-success">
                        <i class="fas fa-plus"></i> Créer une campagne
                    </a>
                    <a href="?controller=Admin&action=users" class="btn btn-info">
                        <i class="fas fa-users"></i> Gérer les utilisateurs
                    </a>
                    <a href="?controller=Admin&action=campagnes" class="btn btn-warning">
                        <i class="fas fa-bullhorn"></i> Gérer les campagnes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques avec Chart.js -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-area"></i> Évolution de l'activité
                </h6>
            </div>
            <div class="card-body">
                <canvas id="activityChart" width="100" height="30"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique d'activité (exemple)
const ctx = document.getElementById('activityChart').getContext('2d');
const activityChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep'],
        datasets: [{
            label: 'Enquêtes créées',
            data: [12, 19, 8, 15, 22, 18, 25, 30, 28],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1
        }, {
            label: 'Réponses reçues',
            data: [25, 35, 20, 28, 45, 38, 52, 65, 58],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Activité mensuelle du système'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}
.text-xs {
    font-size: 0.7rem;
}
</style>
