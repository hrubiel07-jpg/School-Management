<?php
// payments.php - GESTION COMPLÈTE DES PAIEMENTS EN XAF
require_once 'includes/header.php';

// Vérifier les permissions
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_admin', 'admin', 'accountant'])) {
    header('Location: unauthorized.php');
    exit();
}

$page_title = "Gestion des Paiements";

// Connexion à la base
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
$school_id = $_SESSION['school_id'] ?? 1;

// Récupérer les statistiques
function getPaymentStats($db, $school_id) {
    $stats = [
        'total_received' => 0,
        'total_pending' => 0,
        'total_overdue' => 0,
        'total_transactions' => 0,
        'monthly_data' => []
    ];
    
    try {
        // Total perçu ce mois
        $query = "SELECT SUM(amount_paid) as total FROM payments 
                  WHERE school_id = :school_id 
                  AND MONTH(payment_date) = MONTH(CURRENT_DATE())
                  AND YEAR(payment_date) = YEAR(CURRENT_DATE())
                  AND status IN ('paid', 'partial')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':school_id', $school_id);
        $stmt->execute();
        $stats['total_received'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Total en attente
        $query = "SELECT SUM(amount - amount_paid) as total FROM payments 
                  WHERE school_id = :school_id 
                  AND status IN ('pending', 'partial')
                  AND (due_date IS NULL OR due_date >= CURDATE())";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':school_id', $school_id);
        $stmt->execute();
        $stats['total_pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Total en retard
        $query = "SELECT SUM(amount - amount_paid) as total FROM payments 
                  WHERE school_id = :school_id 
                  AND status IN ('pending', 'partial')
                  AND due_date < CURDATE()";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':school_id', $school_id);
        $stmt->execute();
        $stats['total_overdue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Nombre de transactions ce mois
        $query = "SELECT COUNT(*) as total FROM payments 
                  WHERE school_id = :school_id 
                  AND MONTH(payment_date) = MONTH(CURRENT_DATE())
                  AND YEAR(payment_date) = YEAR(CURRENT_DATE())";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':school_id', $school_id);
        $stmt->execute();
        $stats['total_transactions'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Données mensuelles pour le graphique
        $query = "SELECT 
                    DATE_FORMAT(payment_date, '%Y-%m') as month,
                    SUM(amount_paid) as total_paid,
                    COUNT(*) as transaction_count
                  FROM payments 
                  WHERE school_id = :school_id 
                  AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                  GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
                  ORDER BY month";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':school_id', $school_id);
        $stmt->execute();
        $stats['monthly_data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erreur stats paiements: " . $e->getMessage());
    }
    
    return $stats;
}

$stats = getPaymentStats($db, $school_id);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - YOURHOPE School Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        .payment-card {
            border-left: 4px solid;
            transition: transform 0.3s;
        }
        
        .payment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .status-badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            border-radius: 50rem;
        }
        
        .status-paid { background-color: #d1e7dd; color: #0f5132; border-left-color: #198754; }
        .status-pending { background-color: #fff3cd; color: #664d03; border-left-color: #ffc107; }
        .status-partial { background-color: #cff4fc; color: #055160; border-left-color: #0dcaf0; }
        .status-overdue { background-color: #f8d7da; color: #842029; border-left-color: #dc3545; }
        
        .amount-cell {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        
        .xaf-symbol {
            color: var(--accent-color);
            font-weight: bold;
        }
        
        .filter-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .mobile-money-badge {
            background: linear-gradient(135deg, #FFD700, #FF9900);
            color: #333;
        }
        
        .bank-transfer-badge {
            background: linear-gradient(135deg, #009543, #006B36);
            color: white;
        }
        
        .cash-badge {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(90deg, var(--primary-color), var(--accent-color));">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-money-bill-wave me-2"></i>
                Gestion des Paiements
            </a>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include_once 'includes/sidebar.php'; ?>

            <!-- Contenu principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- En-tête -->
                <div class="page-header mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h2"><i class="fas fa-money-bill-wave"></i> Gestion des Paiements</h1>
                            <p class="lead">Suivez les paiements scolaires en <strong class="xaf-symbol">Franc CFA (XAF)</strong></p>
                        </div>
                        <div>
                            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                                <i class="fas fa-plus-circle"></i> Nouveau paiement
                            </button>
                            <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#batchPaymentModal">
                                <i class="fas fa-file-import"></i> Paiements groupés
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Alertes -->
                <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Cartes de statistiques -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card payment-card h-100 status-paid">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Total perçu</h5>
                                        <h2 class="mb-0">
                                            <?php echo number_format($stats['total_received'], 0, ',', ' '); ?> 
                                            <small class="xaf-symbol">XAF</small>
                                        </h2>
                                        <p class="text-muted">Ce mois-ci</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-wallet fa-3x" style="color: var(--primary-color)"></i>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="text-success">
                                        <i class="fas fa-arrow-up"></i> 12.5%
                                    </span>
                                    <span class="text-muted">vs mois dernier</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card payment-card h-100 status-pending">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">En attente</h5>
                                        <h2 class="mb-0">
                                            <?php echo number_format($stats['total_pending'], 0, ',', ' '); ?> 
                                            <small class="xaf-symbol">XAF</small>
                                        </h2>
                                        <p class="text-muted">À percevoir</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-clock fa-3x" style="color: #ffc107"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card payment-card h-100 status-partial">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Transactions</h5>
                                        <h2 class="mb-0"><?php echo $stats['total_transactions']; ?></h2>
                                        <p class="text-muted">Ce mois-ci</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-exchange-alt fa-3x" style="color: #0dcaf0"></i>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="text-success">
                                        <i class="fas fa-arrow-up"></i> 8.3%
                                    </span>
                                    <span class="text-muted">vs mois dernier</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card payment-card h-100 status-overdue">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Retards</h5>
                                        <h2 class="mb-0">
                                            <?php echo number_format($stats['total_overdue'], 0, ',', ' '); ?> 
                                            <small class="xaf-symbol">XAF</small>
                                        </h2>
                                        <p class="text-muted">En souffrance</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-exclamation-triangle fa-3x" style="color: #dc3545"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graphique et filtres -->
                <div class="row mb-4">
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Évolution des paiements (6 derniers mois)</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="paymentChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 mb-4">
                        <div class="card filter-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-filter"></i> Filtres avancés</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Statut</label>
                                    <select class="form-select" id="filterStatus">
                                        <option value="">Tous les statuts</option>
                                        <option value="paid">Payé</option>
                                        <option value="pending">En attente</option>
                                        <option value="partial">Partiel</option>
                                        <option value="overdue">En retard</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Type de paiement</label>
                                    <select class="form-select" id="filterType">
                                        <option value="">Tous les types</option>
                                        <option value="tuition">Scolarité</option>
                                        <option value="registration">Inscription</option>
                                        <option value="uniform">Uniforme</option>
                                        <option value="transport">Transport</option>
                                        <option value="exam">Frais d'examen</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Méthode de paiement</label>
                                    <select class="form-select" id="filterMethod">
                                        <option value="">Toutes méthodes</option>
                                        <option value="cash">Espèces</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="bank_transfer">Virement bancaire</option>
                                        <option value="check">Chèque</option>
                                    </select>
                                </div>
                                
                                <div class="row g-2 mb-3">
                                    <div class="col">
                                        <label class="form-label">Du</label>
                                        <input type="date" class="form-control" id="filterStartDate">
                                    </div>
                                    <div class="col">
                                        <label class="form-label">Au</label>
                                        <input type="date" class="form-control" id="filterEndDate">
                                    </div>
                                </div>
                                
                                <button class="btn btn-primary w-100" onclick="loadPayments()">
                                    <i class="fas fa-search"></i> Appliquer les filtres
                                </button>
                                <button class="btn btn-outline-secondary w-100 mt-2" onclick="resetFilters()">
                                    <i class="fas fa-redo"></i> Réinitialiser
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau des paiements -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-list"></i> Liste des paiements</h5>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-2" onclick="exportToExcel()">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="exportToPDF()">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="paymentsTable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Élève</th>
                                        <th>Type</th>
                                        <th>Montant</th>
                                        <th>Payé</th>
                                        <th>Solde</th>
                                        <th>Date</th>
                                        <th>Méthode</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Les données sont chargées via AJAX -->
                                </tbody>
                            </table>
                        </div>
                        <div id="pagination" class="mt-3">
                            <!-- La pagination est générée dynamiquement -->
                        </div>
                    </div>
                </div>

                <!-- Cartes de synthèse -->
                <div class="row mt-4">
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Répartition par type</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="typeChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Méthodes de paiement</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="methodChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-bell"></i> Alertes de paiement</h6>
                            </div>
                            <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                                <div id="paymentAlerts">
                                    <!-- Alertes chargées dynamiquement -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Nouveau Paiement -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(90deg, var(--primary-color), var(--accent-color)); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-money-bill-wave"></i> Nouveau paiement
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="paymentForm" action="controllers/add_payment.php" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Élève *</label>
                                <select class="form-select" name="student_id" id="studentSelect" required>
                                    <option value="">Sélectionner un élève</option>
                                    <!-- Rempli dynamiquement -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Classe</label>
                                <input type="text" class="form-control" id="studentClass" readonly>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type de paiement *</label>
                                <select class="form-select" name="payment_type" id="paymentType" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="tuition">Scolarité</option>
                                    <option value="registration">Frais d'inscription</option>
                                    <option value="uniform">Uniforme scolaire</option>
                                    <option value="transport">Transport</option>
                                    <option value="exam">Frais d'examen</option>
                                    <option value="library">Bibliothèque</option>
                                    <option value="other">Autre</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date d'échéance</label>
                                <input type="date" class="form-control" name="due_date" id="dueDate">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Montant total (XAF) *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="amount" 
                                           id="amount" placeholder="0" min="0" required step="100">
                                    <span class="input-group-text">XAF</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Montant payé (XAF) *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="amount_paid" 
                                           id="amountPaid" placeholder="0" min="0" required step="100">
                                    <span class="input-group-text">XAF</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Solde restant</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light" readonly 
                                           id="remainingBalance">
                                    <span class="input-group-text">XAF</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Méthode de paiement *</label>
                                <select class="form-select" name="payment_method" id="paymentMethod" required>
                                    <option value="cash">Espèces</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="bank_transfer">Virement bancaire</option>
                                    <option value="check">Chèque</option>
                                    <option value="card">Carte bancaire</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Numéro de transaction</label>
                                <input type="text" class="form-control" name="transaction_id" 
                                       id="transactionId" placeholder="Ex: MTN-123456789">
                                <small class="text-muted" id="transactionHelp">Pour Mobile Money, banque, etc.</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de paiement *</label>
                                <input type="date" class="form-control" name="payment_date" 
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Appliquer une remise</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="discount_amount" 
                                           id="discountAmount" placeholder="0" min="0" step="100">
                                    <button class="btn btn-outline-secondary" type="button" onclick="applyDiscount()">
                                        Appliquer
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="description" 
                                   placeholder="Ex: Scolarité trimestre 1">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="2" 
                                      placeholder="Informations supplémentaires..."></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note :</strong> Le système générera automatiquement un code de paiement unique.
                            Le statut sera déterminé en fonction du montant payé.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer le paiement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Paiements groupés -->
    <div class="modal fade" id="batchPaymentModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(90deg, #6f42c1, #6610f2); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-file-import"></i> Paiements groupés par classe
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenu pour les paiements groupés -->
                    <p>Fonctionnalité en développement...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Détails du paiement -->
    <div class="modal fade" id="paymentDetailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails du paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="paymentDetailContent">
                    <!-- Chargé dynamiquement -->
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Variables globales
        let paymentChart, typeChart, methodChart;
        let currentPage = 1;
        const itemsPerPage = 10;

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
            loadPaymentAlerts();
            loadStudentsForPayment();
            loadPayments();
            
            // Événements
            document.getElementById('amount').addEventListener('input', calculateBalance);
            document.getElementById('amountPaid').addEventListener('input', calculateBalance);
            document.getElementById('studentSelect').addEventListener('change', loadStudentInfo);
            document.getElementById('paymentMethod').addEventListener('change', updateTransactionHelp);
        });

        // Initialiser les graphiques
        function initCharts() {
            // Graphique principal
            const ctx = document.getElementById('paymentChart').getContext('2d');
            paymentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                    datasets: [{
                        label: 'Paiements (XAF)',
                        data: [1200000, 1500000, 1800000, 2100000, 2400000, 2700000],
                        borderColor: 'rgba(0, 149, 67, 1)',
                        backgroundColor: 'rgba(0, 149, 67, 0.1)',
                        borderWidth: 2,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('fr-FR').format(value) + ' XAF';
                                }
                            }
                        }
                    }
                }
            });

            // Graphique par type
            const typeCtx = document.getElementById('typeChart').getContext('2d');
            typeChart = new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Scolarité', 'Inscription', 'Uniforme', 'Transport', 'Examens'],
                    datasets: [{
                        data: [45, 25, 15, 10, 5],
                        backgroundColor: [
                            'rgba(0, 149, 67, 0.8)',
                            'rgba(255, 215, 0, 0.8)',
                            'rgba(220, 36, 31, 0.8)',
                            'rgba(108, 117, 125, 0.8)',
                            'rgba(13, 202, 240, 0.8)'
                        ]
                    }]
                }
            });

            // Graphique par méthode
            const methodCtx = document.getElementById('methodChart').getContext('2d');
            methodChart = new Chart(methodCtx, {
                type: 'bar',
                data: {
                    labels: ['Espèces', 'Mobile Money', 'Virement', 'Chèque'],
                    datasets: [{
                        label: 'Nombre de transactions',
                        data: [65, 25, 8, 2],
                        backgroundColor: [
                            'rgba(108, 117, 125, 0.8)',
                            'rgba(255, 215, 0, 0.8)',
                            'rgba(0, 149, 67, 0.8)',
                            'rgba(13, 202, 240, 0.8)'
                        ]
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 10
                            }
                        }
                    }
                }
            });
        }

        // Calculer le solde
        function calculateBalance() {
            const amount = parseFloat(document.getElementById('amount').value) || 0;
            const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
            const balance = amount - paid;
            
            document.getElementById('remainingBalance').value = 
                new Intl.NumberFormat('fr-FR').format(balance) + ' XAF';
        }

        // Charger les élèves pour le formulaire
        function loadStudentsForPayment() {
            fetch('controllers/get_active_students.php')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('studentSelect');
                    select.innerHTML = '<option value="">Sélectionner un élève</option>';
                    
                    data.students.forEach(student => {
                        const option = document.createElement('option');
                        option.value = student.id;
                        option.textContent = `${student.last_name} ${student.first_name} - ${student.student_code}`;
                        select.appendChild(option);
                    });
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Charger les informations de l'élève
        function loadStudentInfo(studentId) {
            if (!studentId) return;
            
            fetch(`controllers/get_student_info.php?student_id=${studentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('studentClass').value = data.class_name;
                        
                        // Charger automatiquement les frais de la classe
                        if (data.class_id) {
                            loadClassFees(data.class_id);
                        }
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Charger les frais de la classe
        function loadClassFees(classId) {
            fetch(`controllers/get_class_fees.php?class_id=${classId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.fees && data.fees.length > 0) {
                        const feeSelect = document.getElementById('paymentType');
                        const amountInput = document.getElementById('amount');
                        
                        // Réinitialiser
                        feeSelect.innerHTML = '<option value="">Sélectionner...</option>';
                        
                        data.fees.forEach(fee => {
                            const option = document.createElement('option');
                            option.value = fee.fee_type;
                            option.textContent = `${fee.fee_type} - ${new Intl.NumberFormat('fr-FR').format(fee.amount)} XAF`;
                            option.dataset.amount = fee.amount;
                            feeSelect.appendChild(option);
                        });
                        
                        // Ajouter option autre
                        const otherOption = document.createElement('option');
                        otherOption.value = 'other';
                        otherOption.textContent = 'Autre';
                        feeSelect.appendChild(otherOption);
                        
                        // Événement pour mettre à jour le montant
                        feeSelect.addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                            if (selectedOption.dataset.amount) {
                                amountInput.value = selectedOption.dataset.amount;
                                calculateBalance();
                            }
                        });
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Mettre à jour l'aide pour le numéro de transaction
        function updateTransactionHelp() {
            const method = document.getElementById('paymentMethod').value;
            const helpText = document.getElementById('transactionHelp');
            
            switch(method) {
                case 'mobile_money':
                    helpText.textContent = 'Ex: MTN-123456789, Airtel-987654321';
                    break;
                case 'bank_transfer':
                    helpText.textContent = 'Numéro de référence du virement';
                    break;
                case 'check':
                    helpText.textContent = 'Numéro du chèque';
                    break;
                default:
                    helpText.textContent = 'Pour Mobile Money, banque, etc.';
            }
        }

        // Appliquer une remise
        function applyDiscount() {
            const discount = parseFloat(document.getElementById('discountAmount').value) || 0;
            const amount = parseFloat(document.getElementById('amount').value) || 0;
            
            if (discount > 0 && discount <= amount) {
                const newAmount = amount - discount;
                document.getElementById('amount').value = newAmount;
                calculateBalance();
                
                // Afficher un message
                showAlert('success', `Remise de ${new Intl.NumberFormat('fr-FR').format(discount)} XAF appliquée`);
            } else {
                showAlert('error', 'Montant de remise invalide');
            }
        }

        // Charger les paiements avec filtres
        function loadPayments(page = 1) {
            currentPage = page;
            
            const filters = {
                status: document.getElementById('filterStatus').value,
                type: document.getElementById('filterType').value,
                method: document.getElementById('filterMethod').value,
                start_date: document.getElementById('filterStartDate').value,
                end_date: document.getElementById('filterEndDate').value,
                page: page,
                limit: itemsPerPage
            };
            
            // Afficher le chargement
            const tbody = document.querySelector('#paymentsTable tbody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </td>
                </tr>`;
            
            // Récupérer les données
            fetch('controllers/get_payments.php?' + new URLSearchParams(filters))
                .then(response => response.json())
                .then(data => {
                    renderPaymentsTable(data.payments);
                    renderPagination(data.total, data.pages);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="10" class="text-center text-danger py-5">
                                <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                                <p>Erreur de chargement des données</p>
                            </td>
                        </tr>`;
                });
        }

        // Afficher les paiements dans le tableau
        function renderPaymentsTable(payments) {
            const tbody = document.querySelector('#paymentsTable tbody');
            
            if (!payments || payments.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Aucun paiement trouvé</p>
                        </td>
                    </tr>`;
                return;
            }
            
            let html = '';
            
            payments.forEach(payment => {
                const statusClass = `status-${payment.status}`;
                const statusText = getStatusText(payment.status);
                const methodBadge = getMethodBadge(payment.payment_method);
                
                html += `
                    <tr>
                        <td><code>${payment.payment_code}</code></td>
                        <td>
                            <strong>${payment.student_name}</strong><br>
                            <small class="text-muted">${payment.class_name}</small>
                        </td>
                        <td>${getTypeText(payment.payment_type)}</td>
                        <td class="amount-cell">${formatMoney(payment.amount)}</td>
                        <td class="amount-cell">${formatMoney(payment.amount_paid)}</td>
                        <td class="amount-cell ${payment.remaining_balance > 0 ? 'text-danger' : 'text-success'}">
                            ${formatMoney(payment.remaining_balance)}
                        </td>
                        <td>
                            ${formatDate(payment.payment_date)}<br>
                            ${payment.due_date ? `<small class="text-muted">Échéance: ${formatDate(payment.due_date)}</small>` : ''}
                        </td>
                        <td>${methodBadge}</td>
                        <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="viewPaymentDetail(${payment.id})" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" onclick="editPayment(${payment.id})" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-success" onclick="addPayment(${payment.id})" title="Ajouter paiement">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="deletePayment(${payment.id})" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
            });
            
            tbody.innerHTML = html;
        }

        // Afficher la pagination
        function renderPagination(total, pages) {
            const container = document.getElementById('pagination');
            
            if (pages <= 1) {
                container.innerHTML = '';
                return;
            }
            
            let html = '<nav><ul class="pagination justify-content-center">';
            
            // Bouton précédent
            if (currentPage > 1) {
                html += `
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="loadPayments(${currentPage - 1})">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>`;
            }
            
            // Pages
            for (let i = 1; i <= pages; i++) {
                if (i === currentPage) {
                    html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                } else if (i === 1 || i === pages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    html += `
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="loadPayments(${i})">${i}</a>
                        </li>`;
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }
            
            // Bouton suivant
            if (currentPage < pages) {
                html += `
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="loadPayments(${currentPage + 1})">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>`;
            }
            
            html += '</ul></nav>';
            container.innerHTML = html;
        }

        // Réinitialiser les filtres
        function resetFilters() {
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterType').value = '';
            document.getElementById('filterMethod').value = '';
            document.getElementById('filterStartDate').value = '';
            document.getElementById('filterEndDate').value = '';
            loadPayments(1);
        }

        // Charger les alertes de paiement
        function loadPaymentAlerts() {
            const container = document.getElementById('paymentAlerts');
            
            // Simuler des alertes
            const alerts = [
                {type: 'warning', message: '3 paiements en retard', icon: 'exclamation-triangle'},
                {type: 'info', message: 'Paiement partiel pour Jean Dupont', icon: 'info-circle'},
                {type: 'success', message: '5 paiements reçus aujourd\'hui', icon: 'check-circle'}
            ];
            
            let html = '';
            alerts.forEach(alert => {
                html += `
                    <div class="alert alert-${alert.type} alert-dismissible fade show mb-2">
                        <i class="fas fa-${alert.icon} me-2"></i> ${alert.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
            });
            
            container.innerHTML = html;
        }

        // Voir les détails d'un paiement
        function viewPaymentDetail(paymentId) {
            fetch(`controllers/get_payment_detail.php?id=${paymentId}`)
                .then(response => response.json())
                .then(data => {
                    const modal = new bootstrap.Modal(document.getElementById('paymentDetailModal'));
                    const content = document.getElementById('paymentDetailContent');
                    
                    content.innerHTML = `
                        <h6>Détails du paiement</h6>
                        <p><strong>Code:</strong> ${data.payment_code}</p>
                        <p><strong>Élève:</strong> ${data.student_name}</p>
                        <p><strong>Type:</strong> ${getTypeText(data.payment_type)}</p>
                        <p><strong>Montant total:</strong> ${formatMoney(data.amount)}</p>
                        <p><strong>Payé:</strong> ${formatMoney(data.amount_paid)}</p>
                        <p><strong>Solde:</strong> ${formatMoney(data.remaining_balance)}</p>
                        <p><strong>Statut:</strong> <span class="status-badge status-${data.status}">${getStatusText(data.status)}</span></p>
                        <p><strong>Date:</strong> ${formatDate(data.payment_date)}</p>
                        ${data.notes ? `<p><strong>Notes:</strong> ${data.notes}</p>` : ''}
                    `;
                    
                    modal.show();
                });
        }

        // Fonctions utilitaires
        function formatMoney(amount) {
            return new Intl.NumberFormat('fr-FR').format(amount) + ' <span class="xaf-symbol">XAF</span>';
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('fr-FR');
        }

        function getStatusText(status) {
            const statusMap = {
                'paid': 'Payé',
                'pending': 'En attente',
                'partial': 'Partiel',
                'overdue': 'En retard',
                'cancelled': 'Annulé'
            };
            return statusMap[status] || status;
        }

        function getTypeText(type) {
            const typeMap = {
                'tuition': 'Scolarité',
                'registration': 'Inscription',
                'uniform': 'Uniforme',
                'transport': 'Transport',
                'exam': 'Examen',
                'library': 'Bibliothèque',
                'other': 'Autre'
            };
            return typeMap[type] || type;
        }

        function getMethodBadge(method) {
            const badges = {
                'cash': '<span class="badge cash-badge">Espèces</span>',
                'mobile_money': '<span class="badge mobile-money-badge">Mobile Money</span>',
                'bank_transfer': '<span class="badge bank-transfer-badge">Virement</span>',
                'check': '<span class="badge bg-info">Chèque</span>',
                'card': '<span class="badge bg-primary">Carte</span>'
            };
            return badges[method] || method;
        }

        function showAlert(type, message) {
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
            alert.style.zIndex = '9999';
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alert);
            
            setTimeout(() => alert.remove(), 5000);
        }

        // Exporter vers Excel
        function exportToExcel() {
            alert('Export Excel en développement...');
        }

        // Exporter vers PDF
        function exportToPDF() {
            alert('Export PDF en développement...');
        }
    </script>
</body>
</html>
