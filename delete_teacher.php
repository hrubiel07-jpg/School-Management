<?php
// controllers/delete_teacher.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['teacher_id'])) {
        $_SESSION['error'] = "ID enseignant manquant.";
        header('Location: ../teachers.php');
        exit();
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Vérifier si l'enseignant est assigné à des classes
    $check_query = "SELECT COUNT(*) as count FROM classes WHERE teacher_id = :teacher_id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':teacher_id', $_POST['teacher_id']);
    $check_stmt->execute();
    $assignment_count = $check_stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($assignment_count > 0) {
        $_SESSION['error'] = "Cet enseignant est assigné à $assignment_count classe(s). Réassignez d'abord les classes.";
        header('Location: ../teachers.php');
        exit();
    }
    
    // Supprimer l'enseignant
    $query = "DELETE FROM teachers WHERE id = :id AND school_id = :school_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_POST['teacher_id']);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Enseignant supprimé avec succès!";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression.";
    }
    
    header('Location: ../teachers.php');
    exit();
}
?>