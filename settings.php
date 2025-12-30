<?php
// settings.php - Paramètres complets de l'école
require_once 'includes/header.php';

// Vérifier les permissions
if (!checkPermission('view_settings') && $_SESSION['role'] != 'super_admin') {
    header('Location: unauthorized.php');
    exit();
}

$page_title = "Paramètres de l'école";

// Connexion à la base de données
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

// Récupérer les informations de l'école
$school_query = "SELECT * FROM schools WHERE id = :school_id";
$school_stmt = $db->prepare($school_query);
$school_stmt->bindParam(':school_id', $_SESSION['school_id']);
$school_stmt->execute();
$school_data = $school_stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Header -->
            <div class="page-header mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-cog"></i> Paramètres de l'école</h1>
                    <div class="btn-group">
                        <button class="btn btn-success" onclick="saveAllSettings()">
                            <i class="fas fa-save"></i> Tout enregistrer
                        </button>
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                    </div>
                </div>
                <p>Configurez et personnalisez les paramètres de votre école</p>
            </div>

            <!-- Onglets -->
            <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" 
                            type="button" role="tab">
                        <i class="fas fa-info-circle"></i> Général
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="appearance-tab" data-bs-toggle="tab" data-bs-target="#appearance" 
                            type="button" role="tab">
                        <i class="fas fa-palette"></i> Apparence
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" 
                            type="button" role="tab">
                        <i class="fas fa-graduation-cap"></i> Académique
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" 
                            type="button" role="tab">
                        <i class="fas fa-money-bill-wave"></i> Financier
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" 
                            type="button" role="tab">
                        <i class="fas fa-bell"></i> Notifications
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="integrations-tab" data-bs-toggle="tab" data-bs-target="#integrations" 
                            type="button" role="tab">
                        <i class="fas fa-plug"></i> Intégrations
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="backup-tab" data-bs-toggle="tab" data-bs-target="#backup" 
                            type="button" role="tab">
                        <i class="fas fa-database"></i> Sauvegarde
                    </button>
                </li>
            </ul>

            <!-- Contenu des onglets -->
            <div class="tab-content" id="settingsTabsContent">
                <!-- Onglet Général -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-school"></i> Informations de l'école</h5>
                                </div>
                                <div class="card-body">
                                    <form id="schoolInfoForm" action="controllers/update_school_info.php" method="POST">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nom de l'école *</label>
                                                <input type="text" class="form-control" name="school_name" 
                                                       value="<?php echo htmlspecialchars($school_data['school_name'] ?? ''); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Code école</label>
                                                <input type="text" class="form-control" value="<?php echo $school_data['school_code'] ?? ''; ?>" 
                                                       readonly style="background-color: #f8f9fa;">
                                                <small class="text-muted">Identifiant unique du système</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Directeur *</label>
                                                <input type="text" class="form-control" name="director_name" 
                                                       value="<?php echo htmlspecialchars($school_data['director_name'] ?? ''); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Téléphone *</label>
                                                <input type="tel" class="form-control" name="phone" 
                                                       value="<?php echo htmlspecialchars($school_data['phone'] ?? ''); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" name="email" 
                                                       value="<?php echo htmlspecialchars($school_data['email'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Site web</label>
                                                <input type="url" class="form-control" name="website" 
                                                       value="<?php echo htmlspecialchars($school_data['website'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Devise *</label>
                                                <select class="form-select" name="currency" required>
                                                    <option value="XAF" selected>Franc CFA (XAF)</option>
                                                    <option value="USD">Dollar US ($)</option>
                                                    <option value="EUR">Euro (€)</option>
                                                    <option value="CDF">Franc Congolais (CDF)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Langue principale</label>
                                                <select class="form-select" name="language">
                                                    <option value="fr" selected>Français</option>
                                                    <option value="en">Anglais</option>
                                                    <option value="ln">Lingala</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Adresse complète</label>
                                                <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($school_data['address'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Enregistrer les informations
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Statut de l'école -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-chart-line"></i> Statut de l'école</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Statut</label>
                                        <div>
                                            <span class="badge bg-success">Actif</span>
                                            <span class="badge bg-secondary ms-2">Année: 2023-2024</span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Date de création</label>
                                        <p><?php echo date('d/m/Y', strtotime($school_data['created_at'] ?? 'now')); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Dernière mise à jour</label>
                                        <p><?php echo date('d/m/Y H:i', strtotime($school_data['updated_at'] ?? 'now')); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions rapides -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-bolt"></i> Actions rapides</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary" onclick="window.location.href='users.php'">
                                            <i class="fas fa-users-cog"></i> Gérer les utilisateurs
                                        </button>
                                        <button class="btn btn-outline-success" onclick="window.location.href='roles.php'">
                                            <i class="fas fa-user-tag"></i> Gérer les rôles
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="window.location.href='system.php'">
                                            <i class="fas fa-tools"></i> Configuration système
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Onglet Apparence -->
                <div class="tab-pane fade" id="appearance" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-image"></i> Logo et identité visuelle</h5>
                                </div>
                                <div class="card-body">
                                    <form id="logoForm" action="controllers/upload_logo.php" method="POST" enctype="multipart/form-data">
                                        <div class="text-center mb-4">
                                            <div id="logoPreview" class="mb-3">
                                                <?php if(!empty($school_data['logo'])): ?>
                                                    <img src="<?php echo $school_data['logo']; ?>" alt="Logo" 
                                                         class="img-fluid rounded" style="max-height: 150px;">
                                                <?php else: ?>
                                                    <div class="border rounded p-5 text-center" style="background-color: #f8f9fa;">
                                                        <i class="fas fa-school fa-4x text-muted"></i>
                                                        <p class="mt-2 text-muted">Aucun logo</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <input type="file" class="form-control" id="logoFile" name="logo" 
                                                       accept="image/*" style="display: none;">
                                                <button type="button" class="btn btn-primary" onclick="document.getElementById('logoFile').click()">
                                                    <i class="fas fa-upload"></i> Choisir un logo
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" id="removeLogo">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            </div>
                                            <small class="text-muted">Format: JPG, PNG, SVG. Max: 2MB. Dimensions recommandées: 300x300px</small>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-upload"></i> Uploader le logo
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-palette"></i> Thème et couleurs</h5>
                                </div>
                                <div class="card-body">
                                    <form id="themeForm" action="controllers/update_theme.php" method="POST">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Couleur principale</label>
                                                <div class="input-group color-picker">
                                                    <input type="color" class="form-control form-control-color" 
                                                           id="primaryColor" name="primary_color" value="#009543">
                                                    <input type="text" class="form-control" value="#009543" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Couleur secondaire</label>
                                                <div class="input-group color-picker">
                                                    <input type="color" class="form-control form-control-color" 
                                                           id="secondaryColor" name="secondary_color" value="#FBDE4A">
                                                    <input type="text" class="form-control" value="#FBDE4A" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Couleur d'accent</label>
                                                <div class="input-group color-picker">
                                                    <input type="color" class="form-control form-control-color" 
                                                           id="accentColor" name="accent_color" value="#DC241F">
                                                    <input type="text" class="form-control" value="#DC241F" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Couleur de texte</label>
                                                <div class="input-group color-picker">
                                                    <input type="color" class="form-control form-control-color" 
                                                           id="textColor" name="text_color" value="#333333">
                                                    <input type="text" class="form-control" value="#333333" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Thème de l'interface</label>
                                            <select class="form-select" name="theme" id="themeSelect">
                                                <option value="light" selected>Clair</option>
                                                <option value="dark">Sombre</option>
                                                <option value="auto">Automatique (selon l'appareil)</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Style de police</label>
                                            <select class="form-select" name="font_family">
                                                <option value="'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" selected>
                                                    Segoe UI (Par défaut)
                                                </option>
                                                <option value="Arial, sans-serif">Arial</option>
                                                <option value="'Helvetica Neue', sans-serif">Helvetica</option>
                                                <option value="'Roboto', sans-serif">Roboto</option>
                                            </select>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-paint-brush"></i> Appliquer le thème
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Aperçu du thème -->
                                    <div class="mt-4 p-3 border rounded" id="themePreview">
                                        <h6 class="mb-3">Aperçu du thème</h6>
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="color-box bg-primary rounded" style="height: 30px;"></div>
                                                <small>Primaire</small>
                                            </div>
                                            <div class="col-3">
                                                <div class="color-box bg-secondary rounded" style="height: 30px;"></div>
                                                <small>Secondaire</small>
                                            </div>
                                            <div class="col-3">
                                                <div class="color-box" style="background-color: #DC241F; height: 30px; border-radius: 4px;"></div>
                                                <small>Accent</small>
                                            </div>
                                            <div class="col-3">
                                                <div class="color-box bg-dark rounded" style="height: 30px;"></div>
                                                <small>Texte</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Onglet Académique -->
                <div class="tab-pane fade" id="academic" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-graduation-cap"></i> Configuration académique</h5>
                        </div>
                        <div class="card-body">
                            <form id="academicSettingsForm" action="controllers/update_academic_settings.php" method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Année scolaire actuelle *</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="start_year" value="2023" min="2000" max="2100">
                                            <span class="input-group-text">-</span>
                                            <input type="number" class="form-control" name="end_year" value="2024" min="2001" max="2101">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Trimestre actuel</label>
                                        <select class="form-select" name="current_trimester">
                                            <option value="1">Trimestre 1</option>
                                            <option value="2">Trimestre 2</option>
                                            <option value="3" selected>Trimestre 3</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date de début d'année</label>
                                        <input type="date" class="form-control" name="academic_start_date" value="2023-09-01">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date de fin d'année</label>
                                        <input type="date" class="form-control" name="academic_end_date" value="2024-06-30">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Note minimale pour réussir (/20)</label>
                                        <input type="number" class="form-control" name="passing_grade" min="0" max="20" step="0.5" value="10">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Note maximale (/20)</label>
                                        <input type="number" class="form-control" name="max_grade" min="0" max="30" value="20" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Coefficient maximum</label>
                                        <input type="number" class="form-control" name="max_coefficient" min="1" max="10" value="5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Système de notation</label>
                                        <select class="form-select" name="grading_system">
                                            <option value="20" selected>/20</option>
                                            <option value="100">/100</option>
                                            <option value="letter">A-F (Lettres)</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="mb-3"><i class="fas fa-check-circle"></i> Options</h6>
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="autoPromotion" name="auto_promotion" checked>
                                                <label class="form-check-label" for="autoPromotion">Promotion automatique</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="attendanceRequired" name="attendance_required" checked>
                                                <label class="form-check-label" for="attendanceRequired">Présence obligatoire</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="gradeWeighting" name="grade_weighting">
                                                <label class="form-check-label" for="gradeWeighting">Pondération des notes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="showRanking" name="show_ranking">
                                                <label class="form-check-label" for="showRanking">Afficher le classement</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="allowGradeCorrection" name="allow_grade_correction">
                                                <label class="form-check-label" for="allowGradeCorrection">Correction des notes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="publishGradesOnline" name="publish_grades_online" checked>
                                                <label class="form-check-label" for="publishGradesOnline">Publication des notes en ligne</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Enregistrer les paramètres académiques
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Onglet Financier -->
                <div class="tab-pane fade" id="financial" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Configuration financière</h5>
                        </div>
                        <div class="card-body">
                            <form id="financialSettingsForm" action="controllers/update_financial_settings.php" method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Devise par défaut</label>
                                        <select class="form-select" name="default_currency">
                                            <option value="XAF" selected>Franc CFA (XAF)</option>
                                            <option value="USD">Dollar US ($)</option>
                                            <option value="EUR">Euro (€)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jour de paiement des mensualités</label>
                                        <input type="number" class="form-control" name="payment_day" min="1" max="31" value="5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Taux de pénalité de retard (%)</label>
                                        <input type="number" class="form-control" name="late_fee_percentage" min="0" max="100" step="0.5" value="5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Délai de grâce (jours)</label>
                                        <input type="number" class="form-control" name="grace_period" min="0" max="30" value="7">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">TVA applicable (%)</label>
                                        <input type="number" class="form-control" name="vat_rate" min="0" max="100" step="0.1" value="18">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mode de paiement par défaut</label>
                                        <select class="form-select" name="default_payment_method">
                                            <option value="cash" selected>Espèces</option>
                                            <option value="bank_transfer">Virement bancaire</option>
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="check">Chèque</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="mb-3"><i class="fas fa-credit-card"></i> Options de paiement</h6>
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="autoGenerateInvoices" name="auto_generate_invoices" checked>
                                                <label class="form-check-label" for="autoGenerateInvoices">Génération automatique des factures</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="sendPaymentReminders" name="send_payment_reminders" checked>
                                                <label class="form-check-label" for="sendPaymentReminders">Envoi de rappels de paiement</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="onlinePayments" name="online_payments">
                                                <label class="form-check-label" for="onlinePayments">Paiements en ligne</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="partialPayments" name="partial_payments" checked>
                                                <label class="form-check-label" for="partialPayments">Paiements partiels autorisés</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="applyLateFees" name="apply_late_fees" checked>
                                                <label class="form-check-label" for="applyLateFees">Application des pénalités de retard</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="generateReceipts" name="generate_receipts" checked>
                                                <label class="form-check-label" for="generateReceipts">Génération automatique des reçus</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Enregistrer les paramètres financiers
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Onglet Notifications -->
                <div class="tab-pane fade" id="notifications" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-bell"></i> Paramètres de notifications</h5>
                        </div>
                        <div class="card-body">
                            <form id="notificationsForm" action="controllers/update_notifications_settings.php" method="POST">
                                <div class="mb-4">
                                    <h6 class="mb-3"><i class="fas fa-envelope"></i> Notifications par email</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">SMTP Server</label>
                                            <input type="text" class="form-control" name="smtp_server" value="smtp.gmail.com">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Port SMTP</label>
                                            <input type="number" class="form-control" name="smtp_port" value="587">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email d'envoi</label>
                                            <input type="email" class="form-control" name="sender_email" value="noreply@<?php echo $_SESSION['school_name'] ?? 'ecole'; ?>.cg">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nom d'affichage</label>
                                            <input type="text" class="form-control" name="sender_name" value="<?php echo $_SESSION['school_name'] ?? 'École'; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="mb-3"><i class="fas fa-sms"></i> Notifications SMS</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Fournisseur SMS</label>
                                            <select class="form-select" name="sms_provider">
                                                <option value="">Aucun</option>
                                                <option value="africas_talking">Africa's Talking</option>
                                                <option value="twilio">Twilio</option>
                                                <option value="local">SMS Local</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Numéro d'envoi</label>
                                            <input type="text" class="form-control" name="sms_sender" placeholder="+242">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="mb-3"><i class="fas fa-broadcast-tower"></i> Types de notifications</h6>
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="notifPayments" name="notify_payments" checked>
                                                <label class="form-check-label" for="notifPayments">Paiements</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="notifGrades" name="notify_grades" checked>
                                                <label class="form-check-label" for="notifGrades">Nouvelles notes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="notifAttendance" name="notify_attendance" checked>
                                                <label class="form-check-label" for="notifAttendance">Absences</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="notifEvents" name="notify_events" checked>
                                                <label class="form-check-label" for="notifEvents">Événements</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="notifNews" name="notify_news" checked>
                                                <label class="form-check-label" for="notifNews">Actualités</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="notifReminders" name="notify_reminders" checked>
                                                <label class="form-check-label" for="notifReminders">Rappels</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Enregistrer les paramètres de notifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Onglet Intégrations -->
                <div class="tab-pane fade" id="integrations" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-plug"></i> Intégrations et API</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border-primary">
                                        <div class="card-body text-center">
                                            <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                                            <h5>Clé API</h5>
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" id="apiKey" 
                                                       value="sk_live_<?php echo bin2hex(random_bytes(16)); ?>" readonly>
                                                <button class="btn btn-outline-secondary" type="button" onclick="copyApiKey()">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <button class="btn btn-outline-warning" type="button" onclick="regenerateApiKey()">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">Utilisez cette clé pour intégrer votre système</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border-success">
                                        <div class="card-body text-center">
                                            <i class="fas fa-qrcode fa-3x text-success mb-3"></i>
                                            <h5>QR Code pour paiement</h5>
                                            <div class="mb-3" id="qrcode"></div>
                                            <button class="btn btn-outline-success btn-sm" onclick="generateQRCode()">
                                                <i class="fas fa-sync-alt"></i> Générer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Mobile Money</h6>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="mobileMoneyIntegration">
                                                </div>
                                            </div>
                                            <p class="small text-muted">Intégration avec MTN Mobile Money et Airtel Money</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Banques locales</h6>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="bankIntegration">
                                                </div>
                                            </div>
                                            <p class="small text-muted">Intégration avec les banques congolaises</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Ministère de l'Éducation</h6>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="ministryIntegration">
                                                </div>
                                            </div>
                                            <p class="small text-muted">Export des données vers le ministère</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Onglet Sauvegarde -->
                <div class="tab-pane fade" id="backup" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-database"></i> Sauvegarde et restauration</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <h6 class="mb-3"><i class="fas fa-download"></i> Sauvegarde manuelle</h6>
                                            <p>Créez une sauvegarde complète de votre base de données.</p>
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-success" id="createBackup">
                                                    <i class="fas fa-save"></i> Créer une sauvegarde
                                                </button>
                                                <button class="btn btn-outline-primary" id="downloadBackup">
                                                    <i class="fas fa-download"></i> Télécharger la dernière sauvegarde
                                                </button>
                                            </div>
                                            <div class="mt-3" id="backupStatus"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="mb-3"><i class="fas fa-history"></i> Sauvegarde automatique</h6>
                                            <form id="autoBackupForm">
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="enableAutoBackup" checked>
                                                        <label class="form-check-label" for="enableAutoBackup">
                                                            Activer la sauvegarde automatique
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Fréquence</label>
                                                    <select class="form-select" id="backupFrequency">
                                                        <option value="daily">Quotidienne</option>
                                                        <option value="weekly" selected>Hebdomadaire</option>
                                                        <option value="monthly">Mensuelle</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Heure de sauvegarde</label>
                                                    <input type="time" class="form-control" id="backupTime" value="02:00">
                                                </div>
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save"></i> Enregistrer
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="mb-3"><i class="fas fa-upload"></i> Restauration</h6>
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Attention:</strong> La restauration effacera toutes les données actuelles.
                                            </div>
                                            
                                            <form id="restoreForm" enctype="multipart/form-data">
                                                <div class="mb-3">
                                                    <label class="form-label">Fichier de sauvegarde</label>
                                                    <input type="file" class="form-control" id="backupFile" accept=".sql,.gz,.zip">
                                                    <small class="text-muted">Formats acceptés: .sql, .gz, .zip</small>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="confirmRestore">
                                                        <label class="form-check-label" for="confirmRestore">
                                                            Je confirme vouloir restaurer cette sauvegarde
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-danger" id="restoreButton" disabled>
                                                        <i class="fas fa-redo"></i> Restaurer la sauvegarde
                                                    </button>
                                                </div>
                                            </form>
                                            
                                            <hr class="my-4">
                                            
                                            <h6 class="mb-3"><i class="fas fa-list"></i> Sauvegardes disponibles</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Taille</th>
                                                            <th>Type</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="backupList">
                                                        <!-- Les sauvegardes seront chargées ici -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal pour confirmation -->
<div class="modal fade" id="confirmationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                <h5 class="modal-title text-white" id="modalTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalMessage"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des onglets
    const tabTriggers = document.querySelectorAll('#settingsTabs button[data-bs-toggle="tab"]');
    tabTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            localStorage.setItem('lastSettingsTab', this.id);
        });
    });
    
    // Restaurer le dernier onglet ouvert
    const lastTab = localStorage.getItem('lastSettingsTab');
    if (lastTab) {
        const tab = document.querySelector(`#${lastTab}`);
        if (tab) {
            new bootstrap.Tab(tab).show();
        }
    }
    
    // Gestion du logo
    document.getElementById('logoFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logoPreview');
                preview.innerHTML = `<img src="${e.target.result}" alt="Logo" class="img-fluid rounded" style="max-height: 150px;">`;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Supprimer le logo
    document.getElementById('removeLogo').addEventListener('click', function() {
        if (confirm('Voulez-vous vraiment supprimer le logo ?')) {
            fetch('controllers/remove_logo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('logoPreview').innerHTML = `
                        <div class="border rounded p-5 text-center" style="background-color: #f8f9fa;">
                            <i class="fas fa-school fa-4x text-muted"></i>
                            <p class="mt-2 text-muted">Aucun logo</p>
                        </div>
                    `;
                }
            });
        }
    });
    
    // Gestion des couleurs
    const colorPickers = document.querySelectorAll('.color-picker input[type="color"]');
    colorPickers.forEach(picker => {
        picker.addEventListener('input', function() {
            const textInput = this.parentNode.querySelector('input[type="text"]');
            textInput.value = this.value;
            updateThemePreview();
        });
    });
    
    function updateThemePreview() {
        const primary = document.getElementById('primaryColor').value;
        const secondary = document.getElementById('secondaryColor').value;
        const accent = document.getElementById('accentColor').value;
        const text = document.getElementById('textColor').value;
        
        // Mettre à jour l'aperçu
        const preview = document.getElementById('themePreview');
        const boxes = preview.querySelectorAll('.color-box');
        boxes[0].style.backgroundColor = primary;
        boxes[1].style.backgroundColor = secondary;
        boxes[2].style.backgroundColor = accent;
        boxes[3].style.backgroundColor = text;
    }
    
    // Gestion de la restauration
    document.getElementById('confirmRestore').addEventListener('change', function() {
        document.getElementById('restoreButton').disabled = !this.checked;
    });
    
    // Créer une sauvegarde
    document.getElementById('createBackup').addEventListener('click', function() {
        const button = this;
        const originalText = button.innerHTML;
        
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...';
        button.disabled = true;
        
        fetch('controllers/create_backup.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('backupStatus').innerHTML = `
                        <div class="alert alert-success">
                            <i class="fas fa-check"></i> Sauvegarde créée avec succès!
                        </div>
                    `;
                    loadBackupList();
                } else {
                    document.getElementById('backupStatus').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-times"></i> Erreur lors de la création de la sauvegarde
                        </div>
                    `;
                }
            })
            .finally(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            });
    });
    
    // Télécharger la dernière sauvegarde
    document.getElementById('downloadBackup').addEventListener('click', function() {
        window.location.href = 'controllers/download_backup.php';
    });
    
    // Charger la liste des sauvegardes
    function loadBackupList() {
        fetch('controllers/get_backups.php')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('backupList');
                tbody.innerHTML = '';
                
                if (data.backups && data.backups.length > 0) {
                    data.backups.forEach(backup => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${backup.date}</td>
                            <td>${backup.size}</td>
                            <td><span class="badge ${backup.type === 'auto' ? 'bg-success' : 'bg-primary'}">${backup.type}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="downloadBackup('${backup.filename}')">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteBackup('${backup.filename}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Aucune sauvegarde disponible
                            </td>
                        </tr>
                    `;
                }
            });
    }
    
    // Initialiser la liste des sauvegardes
    loadBackupList();
    
    // Gestion du QR Code
    function generateQRCode() {
        const qrcodeDiv = document.getElementById('qrcode');
        qrcodeDiv.innerHTML = '';
        
        // Générer un QR Code pour le paiement
        const paymentUrl = `https://${window.location.host}/payments/qr/${Date.now()}`;
        
        // Utiliser une bibliothèque QR Code si disponible
        if (typeof QRCode !== 'undefined') {
            new QRCode(qrcodeDiv, {
                text: paymentUrl,
                width: 128,
                height: 128
            });
        } else {
            qrcodeDiv.innerHTML = `<div class="alert alert-info">QR Code: ${paymentUrl}</div>`;
        }
    }
    
    // Générer le QR Code au chargement
    generateQRCode();
    
    // Fonction pour copier la clé API
    window.copyApiKey = function() {
        const apiKeyInput = document.getElementById('apiKey');
        apiKeyInput.select();
        document.execCommand('copy');
        
        // Afficher une notification
        const originalText = event.target.innerHTML;
        event.target.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => {
            event.target.innerHTML = originalText;
        }, 2000);
    };
    
    // Fonction pour régénérer la clé API
    window.regenerateApiKey = function() {
        if (confirm('Voulez-vous régénérer la clé API ? Les applications existantes devront être reconfigurées.')) {
            const newKey = 'sk_live_' + Math.random().toString(36).substring(2) + Date.now().toString(36);
            document.getElementById('apiKey').value = newKey;
        }
    };
    
    // Gestion de la restauration
    document.getElementById('restoreForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!confirm('Êtes-vous sûr de vouloir restaurer cette sauvegarde ? Cette action est IRREVERSIBLE !')) {
            return;
        }
        
        const formData = new FormData(this);
        const restoreButton = document.getElementById('restoreButton');
        const originalText = restoreButton.innerHTML;
        
        restoreButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Restauration...';
        restoreButton.disabled = true;
        
        fetch('controllers/restore_backup.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Restauration réussie ! Le système va redémarrer.');
                location.reload();
            } else {
                alert('Erreur lors de la restauration: ' + data.error);
            }
        })
        .finally(() => {
            restoreButton.innerHTML = originalText;
            restoreButton.disabled = false;
        });
    });
    
    // Sauvegarder tous les paramètres
    window.saveAllSettings = function() {
        const forms = document.querySelectorAll('#settingsTabsContent form');
        let allValid = true;
        
        forms.forEach(form => {
            if (!form.checkValidity()) {
                allValid = false;
                form.classList.add('was-validated');
            }
        });
        
        if (allValid) {
            // Soumettre tous les formulaires
            forms.forEach(form => {
                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
            });
            
            // Afficher un message de succès
            const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            document.getElementById('modalTitle').textContent = 'Succès';
            document.getElementById('modalMessage').innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Tous les paramètres ont été enregistrés avec succès!
                </div>
            `;
            document.getElementById('confirmAction').style.display = 'none';
            modal.show();
            
            setTimeout(() => {
                modal.hide();
            }, 2000);
        } else {
            alert('Veuillez corriger les erreurs dans les formulaires avant de sauvegarder.');
        }
    };
});

// Fonctions globales pour les sauvegardes
function downloadBackup(filename) {
    window.location.href = `controllers/download_backup.php?file=${filename}`;
}

function deleteBackup(filename) {
    if (confirm('Voulez-vous vraiment supprimer cette sauvegarde ?')) {
        fetch(`controllers/delete_backup.php?file=${filename}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la suppression');
                }
            });
    }
}
</script>

