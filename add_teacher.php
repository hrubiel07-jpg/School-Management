<?php
// controllers/add_teacher.php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validation des données
    $required_fields = ['last_name', 'first_name', 'gender', 'specialization', 'phone'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = "Le champ '$field' est requis.";
            header('Location: ../teachers.php');
            exit();
        }
    }
    
    // Connexion à la base
    $database = new Database();
    $db = $database->getConnection();
    
    // Générer un code enseignant unique
    $school_code = $_SESSION['school_code'] ?? 'ECO';
    $year = date('y');
    $random = mt_rand(1000, 9999);
    $teacher_code = $school_code . '-ENS-' . $year . '-' . $random;
    
    // Insérer l'enseignant
    $query = "INSERT INTO teachers 
              (school_id, teacher_code, first_name, last_name, gender, specialization,
               diploma, phone, email, address, hire_date, salary, status) 
              VALUES 
              (:school_id, :teacher_code, :first_name, :last_name, :gender, :specialization,
               :diploma, :phone, :email, :address, :hire_date, :salary, :status)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    $stmt->bindParam(':teacher_code', $teacher_code);
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
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Enseignant créé avec succès! Code: $teacher_code";
        
        // Optionnel : Créer automatiquement un compte utilisateur
        if (isset($_POST['create_account']) && $_POST['create_account'] == '1') {
            createTeacherAccount($db, $teacher_code, $_POST);
        }
    } else {
        $_SESSION['error'] = "Erreur lors de la création de l'enseignant.";
    }
    
    header('Location: ../teachers.php');
    exit();
}

function createTeacherAccount($db, $teacher_code, $data) {
    // Générer un nom d'utilisateur
    $username = strtolower(substr($data['first_name'], 0, 1) . $data['last_name']);
    $username = preg_replace('/[^a-z0-9]/', '', $username);
    
    // Vérifier si l'utilisateur existe déjà
    $check_query = "SELECT id FROM school_admins WHERE username = :username";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':username', $username);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        $username = $username . mt_rand(10, 99);
    }
    
    // Mot de passe par défaut
    $password = password_hash('enseignant123', PASSWORD_DEFAULT);
    
    // Récupérer l'ID du rôle "teacher"
    $role_query = "SELECT id FROM roles WHERE role_name = 'teacher' LIMIT 1";
    $role_stmt = $db->prepare($role_query);
    $role_stmt->execute();
    $role_id = $role_stmt->fetch(PDO::FETCH_ASSOC)['id'] ?? 1;
    
    // Créer le compte
    $account_query = "INSERT INTO school_admins 
                      (school_id, username, password, full_name, email, phone, role_id, status) 
                      VALUES 
                      (:school_id, :username, :password, :full_name, :email, :phone, :role_id, 'active')";
    
    $account_stmt = $db->prepare($account_query);
    $account_stmt->bindParam(':school_id', $_SESSION['school_id']);
    $account_stmt->bindParam(':username', $username);
    $account_stmt->bindParam(':password', $password);
    $account_stmt->bindParam(':full_name', $data['first_name'] . ' ' . $data['last_name']);
    $account_stmt->bindParam(':email', $data['email']);
    $account_stmt->bindParam(':phone', $data['phone']);
    $account_stmt->bindParam(':role_id', $role_id);
    
    return $account_stmt->execute();
}
?>