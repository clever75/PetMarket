<?php
// ============================================================
//  ROUTEUR PRINCIPAL — PetMarket
//  Toutes les requêtes passent par ici
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/config/app.php";

// Page demandée (défaut : accueil)
$page = $_GET['page'] ?? 'accueil';

// ============================================================
//  TABLE DE ROUTAGE
// ============================================================
$routes = [

    // ── Pages publiques ──────────────────────────────────────
    'accueil'      => 'views/acceuil/index.php',
    'about'        => 'views/acceuil/about.php',
    'catalogue'    => 'controllers/animalController.php',
    'detail'       => 'views/catalogue/detail.php',
    'login'        => 'controllers/loginController.php',
    'logout'       => 'controllers/logout.php',

    // ── Pages connectées ─────────────────────────────────────
    'profil'       => 'controllers/profilController.php',

    // ── Vendeur ──────────────────────────────────────────────
    'seller'       => 'controllers/sellerAnimalController.php',

    // ── Admin ────────────────────────────────────────────────
    'admin'        => 'controllers/adminAnimalController.php',

    // ── Actions AJAX (répondent en JSON) ─────────────────────
    'panier'       => 'controllers/panierController.php',
    'commande'     => 'controllers/commandeController.php',

    // ── Notifications vendeur (AJAX) ─────────────────────────
    'notif'        => 'controllers/notifController.php',
    'notif_detail' => 'controllers/notifDetailController.php',
];

// ============================================================
//  SÉCURITÉ — pages réservées aux connectés
// ============================================================
$pagesConnecte = ['profil', 'panier', 'commande'];
$pagesVendeur  = ['seller', 'notif', 'notif_detail'];
$pagesAdmin    = ['admin'];

if (in_array($page, $pagesConnecte) && !isset($_SESSION['user_id'])) {
    header("Location: /PetMarket/?page=login");
    exit();
}

if (in_array($page, $pagesVendeur)) {
    if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'vendeur') {
        // Si c'est une requête AJAX → répondre en JSON
        if (in_array($page, ['notif', 'notif_detail'])) {
            header('Content-Type: application/json');
            echo json_encode(['succes' => false, 'message' => 'Non autorisé']);
        } else {
            header("Location: /PetMarket/?page=accueil");
        }
        exit();
    }
}

if (in_array($page, $pagesAdmin)) {
    if (!isset($_SESSION['user_id']) || !($_SESSION['user_is_admin'] ?? false)) {
        header("Location: /PetMarket/?page=login");
        exit();
    }
}

// ============================================================
//  CHARGEMENT DE LA PAGE
// ============================================================
if (isset($routes[$page])) {
    require_once __DIR__ . "/" . $routes[$page];
} else {
    // Page introuvable → 404
    http_response_code(404);
    require_once __DIR__ . "/views/acceuil/index.php";
}