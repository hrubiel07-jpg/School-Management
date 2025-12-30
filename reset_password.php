<?php
// controllers/reset_password.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['user_id']) || empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
        $_SESSION['error'] = "Tous les champs sont requis.";
        header('Location: ../users.php');
        exit();
    }
    
    if ($_POST['new_password'] !== $_POST['confirm_password']) {
        $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
        header('Location: ../users.php');
        exit();
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Hasher le nouveau mot de passe
    $hashed_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    
    // Mettre à jour le mot de passe
    $query = "UPDATE school_admins SET password = :password WHERE id = :id AND school_id = :school_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':id', $_POST['user_id']);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Mot de passe réinitialisé avec succès!";
    } else {
        $_SESSION['error'] = "Erreur lors de la réinitialisation.";
    }
    
    header('Location: ../users.php');
    exit();
}
?>