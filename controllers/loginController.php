<?php
// controllers/loginController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: /PetMarket/?page=accueil");
    exit();
}

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/app.php";
require_once __DIR__ . "/../models/user.php";
require_once __DIR__ . "/../models/panier.php";

$userModel   = new User($pdo);
$panierModel = new Panier($pdo);
$erreur      = "";
$ongletActif = "connexion";
$inscriptionValues = [
    'nom'       => '',
    'pseudo'    => '',
    'email'     => '',
    'telephone' => '',
    'ville'     => '',
    'role'      => 'acheteur',
];

// ── Connexion ────────────────────────────────────────────────
if (isset($_POST['connexion'])) {
    $ongletActif = "connexion";
    $email      = trim($_POST['email'] ?? "");
    $motDePasse = $_POST['password'] ?? "";

    if (empty($email) || empty($motDePasse)) {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        $user = $userModel->connecter($email, $motDePasse);
        if ($user) {
            $_SESSION['user_id']       = $user['idUser'];
            $_SESSION['user_nom']      = $user['nom'];
            $_SESSION['user_pseudo']   = $user['pseudo'] ?? $user['nom'];
            $_SESSION['user_email']    = $user['email'];
            $_SESSION['user_role']     = $user['role'] ?? 'acheteur';
            $_SESSION['user_telephone']= $user['telephone'] ?? '';
            $_SESSION['user_ville']    = $user['ville'] ?? '';
            $_SESSION['user_is_admin'] = (strtolower($user['email']) === strtolower(ADMIN_EMAIL));

            $items = $panierModel->getByUser($_SESSION['user_id']);
            $_SESSION['panier'] = [];
            foreach ($items as $item) {
                $_SESSION['panier'][$item['idAnimal']] = (int)$item['quantite'];
            }

            if ($_SESSION['user_is_admin']) {
                header("Location: /PetMarket/?page=admin");
            } elseif ($_SESSION['user_role'] === 'vendeur') {
                header("Location: /PetMarket/?page=seller");
            } else {
                header("Location: /PetMarket/?page=accueil");
            }
            exit();
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    }
}

// ── Inscription ──────────────────────────────────────────────
if (isset($_POST['inscription'])) {
    $ongletActif = "inscription";
    $nom        = trim($_POST['nom']        ?? "");
    $pseudo     = trim($_POST['pseudo']     ?? "");
    $email      = trim($_POST['email']      ?? "");
    $telephone  = trim($_POST['telephone']  ?? "");
    $ville      = trim($_POST['ville']      ?? "");
    $motDePasse = $_POST['password']        ?? "";
    $role       = $_POST['role']            ?? "acheteur";
    $demandeVendeur = ($role === 'vendeur');
    $inscriptionValues = [
        'nom'       => $nom,
        'pseudo'    => $pseudo,
        'email'     => $email,
        'telephone' => $telephone,
        'ville'     => $ville,
        'role'      => $role,
    ];

    // Validations
    if (empty($nom) || empty($email) || empty($motDePasse) || empty($telephone) || empty($ville)) {
        $erreur = "Veuillez remplir tous les champs obligatoires (y compris téléphone et ville).";
    } elseif (!preg_match('/^[0-9\s\+\-]{6,20}$/', $telephone)) {
        $erreur = "Numéro de téléphone invalide.";
    } elseif (strlen($motDePasse) < 6) {
        $erreur = "Le mot de passe doit faire au moins 6 caractères.";
    } elseif ($userModel->emailExiste($email)) {
        $erreur = "Cet email est déjà utilisé.";
    } else {
        $idUser = $userModel->inscrire($nom, $pseudo, $email, $motDePasse, $role, $telephone, $ville);
        if ($idUser) {
            $estAdmin = (strtolower($email) === strtolower(ADMIN_EMAIL));

            if ($demandeVendeur && !$estAdmin) {
                $_SESSION['flash_succes'] = "Pour activer votre compte vendeur, envoyez 5 000 FCFA via Flooz/T-Money au +228 XX XX XX XX en indiquant votre email comme référence. Activation sous 24h.";
                header("Location: /PetMarket/?page=login");
                exit();
            }

            $_SESSION['user_id']        = $idUser;
            $_SESSION['user_nom']       = $nom;
            $_SESSION['user_pseudo']    = $pseudo ?: $nom;
            $_SESSION['user_email']     = $email;
            $_SESSION['user_role']      = 'acheteur';
            $_SESSION['user_telephone'] = $telephone;
            $_SESSION['user_ville']     = $ville;
            $_SESSION['user_is_admin']  = $estAdmin;
            $_SESSION['panier']         = [];

            if ($estAdmin) {
                header("Location: /PetMarket/?page=admin");
            } else {
                $_SESSION['flash_succes'] = "Bienvenue sur PetMarket, " . htmlspecialchars($pseudo ?: $nom) . " !";
                header("Location: /PetMarket/?page=accueil");
            }
            exit();
        } else {
            $erreur = "Une erreur est survenue. Réessayez.";
        }
    }
}

require __DIR__ . "/../views/layout/login.php";
