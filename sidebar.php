<?php
// includes/sidebar.php - Version originale améliorée
?>
<!-- Sidebar -->
<div class="col-md-3 col-lg-2 sidebar d-md-block collapse">
    <div class="position-sticky pt-3">
        <!-- Logo de l'école -->
        <div class="text-center mb-4">
            <?php if(isset($_SESSION['school_logo']) && !empty($_SESSION['school_logo'])): ?>
                <img src="<?php echo $_SESSION['school_logo']; ?>" alt="Logo École" class="school-logo mb-2">
            <?php else: ?>
                <div style="background: var(--primary); color: white; padding: 15px; border-radius: 10px;">
                    <i class="fas fa-school fa-2x mb-2"></i>
                    <h6><?php echo $_SESSION['school_name'] ?? 'Votre École'; ?></h6>
                </div>
            <?php endif; ?>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : ''; ?>" href="students.php">
                    <i class="fas fa-users"></i> Élèves
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'teachers.php' ? 'active' : ''; ?>" href="teachers.php">
                    <i class="fas fa-chalkboard-teacher"></i> Enseignants
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'classes.php' ? 'active' : ''; ?>" href="classes.php">
                    <i class="fas fa-school"></i> Classes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active' : ''; ?>" href="payments.php">
                    <i class="fas fa-money-bill-wave"></i> Paiements
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'grades.php' ? 'active' : ''; ?>" href="grades.php">
                    <i class="fas fa-chart-bar"></i> Notes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'schedule.php' ? 'active' : ''; ?>" href="schedule.php">
                    <i class="fas fa-calendar-alt"></i> Emploi du temps
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                    <i class="fas fa-file-alt"></i> Rapports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
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