<?php
// controllers/export_teachers.php
session_start();
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$format = $_GET['format'] ?? 'excel';

// Récupérer les enseignants
$query = "SELECT * FROM teachers WHERE school_id = :school_id ORDER BY last_name, first_name";
$stmt = $db->prepare($query);
$stmt->bindParam(':school_id', $_SESSION['school_id']);
$stmt->execute();
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($format == 'excel') {
    // En-têtes pour Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="enseignants_' . date('Y-m-d') . '.xls"');
    
    echo "<table border='1'>";
    echo "<tr>";
    echo "<th>Code</th>";
    echo "<th>Nom</th>";
    echo "<th>Prénom</th>";
    echo "<th>Genre</th>";
    echo "<th>Spécialisation</th>";
    echo "<th>Diplôme</th>";
    echo "<th>Téléphone</th>";
    echo "<th>Email</th>";
    echo "<th>Date d'embauche</th>";
    echo "<th>Salaire (XAF)</th>";
    echo "<th>Statut</th>";
    echo "</tr>";
    
    foreach ($teachers as $teacher) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($teacher['teacher_code']) . "</td>";
        echo "<td>" . htmlspecialchars($teacher['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($teacher['first_name']) . "</td>";
        echo "<td>" . ($teacher['gender'] == 'M' ? 'M' : 'F') . "</td>";
        echo "<td>" . htmlspecialchars($teacher['specialization'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($teacher['diploma'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($teacher['phone'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($teacher['email'] ?? '') . "</td>";
        echo "<td>" . date('d/m/Y', strtotime($teacher['hire_date'])) . "</td>";
        echo "<td>" . number_format($teacher['salary'], 0, ',', ' ') . "</td>";
        echo "<td>" . ($teacher['status'] == 'active' ? 'Actif' : 'Inactif') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} elseif ($format == 'pdf') {
    // Pour PDF, vous utiliseriez une bibliothèque comme TCPDF ou mPDF
    // Voici un exemple simplifié
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="enseignants_' . date('Y-m-d') . '.pdf"');
    
    // Dans une version réelle, générez un PDF propre
    echo "<h1>Liste des Enseignants</h1>";
    echo "<p>Date: " . date('d/m/Y') . "</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Nom</th><th>Spécialisation</th><th>Téléphone</th><th>Statut</th></tr>";
    
    foreach ($teachers as $teacher) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($teacher['last_name'] . ' ' . $teacher['first_name']) . "</td>";
        echo "<td>" . htmlspecialchars($teacher['specialization'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($teacher['phone'] ?? '') . "</td>";
        echo "<td>" . ($teacher['status'] == 'active' ? 'Actif' : 'Inactif') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}
?>