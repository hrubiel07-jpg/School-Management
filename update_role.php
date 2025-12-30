<?php
// controllers/update_role.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['role_id']) || empty($_POST['role_name'])) {
        $_SESSION['error'] = "Données manquantes.";
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
            $_SESSION['error'] = "Impossible de modifier un rôle système.";
            header('Location: ../roles.php');
            exit();
        }
    }
    
    // Préparer les permissions
    $permissions = $_POST['permissions'] ?? [];
    $permissions_json = json_encode($permissions);
    
    // Mettre à jour le rôle
    $query = "UPDATE roles SET 
              role_name = :role_name,
              role_description = :role_description,
              permissions = :permissions
              WHERE id = :id AND (school_id IS NULL OR school_id = :school_id)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':role_name', $_POST['role_name']);
    $stmt->bindParam(':role_description', $_POST['role_description']);
    $stmt->bindParam(':permissions', $permissions_json);
    $stmt->bindParam(':id', $_POST['role_id']);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Rôle mis à jour avec succès!";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour du rôle.";
    }
    
    header('Location: ../roles.php');
    exit();
}
?>