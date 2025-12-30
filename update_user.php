<?php
// controllers/update_user.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['user_id'])) {
        $_SESSION['error'] = "ID utilisateur manquant.";
        header('Location: ../users.php');
        exit();
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Vérifier les permissions (ne pas permettre de modifier son propre rôle)
    if ($_POST['user_id'] == $_SESSION['user_id'] && isset($_POST['role'])) {
        $_SESSION['error'] = "Vous ne pouvez pas modifier votre propre rôle.";
        header('Location: ../users.php');
        exit();
    }
    
    // Préparer la requête de mise à jour
    $query = "UPDATE school_admins SET 
              full_name = :full_name,
              username = :username,
              email = :email,
              phone = :phone,
              role = :role,
              status = :status
              WHERE id = :id AND school_id = :school_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':full_name', $_POST['full_name']);
    $stmt->bindParam(':username', $_POST['username']);
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->bindParam(':phone', $_POST['phone']);
    $stmt->bindParam(':role', $_POST['role']);
    $stmt->bindParam(':status', $_POST['status']);
    $stmt->bindParam(':id', $_POST['user_id']);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Utilisateur mis à jour avec succès!";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour.";
    }
    
    header('Location: ../users.php');
    exit();
}
?>