<style>
/* Styles spécifiques pour les paramètres */
.color-picker {
    position: relative;
}

.color-picker .form-control-color {
    position: absolute;
    width: 40px;
    height: 38px;
    opacity: 0;
    cursor: pointer;
}

.color-picker .form-control {
    padding-left: 50px;
}

#themePreview .color-box {
    transition: all 0.3s;
}

.nav-tabs .nav-link {
    color: #495057;
    font-weight: 500;
    border: none;
    padding: 10px 20px;
}

.nav-tabs .nav-link.active {
    color: var(--primary);
    border-bottom: 3px solid var(--primary);
    background-color: transparent;
}

.nav-tabs .nav-link:hover {
    color: var(--primary);
}

.tab-content {
    padding: 20px 0;
}

.form-switch .form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
}

.card {
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.btn-group .btn {
    border-radius: 4px !important;
}

.alert {
    border: none;
    border-radius: 8px;
}

.input-group-text {
    background-color: #f8f9fa;
}

.border-primary {
    border-color: var(--primary) !important;
}

.border-success {
    border-color: var(--congo-green) !important;
}

.bg-primary {
    background-color: var(--primary) !important;
}

.bg-secondary {
    background-color: var(--congo-yellow) !important;
}

.text-primary {
    color: var(--primary) !important;
}
</style>

<?php require_once 'includes/footer.php'; ?>