<?php
// controllers/delete_role.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['role_id'])) {
        $_SESSION['error'] = "ID rôle manquant.";
        header('Location: ../roles.php');
        exit();
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Vérifier que le rôle n'est pas un rôle système
    $check_query = "SELECT is_system_role FROM roles WHERE id = :id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':id', $_POST['role_id']);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        $role = $check_stmt->fetch(PDO::FETCH_ASSOC);
        if ($role['is_system_role']) {
            $_SESSION['error'] = "Impossible de supprimer un rôle système.";
            header('Location: ../roles.php');
            exit();
        }
    }
    
    // Vérifier si des utilisateurs utilisent ce rôle
    $users_query = "SELECT COUNT(*) as user_count FROM school_admins WHERE role_id = :role_id";
    $users_stmt = $db->prepare($users_query);
    $users_stmt->bindParam(':role_id', $_POST['role_id']);
    $users_stmt->execute();
    $user_count = $users_stmt->fetch(PDO::FETCH_ASSOC)['user_count'];
    
    if ($user_count > 0) {
        $_SESSION['error'] = "Ce rôle est utilisé par $user_count utilisateur(s). Réassignez-les avant de supprimer.";
        header('Location: ../roles.php');
        exit();
    }
    
    // Supprimer le rôle
    $query = "DELETE FROM roles WHERE id = :id AND (school_id IS NULL OR school_id = :school_id)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_POST['role_id']);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Rôle supprimé avec succès!";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression du rôle.";
    }
    
    header('Location: ../roles.php');
    exit();
}
?>