<?php
// controllers/update_teacher.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['teacher_id'])) {
        $_SESSION['error'] = "ID enseignant manquant.";
        header('Location: ../teachers.php');
        exit();
    }
    
    // Validation des données
    $required_fields = ['last_name', 'first_name', 'gender', 'specialization', 'phone'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = "Le champ '$field' est requis.";
            header('Location: ../teachers.php');
            exit();
        }
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Mettre à jour l'enseignant
    $query = "UPDATE teachers SET 
              first_name = :first_name,
              last_name = :last_name,
              gender = :gender,
              specialization = :specialization,
              diploma = :diploma,
              phone = :phone,
              email = :email,
              address = :address,
              hire_date = :hire_date,
              salary = :salary,
              status = :status
              WHERE id = :id AND school_id = :school_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':first_name', $_POST['first_name']);
    $stmt->bindParam(':last_name', $_POST['last_name']);
    $stmt->bindParam(':gender', $_POST['gender']);
    $stmt->bindParam(':specialization', $_POST['specialization']);
    $stmt->bindParam(':diploma', $_POST['diploma']);
    $stmt->bindParam(':phone', $_POST['phone']);
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->bindParam(':address', $_POST['address']);
    $stmt->bindParam(':hire_date', $_POST['hire_date']);
    $stmt->bindParam(':salary', $_POST['salary']);
    $stmt->bindParam(':status', $_POST['status']);
    $stmt->bindParam(':id', $_POST['teacher_id']);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Enseignant mis à jour avec succès!";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour.";
    }
    
    header('Location: ../teachers.php');
    exit();
}
?>