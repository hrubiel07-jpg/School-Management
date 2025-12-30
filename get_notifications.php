<?php
// controllers/get_notifications.php
session_start();
header('Content-Type: application/json');

$notifications = [
    [
        'type' => 'success',
        'icon' => 'fa-user-plus',
        'message' => '5 nouveaux élèves inscrits',
        'time' => 'Aujourd\'hui'
    ],
    [
        'type' => 'warning',
        'icon' => 'fa-money-bill',
        'message' => '3 paiements en attente',
        'time' => 'Hier'
    ],
    [
        'type' => 'info',
        'icon' => 'fa-birthday-cake',
        'message' => '2 anniversaires aujourd\'hui',
        'time' => 'Aujourd\'hui'
    ],
    [
        'type' => 'danger',
        'icon' => 'fa-exclamation-triangle',
        'message' => '1 absence non justifiée',
        'time' => 'Hier'
    ],
    [
        'type' => 'success',
        'icon' => 'fa-check-circle',
        'message' => 'Système mis à jour avec succès',
        'time' => 'Il y a 2 jours'
    ]
];

echo json_encode([
    'success' => true,
    'notifications' => $notifications
]);
?>