<?php
// teachers.php - Gestion complète des enseignants
require_once 'includes/header.php';
checkRole(['super_admin', 'admin']);

$page_title = "Gestion des Enseignants";

// Connexion à la base de données
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Header -->
            <div class="page-header mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-chalkboard-teacher"></i> Gestion des Enseignants</h1>
                    <div class="btn-group">
                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                            <i class="fas fa-user-plus"></i> Nouvel enseignant
                        </button>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importTeachersModal">
                            <i class="fas fa-file-import"></i> Importer
                        </button>
                    </div>
                </div>
                <p>Gérez le personnel enseignant de votre école</p>
                
                <!-- Statistiques rapides -->
                <div class="row mt-3">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Total Enseignants</h6>
                                        <h3 class="mb-0">
                                            <?php
                                            $query = "SELECT COUNT(*) as total FROM teachers WHERE school_id = :school_id";
                                            $stmt = $db->prepare($query);
                                            $stmt->bindParam(':school_id', $_SESSION['school_id']);
                                            $stmt->execute();
                                            echo $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                                            ?>
                                        </h3>
                                    </div>
                                    <i class="fas fa-chalkboard-teacher fa-2x"></i>
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
                                            $query = "SELECT COUNT(*) as total FROM teachers 
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
                                        <h6 class="mb-0">Spécialisations</h6>
                                        <small>12 différentes</small>
                                    </div>
                                    <i class="fas fa-graduation-cap fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Masse salariale</h6>
                                        <h3 class="mb-0">
                                            <?php
                                            $query = "SELECT SUM(salary) as total FROM teachers 
                                                     WHERE school_id = :school_id AND status = 'active'";
                                            $stmt = $db->prepare($query);
                                            $stmt->bindParam(':school_id', $_SESSION['school_id']);
                                            $stmt->execute();
                                            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                                            echo number_format($total, 0, ',', ' ') . ' XAF';
                                            ?>
                                        </h3>
                                    </div>
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Spécialisation</label>
                            <select class="form-select" name="specialization_filter">
                                <option value="">Toutes les spécialisations</option>
                                <option value="Mathématiques">Mathématiques</option>
                                <option value="Français">Français</option>
                                <option value="Sciences">Sciences</option>
                                <option value="Histoire-Géographie">Histoire-Géographie</option>
                                <option value="Anglais">Anglais</option>
                                <option value="Sport">Sport</option>
                                <option value="Arts">Arts</option>
                                <option value="Musique">Musique</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" name="status_filter">
                                <option value="">Tous les statuts</option>
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Recherche</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="Nom, spécialisation ou téléphone...">
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

            <!-- Table des enseignants -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Liste des enseignants</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-success" id="exportTeachers">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                            <button class="btn btn-sm btn-outline-danger" id="exportPDF">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Nom complet</th>
                                    <th>Spécialisation</th>
                                    <th>Téléphone</th>
                                    <th>Email</th>
                                    <th>Diplôme</th>
                                    <th>Salaire (XAF)</th>
                                    <th>Date d'embauche</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Récupérer les enseignants
                                $query = "SELECT * FROM teachers 
                                         WHERE school_id = :school_id 
                                         ORDER BY created_at DESC";
                                
                                $stmt = $db->prepare($query);
                                $stmt->bindParam(':school_id', $_SESSION['school_id']);
                                $stmt->execute();
                                
                                if ($stmt->rowCount() > 0) {
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $status_badge = $row['status'] == 'active' 
                                            ? '<span class="badge bg-success">Actif</span>' 
                                            : '<span class="badge bg-secondary">Inactif</span>';
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['teacher_code']); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title bg-light rounded-circle text-primary">
                                                    <?php echo strtoupper(substr($row['first_name'], 0, 1) . substr($row['last_name'], 0, 1)); ?>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></h6>
                                                <small class="text-muted"><?php echo $row['gender'] == 'M' ? 'Homme' : 'Femme'; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['specialization'] ?? 'Non spécifié'); ?></td>
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
                                        <?php if(!empty($row['email'])): ?>
                                            <a href="mailto:<?php echo $row['email']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($row['email']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['diploma'] ?? 'N/A'); ?></td>
                                    <td class="format-money"><?php echo $row['salary']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['hire_date'])); ?></td>
                                    <td><?php echo $status_badge; ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary btn-view-teacher" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-warning btn-edit-teacher" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-firstname="<?php echo htmlspecialchars($row['first_name']); ?>"
                                                    data-lastname="<?php echo htmlspecialchars($row['last_name']); ?>"
                                                    data-gender="<?php echo $row['gender']; ?>"
                                                    data-specialization="<?php echo htmlspecialchars($row['specialization']); ?>"
                                                    data-diploma="<?php echo htmlspecialchars($row['diploma']); ?>"
                                                    data-phone="<?php echo htmlspecialchars($row['phone']); ?>"
                                                    data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                                    data-address="<?php echo htmlspecialchars($row['address']); ?>"
                                                    data-hire-date="<?php echo $row['hire_date']; ?>"
                                                    data-salary="<?php echo $row['salary']; ?>"
                                                    data-status="<?php echo $row['status']; ?>"
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-delete-teacher" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo '<tr><td colspan="10" class="text-center py-4">
                                            <i class="fas fa-chalkboard-teacher fa-2x text-muted mb-3"></i>
                                            <h5>Aucun enseignant trouvé</h5>
                                            <p class="text-muted">Commencez par ajouter votre premier enseignant</p>
                                          </td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
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

