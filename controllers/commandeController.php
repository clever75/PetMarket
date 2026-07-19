<?php
// controllers/commandeController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['succes' => false, 'message' => 'Non connecté']);
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/panier.php";
require_once __DIR__ . "/../models/commande.php";

$panierModel   = new Panier($pdo);
$commandeModel = new Commande($pdo);
$idUser        = (int)$_SESSION['user_id'];

// ── Vérification profil complet ───────────────────────────────
$stmtUser = $pdo->prepare("SELECT telephone, ville FROM utilisateur WHERE idUser = :id");
$stmtUser->execute(['id' => $idUser]);
$userInfos = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (empty($userInfos['telephone'])) {
    echo json_encode([
        'succes'           => false,
        'profil_incomplet' => true,
        'message'          => 'Ajoutez votre numéro de téléphone dans votre profil avant de commander — le vendeur en a besoin pour vous contacter.',
    ]);
    exit();
}

if (empty($userInfos['ville'])) {
    echo json_encode([
        'succes'           => false,
        'profil_incomplet' => true,
        'message'          => 'Ajoutez votre ville dans votre profil avant de commander.',
    ]);
    exit();
}

// ── Panier non vide ───────────────────────────────────────────
$articles = $panierModel->getByUser($idUser);

if (empty($articles)) {
    echo json_encode(['succes' => false, 'message' => 'Votre panier est vide.']);
    exit();
}

// ── Transaction ───────────────────────────────────────────────
try {
    $pdo->beginTransaction();

    foreach ($articles as $article) {
        $idAnimal = (int)$article['idAnimal'];
        $prix     = (int)$article['prix'];

        $stmt = $pdo->prepare("SELECT statut FROM animal WHERE idAnimal = :id FOR UPDATE");
        $stmt->execute(['id' => $idAnimal]);
        $animal = $stmt->fetch();

        if (!$animal || $animal['statut'] !== 'disponible') {
            continue;
        }

        $pdo->prepare("UPDATE animal SET statut = 'reserve' WHERE idAnimal = :id")
            ->execute(['id' => $idAnimal]);

        $commandeModel->creer($idUser, $idAnimal, $prix);
    }

    $panierModel->vider($idUser);
    $_SESSION['panier'] = [];

    $pdo->commit();

    echo json_encode(['succes' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['succes' => false, 'message' => 'Erreur technique. Réessayez.']);
}