<?php
// roles.php - Gestion des Rôles et Permissions (en français)
require_once 'includes/header.php';

// Vérifier les permissions
if (!checkPermission('manage_roles') && $_SESSION['role'] != 'super_admin') {
    header('Location: unauthorized.php');
    exit();
}

$page_title = "Gestion des Rôles et Permissions";
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Header -->
            <div class="page-header mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-user-tag"></i> Gestion des Rôles et Permissions</h1>
                    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                        <i class="fas fa-plus-circle"></i> Nouveau Rôle
                    </button>
                </div>
                <p>Créez et gérez les rôles et leurs permissions dans le système</p>
            </div>

            <!-- Statistiques des rôles -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Rôles Actifs</h6>
                                    <h3 class="mb-0">
                                        <?php
                                        require_once 'config/database.php';
                                        $database = new Database();
                                        $db = $database->getConnection();
                                        
                                        $query = "SELECT COUNT(DISTINCT role_id) as total 
                                                 FROM school_admins WHERE school_id = :school_id";
                                        $stmt = $db->prepare($query);
                                        $stmt->bindParam(':school_id', $_SESSION['school_id']);
                                        $stmt->execute();
                                        echo $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                                        ?>
                                    </h3>
                                </div>
                                <i class="fas fa-user-tag fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Permissions Totales</h6>
                                    <h3 class="mb-0">48</h3>
                                </div>
                                <i class="fas fa-key fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Rôle le plus utilisé</h6>
                                    <small>Enseignant</small>
                                </div>
                                <i class="fas fa-crown fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Dernière modification</h6>
                                    <small>Aujourd'hui</small>
                                </div>
                                <i class="fas fa-history fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des rôles -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Liste des Rôles</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-success" onclick="exportRoles()">
                                <i class="fas fa-file-excel"></i> Exporter
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="refreshRoles()">
                                <i class="fas fa-sync-alt"></i> Actualiser
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="rolesTable">
                            <thead>
                                <tr>
                                    <th>Nom du Rôle</th>
                                    <th>Description</th>
                                    <th>Permissions</th>
                                    <th>Utilisateurs</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Récupérer tous les rôles
                                $query = "SELECT r.*, COUNT(sa.id) as user_count 
                                         FROM roles r 
                                         LEFT JOIN school_admins sa ON r.id = sa.role_id AND sa.school_id = :school_id
                                         WHERE r.school_id IS NULL OR r.school_id = :school_id
                                         GROUP BY r.id
                                         ORDER BY r.is_system_role DESC, r.role_name";
                                
                                $stmt = $db->prepare($query);
                                $stmt->bindParam(':school_id', $_SESSION['school_id']);
                                $stmt->execute();
                                
                                // Traduction des permissions en français
                                $permission_translations = [
                                    'view_students' => 'Voir élèves',
                                    'add_students' => 'Ajouter élèves',
                                    'edit_students' => 'Modifier élèves',
                                    'delete_students' => 'Supprimer élèves',
                                    'view_teachers' => 'Voir enseignants',
                                    'add_teachers' => 'Ajouter enseignants',
                                    'edit_teachers' => 'Modifier enseignants',
                                    'delete_teachers' => 'Supprimer enseignants',
                                    'view_classes' => 'Voir classes',
                                    'add_classes' => 'Ajouter classes',
                                    'edit_classes' => 'Modifier classes',
                                    'delete_classes' => 'Supprimer classes',
                                    'view_payments' => 'Voir paiements',
                                    'add_payments' => 'Ajouter paiements',
                                    'edit_payments' => 'Modifier paiements',
                                    'delete_payments' => 'Supprimer paiements',
                                    'view_grades' => 'Voir notes',
                                    'add_grades' => 'Ajouter notes',
                                    'edit_grades' => 'Modifier notes',
                                    'delete_grades' => 'Supprimer notes',
                                    'view_schedule' => "Voir emploi du temps",
                                    'add_schedule' => "Ajouter emploi du temps",
                                    'edit_schedule' => "Modifier emploi du temps",
                                    'delete_schedule' => "Supprimer emploi du temps",
                                    'view_users' => 'Voir utilisateurs',
                                    'add_users' => 'Ajouter utilisateurs',
                                    'edit_users' => 'Modifier utilisateurs',
                                    'delete_users' => 'Supprimer utilisateurs',
                                    'manage_roles' => 'Gérer rôles',
                                    'view_settings' => 'Voir paramètres',
                                    'edit_settings' => 'Modifier paramètres',
                                    '*' => 'Toutes permissions'
                                ];
                                
                                // Traduction des noms de rôles système
                                $system_role_translations = [
                                    'super_admin' => 'Super Administrateur',
                                    'admin' => 'Administrateur',
                                    'accountant' => 'Comptable',
                                    'teacher' => 'Enseignant',
                                    'secretary' => 'Secrétaire',
                                    'parent' => 'Parent',
                                    'student' => 'Élève'
                                ];
                                
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $permissions = json_decode($row['permissions'], true);
                                    $permission_count = is_array($permissions) ? count($permissions) : 0;
                                    
                                    // Traduire le nom du rôle système
                                    $role_name = $row['role_name'];
                                    if ($row['is_system_role'] && isset($system_role_translations[$role_name])) {
                                        $role_name = $system_role_translations[$role_name];
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="role-icon me-2">
                                                <i class="fas fa-user-tag fa-lg 
                                                    <?php echo $row['is_system_role'] ? 'text-primary' : 'text-success'; ?>"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($role_name); ?></h6>
                                                <?php if($row['is_system_role']): ?>
                                                <small class="text-muted">Rôle système</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['role_description'] ?? 'Aucune description'); ?></small>
                                    </td>
                                    <td>
                                        <div class="permissions-preview" style="max-width: 200px;">
                                            <?php 
                                            if (is_array($permissions)) {
                                                $displayed = 0;
                                                foreach ($permissions as $perm) {
                                                    if ($displayed < 3) {
                                                        $translated = $permission_translations[$perm] ?? $perm;
                                                        echo '<span class="badge bg-info me-1 mb-1">' . $translated . '</span>';
                                                        $displayed++;
                                                    }
                                                }
                                                if ($permission_count > 3) {
                                                    echo '<span class="badge bg-secondary">+' . ($permission_count - 3) . ' autres</span>';
                                                }
                                                if (in_array('*', $permissions)) {
                                                    echo '<span class="badge bg-success">Toutes permissions</span>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo $row['user_count']; ?> utilisateurs</span>
                                    </td>
                                    <td>
                                        <?php if($row['is_system_role']): ?>
                                        <span class="badge bg-warning">Système</span>
                                        <?php else: ?>
                                        <span class="badge bg-success">Personnalisé</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary btn-view-role" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if(!$row['is_system_role']): ?>
                                            <button class="btn btn-outline-warning btn-edit-role" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($row['role_name']); ?>"
                                                    data-description="<?php echo htmlspecialchars($row['role_description']); ?>"
                                                    data-permissions='<?php echo htmlspecialchars($row['permissions']); ?>'
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-delete-role" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($row['role_name']); ?>"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php else: ?>
                                            <button class="btn btn-outline-secondary" disabled title="Rôle système non modifiable">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Guide des permissions -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-book"></i> Guide des Permissions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6><i class="fas fa-users text-primary"></i> Gestion des Personnes</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> <strong>Voir élèves:</strong> Consultation de la liste des élèves</li>
                                <li><i class="fas fa-plus text-info"></i> <strong>Ajouter élèves:</strong> Création de nouveaux élèves</li>
                                <li><i class="fas fa-edit text-warning"></i> <strong>Modifier élèves:</strong> Édition des informations élèves</li>
                                <li><i class="fas fa-trash text-danger"></i> <strong>Supprimer élèves:</strong> Suppression d'élèves</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-graduation-cap text-primary"></i> Gestion Académique</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chart-bar text-info"></i> <strong>Voir notes:</strong> Consultation des résultats</li>
                                <li><i class="fas fa-calendar-alt text-success"></i> <strong>Voir emploi du temps:</strong> Consultation des horaires</li>
                                <li><i class="fas fa-clipboard-check text-warning"></i> <strong>Voir présences:</strong> Consultation des absences</li>
                                <li><i class="fas fa-school text-secondary"></i> <strong>Voir classes:</strong> Consultation des classes</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-cogs text-primary"></i> Administration</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-users-cog text-info"></i> <strong>Gérer utilisateurs:</strong> Gestion des comptes</li>
                                <li><i class="fas fa-user-tag text-success"></i> <strong>Gérer rôles:</strong> Configuration des permissions</li>
                                <li><i class="fas fa-school text-warning"></i> <strong>Modifier paramètres:</strong> Configuration de l'école</li>
                                <li><i class="fas fa-chart-pie text-secondary"></i> <strong>Générer rapports:</strong> Création de rapports</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal pour ajouter un rôle -->
<div class="modal fade" id="addRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-user-tag"></i> Créer un Nouveau Rôle
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addRoleForm" action="controllers/add_role.php" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom du Rôle *</label>
                            <input type="text" class="form-control" name="role_name" required 
                                   placeholder="Ex: Coordinateur, Surveillant">
                            <small class="text-muted">Le nom qui apparaîtra dans le système</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="role_description" 
                                   placeholder="Brève description du rôle">
                        </div>
                    </div>
                    
                    <!-- Permissions par catégories -->
                    <div class="mb-4">
                        <h6 class="mb-3"><i class="fas fa-key"></i> Sélectionner les Permissions</h6>
                        
                        <!-- Gestion des Élèves -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="form-check">
                                    <input class="form-check-input select-category" type="checkbox" 
                                           data-category="students">
                                    <label class="form-check-label fw-bold">
                                        <i class="fas fa-users text-primary"></i> Gestion des Élèves
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="view_students" id="view_students">
                                            <label class="form-check-label" for="view_students">
                                                Voir les élèves
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="add_students" id="add_students">
                                            <label class="form-check-label" for="add_students">
                                                Ajouter des élèves
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="edit_students" id="edit_students">
                                            <label class="form-check-label" for="edit_students">
                                                Modifier des élèves
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="delete_students" id="delete_students">
                                            <label class="form-check-label" for="delete_students">
                                                Supprimer des élèves
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gestion des Enseignants -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="form-check">
                                    <input class="form-check-input select-category" type="checkbox" 
                                           data-category="teachers">
                                    <label class="form-check-label fw-bold">
                                        <i class="fas fa-chalkboard-teacher text-primary"></i> Gestion des Enseignants
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="view_teachers" id="view_teachers">
                                            <label class="form-check-label" for="view_teachers">
                                                Voir les enseignants
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="add_teachers" id="add_teachers">
                                            <label class="form-check-label" for="add_teachers">
                                                Ajouter des enseignants
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="edit_teachers" id="edit_teachers">
                                            <label class="form-check-label" for="edit_teachers">
                                                Modifier des enseignants
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="delete_teachers" id="delete_teachers">
                                            <label class="form-check-label" for="delete_teachers">
                                                Supprimer des enseignants
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gestion Académique -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="form-check">
                                    <input class="form-check-input select-category" type="checkbox" 
                                           data-category="academic">
                                    <label class="form-check-label fw-bold">
                                        <i class="fas fa-graduation-cap text-primary"></i> Gestion Académique
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="view_classes" id="view_classes">
                                            <label class="form-check-label" for="view_classes">
                                                Voir les classes
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="view_grades" id="view_grades">
                                            <label class="form-check-label" for="view_grades">
                                                Voir les notes
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="view_schedule" id="view_schedule">
                                            <label class="form-check-label" for="view_schedule">
                                                Voir l'emploi du temps
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="view_attendance" id="view_attendance">
                                            <label class="form-check-label" for="view_attendance">
                                                Voir les présences
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gestion Financière -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="form-check">
                                    <input class="form-check-input select-category" type="checkbox" 
                                           data-category="financial">
                                    <label class="form-check-label fw-bold">
                                        <i class="fas fa-money-bill-wave text-primary"></i> Gestion Financière
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="view_payments" id="view_payments">
                                            <label class="form-check-label" for="view_payments">
                                                Voir les paiements
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="add_payments" id="add_payments">
                                            <label class="form-check-label" for="add_payments">
                                                Ajouter des paiements
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="generate_invoices" id="generate_invoices">
                                            <label class="form-check-label" for="generate_invoices">
                                                Générer des factures
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="view_financial_reports" id="view_financial_reports">
                                            <label class="form-check-label" for="view_financial_reports">
                                                Voir rapports financiers
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Administration -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="form-check">
                                    <input class="form-check-input select-category" type="checkbox" 
                                           data-category="administration">
                                    <label class="form-check-label fw-bold">
                                        <i class="fas fa-cogs text-primary"></i> Administration
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="view_users" id="view_users">
                                            <label class="form-check-label" for="view_users">
                                                Voir les utilisateurs
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="manage_roles" id="manage_roles">
                                            <label class="form-check-label" for="manage_roles">
                                                Gérer les rôles
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="view_settings" id="view_settings">
                                            <label class="form-check-label" for="view_settings">
                                                Voir les paramètres
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" type="checkbox" 
                                                   name="permissions[]" value="view_reports" id="view_reports">
                                            <label class="form-check-label" for="view_reports">
                                                Voir les rapports
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Options rapides -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary me-2" id="selectAllPermissions">
                                <i class="fas fa-check-square"></i> Tout sélectionner
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllPermissions">
                                <i class="fas fa-square"></i> Tout désélectionner
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info" id="selectCommonTeacher">
                                <i class="fas fa-user-graduate"></i> Profil Enseignant
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success" id="selectCommonAccountant">
                                <i class="fas fa-calculator"></i> Profil Comptable
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer le Rôle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour modifier un rôle -->
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-edit"></i> Modifier le Rôle
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRoleForm" action="controllers/update_role.php" method="POST">
                <input type="hidden" name="role_id" id="edit_role_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom du Rôle *</label>
                            <input type="text" class="form-control" name="role_name" id="edit_role_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="role_description" id="edit_role_description">
                        </div>
                    </div>
                    
                    <!-- Permissions -->
                    <div class="mb-3">
                        <h6 class="mb-3"><i class="fas fa-key"></i> Permissions</h6>
                        <div id="editPermissionsContainer">
                            <!-- Les permissions seront chargées dynamiquement -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour voir les détails -->
<div class="modal fade" id="viewRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-eye"></i> Détails du Rôle
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="roleDetailsContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="editFromView">
                    <i class="fas fa-edit"></i> Modifier
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner/désélectionner toutes les permissions
    document.getElementById('selectAllPermissions').addEventListener('click', function() {
        document.querySelectorAll('.permission-check').forEach(checkbox => {
            checkbox.checked = true;
        });
        updateCategoryCheckboxes();
    });
    
    document.getElementById('deselectAllPermissions').addEventListener('click', function() {
        document.querySelectorAll('.permission-check').forEach(checkbox => {
            checkbox.checked = false;
        });
        updateCategoryCheckboxes();
    });
    
    // Profil prédéfini : Enseignant
    document.getElementById('selectCommonTeacher').addEventListener('click', function() {
        // Désélectionner tout d'abord
        document.querySelectorAll('.permission-check').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Sélectionner les permissions pour un enseignant
        const teacherPermissions = ['view_students', 'view_grades', 'add_grades', 'edit_grades', 
                                   'view_schedule', 'view_attendance', 'manage_attendance'];
        
        teacherPermissions.forEach(perm => {
            const checkbox = document.getElementById(perm);
            if (checkbox) checkbox.checked = true;
        });
        
        updateCategoryCheckboxes();
    });
    
    // Profil prédéfini : Comptable
    document.getElementById('selectCommonAccountant').addEventListener('click', function() {
        // Désélectionner tout d'abord
        document.querySelectorAll('.permission-check').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Sélectionner les permissions pour un comptable
        const accountantPermissions = ['view_payments', 'add_payments', 'edit_payments', 
                                      'generate_invoices', 'view_financial_reports'];
        
        accountantPermissions.forEach(perm => {
            const checkbox = document.getElementById(perm);
            if (checkbox) checkbox.checked = true;
        });
        
        updateCategoryCheckboxes();
    });
    
    // Mettre à jour les cases à cocher de catégories
    function updateCategoryCheckboxes() {
        document.querySelectorAll('.select-category').forEach(categoryCheckbox => {
            const category = categoryCheckbox.dataset.category;
            const categoryPermissions = document.querySelectorAll(`.permission-check[data-category="${category}"]`);
            let allChecked = true;
            let anyChecked = false;
            
            categoryPermissions.forEach(checkbox => {
                if (!checkbox.checked) allChecked = false;
                if (checkbox.checked) anyChecked = true;
            });
            
            categoryCheckbox.checked = allChecked;
            categoryCheckbox.indeterminate = anyChecked && !allChecked;
        });
    }
    
    // Gérer la sélection de catégories
    document.querySelectorAll('.select-category').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const category = this.dataset.category;
            const categoryPermissions = document.querySelectorAll(`.permission-check[data-category="${category}"]`);
            
            categoryPermissions.forEach(permCheckbox => {
                permCheckbox.checked = this.checked;
            });
        });
    });
    
    // Mettre à jour les cases à cocher de catégories quand les permissions changent
    document.querySelectorAll('.permission-check').forEach(checkbox => {
        checkbox.addEventListener('change', updateCategoryCheckboxes);
    });
    
    // Initialiser l'état des catégories
    updateCategoryCheckboxes();
    
    // Édition d'un rôle
    document.querySelectorAll('.btn-edit-role').forEach(button => {
        button.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('editRoleModal'));
            
            // Remplir les champs
            document.getElementById('edit_role_id').value = this.dataset.id;
            document.getElementById('edit_role_name').value = this.dataset.name;
            document.getElementById('edit_role_description').value = this.dataset.description;
            
            // Charger les permissions
            const permissions = JSON.parse(this.dataset.permissions);
            const container = document.getElementById('editPermissionsContainer');
            
            // Copier la structure des permissions du modal d'ajout
            const addModal = document.getElementById('addRoleModal');
            const permissionsHTML = addModal.querySelector('.modal-body').innerHTML;
            container.innerHTML = permissionsHTML;
            
            // Cochez les permissions existantes
            permissions.forEach(perm => {
                const checkbox = container.querySelector(`input[value="${perm}"]`);
                if (checkbox) checkbox.checked = true;
            });
            
            // Mettre à jour les catégories
            setTimeout(() => {
                updateCategoryCheckboxes();
            }, 100);
            
            modal.show();
        });
    });
    
    // Suppression d'un rôle
    document.querySelectorAll('.btn-delete-role').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm(`Êtes-vous sûr de vouloir supprimer le rôle "${this.dataset.name}" ?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'controllers/delete_role.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'role_id';
                input.value = this.dataset.id;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
    
    // Voir les détails d'un rôle
    document.querySelectorAll('.btn-view-role').forEach(button => {
        button.addEventListener('click', function() {
            const roleId = this.dataset.id;
            const modal = new bootstrap.Modal(document.getElementById('viewRoleModal'));
            
            // Charger les détails via AJAX
            fetch(`controllers/get_role_details.php?id=${roleId}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('roleDetailsContent').innerHTML = html;
                    modal.show();
                });
        });
    });
    
    // Édition depuis la vue détaillée
    document.getElementById('editFromView').addEventListener('click', function() {
        const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewRoleModal'));
        viewModal.hide();
        
        // Récupérer l'ID du rôle et ouvrir le modal d'édition
        const roleId = document.querySelector('#roleDetailsContent [data-role-id]').dataset.roleId;
        const editButton = document.querySelector(`.btn-edit-role[data-id="${roleId}"]`);
        if (editButton) {
            editButton.click();
        }
    });
    
    // Exporter les rôles
    window.exportRoles = function() {
        window.location.href = 'controllers/export_roles.php?format=excel';
    };
    
    // Actualiser la page
    window.refreshRoles = function() {
        location.reload();
    };
    
    // Ajouter des attributs data-category aux permissions
    const permissionCategories = {
        'students': ['view_students', 'add_students', 'edit_students', 'delete_students'],
        'teachers': ['view_teachers', 'add_teachers', 'edit_teachers', 'delete_teachers'],
        'academic': ['view_classes', 'view_grades', 'view_schedule', 'view_attendance'],
        'financial': ['view_payments', 'add_payments', 'generate_invoices', 'view_financial_reports'],
        'administration': ['view_users', 'manage_roles', 'view_settings', 'view_reports']
    };
    
    for (const [category, perms] of Object.entries(permissionCategories)) {
        perms.forEach(perm => {
            const checkbox = document.getElementById(perm);
            if (checkbox) {
                checkbox.setAttribute('data-category', category);
            }
        });
    }
});
</script>

