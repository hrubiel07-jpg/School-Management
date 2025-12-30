<?php
require_once 'includes/header.php';
$page_title = "Accès non autorisé";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h3><i class="fas fa-exclamation-triangle"></i> Accès non autorisé</h3>
                </div>
                <div class="card-body">
                    <i class="fas fa-lock fa-5x text-danger mb-4"></i>
                    <h4 class="mb-3">Vous n'avez pas les permissions nécessaires</h4>
                    <p class="text-muted">Vous essayez d'accéder à une ressource qui nécessite des privilèges supplémentaires.</p>
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Retour à l'accueil
                        </a>
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i> Se déconnecter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>