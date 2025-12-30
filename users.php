<?php
// users.php - Gestion complète des utilisateurs
require_once 'includes/header.php';

// Vérifier les permissions
if (!checkPermission('manage_users') && $_SESSION['role'] != 'super_admin') {
    header('Location: unauthorized.php');
    exit();
}

$page_title = "Gestion des Utilisateurs";

// Connexion à la base de données
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Header avec statistiques -->
            <div class="page-header mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-users-cog"></i> Gestion des Utilisateurs</h1>
                    <div class="btn-group">
                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-user-plus"></i> Nouvel utilisateur
                        </button>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importUsersModal">
                            <i class="fas fa-file-import"></i> Importer
                        </button>
                    </div>
                </div>
                <p>Gérez les comptes utilisateurs et leurs permissions</p>
                
                <!-- Statistiques rapides -->
                <div class="row mt-3">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Total Utilisateurs</h6>
                                        <h3 class="mb-0">
                                            <?php
                                            $query = "SELECT COUNT(*) as total FROM school_admins WHERE school_id = :school_id";
                                            $stmt = $db->prepare($query);
                                            $stmt->bindParam(':school_id', $_SESSION['school_id']);
                                            $stmt->execute();
                                            echo $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                                            ?>
                                        </h3>
                                    </div>
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Actifs</h6>
                                        <h3 class="mb-0">
                                            <?php
                                            $query = "SELECT COUNT(*) as total FROM school_admins 
                                                     WHERE school_id = :school_id AND status = 'active'";
                                            $stmt = $db->prepare($query);
                                            $stmt->bindParam(':school_id', $_SESSION['school_id']);
                                            $stmt->execute();
                                            echo $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                                            ?>
                                        </h3>
                                    </div>
                                    <i class="fas fa-user-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Dernier accès</h6>
                                        <small>Il y a 2 heures</small>
                                    </div>
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Rôles actifs</h6>
                                        <h3 class="mb-0">
                                            <?php
                                            $query = "SELECT COUNT(DISTINCT role_id) as total FROM school_admins 
                                                     WHERE school_id = :school_id";
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
                </div>
            </div>

            <!-- Filtres avancés -->
            <div class="card mb-4">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Rôle</label>
                            <select class="form-select" name="role_filter" id="roleFilter">
                                <option value="">Tous les rôles</option>
                                <?php
                                $roles_query = "SELECT r.* FROM roles r 
                                                WHERE r.school_id IS NULL OR r.school_id = :school_id 
                                                ORDER BY r.role_name";
                                $roles_stmt = $db->prepare($roles_query);
                                $roles_stmt->bindParam(':school_id', $_SESSION['school_id']);
                                $roles_stmt->execute();
                                
                                while ($role = $roles_stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . $role['id'] . '">' . 
                                         htmlspecialchars($role['role_name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" name="status_filter" id="statusFilter">
                                <option value="">Tous les statuts</option>
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                                <option value="locked">Bloqué</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Recherche</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="Nom, email ou téléphone..." id="searchUser">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="applyFilter">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table des utilisateurs -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Liste des utilisateurs</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-success" id="exportExcel">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                            <button class="btn btn-sm btn-outline-danger" id="exportPDF">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="usersTableContainer">
                        <table class="table table-hover table-striped" id="usersTable">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th>Nom complet</th>
                                    <th>Nom d'utilisateur</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Dernière connexion</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Récupérer les utilisateurs avec leurs rôles
                                $query = "SELECT sa.*, r.role_name, r.permissions 
                                         FROM school_admins sa 
                                         LEFT JOIN roles r ON sa.role_id = r.id 
                                         WHERE sa.school_id = :school_id 
                                         ORDER BY sa.created_at DESC";
                                
                                $stmt = $db->prepare($query);
                                $stmt->bindParam(':school_id', $_SESSION['school_id']);
                                $stmt->execute();
                                
                                if ($stmt->rowCount() > 0) {
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        // Déterminer la couleur du badge selon le rôle
                                        $role_badge_color = match($row['role_name']) {
                                            'super_admin' => 'bg-danger',
                                            'admin' => 'bg-primary',
                                            'accountant' => 'bg-success',
                                            'teacher' => 'bg-info',
                                            'secretary' => 'bg-warning',
                                            'parent' => 'bg-secondary',
                                            'student' => 'bg-dark',
                                            default => 'bg-secondary'
                                        };
                                        
                                        // Déterminer le statut
                                        $status_badge = match($row['status']) {
                                            'active' => '<span class="badge bg-success">Actif</span>',
                                            'inactive' => '<span class="badge bg-secondary">Inactif</span>',
                                            'locked' => '<span class="badge bg-danger">Bloqué</span>',
                                            default => '<span class="badge bg-secondary">' . $row['status'] . '</span>'
                                        };
                                        
                                        // Dernière connexion formatée
                                        $last_login = !empty($row['last_login']) 
                                            ? date('d/m/Y H:i', strtotime($row['last_login']))
                                            : 'Jamais';
                                ?>
                                <tr data-user-id="<?php echo $row['id']; ?>">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input user-checkbox" type="checkbox" 
                                                   value="<?php echo $row['id']; ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title bg-light rounded-circle text-primary">
                                                    <?php echo strtoupper(substr($row['full_name'], 0, 1)); ?>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($row['full_name']); ?></h6>
                                                <small class="text-muted">Créé le: <?php echo date('d/m/Y', strtotime($row['created_at'])); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td>
                                        <?php if(!empty($row['email'])): ?>
                                            <a href="mailto:<?php echo $row['email']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($row['email']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(!empty($row['phone'])): ?>
                                            <a href="tel:<?php echo $row['phone']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($row['phone']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $role_badge_color; ?>">
                                            <?php echo htmlspecialchars($row['role_name'] ?? 'Non défini'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $status_badge; ?></td>
                                    <td>
                                        <small class="text-muted"><?php echo $last_login; ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary btn-view-user" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-warning btn-edit-user" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-fullname="<?php echo htmlspecialchars($row['full_name']); ?>"
                                                    data-username="<?php echo htmlspecialchars($row['username']); ?>"
                                                    data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                                    data-phone="<?php echo htmlspecialchars($row['phone']); ?>"
                                                    data-role-id="<?php echo $row['role_id']; ?>"
                                                    data-status="<?php echo $row['status']; ?>"
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                            <button class="btn btn-outline-danger btn-delete-user" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($row['full_name']); ?>"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary dropdown-toggle" 
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item btn-reset-pwd" href="#" 
                                                           data-id="<?php echo $row['id']; ?>"
                                                           data-name="<?php echo htmlspecialchars($row['full_name']); ?>">
                                                            <i class="fas fa-key text-warning"></i> Réinitialiser mot de passe
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item btn-send-credentials" href="#"
                                                           data-id="<?php echo $row['id']; ?>"
                                                           data-email="<?php echo htmlspecialchars($row['email']); ?>">
                                                            <i class="fas fa-envelope text-info"></i> Envoyer identifiants
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <?php if($row['status'] == 'active'): ?>
                                                        <a class="dropdown-item btn-deactivate-user" href="#"
                                                           data-id="<?php echo $row['id']; ?>"
                                                           data-name="<?php echo htmlspecialchars($row['full_name']); ?>">
                                                            <i class="fas fa-user-slash text-danger"></i> Désactiver
                                                        </a>
                                                        <?php else: ?>
                                                        <a class="dropdown-item btn-activate-user" href="#"
                                                           data-id="<?php echo $row['id']; ?>"
                                                           data-name="<?php echo htmlspecialchars($row['full_name']); ?>">
                                                            <i class="fas fa-user-check text-success"></i> Activer
                                                        </a>
                                                        <?php endif; ?>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo '<tr><td colspan="9" class="text-center py-4">
                                            <i class="fas fa-users-slash fa-2x text-muted mb-3"></i>
                                            <h5>Aucun utilisateur trouvé</h5>
                                            <p class="text-muted">Commencez par ajouter votre premier utilisateur</p>
                                          </td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Actions en masse -->
                    <div class="mt-3" id="bulkActions" style="display: none;">
                        <div class="card border-primary">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span id="selectedCount">0 utilisateur(s) sélectionné(s)</span>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary" id="bulkActivate">
                                            <i class="fas fa-user-check"></i> Activer
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" id="bulkDeactivate">
                                            <i class="fas fa-user-slash"></i> Désactiver
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" id="bulkDelete">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" id="cancelBulk">
                                            <i class="fas fa-times"></i> Annuler
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    <nav aria-label="Page navigation" class="mt-3">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#">Précédent</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Suivant</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal pour ajouter un utilisateur -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-user-plus"></i> Nouvel utilisateur
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm" action="controllers/add_user.php" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom complet *</label>
                            <input type="text" class="form-control" name="full_name" required
                                   placeholder="Ex: Jean KABEYA">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom d'utilisateur *</label>
                            <input type="text" class="form-control" name="username" required
                                   placeholder="Ex: jkabeya">
                            <small class="text-muted">Utilisé pour se connecter au système</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                   placeholder="exemple@ecole.cg">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" name="phone"
                                   placeholder="+242 XX XX XX XX">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rôle *</label>
                            <select class="form-select" name="role_id" required>
                                <option value="">Sélectionner un rôle...</option>
                                <?php
                                $roles_query = "SELECT * FROM roles 
                                                WHERE (school_id IS NULL OR school_id = :school_id) 
                                                AND role_name != 'super_admin'
                                                ORDER BY role_name";
                                $roles_stmt = $db->prepare($roles_query);
                                $roles_stmt->bindParam(':school_id', $_SESSION['school_id']);
                                $roles_stmt->execute();
                                
                                while ($role = $roles_stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . $role['id'] . '" data-permissions=\'' . $role['permissions'] . '\'>';
                                    echo htmlspecialchars($role['role_name']);
                                    if ($role['is_system_role']) {
                                        echo ' (Système)';
                                    }
                                    echo ' - ' . htmlspecialchars($role['role_description']);
                                    echo '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Statut initial *</label>
                            <select class="form-select" name="status" required>
                                <option value="active" selected>Actif</option>
                                <option value="inactive">Inactif</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mot de passe temporaire *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="tempPassword" 
                                       required value="Password123">
                                <button class="btn btn-outline-secondary" type="button" id="generatePassword">
                                    <i class="fas fa-random"></i>
                                </button>
                                <button class="btn btn-outline-secondary" type="button" id="showPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimum 8 caractères avec majuscule, minuscule et chiffre</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirmer le mot de passe *</label>
                            <input type="password" class="form-control" name="confirm_password" required
                                   value="Password123">
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="send_credentials" id="sendCredentials" checked>
                                <label class="form-check-label" for="sendCredentials">
                                    Envoyer les identifiants par email (si email fourni)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour modifier un utilisateur -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-edit"></i> Modifier l'utilisateur
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" action="controllers/update_user.php" method="POST">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom complet *</label>
                        <input type="text" class="form-control" name="full_name" id="edit_full_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nom d'utilisateur *</label>
                        <input type="text" class="form-control" name="username" id="edit_username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="edit_email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" name="phone" id="edit_phone" placeholder="+242">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rôle *</label>
                        <select class="form-select" name="role_id" id="edit_role_id" required>
                            <option value="">Sélectionner un rôle...</option>
                            <?php
                            $roles_stmt->execute();
                            while ($role = $roles_stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $role['id'] . '">' . 
                                     htmlspecialchars($role['role_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Statut *</label>
                        <select class="form-select" name="status" id="edit_status" required>
                            <option value="active">Actif</option>
                            <option value="inactive">Inactif</option>
                            <option value="locked">Bloqué</option>
                        </select>
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

<!-- Modal pour réinitialiser le mot de passe -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-key"></i> Réinitialiser le mot de passe
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPasswordForm" action="controllers/reset_password.php" method="POST">
                <input type="hidden" name="user_id" id="reset_user_id">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Vous allez réinitialiser le mot de passe de <strong id="reset_user_name"></strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nouveau mot de passe *</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="new_password" id="newPassword" required>
                            <button class="btn btn-outline-secondary" type="button" id="generateNewPassword">
                                <i class="fas fa-random"></i>
                            </button>
                        </div>
                        <div class="password-strength mt-2">
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" id="passwordStrength" style="width: 0%"></div>
                            </div>
                            <small class="text-muted" id="passwordFeedback"></small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmer le mot de passe *</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirmNewPassword" required>
                        <div class="mt-1">
                            <small class="text-success" id="passwordMatch" style="display: none;">
                                <i class="fas fa-check"></i> Les mots de passe correspondent
                            </small>
                            <small class="text-danger" id="passwordMismatch" style="display: none;">
                                <i class="fas fa-times"></i> Les mots de passe ne correspondent pas
                            </small>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="force_password_change" id="forcePasswordChange" checked>
                        <label class="form-check-label" for="forcePasswordChange">
                            Forcer le changement de mot de passe à la prochaine connexion
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour voir les détails -->
<div class="modal fade" id="viewUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-user"></i> Détails de l'utilisateur
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="userDetailsContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
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

<!-- Modal pour importer des utilisateurs -->
<div class="modal fade" id="importUsersModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-file-import"></i> Importer des utilisateurs
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="importUsersForm" action="controllers/import_users.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Téléchargez le modèle Excel, remplissez-le et importez-le ici.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Télécharger le modèle</label>
                        <a href="templates/users_template.xlsx" class="btn btn-outline-primary btn-sm" download>
                            <i class="fas fa-download"></i> Modèle Excel
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Fichier Excel à importer *</label>
                        <input type="file" class="form-control" name="users_file" accept=".xlsx,.xls" required>
                        <small class="text-muted">Formats acceptés: .xlsx, .xls</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Rôle par défaut</label>
                        <select class="form-select" name="default_role">
                            <option value="">Sélectionner un rôle...</option>
                            <?php
                            $roles_stmt->execute();
                            while ($role = $roles_stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $role['id'] . '">' . 
                                     htmlspecialchars($role['role_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="send_welcome_email" id="sendWelcomeEmail">
                        <label class="form-check-label" for="sendWelcomeEmail">
                            Envoyer un email de bienvenue avec les identifiants
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour confirmation d'action en masse -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white" id="bulkActionTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="bulkActionMessage"></p>
                <input type="hidden" id="bulkActionType">
                <input type="hidden" id="bulkSelectedIds">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmBulkAction">
                    <i class="fas fa-check"></i> Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let selectedUsers = new Set();
    
    // Gestion de la sélection de tous les utilisateurs
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            if (this.checked) {
                selectedUsers.add(checkbox.value);
            } else {
                selectedUsers.delete(checkbox.value);
            }
        });
        updateBulkActions();
    });
    
    // Gestion de la sélection individuelle
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                selectedUsers.add(this.value);
            } else {
                selectedUsers.delete(this.value);
                document.getElementById('selectAll').checked = false;
            }
            updateBulkActions();
        });
    });
    
    // Fonction pour mettre à jour les actions en masse
    function updateBulkActions() {
        const count = selectedUsers.size;
        const bulkActionsDiv = document.getElementById('bulkActions');
        const selectedCountSpan = document.getElementById('selectedCount');
        
        if (count > 0) {
            selectedCountSpan.textContent = `${count} utilisateur(s) sélectionné(s)`;
            bulkActionsDiv.style.display = 'block';
        } else {
            bulkActionsDiv.style.display = 'none';
            document.getElementById('selectAll').checked = false;
        }
    }
    
    // Annuler la sélection en masse
    document.getElementById('cancelBulk').addEventListener('click', function() {
        selectedUsers.clear();
        document.querySelectorAll('.user-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        document.getElementById('selectAll').checked = false;
        updateBulkActions();
    });
    
    // Générer un mot de passe aléatoire
    function generatePassword() {
        const length = 12;
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        let password = "";
        for (let i = 0; i < length; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        return password;
    }
    
    // Générateur de mot de passe dans le modal d'ajout
    document.getElementById('generatePassword').addEventListener('click', function() {
        const password = generatePassword();
        document.getElementById('tempPassword').value = password;
        document.querySelector('input[name="confirm_password"]').value = password;
    });
    
    // Afficher/masquer le mot de passe
    document.getElementById('showPassword').addEventListener('click', function() {
        const passwordField = document.getElementById('tempPassword');
        const confirmField = document.querySelector('input[name="confirm_password"]');
        const type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;
        confirmField.type = type;
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });
    
    // Générateur de mot de passe dans le modal de réinitialisation
    document.getElementById('generateNewPassword').addEventListener('click', function() {
        const password = generatePassword();
        document.getElementById('newPassword').value = password;
        document.getElementById('confirmNewPassword').value = password;
        checkPasswordStrength(password);
        checkPasswordMatch();
    });
    
    // Vérifier la force du mot de passe
    function checkPasswordStrength(password) {
        let strength = 0;
        const feedback = document.getElementById('passwordFeedback');
        const strengthBar = document.getElementById('passwordStrength');
        
        if (password.length >= 8) strength += 25;
        if (/[a-z]/.test(password)) strength += 25;
        if (/[A-Z]/.test(password)) strength += 25;
        if (/[0-9]/.test(password)) strength += 25;
        
        strengthBar.style.width = strength + '%';
        
        if (strength < 50) {
            strengthBar.className = 'progress-bar bg-danger';
            feedback.textContent = 'Mot de passe faible';
            feedback.className = 'text-danger';
        } else if (strength < 75) {
            strengthBar.className = 'progress-bar bg-warning';
            feedback.textContent = 'Mot de passe moyen';
            feedback.className = 'text-warning';
        } else {
            strengthBar.className = 'progress-bar bg-success';
            feedback.textContent = 'Mot de passe fort';
            feedback.className = 'text-success';
        }
    }
    
    // Vérifier la correspondance des mots de passe
    function checkPasswordMatch() {
        const password = document.getElementById('newPassword').value;
        const confirm = document.getElementById('confirmNewPassword').value;
        const match = document.getElementById('passwordMatch');
        const mismatch = document.getElementById('passwordMismatch');
        
        if (confirm === '') {
            match.style.display = 'none';
            mismatch.style.display = 'none';
        } else if (password === confirm) {
            match.style.display = 'block';
            mismatch.style.display = 'none';
        } else {
            match.style.display = 'none';
            mismatch.style.display = 'block';
        }
    }
    
    // Écouter les changements de mot de passe
    document.getElementById('newPassword').addEventListener('input', function() {
        checkPasswordStrength(this.value);
        checkPasswordMatch();
    });
    
    document.getElementById('confirmNewPassword').addEventListener('input', checkPasswordMatch);
    
    // Édition d'un utilisateur
    document.querySelectorAll('.btn-edit-user').forEach(button => {
        button.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            document.getElementById('edit_user_id').value = this.dataset.id;
            document.getElementById('edit_full_name').value = this.dataset.fullname;
            document.getElementById('edit_username').value = this.dataset.username;
            document.getElementById('edit_email').value = this.dataset.email;
            document.getElementById('edit_phone').value = this.dataset.phone;
            document.getElementById('edit_role_id').value = this.dataset.roleId;
            document.getElementById('edit_status').value = this.dataset.status;
            modal.show();
        });
    });
    
    // Suppression d'un utilisateur
    document.querySelectorAll('.btn-delete-user').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${this.dataset.name}" ?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'controllers/delete_user.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'user_id';
                input.value = this.dataset.id;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
    
    // Réinitialisation du mot de passe
    document.querySelectorAll('.btn-reset-pwd').forEach(button => {
        button.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            document.getElementById('reset_user_id').value = this.dataset.id;
            document.getElementById('reset_user_name').textContent = this.dataset.name;
            
            // Générer un mot de passe par défaut
            const password = generatePassword();
            document.getElementById('newPassword').value = password;
            document.getElementById('confirmNewPassword').value = password;
            checkPasswordStrength(password);
            checkPasswordMatch();
            
            modal.show();
        });
    });
    
    // Activation/désactivation d'utilisateurs
    document.querySelectorAll('.btn-activate-user, .btn-deactivate-user').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.classList.contains('btn-activate-user') ? 'Activer' : 'Désactiver';
            const userId = this.closest('.dropdown-item').dataset.id;
            const userName = this.closest('.dropdown-item').dataset.name;
            
            if (confirm(`${action} l'utilisateur "${userName}" ?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'controllers/change_user_status.php';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'user_id';
                idInput.value = userId;
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = action === 'Activer' ? 'active' : 'inactive';
                
                form.appendChild(idInput);
                form.appendChild(statusInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
    
    // Envoyer les identifiants
    document.querySelectorAll('.btn-send-credentials').forEach(button => {
        button.addEventListener('click', function() {
            const email = this.dataset.email;
            if (!email) {
                alert('Cet utilisateur n\'a pas d\'email configuré.');
                return;
            }
            
            if (confirm(`Envoyer les identifiants à ${email} ?`)) {
                // Simuler l'envoi d'email
                fetch('controllers/send_credentials.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: this.dataset.id,
                        email: email
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Identifiants envoyés avec succès!');
                    } else {
                        alert('Erreur lors de l\'envoi des identifiants.');
                    }
                });
            }
        });
    });
    
    // Voir les détails d'un utilisateur
    document.querySelectorAll('.btn-view-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.id;
            const modal = new bootstrap.Modal(document.getElementById('viewUserModal'));
            
            // Charger les détails via AJAX
            fetch(`controllers/get_user_details.php?id=${userId}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('userDetailsContent').innerHTML = html;
                    modal.show();
                });
        });
    });
    
    // Édition depuis la vue détaillée
    document.getElementById('editFromView').addEventListener('click', function() {
        const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewUserModal'));
        viewModal.hide();
        
        // Récupérer l'ID de l'utilisateur et ouvrir le modal d'édition
        const userId = document.querySelector('#userDetailsContent [data-user-id]').dataset.userId;
        const editButton = document.querySelector(`.btn-edit-user[data-id="${userId}"]`);
        if (editButton) {
            editButton.click();
        }
    });
    
    // Actions en masse
    document.getElementById('bulkActivate').addEventListener('click', function() {
        showBulkActionModal('activate', 'Activer', 'Voulez-vous activer les utilisateurs sélectionnés ?');
    });
    
    document.getElementById('bulkDeactivate').addEventListener('click', function() {
        showBulkActionModal('deactivate', 'Désactiver', 'Voulez-vous désactiver les utilisateurs sélectionnés ?');
    });
    
    document.getElementById('bulkDelete').addEventListener('click', function() {
        showBulkActionModal('delete', 'Supprimer', 'Voulez-vous supprimer les utilisateurs sélectionnés ? Cette action est irréversible !');
    });
    
    function showBulkActionModal(action, title, message) {
        document.getElementById('bulkActionTitle').textContent = title + ' les utilisateurs';
        document.getElementById('bulkActionMessage').textContent = message;
        document.getElementById('bulkActionType').value = action;
        document.getElementById('bulkSelectedIds').value = Array.from(selectedUsers).join(',');
        
        const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
        modal.show();
    }
    
    document.getElementById('confirmBulkAction').addEventListener('click', function() {
        const action = document.getElementById('bulkActionType').value;
        const userIds = document.getElementById('bulkSelectedIds').value;
        
        fetch('controllers/bulk_action_users.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: action,
                user_ids: userIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de l\'exécution de l\'action.');
            }
        });
    });
    
    // Exporter en Excel
    document.getElementById('exportExcel').addEventListener('click', function() {
        window.location.href = 'controllers/export_users.php?format=excel';
    });
    
    // Exporter en PDF
    document.getElementById('exportPDF').addEventListener('click', function() {
        window.location.href = 'controllers/export_users.php?format=pdf';
    });
    
    // Appliquer les filtres
    document.getElementById('applyFilter').addEventListener('click', function() {
        const roleFilter = document.getElementById('roleFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const searchTerm = document.getElementById('searchUser').value;
        
        // Filtrer les lignes du tableau
        const rows = document.querySelectorAll('#usersTable tbody tr');
        rows.forEach(row => {
            let showRow = true;
            
            // Filtrer par rôle
            if (roleFilter) {
                const role = row.querySelector('td:nth-child(6)').textContent.trim();
                if (!role.includes(roleFilter)) {
                    showRow = false;
                }
            }
            
            // Filtrer par statut
            if (statusFilter) {
                const status = row.querySelector('td:nth-child(7)').textContent.trim();
                if (!status.toLowerCase().includes(statusFilter.toLowerCase())) {
                    showRow = false;
                }
            }
            
            // Filtrer par recherche
            if (searchTerm) {
                const fullName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const username = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                const phone = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                const searchLower = searchTerm.toLowerCase();
                
                if (!fullName.includes(searchLower) && 
                    !username.includes(searchLower) && 
                    !email.includes(searchLower) && 
                    !phone.includes(searchLower)) {
                    showRow = false;
                }
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    });
    
    // Actualiser la page
    document.getElementById('refreshUsers').addEventListener('click', function() {
        location.reload();
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>