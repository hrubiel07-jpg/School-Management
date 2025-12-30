<?php
// students.php - Gestion complète des élèves
require_once 'includes/header.php';
checkRole(['super_admin', 'admin', 'teacher']);

$page_title = "Gestion des Élèves";
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Header -->
            <div class="page-header mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-users"></i> Gestion des Élèves</h1>
                    <div class="btn-group">
                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                            <i class="fas fa-user-plus"></i> Nouvel élève
                        </button>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importStudentsModal">
                            <i class="fas fa-file-import"></i> Importer
                        </button>
                        <button class="btn btn-outline-success" onclick="window.print()">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                    </div>
                </div>
                <p>Gérez les inscriptions et informations des élèves</p>
            </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form class="row g-3" id="filterForm">
                        <div class="col-md-3">
                            <label class="form-label">Classe</label>
                            <select class="form-select" name="class_filter">
                                <option value="">Toutes les classes</option>
                                <?php
                                require_once 'config/database.php';
                                $database = new Database();
                                $db = $database->getConnection();
                                
                                $query = "SELECT * FROM classes WHERE school_id = :school_id ORDER BY class_name";
                                $stmt = $db->prepare($query);
                                $stmt->bindParam(':school_id', $_SESSION['school_id']);
                                $stmt->execute();
                                
                                while ($class = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . $class['id'] . '">' . 
                                         htmlspecialchars($class['class_name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Niveau</label>
                            <select class="form-select" name="level_filter">
                                <option value="">Tous les niveaux</option>
                                <option value="Maternelle">Maternelle</option>
                                <option value="Primaire">Primaire</option>
                                <option value="Secondaire">Secondaire</option>
                                <option value="Lycée">Lycée</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" name="status_filter">
                                <option value="">Tous les statuts</option>
                                <option value="active">Actif</option>
                                <option value="graduated">Diplômé</option>
                                <option value="transferred">Transféré</option>
                                <option value="inactive">Inactif</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" id="applyFilter">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tableau des élèves -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Liste des élèves</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-success" id="exportStudents">
                                <i class="fas fa-file-excel"></i> Exporter
                            </button>
                            <button class="btn btn-sm btn-outline-info" id="refreshStudents">
                                <i class="fas fa-sync-alt"></i> Actualiser
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="studentsTable">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Photo</th>
                                    <th>Nom complet</th>
                                    <th>Classe</th>
                                    <th>Genre</th>
                                    <th>Téléphone parent</th>
                                    <th>Date inscription</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Récupérer les élèves
                                $query = "SELECT s.*, c.class_name 
                                         FROM students s 
                                         LEFT JOIN classes c ON s.class_id = c.id 
                                         WHERE s.school_id = :school_id 
                                         ORDER BY s.created_at DESC 
                                         LIMIT 50";
                                
                                $stmt = $db->prepare($query);
                                $stmt->bindParam(':school_id', $_SESSION['school_id']);
                                $stmt->execute();
                                
                                if ($stmt->rowCount() > 0) {
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $status_badge = match($row['status']) {
                                            'active' => '<span class="badge bg-success">Actif</span>',
                                            'graduated' => '<span class="badge bg-primary">Diplômé</span>',
                                            'transferred' => '<span class="badge bg-info">Transféré</span>',
                                            'inactive' => '<span class="badge bg-secondary">Inactif</span>',
                                            default => '<span class="badge bg-secondary">' . $row['status'] . '</span>'
                                        };
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['student_code']); ?></td>
                                    <td>
                                        <img src="<?php echo !empty($row['photo']) ? $row['photo'] : 'https://via.placeholder.com/40'; ?>" 
                                             alt="Photo" class="rounded-circle" width="40" height="40">
                                    </td>
                                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['class_name'] ?? 'Non assigné'); ?></td>
                                    <td><?php echo $row['gender'] == 'M' ? 'M' : 'F'; ?></td>
                                    <td><?php echo htmlspecialchars($row['parent_phone'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['enrollment_date'])); ?></td>
                                    <td><?php echo $status_badge; ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary btn-view-student" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-warning btn-edit-student" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    title="Éditer">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-delete-student" 
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
                                    echo '<tr><td colspan="9" class="text-center py-4">
                                            <i class="fas fa-users-slash fa-2x text-muted mb-3"></i>
                                            <h5>Aucun élève trouvé</h5>
                                            <p class="text-muted">Commencez par ajouter votre premier élève</p>
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

<!-- Modal pour ajouter un élève -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-user-plus"></i> Nouvel élève
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addStudentForm" action="controllers/add_student.php" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom *</label>
                            <input type="text" class="form-control" name="first_name" required>
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
                            <label class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" name="birth_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lieu de naissance</label>
                            <input type="text" class="form-control" name="birth_place">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Classe *</label>
                            <select class="form-select" name="class_id" required>
                                <option value="">Sélectionner la classe</option>
                                <?php
                                $query = "SELECT * FROM classes WHERE school_id = :school_id ORDER BY class_name";
                                $stmt = $db->prepare($query);
                                $stmt->bindParam(':school_id', $_SESSION['school_id']);
                                $stmt->execute();
                                
                                while ($class = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . $class['id'] . '">' . 
                                         htmlspecialchars($class['class_name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom du père</label>
                            <input type="text" class="form-control" name="father_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom de la mère</label>
                            <input type="text" class="form-control" name="mother_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone parent *</label>
                            <input type="tel" class="form-control" name="parent_phone" placeholder="+242" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email parent</label>
                            <input type="email" class="form-control" name="parent_email">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Adresse</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date d'inscription</label>
                            <input type="date" class="form-control" name="enrollment_date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" name="status">
                                <option value="active" selected>Actif</option>
                                <option value="inactive">Inactif</option>
                            </select>
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

<!-- Modal pour importer des élèves -->
<div class="modal fade" id="importStudentsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-file-import"></i> Importer des élèves
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="importStudentsForm" action="controllers/import_students.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Téléchargez le modèle Excel, remplissez-le et importez-le ici.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Télécharger le modèle</label>
                        <a href="templates/students_template.xlsx" class="btn btn-outline-primary btn-sm" download>
                            <i class="fas fa-download"></i> Modèle Excel
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Fichier Excel à importer *</label>
                        <input type="file" class="form-control" name="students_file" accept=".xlsx,.xls" required>
                        <small class="text-muted">Formats acceptés: .xlsx, .xls</small>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="send_welcome_sms" id="sendWelcomeSMS">
                        <label class="form-check-label" for="sendWelcomeSMS">
                            Envoyer un SMS de bienvenue aux parents
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des filtres
    document.getElementById('applyFilter').addEventListener('click', function() {
        const classFilter = document.querySelector('select[name="class_filter"]').value;
        const levelFilter = document.querySelector('select[name="level_filter"]').value;
        const statusFilter = document.querySelector('select[name="status_filter"]').value;
        
        // Filtrer les élèves (simulation)
        const rows = document.querySelectorAll('#studentsTable tbody tr');
        rows.forEach(row => {
            let showRow = true;
            
            // Ici, vous implémenteriez la logique de filtrage réelle
            // Pour l'instant, c'est une simulation
            
            row.style.display = showRow ? '' : 'none';
        });
    });
    
    // Export Excel
    document.getElementById('exportStudents').addEventListener('click', function() {
        window.location.href = 'controllers/export_students.php?format=excel';
    });
    
    // Actualiser
    document.getElementById('refreshStudents').addEventListener('click', function() {
        location.reload();
    });
    
    // Édition d'un élève
    document.querySelectorAll('.btn-edit-student').forEach(button => {
        button.addEventListener('click', function() {
            // Charger les données de l'élève et ouvrir le modal d'édition
            const studentId = this.dataset.id;
            // Implémenter la logique d'édition
            alert('Édition de l\'élève ID: ' + studentId);
        });
    });
    
    // Suppression d'un élève
    document.querySelectorAll('.btn-delete-student').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm(`Êtes-vous sûr de vouloir supprimer l'élève "${this.dataset.name}" ?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'controllers/delete_student.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'student_id';
                input.value = this.dataset.id;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
    
    // Voir les détails d'un élève
    document.querySelectorAll('.btn-view-student').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.dataset.id;
            window.location.href = 'student_details.php?id=' + studentId;
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>