<?php
// login.php - Version corrigée
require_once 'includes/header.php';

$page_title = "Connexion";

// Si déjà connecté, rediriger
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Connexion à la base
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    // Vérifier les identifiants (requête corrigée)
    $query = "SELECT sa.*, s.school_name, s.logo as school_logo 
              FROM school_admins sa 
              LEFT JOIN schools s ON sa.school_id = s.id 
              WHERE sa.username = :username";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Vérifier le mot de passe (pour le test, utiliser admin123)
        // En production, utiliser: if (password_verify($password, $user['password'])) {
        if ($password == 'admin123' || password_verify($password, $user['password'])) {
            // Créer la session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['school_id'] = $user['school_id'];
            $_SESSION['school_name'] = $user['school_name'];
            $_SESSION['school_logo'] = $user['school_logo'];
            
            header('Location: index.php');
            exit();
        } else {
            $error = "Mot de passe incorrect!";
        }
    } else {
        $error = "Utilisateur non trouvé!";
    }
}
?>

<!-- Le reste du code HTML reste le même -->
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="card-header text-center" style="background: linear-gradient(90deg, var(--primary), var(--congo-red));">
                    <h3 class="text-white mb-0">
                        <i class="fas fa-graduation-cap"></i> CONNEXION
                    </h3>
                    <p class="text-light mb-0">Système de Gestion Scolaire YOURHOPE</p>
                </div>
                <div class="card-body p-4">
                    <!-- Logo YOURHOPE -->
                    <div class="text-center mb-4">
                        <div style="background-color: var(--primary); color: white; padding: 15px; border-radius: 10px; display: inline-block;">
                            <h2 class="mb-0">YOURHOPE</h2>
                            <small>Solutions Éducatives Congo</small>
                        </div>
                    </div>
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user"></i> Nom d'utilisateur
                            </label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Entrez votre nom d'utilisateur" required value="admin">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Mot de passe
                            </label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Entrez votre mot de passe" required value="admin123">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Se connecter
                            </button>
                        </div>
                        
                        <div class="text-center mt-3">
                            <p class="text-muted">
                                <small>Identifiants de test:<br>
                                Utilisateur: <strong>admin</strong><br>
                                Mot de passe: <strong>admin123</strong></small>
                            </p>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center text-muted">
                    <small>© <?php echo date('Y'); ?> YOURHOPE - Système de Gestion Scolaire Congo</small>
                    <br>
                    <small>Version 1.0 - Francs CFA (XAF)</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>