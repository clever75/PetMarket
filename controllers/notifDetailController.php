<?php
// controllers/notifDetailController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'vendeur') {
    echo json_encode(['succes' => false, 'message' => 'Non autorise']);
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/commande.php";

$commandeModel = new Commande($pdo);
$sellerId      = (int) $_SESSION['user_id'];

$reservations = array_values(array_filter(
    $commandeModel->getByVendeur($sellerId),
    fn($commande) => $commande['statut'] === 'en_attente'
));

echo json_encode([
    'succes'       => true,
    'reservations' => $reservations,
]);
