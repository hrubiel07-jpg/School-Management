<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Gestion Scolaire Congo</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Style personnalisé avec les couleurs du Congo -->
    <style>
        :root {
            --congo-green: #009543;
            --congo-yellow: #FBDE4A;
            --congo-red: #DC241F;
            --primary: var(--congo-green);
            --secondary: var(--congo-yellow);
            --accent: var(--congo-red);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary) !important;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: #00803a;
            border-color: #00803a;
        }
        
        .btn-secondary {
            background-color: var(--secondary);
            border-color: var(--secondary);
            color: #000;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--primary) 0%, #006b32 100%);
            min-height: 100vh;
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: white;
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: var(--congo-yellow);
        }
        
        .sidebar .nav-link i {
            width: 25px;
            text-align: center;
        }
        
        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background-color: var(--primary);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .stat-card {
            border-left: 4px solid var(--primary);
        }
        
        .school-logo {
            max-width: 150px;
            max-height: 80px;
            object-fit: contain;
        }
        
        .page-header {
            background: linear-gradient(90deg, var(--primary), var(--congo-red));
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .footer {
            background-color: var(--primary);
            color: white;
            padding: 20px 0;
            margin-top: 40px;
        }
        
        .table th {
            background-color: #f1f8e9;
            color: var(--primary);
            border-top: none;
        }
        
        .badge-success {
            background-color: var(--primary);
        }
        
        .badge-warning {
            background-color: var(--secondary);
            color: #000;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(0,149,67,0.25);
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <?php
    session_start();
    require_once 'config/database.php';
    
    // Vérifier si l'utilisateur est connecté
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Fonction pour vérifier les permissions
function checkPermission($required_permission) {
    if ($required_permission == '*') return true;
    
    if (!isset($_SESSION['role_id'])) {
        return false;
    }
    
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    // Récupérer les permissions du rôle
    $query = "SELECT permissions FROM roles WHERE id = :role_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':role_id', $_SESSION['role_id']);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        $permissions = json_decode($role['permissions'], true);
        
        // Vérifier si l'utilisateur a la permission ou toutes les permissions (*)
        return in_array('*', $permissions) || in_array($required_permission, $permissions);
    }
    
    return false;
}

// Fonction pour vérifier si l'utilisateur a au moins un des rôles
function checkRole($allowed_roles) {
    if (!isset($_SESSION['role'])) {
        header('Location: login.php');
        exit();
    }
    
    // Si super_admin, toujours autorisé
    if ($_SESSION['role'] == 'super_admin') {
        return true;
    }
    
    // Vérifier les rôles autorisés
    return in_array($_SESSION['role'], $allowed_roles);
}

// Nouvelle fonction pour vérifier une permission spécifique
function requirePermission($permission) {
    if (!checkPermission($permission)) {
        header('Location: unauthorized.php');
        exit();
    }
}
    
    // Gestion des messages de succès/erreur
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-check-circle"></i> ' . $_SESSION['success'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-exclamation-circle"></i> ' . $_SESSION['error'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>';
    unset($_SESSION['error']);
}
?>