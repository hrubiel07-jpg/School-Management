<?php
require_once 'includes/header.php';
checkRole(['super_admin', 'admin', 'accountant']);

$page_title = "Gestion des Paiements";
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Header -->
            <div class="page-header mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-money-bill-wave"></i> Gestion des Paiements</h1>
                    <div>
                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                            <i class="fas fa-plus-circle"></i> Nouveau paiement
                        </button>
                        <button class="btn btn-success" onclick="window.print()">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                    </div>
                </div>
                <p>Gérez les paiements scolaires en Francs CFA (XAF)</p>
            </div>

            <!-- Résumé financier -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6>Payés</h6>
                            <h3>25,450,000 XAF</h3>
                            <small>Ce mois-ci</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <h6>En attente</h6>
                            <h3>8,750,000 XAF</h3>
                            <small>À recouvrer</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h6>Retard</h6>
                            <h3>3,250,000 XAF</h3>
                            <small>Plus de 30 jours</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6>Total annuel</h6>
                            <h3>185,500,000 XAF</h3>
                            <small>2023-2024</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des paiements -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Derniers paiements</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Facture</th>
                                    <th>Élève</th>
                                    <th>Type</th>
                                    <th>Montant (XAF)</th>
                                    <th>Payé (XAF)</th>
                                    <th>Reste (XAF)</th>
                                    <th>Date échéance</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>FAC-2023-001</td>
                                    <td>KABEYA Jean</td>
                                    <td>Mensualité</td>
                                    <td>150,000</td>
                                    <td>150,000</td>
                                    <td>0</td>
                                    <td>05/10/2023</td>
                                    <td><span class="badge bg-success">Payé</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" title="Reçu">
                                            <i class="fas fa-receipt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FAC-2023-002</td>
                                    <td>MBOUALA Marie</td>
                                    <td>Inscription</td>
                                    <td>250,000</td>
                                    <td>100,000</td>
                                    <td>150,000</td>
                                    <td>10/10/2023</td>
                                    <td><span class="badge bg-warning">Partiel</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success" title="Payer">
                                            <i class="fas fa-money-bill"></i>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Plus de lignes -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reçu de paiement (caché pour impression) -->
            <div class="card mt-4 no-print" id="receipt" style="display: none;">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3>REÇU DE PAIEMENT</h3>
                        <p>École: <?php echo $_SESSION['school_name']; ?></p>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Facture:</strong> FAC-2023-001</p>
                            <p><strong>Date:</strong> 05/10/2023</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p><strong>Élève:</strong> KABEYA Jean</p>
                            <p><strong>Classe:</strong> 7ème Primaire A</p>
                        </div>
                    </div>
                    
                    <table class="table table-bordered">
                        <tr>
                            <th>Description</th>
                            <th>Montant (XAF)</th>
                        </tr>
                        <tr>
                            <td>Mensualité Octobre 2023</td>
                            <td>150,000</td>
                        </tr>
                        <tr>
                            <td><strong>TOTAL</strong></td>
                            <td><strong>150,000 XAF</strong></td>
                        </tr>
                    </table>
                    
                    <div class="mt-4 text-center">
                        <p>Reçu électronique - Signature</p>
                        <hr>
                        <small>Merci pour votre confiance</small>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal pour nouveau paiement -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white">
                    <i class="fas fa-money-bill-wave"></i> Nouveau paiement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Élève *</label>
                            <select class="form-select" required>
                                <option value="">Sélectionner un élève</option>
                                <!-- Options élèves -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type de paiement *</label>
                            <select class="form-select" required>
                                <option value="mensualité">Mensualité</option>
                                <option value="inscription">Inscription</option>
                                <option value="transport">Transport</option>
                                <option value="uniforme">Uniforme</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Montant (XAF) *</label>
                            <input type="number" class="form-control" min="0" step="500" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date échéance</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Méthode de paiement</label>
                            <select class="form-select">
                                <option value="cash">Espèces</option>
                                <option value="bank_transfer">Virement bancaire</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="check">Chèque</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date paiement</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="3" placeholder="Informations supplémentaires..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer paiement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showReceipt() {
    document.getElementById('receipt').style.display = 'block';
    window.print();
    document.getElementById('receipt').style.display = 'none';
}
</script>

<?php require_once 'includes/footer.php'; ?>