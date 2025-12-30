<?php
// system.php - Configuration système
require_once 'includes/header.php';
checkRole(['super_admin']);

$page_title = "Configuration Système";
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Header -->
            <div class="page-header mt-3">
                <h1><i class="fas fa-tools"></i> Configuration Système</h1>
                <p>Paramètres avancés du système YOURHOPE</p>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="list-group">
                        <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                            <i class="fas fa-cog"></i> Général
                        </a>
                        <a href="#academic" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                            <i class="fas fa-graduation-cap"></i> Paramètres académiques
                        </a>
                        <a href="#financial" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                            <i class="fas fa-money-bill-wave"></i> Paramètres financiers
                        </a>
                        <a href="#backup" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                            <i class="fas fa-database"></i> Sauvegarde
                        </a>
                        <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                            <i class="fas fa-shield-alt"></i> Sécurité
                        </a>
                        <a href="#about" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                            <i class="fas fa-info-circle"></i> À propos
                        </a>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="tab-content">
                        <!-- Onglet Général -->
                        <div class="tab-pane fade show active" id="general">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-cog"></i> Paramètres généraux</h5>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <div class="mb-3">
                                            <label class="form-label">Nom du système</label>
                                            <input type="text" class="form-control" value="YOURHOPE School Management">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Devise par défaut</label>
                                            <select class="form-select">
                                                <option value="XAF" selected>Franc CFA (XAF)</option>
                                                <option value="USD">Dollar US ($)</option>
                                                <option value="EUR">Euro (€)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Langue par défaut</label>
                                            <select class="form-select">
                                                <option value="fr" selected>Français</option>
                                                <option value="en">Anglais</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Fuseau horaire</label>
                                            <select class="form-select">
                                                <option value="Africa/Brazzaville" selected>Afrique/Brazzaville (GMT+1)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Format de date</label>
                                            <select class="form-select">
                                                <option value="d/m/Y" selected>JJ/MM/AAAA</option>
                                                <option value="Y-m-d">AAAA-MM-JJ</option>
                                                <option value="m/d/Y">MM/JJ/AAAA</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Enregistrer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Paramètres académiques -->
                        <div class="tab-pane fade" id="academic">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-graduation-cap"></i> Paramètres académiques</h5>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Année scolaire actuelle</label>
                                                <input type="text" class="form-control" value="2023-2024">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Trimestre actuel</label>
                                                <select class="form-select">
                                                    <option value="1">Trimestre 1</option>
                                                    <option value="2">Trimestre 2</option>
                                                    <option value="3" selected>Trimestre 3</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Note minimale pour réussir (/20)</label>
                                            <input type="number" class="form-control" min="0" max="20" step="0.5" value="10">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Coefficient maximum par matière</label>
                                            <input type="number" class="form-control" min="1" max="10" value="5">
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="autoPromotion" checked>
                                                <label class="form-check-label" for="autoPromotion">
                                                    Promotion automatique des élèves
                                                </label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Enregistrer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Paramètres financiers -->
                        <div class="tab-pane fade" id="financial">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-money-bill-wave"></i> Paramètres financiers</h5>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <div class="mb-3">
                                            <label class="form-label">Mois de début de l'année scolaire</label>
                                            <select class="form-select">
                                                <option value="9" selected>Septembre</option>
                                                <option value="1">Janvier</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Jour de paiement des mensualités</label>
                                            <input type="number" class="form-control" min="1" max="31" value="5">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Taux de pénalité de retard (%)</label>
                                            <input type="number" class="form-control" min="0" max="100" step="0.5" value="5">
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="autoInvoice" checked>
                                                <label class="form-check-label" for="autoInvoice">
                                                    Génération automatique des factures
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="smsReminder">
                                                <label class="form-check-label" for="smsReminder">
                                                    Envoyer des rappels SMS pour les paiements
                                                </label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Enregistrer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Sauvegarde -->
                        <div class="tab-pane fade" id="backup">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-database"></i> Sauvegarde des données</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> 
                                        Il est recommandé de faire des sauvegardes régulières de vos données.
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h6>Sauvegarde manuelle</h6>
                                        <p>Créer une sauvegarde complète de la base de données</p>
                                        <button class="btn btn-success" id="backupNow">
                                            <i class="fas fa-download"></i> Sauvegarder maintenant
                                        </button>
                                        <div id="backupStatus" class="mt-2" style="display: none;">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">Chargement...</span>
                                            </div>
                                            <span class="ms-2">Création de la sauvegarde...</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h6>Sauvegarde automatique</h6>
                                        <form>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="autoBackup" checked>
                                                <label class="form-check-label" for="autoBackup">
                                                    Activer la sauvegarde automatique
                                                </label>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Fréquence des sauvegardes</label>
                                                <select class="form-select">
                                                    <option value="daily">Quotidienne</option>
                                                    <option value="weekly" selected>Hebdomadaire</option>
                                                    <option value="monthly">Mensuelle</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Conserver les sauvegardes pendant</label>
                                                <select class="form-select">
                                                    <option value="7">7 jours</option>
                                                    <option value="30" selected>30 jours</option>
                                                    <option value="90">90 jours</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Enregistrer
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <div>
                                        <h6>Sauvegardes disponibles</h6>
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
                                                <tbody>
                                                    <tr>
                                                        <td>01/10/2023 14:30</td>
                                                        <td>15.2 MB</td>
                                                        <td><span class="badge bg-success">Automatique</span></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-download"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Sécurité -->
                        <div class="tab-pane fade" id="security">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-shield-alt"></i> Paramètres de sécurité</h5>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <div class="mb-3">
                                            <label class="form-label">Durée de session (minutes)</label>
                                            <input type="number" class="form-control" min="5" max="480" value="30">
                                            <small class="text-muted">Temps d'inactivité avant déconnexion automatique</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nombre maximum de tentatives de connexion</label>
                                            <input type="number" class="form-control" min="1" max="10" value="3">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Durée de blocage après échecs (minutes)</label>
                                            <input type="number" class="form-control" min="1" max="1440" value="15">
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="forcePasswordChange">
                                                <label class="form-check-label" for="forcePasswordChange">
                                                    Forcer le changement de mot de passe après 90 jours
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="complexPassword" checked>
                                                <label class="form-check-label" for="complexPassword">
                                                    Exiger des mots de passe complexes
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="twoFactorAuth">
                                                <label class="form-check-label" for="twoFactorAuth">
                                                    Activer l'authentification à deux facteurs
                                                </label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Enregistrer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet À propos -->
                        <div class="tab-pane fade" id="about">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-info-circle"></i> À propos du système</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-4">
                                        <div style="background-color: var(--primary); color: white; padding: 20px; border-radius: 10px; display: inline-block;">
                                            <h2 class="mb-0">YOURHOPE</h2>
                                            <p>Système de Gestion Scolaire</p>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6><i class="fas fa-info-circle text-primary"></i> Informations système</h6>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span>Version</span>
                                                    <span class="badge bg-primary">1.0.0</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span>Développeur</span>
                                                    <span>YOURHOPE Entreprise</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span>Date de sortie</span>
                                                    <span>Octobre 2023</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span>Licence</span>
                                                    <span>Propriétaire</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6><i class="fas fa-server text-primary"></i> Informations techniques</h6>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span>PHP Version</span>
                                                    <span><?php echo phpversion(); ?></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span>Base de données</span>
                                                    <span>MySQL</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span>Serveur</span>
                                                    <span><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span>Version système</span>
                                                    <span>2023.10.01</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <h6><i class="fas fa-feather-alt text-primary"></i> Fonctionnalités principales</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul>
                                                    <li>Gestion complète des élèves</li>
                                                    <li>Gestion des enseignants</li>
                                                    <li>Suivi financier en XAF</li>
                                                    <li>Bulletins de notes</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul>
                                                    <li>Emplois du temps</li>
                                                    <li>Rapports détaillés</li>
                                                    <li>Multi-écoles</li>
                                                    <li>Interface responsive</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-warning mt-4">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Support technique:</strong> contact@yourhope.cg | +242 XX XXX XXXX
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

<script>
// Gestion de la sauvegarde
document.getElementById('backupNow').addEventListener('click', function() {
    const statusDiv = document.getElementById('backupStatus');
    statusDiv.style.display = 'block';
    
    // Simuler la création de sauvegarde
    setTimeout(() => {
        statusDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check"></i> Sauvegarde créée avec succès!</div>';
        setTimeout(() => {
            statusDiv.style.display = 'none';
            statusDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Chargement...</span></div><span class="ms-2">Création de la sauvegarde...</span>';
        }, 3000);
    }, 2000);
});

// Gestion des onglets
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser tous les onglets
    var triggerTabList = [].slice.call(document.querySelectorAll('a[data-bs-toggle="tab"]'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>