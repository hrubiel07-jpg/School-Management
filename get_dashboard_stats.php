<?php
// controllers/get_dashboard_stats.php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$response = [
    'success' => true,
    'students' => [
        'total' => 0,
        'growth' => 5.2
    ],
    'teachers' => [
        'total' => 0
    ],
    'payments' => [
        'total' => 0,
        'growth' => 12.5
    ],
    'classes' => [
        'total' => 0
    ]
];

try {
    // Total élèves
    $query = "SELECT COUNT(*) as total FROM students WHERE school_id = :school_id AND status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    $stmt->execute();
    $response['students']['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total enseignants
    $query = "SELECT COUNT(*) as total FROM teachers WHERE school_id = :school_id AND status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    $stmt->execute();
    $response['teachers']['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total paiements ce mois-ci
    $current_month = date('Y-m');
    $query = "SELECT SUM(amount_paid) as total FROM payments 
              WHERE school_id = :school_id AND DATE_FORMAT(payment_date, '%Y-%m') = :current_month";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    $stmt->bindParam(':current_month', $current_month);
    $stmt->execute();
    $response['payments']['total'] = (float)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // Total classes
    $query = "SELECT COUNT(*) as total FROM classes WHERE school_id = :school_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':school_id', $_SESSION['school_id']);
    $stmt->execute();
    $response['classes']['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

} catch (PDOException $e) {
    $response['success'] = false;
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>