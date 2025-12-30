<?php
require_once 'includes/header.php';
checkRole(['super_admin', 'admin', 'teacher']);

$page_title = "Emploi du temps";
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="page-header mt-3">
                <h1><i class="fas fa-calendar-alt"></i> Emploi du temps</h1>
                <p>Organisation des cours et activités</p>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Sélectionner une classe</label>
                            <select class="form-select" id="classSelect">
                                <option value="">-- Choisir une classe --</option>
                                <!-- Options classes -->
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-week"></i> Emploi du temps - Classe: 7ème Primaire A</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th>Heure</th>
                                    <th>Lundi</th>
                                    <th>Mardi</th>
                                    <th>Mercredi</th>
                                    <th>Jeudi</th>
                                    <th>Vendredi</th>
                                    <th>Samedi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>7:30 - 8:30</td>
                                    <td class="bg-primary text-white">Mathématiques</td>
                                    <td>Français</td>
                                    <td>Sciences</td>
                                    <td>Mathématiques</td>
                                    <td>Histoire-Géo</td>
                                    <td rowspan="2" class="bg-secondary text-white">
                                        <i class="fas fa-football-ball"></i><br>Sport
                                    </td>
                                </tr>
                                <!-- Plus de lignes -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>