<style>
/* Styles spécifiques pour les rôles */
.role-icon {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: rgba(0, 149, 67, 0.1);
}

.permissions-preview {
    max-height: 60px;
    overflow-y: auto;
}

.permissions-preview .badge {
    font-size: 0.75rem;
    padding: 4px 8px;
}

.card .card-header .form-check {
    margin-bottom: 0;
}

.card .card-header .form-check-label {
    cursor: pointer;
}

.select-category:indeterminate {
    background-color: var(--primary);
    border-color: var(--primary);
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.modal-body .card {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.modal-body .card .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.modal-body .form-check {
    margin-bottom: 0.5rem;
}

/* Animation pour les nouveaux rôles */
@keyframes highlightNew {
    0% {
        background-color: rgba(0, 149, 67, 0.2);
    }
    100% {
        background-color: transparent;
    }
}

tr.new-role {
    animation: highlightNew 2s ease-out;
}

/* Style pour les permissions dans le tableau */
.permissions-preview {
    line-height: 1.8;
}

/* Responsive */
@media (max-width: 768px) {
    .modal-dialog.modal-lg {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
    }
    
    .modal-body .card .card-body .row {
        margin: 0;
    }
    
    .modal-body .card .card-body .col-md-3 {
        margin-bottom: 0.5rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>