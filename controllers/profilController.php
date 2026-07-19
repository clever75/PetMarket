<?php
// controllers/profilController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: /PetMarket/?page=login");
    exit();
}
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/user.php";
require_once __DIR__ . "/../models/commande.php";

$userModel     = new User($pdo);
$commandeModel = new Commande($pdo);
$idUser        = (int)$_SESSION['user_id'];
$erreur        = "";

// ── Modifier les infos du profil ─────────────────────────────
if (isset($_POST['modifier_profil'])) {
    $nom       = trim($_POST['nom']       ?? "");
    $pseudo    = trim($_POST['pseudo']    ?? "");
    $telephone = trim($_POST['telephone'] ?? "");
    $ville     = trim($_POST['ville']     ?? "");

    if (empty($nom)) {
        $erreur = "Le nom est obligatoire.";
    } else {
        $stmt = $pdo->prepare(
            "UPDATE utilisateur
             SET nom = :nom, pseudo = :pseudo, telephone = :telephone, ville = :ville
             WHERE idUser = :id"
        );
        $stmt->execute([
            'nom'       => $nom,
            'pseudo'    => $pseudo ?: $nom,
            'telephone' => $telephone,
            'ville'     => $ville,
            'id'        => $idUser,
        ]);
        $_SESSION['user_nom']      = $nom;
        $_SESSION['user_pseudo']   = $pseudo ?: $nom;
        $_SESSION['user_telephone']= $telephone;
        $_SESSION['user_ville']    = $ville;
        $_SESSION['flash_succes']  = "Profil mis à jour !";
        header("Location: /PetMarket/?page=profil");
        exit();
    }
}

// ── Changer le mot de passe ───────────────────────────────────
if (isset($_POST['changer_mdp'])) {
    $ancien  = $_POST['ancien_mdp']  ?? "";
    $nouveau = $_POST['nouveau_mdp'] ?? "";
    $confirm = $_POST['confirm_mdp'] ?? "";
    $user    = $userModel->getById($idUser);

    if (!password_verify($ancien, $user['password'])) {
        $erreur = "Ancien mot de passe incorrect.";
    } elseif (empty($nouveau) || strlen($nouveau) < 6) {
        $erreur = "Le nouveau mot de passe doit faire au moins 6 caractères.";
    } elseif ($nouveau !== $confirm) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        $hash = password_hash($nouveau, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE utilisateur SET password = :password WHERE idUser = :id")
            ->execute(['password' => $hash, 'id' => $idUser]);
        $_SESSION['flash_succes'] = "Mot de passe changé !";
        header("Location: /PetMarket/?page=profil");
        exit();
    }
}

// ── Demande vendeur depuis le profil ─────────────────────────
if (isset($_POST['demander_vendeur'])) {
    $role = $_SESSION['user_role'] ?? 'acheteur';
    if ($role === 'acheteur') {
        $pdo->prepare(
            "UPDATE utilisateur SET seller_request = 1 WHERE idUser = :id"
        )->execute(['id' => $idUser]);
        $_SESSION['flash_succes'] = "Demande envoyée ! Un administrateur va valider votre compte vendeur.";
        header("Location: /PetMarket/?page=profil");
        exit();
    }
}

// ── Données pour la vue ───────────────────────────────────────
$user      = $userModel->getById($idUser);
$commandes = $commandeModel->getByUser($idUser);

require_once __DIR__ . "/../views/profil/profil.php";