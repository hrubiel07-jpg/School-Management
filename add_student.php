<?php
// controllers/add_student.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validation des données
    $required_fields = ['last_name', 'first_name', 'gender', 'class_id', 'parent_phone'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = "Le champ '$field' est requis.";
            header('Location: ../students.php');
            exit();
        }
    }
    
    // Connexion à la base
    $database = new Database();
    $db = $database->getConnection();
    
    // Générer un code élève unique
    $school_code = $_SESSION['school_code'] ?? 'ECO';
    $year = date('y');
    $random = mt_rand(1000, 9999);
    $student_code = $school_code . '-ELV-' . $year . '-' . $random;
    
    // Insérer l'élève
    $query = "INSERT INTO students 
              (school_id, student_code, first_name, last_name, gender, birth_date, birth_place,
               class_id, father_name, mother_name, parent_phone, parent_email, address,
               enrollment_date, status) 
              VALUES 
              (:school_id, :student_code, :first_name, :last_name, :gender, :birth_date, :birth_place,
               :class_id, :father_name, :mother_name, :parent_phone, :parent_email, :address,
               :enrollment_date, :status)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    $stmt->bindParam(':student_code', $student_code);
    $stmt->bindParam(':first_name', $_POST['first_name']);
    $stmt->bindParam(':last_name', $_POST['last_name']);
    $stmt->bindParam(':gender', $_POST['gender']);
    $stmt->bindParam(':birth_date', $_POST['birth_date']);
    $stmt->bindParam(':birth_place', $_POST['birth_place']);
    $stmt->bindParam(':class_id', $_POST['class_id']);
    $stmt->bindParam(':father_name', $_POST['father_name']);
    $stmt->bindParam(':mother_name', $_POST['mother_name']);
    $stmt->bindParam(':parent_phone', $_POST['parent_phone']);
    $stmt->bindParam(':parent_email', $_POST['parent_email']);
    $stmt->bindParam(':address', $_POST['address']);
    $stmt->bindParam(':enrollment_date', $_POST['enrollment_date']);
    $stmt->bindParam(':status', $_POST['status']);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Élève créé avec succès! Code: $student_code";
    } else {
        $_SESSION['error'] = "Erreur lors de la création de l'élève.";
    }
    
    header('Location: ../students.php');
    exit();
}
?>