<!-- Modal pour ajouter un enseignant -->
<div class="modal fade" id="addTeacherModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-user-plus"></i> Nouvel enseignant
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addTeacherForm" action="controllers/add_teacher.php" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" class="form-control" name="last_name" required
                                   placeholder="Ex: KABEYA">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom *</label>
                            <input type="text" class="form-control" name="first_name" required
                                   placeholder="Ex: Jean">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Genre *</label>
                            <select class="form-select" name="gender" required>
                                <option value="">Sélectionner...</option>
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Spécialisation *</label>
                            <select class="form-select" name="specialization" required>
                                <option value="">Sélectionner...</option>
                                <option value="Mathématiques">Mathématiques</option>
                                <option value="Français">Français</option>
                                <option value="Sciences">Sciences</option>
                                <option value="Histoire-Géographie">Histoire-Géographie</option>
                                <option value="Anglais">Anglais</option>
                                <option value="Sport">Sport</option>
                                <option value="Arts">Arts</option>
                                <option value="Musique">Musique</option>
                                <option value="Informatique">Informatique</option>
                                <option value="Philosophie">Philosophie</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Diplôme</label>
                            <input type="text" class="form-control" name="diploma"
                                   placeholder="Ex: Licence, Master, Doctorat">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone *</label>
                            <input type="tel" class="form-control" name="phone" required
                                   placeholder="+242 XX XX XX XX">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                   placeholder="exemple@ecole.cg">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date d'embauche</label>
                            <input type="date" class="form-control" name="hire_date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Salaire mensuel (XAF)</label>
                            <input type="number" class="form-control" name="salary" min="0" step="5000" value="250000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Statut *</label>
                            <select class="form-select" name="status" required>
                                <option value="active" selected>Actif</option>
                                <option value="inactive">Inactif</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Adresse</label>
                            <textarea class="form-control" name="address" rows="2" placeholder="Adresse complète"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour importer des enseignants -->
<div class="modal fade" id="importTeachersModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-file-import"></i> Importer des enseignants
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="importTeachersForm" action="controllers/import_teachers.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Téléchargez le modèle Excel, remplissez-le et importez-le ici.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Télécharger le modèle</label>
                        <a href="templates/teachers_template.xlsx" class="btn btn-outline-primary btn-sm" download>
                            <i class="fas fa-download"></i> Modèle Excel
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Fichier Excel à importer *</label>
                        <input type="file" class="form-control" name="teachers_file" accept=".xlsx,.xls" required>
                        <small class="text-muted">Formats acceptés: .xlsx, .xls</small>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="create_user_accounts" id="createUserAccounts" checked>
                        <label class="form-check-label" for="createUserAccounts">
                            Créer automatiquement des comptes utilisateurs
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

<!-- Modal pour voir les détails -->
<div class="modal fade" id="viewTeacherModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-user"></i> Détails de l'enseignant
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="teacherDetailsContent">
                <!-- Chargé dynamiquement -->
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

