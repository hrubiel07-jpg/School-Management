<?php
require_once 'includes/header.php';
checkRole(['super_admin', 'admin', 'teacher']);

$page_title = "Gestion des Notes";
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="page-header mt-3">
                <h1><i class="fas fa-chart-bar"></i> Gestion des Notes</h1>
                <p>Saisie et consultation des résultats scolaires</p>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>Trimestre actuel</h6>
                            <h1 class="display-4 text-primary">1</h1>
                            <p>Année scolaire: 2023-2024</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Classe</label>
                                    <select class="form-select">
                                        <option value="">Toutes les classes</option>
                                        <!-- Options classes -->
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Matière</label>
                                    <select class="form-select">
                                        <option value="">Toutes les matières</option>
                                        <!-- Options matières -->
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Trimestre</label>
                                    <select class="form-select">
                                        <option value="1">Trimestre 1</option>
                                        <option value="2">Trimestre 2</option>
                                        <option value="3">Trimestre 3</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table"></i> Saisie des notes</h5>
                </div>
                <div class="card-body">
                    <form>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Élève</th>
                                    <th>Note (/20)</th>
                                    <th>Coefficient</th>
                                    <th>Appréciation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>KABEYA Jean</td>
                                    <td><input type="number" class="form-control" min="0" max="20" step="0.25"></td>
                                    <td><input type="number" class="form-control" value="1" min="1"></td>
                                    <td><input type="text" class="form-control" placeholder="Très bien"></td>
                                </tr>
                                <!-- Plus d'élèves -->
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer toutes les notes
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>