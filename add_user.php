<?php
// controllers/add_user.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validation des données
    $required_fields = ['full_name', 'username', 'password', 'role'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = "Le champ '$field' est requis.";
            header('Location: ../users.php');
            exit();
        }
    }
    
    // Connexion à la base
    $database = new Database();
    $db = $database->getConnection();
    
    // Vérifier si l'utilisateur existe déjà
    $check_query = "SELECT id FROM school_admins WHERE username = :username";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':username', $_POST['username']);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        $_SESSION['error'] = "Ce nom d'utilisateur existe déjà.";
        header('Location: ../users.php');
        exit();
    }
    
    // Hasher le mot de passe
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Insérer l'utilisateur
    $query = "INSERT INTO school_admins 
              (school_id, username, password, full_name, email, phone, role, status) 
              VALUES 
              (:school_id, :username, :password, :full_name, :email, :phone, :role, 'active')";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    $stmt->bindParam(':username', $_POST['username']);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':full_name', $_POST['full_name']);
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->bindParam(':phone', $_POST['phone']);
    $stmt->bindParam(':role', $_POST['role']);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Utilisateur créé avec succès!";
    } else {
        $_SESSION['error'] = "Erreur lors de la création de l'utilisateur.";
    }
    
    header('Location: ../users.php');
    exit();
}
?>