<!-- Modal pour modifier un enseignant -->
<div class="modal fade" id="editTeacherModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-edit"></i> Modifier l'enseignant
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTeacherForm" action="controllers/update_teacher.php" method="POST">
                <input type="hidden" name="teacher_id" id="edit_teacher_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" class="form-control" name="last_name" id="edit_last_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom *</label>
                            <input type="text" class="form-control" name="first_name" id="edit_first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Genre *</label>
                            <select class="form-select" name="gender" id="edit_gender" required>
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Spécialisation *</label>
                            <select class="form-select" name="specialization" id="edit_specialization" required>
                                <option value="Mathématiques">Mathématiques</option>
                                <option value="Français">Français</option>
                                <option value="Sciences">Sciences</option>
                                <option value="Histoire-Géographie">Histoire-Géographie</option>
                                <option value="Anglais">Anglais</option>
                                <option value="Sport">Sport</option>
                                <option value="Arts">Arts</option>
                                <option value="Musique">Musique</option>
                                <option value="Informatique">Informatique</option>
                                <option value="Philosophie">Philosophie</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Diplôme</label>
                            <input type="text" class="form-control" name="diploma" id="edit_diploma">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone *</label>
                            <input type="tel" class="form-control" name="phone" id="edit_phone" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="edit_email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date d'embauche</label>
                            <input type="date" class="form-control" name="hire_date" id="edit_hire_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Salaire mensuel (XAF)</label>
                            <input type="number" class="form-control" name="salary" id="edit_salary" min="0" step="5000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Statut *</label>
                            <select class="form-select" name="status" id="edit_status" required>
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Adresse</label>
                            <textarea class="form-control" name="address" id="edit_address" rows="2"></textarea>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des filtres
    document.getElementById('applyFilter').addEventListener('click', function() {
        // Implémenter la logique de filtrage
        alert('Fonction de filtrage à implémenter');
    });
    
    // Exporter en Excel
    document.getElementById('exportTeachers').addEventListener('click', function() {
        window.location.href = 'controllers/export_teachers.php?format=excel';
    });
    
    // Exporter en PDF
    document.getElementById('exportPDF').addEventListener('click', function() {
        window.location.href = 'controllers/export_teachers.php?format=pdf';
    });
    
    // Édition d'un enseignant
    document.querySelectorAll('.btn-edit-teacher').forEach(button => {
        button.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('editTeacherModal'));
            
            // Remplir les champs
            document.getElementById('edit_teacher_id').value = this.dataset.id;
            document.getElementById('edit_last_name').value = this.dataset.lastname;
            document.getElementById('edit_first_name').value = this.dataset.firstname;
            document.getElementById('edit_gender').value = this.dataset.gender;
            document.getElementById('edit_specialization').value = this.dataset.specialization;
            document.getElementById('edit_diploma').value = this.dataset.diploma;
            document.getElementById('edit_phone').value = this.dataset.phone;
            document.getElementById('edit_email').value = this.dataset.email;
            document.getElementById('edit_address').value = this.dataset.address;
            document.getElementById('edit_hire_date').value = this.dataset.hireDate;
            document.getElementById('edit_salary').value = this.dataset.salary;
            document.getElementById('edit_status').value = this.dataset.status;
            
            modal.show();
        });
    });
    
    // Suppression d'un enseignant
    document.querySelectorAll('.btn-delete-teacher').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm(`Êtes-vous sûr de vouloir supprimer l'enseignant "${this.dataset.name}" ?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'controllers/delete_teacher.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'teacher_id';
                input.value = this.dataset.id;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
    
    // Voir les détails d'un enseignant
    document.querySelectorAll('.btn-view-teacher').forEach(button => {
        button.addEventListener('click', function() {
            const teacherId = this.dataset.id;
            const modal = new bootstrap.Modal(document.getElementById('viewTeacherModal'));
            
            // Simuler le chargement des données
            const detailsContent = document.getElementById('teacherDetailsContent');
            detailsContent.innerHTML = `
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            `;
            
            // Dans une version réelle, vous feriez un appel AJAX
            setTimeout(() => {
                detailsContent.innerHTML = `
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="avatar-lg mx-auto mb-3">
                                <div class="avatar-title bg-primary rounded-circle text-white" style="width: 100px; height: 100px; font-size: 2.5rem;">
                                    ${this.closest('tr').querySelector('.avatar-title').textContent}
                                </div>
                            </div>
                            <h4>${this.dataset.firstname} ${this.dataset.lastname}</h4>
                            <p class="text-muted">${this.dataset.specialization}</p>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Code enseignant</th>
                                    <td>${this.closest('tr').querySelector('td:first-child').textContent}</td>
                                </tr>
                                <tr>
                                    <th>Genre</th>
                                    <td>${this.dataset.gender === 'M' ? 'Masculin' : 'Féminin'}</td>
                                </tr>
                                <tr>
                                    <th>Diplôme</th>
                                    <td>${this.dataset.diploma || 'Non spécifié'}</td>
                                </tr>
                                <tr>
                                    <th>Téléphone</th>
                                    <td>${this.dataset.phone || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>${this.dataset.email || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <th>Date d'embauche</th>
                                    <td>${new Date(this.dataset.hireDate).toLocaleDateString('fr-FR')}</td>
                                </tr>
                                <tr>
                                    <th>Salaire</th>
                                    <td class="format-money">${this.dataset.salary} XAF</td>
                                </tr>
                                <tr>
                                    <th>Adresse</th>
                                    <td>${this.dataset.address || 'Non spécifiée'}</td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td><span class="badge ${this.dataset.status === 'active' ? 'bg-success' : 'bg-secondary'}">
                                        ${this.dataset.status === 'active' ? 'Actif' : 'Inactif'}
                                    </span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h5>Classes assignées</h5>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Cet enseignant n'est pas encore assigné à des classes.
                        </div>
                    </div>
                `;
                
                // Formater l'argent
                document.querySelectorAll('.format-money').forEach(el => {
                    const value = el.textContent.trim().replace(' XAF', '');
                    if (!isNaN(value) && value !== '') {
                        el.textContent = parseInt(value).toLocaleString('fr-FR') + ' XAF';
                    }
                });
            }, 500);
            
            modal.show();
        });
    });
    
    // Édition depuis la vue détaillée
    document.getElementById('editFromView').addEventListener('click', function() {
        const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewTeacherModal'));
        viewModal.hide();
        
        // Simuler l'ouverture du modal d'édition
        setTimeout(() => {
            const editButton = document.querySelector('.btn-edit-teacher[data-id]');
            if (editButton) {
                editButton.click();
            }
        }, 300);
    });
    
    // Formater les montants en XAF
    document.querySelectorAll('.format-money').forEach(element => {
        const value = element.textContent.trim();
        if (!isNaN(value) && value !== '') {
            element.textContent = parseInt(value).toLocaleString('fr-FR') + ' XAF';
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>