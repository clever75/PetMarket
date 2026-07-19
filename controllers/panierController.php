<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/panier.php";
require_once __DIR__ . "/../models/animal.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['succes' => false, 'message' => 'Non connecté']);
    exit();
}

$panierModel = new Panier($pdo);
$animalModel = new Animal($pdo);
$idUser      = (int)$_SESSION['user_id'];
$action      = $_POST['action'] ?? '';
$idAnimal    = (int)($_POST['idAnimal'] ?? 0);

if ($idAnimal <= 0) {
    echo json_encode(['succes' => false, 'message' => 'Animal invalide']);
    exit();
}

if ($action === 'ajouter') {
    // Quantite = 1 toujours, on utilise setQuantite pour éviter les doublons
    $panierModel->setQuantite($idUser, $idAnimal, 1);
    if (!isset($_SESSION['panier'])) $_SESSION['panier'] = [];
    $_SESSION['panier'][$idAnimal] = 1;

} elseif ($action === 'supprimer') {
    $panierModel->supprimer($idUser, $idAnimal);
    if (isset($_SESSION['panier'][$idAnimal])) {
        unset($_SESSION['panier'][$idAnimal]);
    }

} elseif ($action === 'vider') {
    $panierModel->vider($idUser);
    $_SESSION['panier'] = [];
}

// Recalculer le total depuis la base
$articles  = $panierModel->getByUser($idUser);
$totalQte  = count($articles);       // nb d'animaux (pas de quantite)
$totalPrix = 0;
foreach ($articles as $a) {
    $totalPrix += (int)$a['prix'];   // 1 animal = 1 prix
}

echo json_encode([
    'succes'     => true,
    'total'      => $totalQte,
    'total_prix' => $totalPrix
]);