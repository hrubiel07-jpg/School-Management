<?php
require_once 'includes/header.php';
checkRole(['super_admin', 'admin']);

$page_title = "Gestion des Classes";
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="page-header mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-school"></i> Gestion des Classes</h1>
                    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                        <i class="fas fa-plus"></i> Nouvelle classe
                    </button>
                </div>
                <p>Organisez les classes et niveaux de votre école</p>
            </div>

            <div class="row">
                <?php
                require_once 'config/database.php';
                $database = new Database();
                $db = $database->getConnection();
                
                $query = "SELECT c.*, COUNT(s.id) as student_count 
                         FROM classes c 
                         LEFT JOIN students s ON c.id = s.class_id AND s.status = 'active'
                         WHERE c.school_id = :school_id 
                         GROUP BY c.id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':school_id', $_SESSION['school_id']);
                $stmt->execute();
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $percentage = ($row['student_count'] / $row['max_students']) * 100;
                    $progress_color = $percentage >= 90 ? 'bg-danger' : ($percentage >= 75 ? 'bg-warning' : 'bg-success');
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header" style="background-color: <?php 
                            echo $row['level'] == 'Maternelle' ? '#FFD700' : 
                                 ($row['level'] == 'Primaire' ? '#009543' : 
                                 ($row['level'] == 'Secondaire' ? '#DC241F' : '#6F42C1')); 
                        ?>; color: white;">
                            <h5 class="mb-0"><?php echo $row['class_name']; ?></h5>
                            <small><?php echo $row['level']; ?> - Section <?php echo $row['section']; ?></small>
                        </div>
                        <div class="card-body">
                            <p><i class="fas fa-users"></i> Élèves: <?php echo $row['student_count']; ?>/<?php echo $row['max_students']; ?></p>
                            <div class="progress mb-3">
                                <div class="progress-bar <?php echo $progress_color; ?>" 
                                     style="width: <?php echo $percentage; ?>%">
                                    <?php echo round($percentage); ?>%
                                </div>
                            </div>
                            <p><i class="fas fa-money-bill-wave"></i> Frais annuels: 
                               <strong><?php echo number_format($row['annual_fee'], 0, ',', ' '); ?> XAF</strong></p>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Voir
                                </button>
                                <button class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </main>
    </div>
</div>

<!-- Modal pour nouvelle classe -->
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-plus"></i> Nouvelle classe
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom de la classe *</label>
                        <input type="text" class="form-control" placeholder="Ex: CP1, 6ème A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Niveau *</label>
                        <select class="form-select" required>
                            <option value="">Sélectionner...</option>
                            <option value="Maternelle">Maternelle</option>
                            <option value="Primaire">Primaire</option>
                            <option value="Secondaire">Secondaire</option>
                            <option value="Lycée">Lycée</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Section</label>
                        <input type="text" class="form-control" placeholder="Ex: A, B, Scientifique">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre maximum d'élèves</label>
                            <input type="number" class="form-control" value="40" min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Frais annuels (XAF)</label>
                            <input type="number" class="form-control" value="250000" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer la classe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>