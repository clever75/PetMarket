<?php
// controllers/notifController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Seuls les vendeurs peuvent interroger cet endpoint
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'vendeur') {
    echo json_encode(['succes' => false]);
    exit();
}

require_once __DIR__ . "/../config/database.php";

$sellerId = (int)$_SESSION['user_id'];

// Compter les animaux réservés appartenant à ce vendeur
// avec une commande en_attente associée
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS nb
    FROM commande c
    JOIN animal a ON a.idAnimal = c.idAnimal
    WHERE a.idUser   = :sellerId
      AND c.statut   = 'en_attente'
      AND a.statut   = 'reserve'
");
$stmt->execute(['sellerId' => $sellerId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$nb  = (int)($row['nb'] ?? 0);

// Dernière commande reçue (pour savoir si c'est nouveau)
$stmtLast = $pdo->prepare("
    SELECT MAX(c.date_commande) AS derniere
    FROM commande c
    JOIN animal a ON a.idAnimal = c.idAnimal
    WHERE a.idUser = :sellerId
      AND c.statut = 'en_attente'
");
$stmtLast->execute(['sellerId' => $sellerId]);
$last = $stmtLast->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'succes'   => true,
    'nb'       => $nb,
    'derniere' => $last['derniere'] ?? null,
]);