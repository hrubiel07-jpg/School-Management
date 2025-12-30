<?php
require_once 'includes/header.php';

// Si non connecté, rediriger vers login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$page_title = "Tableau de Bord";
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar d-md-block collapse">
            <div class="position-sticky pt-3">
                <!-- Logo de l'école -->
                <div class="text-center mb-4">
                    <?php if(isset($_SESSION['school_logo']) && !empty($_SESSION['school_logo'])): ?>
                        <img src="<?php echo $_SESSION['school_logo']; ?>" alt="Logo École" class="school-logo mb-2">
                    <?php endif; ?>
                    <h6 class="text-white"><?php echo $_SESSION['school_name'] ?? 'Votre École'; ?></h6>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-tachometer-alt"></i> Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="students.php">
                            <i class="fas fa-users"></i> Élèves
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="teachers.php">
                            <i class="fas fa-chalkboard-teacher"></i> Enseignants
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="classes.php">
                            <i class="fas fa-school"></i> Classes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="payments.php">
                            <i class="fas fa-money-bill-wave"></i> Paiements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="grades.php">
                            <i class="fas fa-chart-bar"></i> Notes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="schedule.php">
                            <i class="fas fa-calendar-alt"></i> Emploi du temps
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-file-alt"></i> Rapports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cog"></i> Paramètres
                        </a>
                    </li>
                    <li class="nav-item mt-4">
                        <a class="nav-link text-warning" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Header -->
            <div class="page-header mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-tachometer-alt"></i> Tableau de Bord</h1>
                    <span class="badge bg-secondary">Année scolaire: 2023-2024</span>
                </div>
                <p>Bienvenue, <?php echo $_SESSION['full_name'] ?? 'Administrateur'; ?>!</p>
            </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title text-primary">Élèves</h5>
                                    <h2 class="mb-0">350</h2>
                                    <p class="text-muted">Total inscrits</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-3x text-primary"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="text-success"><i class="fas fa-arrow-up"></i> 5.2%</span>
                                <span class="text-muted">depuis le mois dernier</span>
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
                                    <h2 class="mb-0">28</h2>
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
                                    <h2 class="mb-0">15,240,500 XAF</h2>
                                    <p class="text-muted">Ce mois-ci</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-money-bill-wave fa-3x text-primary"></i>
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
                                    <h5 class="card-title text-primary">Classes</h5>
                                    <h2 class="mb-0">14</h2>
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

            <!-- Dernières activités et graphiques -->
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-line"></i> Paiements mensuels (XAF)</h5>
                        </div>
                        <div class="card-body">
                            <!-- Graphique simple -->
                            <div style="height: 300px; background: #f8f9fa; border-radius: 5px;" class="d-flex align-items-center justify-content-center">
                                <canvas id="paymentChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-bell"></i> Notifications récentes</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <i class="fas fa-user-plus text-success"></i>
                                    5 nouveaux élèves inscrits
                                    <small class="text-muted d-block">Aujourd'hui</small>
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-money-bill text-warning"></i>
                                    3 paiements en attente
                                    <small class="text-muted d-block">Hier</small>
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-birthday-cake text-info"></i>
                                    2 anniversaires aujourd'hui
                                    <small class="text-muted d-block">Aujourd'hui</small>
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-exclamation-triangle text-danger"></i>
                                    1 absence non justifiée
                                    <small class="text-muted d-block">Hier</small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>