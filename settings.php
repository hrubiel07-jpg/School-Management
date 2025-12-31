<?php
// settings.php - PAGE DE PARAMÈTRES PROFESSIONNELLE
require_once 'includes/header.php';

// Vérifier les permissions
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
    header('Location: unauthorized.php');
    exit();
}

$page_title = "Paramètres de l'École";

// Connexion à la base
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

// Récupérer les informations actuelles de l'école
$school_id = $_SESSION['school_id'] ?? 1;
$query = "SELECT * FROM schools WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $school_id);
$stmt->execute();
$school = $stmt->fetch(PDO::FETCH_ASSOC);

// Si pas d'école trouvée, rediriger
if (!$school) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - YOURHOPE School Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        :root {
            --primary-color: <?php echo $school['primary_color'] ?? '#009543'; ?>;
            --secondary-color: <?php echo $school['secondary_color'] ?? '#FFD700'; ?>;
            --accent-color: <?php echo $school['accent_color'] ?? '#DC241F'; ?>;
        }
        
        .logo-preview {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .logo-preview:hover {
            border-color: var(--primary-color);
            background-color: rgba(0, 149, 67, 0.05);
        }
        
        .color-box {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .color-box:hover, .color-box.active {
            border-color: #333;
            transform: scale(1.1);
        }
        
        .stat-card {
            border-left: 4px solid var(--primary-color);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .nav-tabs .nav-link.active {
            border-bottom: 3px solid var(--accent-color);
            font-weight: bold;
            color: var(--accent-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: <?php echo $school['primary_color'] ?? '#009543'; ?>e0;
            border-color: <?php echo $school['primary_color'] ?? '#009543'; ?>e0;
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: #333;
        }
        
        .alert-congo {
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(90deg, var(--primary-color), var(--accent-color));">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-school me-2"></i>
                <?php 
                if (!empty($school['logo'])) {
                    echo '<img src="' . $school['logo'] . '" alt="Logo" height="30" class="me-2">';
                }
                echo $school['school_name']; 
                ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['full_name'] ?? 'Admin'; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="settings.php">
                                <i class="fas fa-cog"></i> Paramètres
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebarMenu">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="students.php">
                                <i class="fas fa-users me-2"></i> Élèves
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="teachers.php">
                                <i class="fas fa-chalkboard-teacher me-2"></i> Enseignants
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="classes.php">
                                <i class="fas fa-school me-2"></i> Classes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="payments.php">
                                <i class="fas fa-money-bill-wave me-2"></i> Paiements
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="settings.php">
                                <i class="fas fa-cog me-2"></i> Paramètres
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <div class="card border-0" style="background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); color: white;">
                                <div class="card-body text-center">
                                    <i class="fas fa-flag fa-2x mb-2"></i>
                                    <h6 class="card-title">République du Congo</h6>
                                    <small>Gestion scolaire en XAF</small>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Contenu principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- En-tête -->
                <div class="page-header mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h2"><i class="fas fa-cog"></i> Paramètres de l'École</h1>
                            <p class="lead">Personnalisez votre espace administratif</p>
                        </div>
                        <div>
                            <span class="badge bg-secondary">
                                <i class="fas fa-calendar"></i> <?php echo $school['academic_year'] ?? '2024-2025'; ?>
                            </span>
                            <span class="badge bg-secondary ms-2">
                                <i class="fas fa-money-bill"></i> <?php echo $school['currency'] ?? 'XAF'; ?>
                            </span>
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
                        <div class="card stat-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Configuration</h5>
                                        <h2 class="mb-0"><i class="fas fa-check-circle text-success"></i></h2>
                                        <p class="text-muted">Active</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-cogs fa-2x text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Logo</h5>
                                        <h2 class="mb-0">
                                            <?php echo !empty($school['logo']) ? 
                                                '<i class="fas fa-check-circle text-success"></i>' : 
                                                '<i class="fas fa-times-circle text-warning"></i>'; ?>
                                        </h2>
                                        <p class="text-muted"><?php echo !empty($school['logo']) ? 'Défini' : 'Non défini'; ?></p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-image fa-2x" style="color: var(--secondary-color)"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Couleurs</h5>
                                        <h2 class="mb-0">3</h2>
                                        <p class="text-muted">Personnalisées</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-palette fa-2x" style="color: var(--accent-color)"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Dernière mise à jour</h5>
                                        <h6 class="mb-0"><?php echo date('d/m/Y', strtotime($school['created_at'])); ?></h6>
                                        <p class="text-muted">Date de configuration</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-clock fa-2x text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation par onglets -->
                <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button">
                            <i class="fas fa-school me-1"></i> Informations générales
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="appearance-tab" data-bs-toggle="tab" data-bs-target="#appearance" type="button">
                            <i class="fas fa-palette me-1"></i> Apparence
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" type="button">
                            <i class="fas fa-calendar-alt me-1"></i> Année académique
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button">
                            <i class="fas fa-tools me-1"></i> Système
                        </button>
                    </li>
                </ul>

                <!-- Contenu des onglets -->
                <div class="tab-content" id="settingsTabContent">
                    <!-- ONGLET 1: Informations générales -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <div class="card">
                            <div class="card-header" style="background: linear-gradient(90deg, var(--primary-color), #00703c); color: white;">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informations de l'école</h5>
                            </div>
                            <div class="card-body">
                                <form action="controllers/update_school_info.php" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label class="form-label">Nom de l'école *</label>
                                                <input type="text" class="form-control" name="school_name" 
                                                       value="<?php echo htmlspecialchars($school['school_name']); ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Slogan</label>
                                                <input type="text" class="form-control" name="slogan" 
                                                       value="<?php echo htmlspecialchars($school['slogan'] ?? ''); ?>"
                                                       placeholder="Ex: L'excellence par l'éducation">
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Téléphone *</label>
                                                    <input type="tel" class="form-control" name="phone" 
                                                           value="<?php echo htmlspecialchars($school['phone'] ?? '+242'); ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="form-control" name="email"
                                                           value="<?php echo htmlspecialchars($school['email'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Adresse</label>
                                                <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($school['address'] ?? ''); ?></textarea>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Nom du directeur</label>
                                                <input type="text" class="form-control" name="director_name"
                                                       value="<?php echo htmlspecialchars($school['director_name'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <!-- Upload Logo -->
                                            <div class="mb-3">
                                                <label class="form-label d-block">Logo de l'école</label>
                                                <div class="logo-preview mb-3 text-center">
                                                    <?php if (!empty($school['logo'])): ?>
                                                    <img src="<?php echo $school['logo']; ?>" 
                                                         alt="Logo de l'école" class="img-fluid rounded" style="max-height: 180px;">
                                                    <div class="mt-2">
                                                        <a href="<?php echo $school['logo']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-external-link-alt"></i> Voir
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLogo()">
                                                            <i class="fas fa-trash"></i> Supprimer
                                                        </button>
                                                    </div>
                                                    <?php else: ?>
                                                    <div class="py-5">
                                                        <i class="fas fa-school fa-4x text-muted mb-3"></i>
                                                        <p class="text-muted">Aucun logo téléchargé</p>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="input-group">
                                                    <input type="file" class="form-control" name="logo" id="logoInput" accept="image/*">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('logoInput').click()">
                                                        <i class="fas fa-upload"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted">PNG, JPG max 2MB. Taille recommandée: 300x300px</small>
                                            </div>
                                            
                                            <div class="alert alert-info">
                                                <i class="fas fa-lightbulb"></i> <strong>Conseil :</strong><br>
                                                Utilisez un logo carré ou rond pour un meilleur rendu.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary" onclick="previewChanges()">
                                            <i class="fas fa-eye"></i> Prévisualiser
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Enregistrer les modifications
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ONGLET 2: Apparence -->
                    <div class="tab-pane fade" id="appearance" role="tabpanel">
                        <div class="card">
                            <div class="card-header" style="background: linear-gradient(90deg, var(--primary-color), var(--secondary-color)); color: #333;">
                                <h5 class="mb-0"><i class="fas fa-paint-brush me-2"></i> Personnalisation des couleurs</h5>
                                <p class="mb-0">Couleurs aux standards de la République du Congo</p>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-congo mb-4">
                                    <i class="fas fa-flag me-2"></i>
                                    <strong>Palette nationale :</strong> Vert (#009543), Jaune (#FFD700), Rouge (#DC241F)
                                </div>
                                
                                <form action="controllers/update_school_colors.php" method="POST">
                                    <div class="row mb-4">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Couleur principale</label>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="color-box me-3" style="background-color: <?php echo $school['primary_color'] ?? '#009543'; ?>;"
                                                     onclick="selectColor('primary', this)" id="colorPrimary"></div>
                                                <input type="text" class="form-control" name="primary_color" 
                                                       value="<?php echo $school['primary_color'] ?? '#009543'; ?>"
                                                       id="primaryColorInput">
                                            </div>
                                            <small class="text-muted">Utilisée pour les en-têtes, boutons principaux</small>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Couleur secondaire</label>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="color-box me-3" style="background-color: <?php echo $school['secondary_color'] ?? '#FFD700'; ?>;"
                                                     onclick="selectColor('secondary', this)" id="colorSecondary"></div>
                                                <input type="text" class="form-control" name="secondary_color" 
                                                       value="<?php echo $school['secondary_color'] ?? '#FFD700'; ?>"
                                                       id="secondaryColorInput">
                                            </div>
                                            <small class="text-muted">Utilisée pour les accents, surbrillance</small>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Couleur d'accent</label>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="color-box me-3" style="background-color: <?php echo $school['accent_color'] ?? '#DC241F'; ?>;"
                                                     onclick="selectColor('accent', this)" id="colorAccent"></div>
                                                <input type="text" class="form-control" name="accent_color" 
                                                       value="<?php echo $school['accent_color'] ?? '#DC241F'; ?>"
                                                       id="accentColorInput">
                                            </div>
                                            <small class="text-muted">Utilisée pour les alertes, indicateurs</small>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <label class="form-label">Palettes prédéfinies</label>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="palette-item" onclick="applyPreset('congo')">
                                                    <div class="d-flex">
                                                        <div style="width:30px;height:30px;background:#009543;"></div>
                                                        <div style="width:30px;height:30px;background:#FFD700;"></div>
                                                        <div style="width:30px;height:30px;background:#DC241F;"></div>
                                                    </div>
                                                    <small>Drapeau Congo</small>
                                                </div>
                                                <div class="palette-item" onclick="applyPreset('education')">
                                                    <div class="d-flex">
                                                        <div style="width:30px;height:30px;background:#1E3A8A;"></div>
                                                        <div style="width:30px;height:30px;background:#10B981;"></div>
                                                        <div style="width:30px;height:30px;background:#F59E0B;"></div>
                                                    </div>
                                                    <small>Éducation</small>
                                                </div>
                                                <div class="palette-item" onclick="applyPreset('modern')">
                                                    <div class="d-flex">
                                                        <div style="width:30px;height:30px;background:#7C3AED;"></div>
                                                        <div style="width:30px;height:30px;background:#EC4899;"></div>
                                                        <div style="width:30px;height:30px;background:#06B6D4;"></div>
                                                    </div>
                                                    <small>Moderne</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="preview-section mb-4">
                                        <h6><i class="fas fa-desktop me-2"></i> Prévisualisation</h6>
                                        <div class="border rounded p-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="preview-navbar p-2 rounded" style="background: linear-gradient(90deg, var(--primary-color), var(--accent-color)); width: 100%;">
                                                    <span class="text-white">Navigation</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="preview-sidebar p-2 rounded" style="background-color: <?php echo $school['secondary_color'] ?? '#FFD700'; ?>20; border-left: 4px solid <?php echo $school['primary_color'] ?? '#009543'; ?>;">
                                                        <small>Barre latérale</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="preview-content">
                                                        <button class="btn btn-sm mb-2" style="background-color: <?php echo $school['primary_color'] ?? '#009543'; ?>; color: white;">
                                                            Bouton primaire
                                                        </button>
                                                        <button class="btn btn-sm mb-2" style="background-color: <?php echo $school['secondary_color'] ?? '#FFD700'; ?>; color: #333;">
                                                            Bouton secondaire
                                                        </button>
                                                        <span class="badge ms-2" style="background-color: <?php echo $school['accent_color'] ?? '#DC241F'; ?>; color: white;">
                                                            Badge
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-outline-secondary me-2" onclick="resetColors()">
                                            <i class="fas fa-undo"></i> Réinitialiser
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-sync-alt"></i> Appliquer les couleurs
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ONGLET 3: Année académique -->
                    <div class="tab-pane fade" id="academic" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-calendar me-2"></i> Configuration académique</h5>
                            </div>
                            <div class="card-body">
                                <form action="controllers/update_academic_settings.php" method="POST">
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Année académique *</label>
                                            <input type="text" class="form-control" name="academic_year" 
                                                   value="<?php echo htmlspecialchars($school['academic_year'] ?? '2024-2025'); ?>" 
                                                   placeholder="Ex: 2024-2025" required>
                                            <small class="text-muted">Format: 2024-2025</small>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Devise *</label>
                                            <select class="form-select" name="currency" required>
                                                <option value="XAF" <?php echo ($school['currency'] ?? 'XAF') == 'XAF' ? 'selected' : ''; ?>>Franc CFA (XAF)</option>
                                                <option value="USD" <?php echo ($school['currency'] ?? 'XAF') == 'USD' ? 'selected' : ''; ?>>Dollar US (USD)</option>
                                                <option value="EUR" <?php echo ($school['currency'] ?? 'XAF') == 'EUR' ? 'selected' : ''; ?>>Euro (EUR)</option>
                                                <option value="CDF" <?php echo ($school['currency'] ?? 'XAF') == 'CDF' ? 'selected' : ''; ?>>Franc Congolais (CDF)</option>
                                            </select>
                                            <small class="text-muted">Devise utilisée pour tous les paiements et factures</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label">Périodes académiques (trimestres)</label>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Trimestre</th>
                                                        <th>Date début</th>
                                                        <th>Date fin</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="academicPeriods">
                                                    <tr>
                                                        <td><input type="text" class="form-control" name="period_name[]" placeholder="Trimestre 1" value="Trimestre 1"></td>
                                                        <td><input type="date" class="form-control" name="start_date[]" value="2024-09-02"></td>
                                                        <td><input type="date" class="form-control" name="end_date[]" value="2024-12-20"></td>
                                                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removePeriod(this)">
                                                            <i class="fas fa-times"></i>
                                                        </button></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="text" class="form-control" name="period_name[]" placeholder="Trimestre 2" value="Trimestre 2"></td>
                                                        <td><input type="date" class="form-control" name="start_date[]" value="2025-01-06"></td>
                                                        <td><input type="date" class="form-control" name="end_date[]" value="2025-04-04"></td>
                                                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removePeriod(this)">
                                                            <i class="fas fa-times"></i>
                                                        </button></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="text" class="form-control" name="period_name[]" placeholder="Trimestre 3" value="Trimestre 3"></td>
                                                        <td><input type="date" class="form-control" name="start_date[]" value="2025-04-14"></td>
                                                        <td><input type="date" class="form-control" name="end_date[]" value="2025-06-27"></td>
                                                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removePeriod(this)">
                                                            <i class="fas fa-times"></i>
                                                        </button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPeriod()">
                                            <i class="fas fa-plus"></i> Ajouter une période
                                        </button>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label">Vacances scolaires</label>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Vacances</th>
                                                        <th>Date début</th>
                                                        <th>Date fin</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="holidaysList">
                                                    <tr>
                                                        <td><input type="text" class="form-control" name="holiday_name[]" placeholder="Vacances de Noël" value="Vacances de Noël"></td>
                                                        <td><input type="date" class="form-control" name="holiday_start[]" value="2024-12-21"></td>
                                                        <td><input type="date" class="form-control" name="holiday_end[]" value="2025-01-05"></td>
                                                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeHoliday(this)">
                                                            <i class="fas fa-times"></i>
                                                        </button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addHoliday()">
                                            <i class="fas fa-plus"></i> Ajouter des vacances
                                        </button>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Enregistrer la configuration
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ONGLET 4: Système -->
                    <div class="tab-pane fade" id="system" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-tools me-2"></i> Paramètres système</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Attention :</strong> Ces paramètres affectent le fonctionnement global du système.
                                </div>
                                
                                <form action="controllers/update_system_settings.php" method="POST">
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Code de l'école</label>
                                            <input type="text" class="form-control" value="<?php echo $school['school_code']; ?>" readonly>
                                            <small class="text-muted">Identifiant unique du système</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Date de création</label>
                                            <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i', strtotime($school['created_at'])); ?>" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h6><i class="fas fa-shield-alt me-2"></i> Sécurité</h6>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="require_strong_passwords" id="requireStrongPasswords">
                                            <label class="form-check-label" for="requireStrongPasswords">
                                                Exiger des mots de passe forts
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="two_factor_auth" id="twoFactorAuth">
                                            <label class="form-check-label" for="twoFactorAuth">
                                                Authentification à deux facteurs (recommandé)
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="session_timeout" id="sessionTimeout" checked>
                                            <label class="form-check-label" for="sessionTimeout">
                                                Déconnexion automatique après 30 minutes d'inactivité
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h6><i class="fas fa-bell me-2"></i> Notifications</h6>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="email_notifications" id="emailNotifications" checked>
                                            <label class="form-check-label" for="emailNotifications">
                                                Notifications par email
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="sms_notifications" id="smsNotifications">
                                            <label class="form-check-label" for="smsNotifications">
                                                Notifications par SMS
                                            </label>
                                            <small class="text-muted d-block">(Nécessite une configuration SMS)</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h6><i class="fas fa-database me-2"></i> Maintenance</h6>
                                        <button type="button" class="btn btn-outline-secondary me-2" onclick="backupDatabase()">
                                            <i class="fas fa-download"></i> Sauvegarde de la base
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="clearCache()">
                                            <i class="fas fa-broom"></i> Vider le cache
                                        </button>
                                    </div>
                                    
                                    <div class="alert alert-danger">
                                        <h6><i class="fas fa-exclamation-circle me-2"></i> Zone de danger</h6>
                                        <p>Ces actions sont irréversibles</p>
                                        <button type="button" class="btn btn-outline-danger me-2" data-bs-toggle="modal" data-bs-target="#resetModal">
                                            <i class="fas fa-redo"></i> Réinitialiser les paramètres
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="fas fa-trash"></i> Désactiver l'école
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Réinitialisation -->
    <div class="modal fade" id="resetModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir réinitialiser tous les paramètres de l'école aux valeurs par défaut ?</p>
                    <p class="text-danger"><strong>Attention :</strong> Cette action ne peut pas être annulée.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-warning" onclick="resetSettings()">Réinitialiser</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gestion des couleurs
        function selectColor(type, element) {
            // Retirer la classe active de toutes les boîtes de couleur
            document.querySelectorAll('.color-box').forEach(box => {
                box.classList.remove('active');
            });
            
            // Ajouter la classe active à l'élément cliqué
            element.classList.add('active');
            
            // Mettre à jour le champ de saisie correspondant
            const input = document.getElementById(type + 'ColorInput');
            const color = window.getComputedStyle(element).backgroundColor;
            input.value = rgbToHex(color);
            
            // Mettre à jour la prévisualisation
            updatePreview();
        }
        
        function rgbToHex(rgb) {
            const result = rgb.match(/\d+/g);
            if (!result) return '#000000';
            return '#' + result.map(x => {
                const hex = parseInt(x).toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            }).join('');
        }
        
        function applyPreset(preset) {
            let primary, secondary, accent;
            
            switch(preset) {
                case 'congo':
                    primary = '#009543';
                    secondary = '#FFD700';
                    accent = '#DC241F';
                    break;
                case 'education':
                    primary = '#1E3A8A';
                    secondary = '#10B981';
                    accent = '#F59E0B';
                    break;
                case 'modern':
                    primary = '#7C3AED';
                    secondary = '#EC4899';
                    accent = '#06B6D4';
                    break;
            }
            
            document.getElementById('primaryColorInput').value = primary;
            document.getElementById('secondaryColorInput').value = secondary;
            document.getElementById('accentColorInput').value = accent;
            
            // Mettre à jour les boîtes de couleur
            document.getElementById('colorPrimary').style.backgroundColor = primary;
            document.getElementById('colorSecondary').style.backgroundColor = secondary;
            document.getElementById('colorAccent').style.backgroundColor = accent;
            
            updatePreview();
        }
        
        function resetColors() {
            applyPreset('congo');
        }
        
        function updatePreview() {
            const primary = document.getElementById('primaryColorInput').value;
            const secondary = document.getElementById('secondaryColorInput').value;
            const accent = document.getElementById('accentColorInput').value;
            
            // Mettre à jour les éléments de prévisualisation
            document.querySelector('.preview-navbar').style.background = `linear-gradient(90deg, ${primary}, ${accent})`;
            document.querySelector('.preview-sidebar').style.borderLeftColor = primary;
            document.querySelector('.preview-sidebar').style.backgroundColor = `${secondary}20`;
            document.querySelector('.preview-content button:nth-child(1)').style.backgroundColor = primary;
            document.querySelector('.preview-content .badge').style.backgroundColor = accent;
            document.querySelector('.preview-content button:nth-child(2)').style.backgroundColor = secondary;
        }
        
        // Gestion des périodes académiques
        function addPeriod() {
            const tbody = document.getElementById('academicPeriods');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" class="form-control" name="period_name[]" placeholder="Ex: Trimestre"></td>
                <td><input type="date" class="form-control" name="start_date[]"></td>
                <td><input type="date" class="form-control" name="end_date[]"></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removePeriod(this)">
                    <i class="fas fa-times"></i>
                </button></td>
            `;
            tbody.appendChild(row);
        }
        
        function removePeriod(button) {
            if (document.querySelectorAll('#academicPeriods tr').length > 1) {
                button.closest('tr').remove();
            } else {
                alert('Au moins une période doit être définie.');
            }
        }
        
        // Gestion des vacances
        function addHoliday() {
            const tbody = document.getElementById('holidaysList');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" class="form-control" name="holiday_name[]" placeholder="Ex: Vacances"></td>
                <td><input type="date" class="form-control" name="holiday_start[]"></td>
                <td><input type="date" class="form-control" name="holiday_end[]"></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeHoliday(this)">
                    <i class="fas fa-times"></i>
                </button></td>
            `;
            tbody.appendChild(row);
        }
        
        function removeHoliday(button) {
            button.closest('tr').remove();
        }
        
        // Suppression de logo
        function removeLogo() {
            if (confirm('Êtes-vous sûr de vouloir supprimer le logo ?')) {
                fetch('controllers/remove_logo.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
            }
        }
        
        // Prévisualisation des changements
        function previewChanges() {
            alert('Prévisualisation des changements... (Cette fonctionnalité serait implémentée avec JavaScript avancé)');
        }
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            // Activer la première boîte de couleur
            document.querySelector('.color-box').classList.add('active');
            
            // Initialiser la prévisualisation
            updatePreview();
            
            // Gérer l'upload de logo
            document.getElementById('logoInput').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.querySelector('.logo-preview').innerHTML = `
                            <img src="${e.target.result}" alt="Prévisualisation" class="img-fluid rounded" style="max-height: 180px;">
                            <div class="mt-2">
                                <span class="badge bg-success">Nouveau logo</span>
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</body>
</html>
