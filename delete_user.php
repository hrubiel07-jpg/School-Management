<?php
// controllers/delete_user.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['user_id'])) {
        $_SESSION['error'] = "ID utilisateur manquant.";
        header('Location: ../users.php');
        exit();
    }
    
    // Empêcher la suppression de son propre compte
    if ($_POST['user_id'] == $_SESSION['user_id']) {
        $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte.";
        header('Location: ../users.php');
        exit();
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Supprimer l'utilisateur
    $query = "DELETE FROM school_admins WHERE id = :id AND school_id = :school_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_POST['user_id']);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Utilisateur supprimé avec succès!";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression.";
    }
    
    header('Location: ../users.php');
    exit();
}
?>