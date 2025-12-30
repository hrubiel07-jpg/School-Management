<?php
// index.php - Tableau de bord amélioré
require_once 'includes/header.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$page_title = "Tableau de Bord";
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Header -->
            <div class="page-header mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-tachometer-alt"></i> Tableau de Bord</h1>
                    <span class="badge bg-secondary">Année scolaire: 2023-2024</span>
                </div>
                <p>Bienvenue, <?php echo $_SESSION['full_name'] ?? 'Administrateur'; ?>!</p>
            </div>

            <!-- Statistiques en temps réel -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title text-primary">Élèves</h5>
                                    <h2 class="mb-0" id="totalStudents">0</h2>
                                    <p class="text-muted">Total inscrits</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-3x text-primary"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="text-success"><i class="fas fa-arrow-up"></i> <span id="studentGrowth">0</span>%</span>
                                <span class="text-muted">ce mois-ci</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title text-primary">Enseignants</h5>
                                    <h2 class="mb-0" id="totalTeachers">0</h2>
                                    <p class="text-muted">Total employés</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chalkboard-teacher fa-3x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title text-primary">Paiements</h5>
                                    <h2 class="mb-0" id="totalPayments">0 XAF</h2>
                                    <p class="text-muted">Ce mois-ci</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-money-bill-wave fa-3x text-primary"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="text-success"><i class="fas fa-arrow-up"></i> <span id="paymentGrowth">0</span>%</span>
                                <span class="text-muted">vs mois dernier</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title text-primary">Classes</h5>
                                    <h2 class="mb-0" id="totalClasses">0</h2>
                                    <p class="text-muted">Classes actives</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-school fa-3x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques et rapports -->
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-line"></i> Paiements mensuels (XAF)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="paymentChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-bell"></i> Notifications récentes</h5>
                        </div>
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            <div id="notificationsList">
                                <!-- Notifications chargées dynamiquement -->
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Chargement...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Derniers ajouts -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user-plus"></i> Derniers élèves inscrits</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Classe</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentStudents">
                                        <!-- Chargé dynamiquement -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end">
                                <a href="students.php" class="btn btn-sm btn-outline-primary">Voir tous</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-credit-card"></i> Derniers paiements</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Élève</th>
                                            <th>Montant</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentPayments">
                                        <!-- Chargé dynamiquement -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end">
                                <a href="payments.php" class="btn btn-sm btn-outline-primary">Voir tous</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendrier des événements -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar"></i> Calendrier scolaire</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div id="calendar"></div>
                        </div>
                        <div class="col-md-4">
                            <h6>Événements à venir</h6>
                            <ul class="list-group list-group-flush" id="upcomingEvents">
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>Rentrée scolaire</strong>
                                            <br>
                                            <small class="text-muted">04 Septembre 2023</small>
                                        </div>
                                        <span class="badge bg-primary">Important</span>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>Conseil de classe</strong>
                                            <br>
                                            <small class="text-muted">15 Octobre 2023</small>
                                        </div>
                                        <span class="badge bg-warning">Moyen</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Charger les statistiques
    loadStatistics();
    
    // Charger les notifications
    loadNotifications();
    
    // Charger les derniers élèves
    loadRecentStudents();
    
    // Charger les derniers paiements
    loadRecentPayments();
    
    // Initialiser le graphique
    initPaymentChart();
    
    // Initialiser le calendrier
    initCalendar();
});

function loadStatistics() {
    fetch('controllers/get_dashboard_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalStudents').textContent = data.students.total;
                document.getElementById('studentGrowth').textContent = data.students.growth;
                document.getElementById('totalTeachers').textContent = data.teachers.total;
                document.getElementById('totalPayments').textContent = 
                    new Intl.NumberFormat('fr-FR').format(data.payments.total) + ' XAF';
                document.getElementById('paymentGrowth').textContent = data.payments.growth;
                document.getElementById('totalClasses').textContent = data.classes.total;
            }
        });
}

function loadNotifications() {
    fetch('controllers/get_notifications.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('notificationsList');
            container.innerHTML = '';
            
            if (data.notifications && data.notifications.length > 0) {
                data.notifications.forEach(notif => {
                    const item = document.createElement('div');
                    item.className = 'alert alert-' + notif.type + ' alert-dismissible fade show mb-2';
                    item.innerHTML = `
                        <i class="fas ${notif.icon}"></i> ${notif.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <small class="d-block text-muted">${notif.time}</small>
                    `;
                    container.appendChild(item);
                });
            } else {
                container.innerHTML = '<p class="text-muted text-center">Aucune notification</p>';
            }
        });
}

function loadRecentStudents() {
    fetch('controllers/get_recent_students.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('recentStudents');
            tbody.innerHTML = '';
            
            if (data.students && data.students.length > 0) {
                data.students.forEach(student => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${student.name}</td>
                        <td>${student.class}</td>
                        <td>${student.date}</td>
                        <td><span class="badge bg-success">${student.status}</span></td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">Aucun élève récent</td></tr>';
            }
        });
}

function loadRecentPayments() {
    fetch('controllers/get_recent_payments.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('recentPayments');
            tbody.innerHTML = '';
            
            if (data.payments && data.payments.length > 0) {
                data.payments.forEach(payment => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${payment.student}</td>
                        <td>${new Intl.NumberFormat('fr-FR').format(payment.amount)} XAF</td>
                        <td>${payment.date}</td>
                        <td><span class="badge bg-${payment.status === 'paid' ? 'success' : 'warning'}">${payment.status}</span></td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">Aucun paiement récent</td></tr>';
            }
        });
}

function initPaymentChart() {
    const ctx = document.getElementById('paymentChart').getContext('2d');
    const paymentChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Paiements (XAF)',
                data: [12000000, 15000000, 14000000, 18000000, 20000000, 22000000, 
                       21000000, 19000000, 25000000, 25450000, 0, 0],
                borderColor: '#009543',
                backgroundColor: 'rgba(0, 149, 67, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' XAF';
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR').format(value) + ' XAF';
                        }
                    }
                }
            }
        }
    });
}

function initCalendar() {
    // Simple calendrier pour le moment
    const calendarEl = document.getElementById('calendar');
    
    // Pour une version complète, utiliser une bibliothèque comme FullCalendar
    calendarEl.innerHTML = `
        <div class="text-center py-5">
            <h4>Calendrier Scolaire</h4>
            <p class="text-muted">Intégration du calendrier à venir</p>
            <div class="row mt-4">
                <div class="col-3">
                    <div class="p-3 border rounded bg-primary text-white">
                        <div>Sep</div>
                        <h3>04</h3>
                        <small>Rentrée</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-3 border rounded bg-success text-white">
                        <div>Oct</div>
                        <h3>15</h3>
                        <small>Conseil</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-3 border rounded bg-warning text-dark">
                        <div>Déc</div>
                        <h3>20</h3>
                        <small>Vacances</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-3 border rounded bg-info text-white">
                        <div>Jan</div>
                        <h3>08</h3>
                        <small>Rentrée</small>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Actualiser les données toutes les 5 minutes
setInterval(function() {
    loadStatistics();
    loadNotifications();
}, 300000);
</script>

<?php require_once 'includes/footer.php'; ?>