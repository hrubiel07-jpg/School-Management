<?php
// controllers/add_role.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['role_name'])) {
        $_SESSION['error'] = "Le nom du rôle est requis.";
        header('Location: ../roles.php');
        exit();
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Vérifier si le rôle existe déjà pour cette école
    $check_query = "SELECT id FROM roles WHERE role_name = :role_name AND (school_id IS NULL OR school_id = :school_id)";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':role_name', $_POST['role_name']);
    $check_stmt->bindParam(':school_id', $_SESSION['school_id']);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        $_SESSION['error'] = "Ce nom de rôle existe déjà.";
        header('Location: ../roles.php');
        exit();
    }
    
    // Préparer les permissions
    $permissions = $_POST['permissions'] ?? [];
    $permissions_json = json_encode($permissions);
    
    // Insérer le rôle
    $query = "INSERT INTO roles (school_id, role_name, role_description, permissions) 
              VALUES (:school_id, :role_name, :role_description, :permissions)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    $stmt->bindParam(':role_name', $_POST['role_name']);
    $stmt->bindParam(':role_description', $_POST['role_description']);
    $stmt->bindParam(':permissions', $permissions_json);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Rôle créé avec succès!";
    } else {
        $_SESSION['error'] = "Erreur lors de la création du rôle.";
    }
    
    header('Location: ../roles.php');
    exit();
}
?>