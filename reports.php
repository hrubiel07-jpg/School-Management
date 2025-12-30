<?php
require_once 'includes/header.php';
checkRole(['super_admin', 'admin']);

$page_title = "Rapports et Statistiques";
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="page-header mt-3">
                <h1><i class="fas fa-file-alt"></i> Rapports et Statistiques</h1>
                <p>Générez des rapports détaillés sur votre école</p>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                            <h5>Rapport financier</h5>
                            <button class="btn btn-primary btn-sm mt-2">Générer PDF</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-bar fa-3x text-success mb-3"></i>
                            <h5>Statistiques élèves</h5>
                            <button class="btn btn-primary btn-sm mt-2">Voir rapport</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-3x text-warning mb-3"></i>
                            <h5>Liste des enseignants</h5>
                            <button class="btn btn-primary btn-sm mt-2">Exporter Excel</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-graduation-cap fa-3x text-info mb-3"></i>
                            <h5>Bulletins scolaires</h5>
                            <button class="btn btn-primary btn-sm mt-2">Générer tous</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Statistiques générales</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="studentsChart" width="400" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <canvas id="paymentsChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Graphique des élèves par niveau
var ctx1 = document.getElementById('studentsChart').getContext('2d');
var studentsChart = new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: ['Maternelle', 'Primaire', 'Secondaire', 'Lycée'],
        datasets: [{
            label: 'Nombre d\'élèves',
            data: [45, 180, 95, 30],
            backgroundColor: ['#FFD700', '#009543', '#DC241F', '#6F42C1']
        }]
    },
    options: {
        responsive: true
    }
});

// Graphique des paiements
var ctx2 = document.getElementById('paymentsChart').getContext('2d');
var paymentsChart = new Chart(ctx2, {
    type: 'pie',
    data: {
        labels: ['Payés', 'En attente', 'Retard'],
        datasets: [{
            data: [70, 20, 10],
            backgroundColor: ['#009543', '#FFC107', '#DC3545']
        }]
    },
    options: {
        responsive: true
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>