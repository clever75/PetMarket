<?php
// controllers/sellerAnimalController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/animal.php";
require_once __DIR__ . "/../models/categorie.php";
require_once __DIR__ . "/../models/commande.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'vendeur') {
    header("Location: /PetMarket/?page=accueil");
    exit();
}

$animalModel    = new Animal($pdo);
$categorieModel = new Categorie($pdo);
$commandeModel  = new Commande($pdo);
$sellerId       = (int)$_SESSION['user_id'];
$erreur         = "";

// ── Vérification profil vendeur complet ──────────────────────
$stmtVendeur = $pdo->prepare(
    "SELECT telephone, ville FROM utilisateur WHERE idUser = :id"
);
$stmtVendeur->execute(['id' => $sellerId]);
$vendeurInfos      = $stmtVendeur->fetch(PDO::FETCH_ASSOC);
$profilVendeurOk   = !empty($vendeurInfos['telephone']) && !empty($vendeurInfos['ville']);

// ── Upload photo ─────────────────────────────────────────────
function traiterPhotoVendeur($champ, &$erreur) {
    if (!isset($_FILES[$champ]) || $_FILES[$champ]['error'] !== 0) {
        $erreur = "Photo obligatoire.";
        return null;
    }
    $extensionsOk = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($_FILES[$champ]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $extensionsOk)) {
        $erreur = "Format invalide.";
        return null;
    }
    $nomFichier  = uniqid("seller_") . "." . $ext;
    $destination = __DIR__ . "/../public/images/" . $nomFichier;
    if (!move_uploaded_file($_FILES[$champ]['tmp_name'], $destination)) {
        $erreur = "Erreur upload.";
        return null;
    }
    return $nomFichier;
}

function resoudreCategorieAnnonce($categorieModel, &$erreur) {
    $idCategorie = (int)($_POST['idCategorie'] ?? 0);
    $nouvelleCategorie = trim($_POST['nouvelleCategorie'] ?? "");

    if ($idCategorie > 0) {
        return $idCategorie;
    }

    if ($nouvelleCategorie === "") {
        $erreur = "Choisissez une categorie ou saisissez une nouvelle categorie.";
        return 0;
    }

    $tailleCategorie = function_exists('mb_strlen') ? mb_strlen($nouvelleCategorie) : strlen($nouvelleCategorie);
    if ($tailleCategorie > 50) {
        $erreur = "La nouvelle categorie ne doit pas depasser 50 caracteres.";
        return 0;
    }

    $categorieExistante = $categorieModel->getByLibelle($nouvelleCategorie);
    if ($categorieExistante) {
        return (int)$categorieExistante['idCategorie'];
    }

    return $categorieModel->create($nouvelleCategorie);
}

// ── Créer une annonce ────────────────────────────────────────
if (isset($_POST['creer'])) {
    // Bloquer si profil incomplet
    if (!$profilVendeurOk) {
        $erreur = "Complétez votre profil (téléphone et ville) avant de publier une annonce. Les acheteurs ont besoin de ces informations pour vous contacter.";
    } else {
        $nom         = trim($_POST['nom'] ?? "");
        $prix        = (int)($_POST['prix'] ?? 0);
        $description = trim($_POST['description'] ?? "");
        $idCategorie = resoudreCategorieAnnonce($categorieModel, $erreur);

        if (empty($erreur) && (empty($nom) || $prix <= 0 || $idCategorie <= 0)) {
            $erreur = "Tous les champs obligatoires doivent être remplis.";
        }

        if (empty($erreur)) {
            $photo = traiterPhotoVendeur("photo", $erreur);
            if ($photo) {
                $animalModel->create($nom, $prix, $description, $photo, $idCategorie, $sellerId);
                $_SESSION['flash_succes'] = "Annonce publiée !";
                header("Location: /PetMarket/?page=seller");
                exit();
            }
        }
    }
}

// ── Modifier une annonce ─────────────────────────────────────
if (isset($_POST['modifier'])) {
    $id          = (int)($_POST['idAnimal'] ?? 0);
    $nom         = trim($_POST['nom'] ?? "");
    $prix        = (int)($_POST['prix'] ?? 0);
    $description = trim($_POST['description'] ?? "");
    $idCategorie = (int)($_POST['idCategorie'] ?? 0);
    $animal      = $animalModel->getById($id);

    if (!$animal || (int)$animal['idUser'] !== $sellerId) {
        $erreur = "Vous ne pouvez pas modifier cette annonce.";
    } elseif (empty($nom) || $prix <= 0 || $idCategorie <= 0) {
        $erreur = "Tous les champs obligatoires doivent être remplis.";
    } else {
        $photo = $animal['photo'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $nouvellePhoto = traiterPhotoVendeur("photo", $erreur);
            if ($nouvellePhoto) $photo = $nouvellePhoto;
        }
        if (empty($erreur)) {
            $animalModel->update($id, $nom, $prix, $description, $photo, $idCategorie);
            $_SESSION['flash_succes'] = "Annonce modifiée.";
            header("Location: /PetMarket/?page=seller");
            exit();
        }
    }
}

// ── Supprimer une annonce ────────────────────────────────────
if (isset($_POST['supprimer'])) {
    $id     = (int)($_POST['idAnimal'] ?? 0);
    $animal = $animalModel->getById($id);
    if (!$animal || (int)$animal['idUser'] !== $sellerId) {
        $erreur = "Vous ne pouvez pas supprimer cette annonce.";
    } else {
        $animalModel->delete($id);
        $_SESSION['flash_succes'] = "Annonce supprimée.";
        header("Location: /PetMarket/?page=seller");
        exit();
    }
}

// ── Confirmer une commande (paiement reçu) ───────────────────
if (isset($_POST['confirmer_commande'])) {
    $idAnimal   = (int)($_POST['idAnimal']   ?? 0);
    $idCommande = (int)($_POST['idCommande'] ?? 0);
    $animal     = $animalModel->getById($idAnimal);

    if (!$animal || (int)$animal['idUser'] !== $sellerId) {
        $_SESSION['flash_erreur'] = "Action non autorisée.";
    } elseif ($animal['statut'] !== 'reserve') {
        $_SESSION['flash_erreur'] = "Seul un animal réservé peut être confirmé.";
    } else {
        try {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE animal SET statut = 'vendu' WHERE idAnimal = :id")
                ->execute(['id' => $idAnimal]);
            $commandeModel->changerStatut($idCommande, 'confirme');
            $pdo->commit();
            $_SESSION['flash_succes'] = "Commande confirmée — animal marqué comme vendu.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash_erreur'] = "Erreur technique. Réessayez.";
        }
    }
    header("Location: /PetMarket/?page=seller");
    exit();
}

// ── Annuler une commande ─────────────────────────────────────
if (isset($_POST['annuler_commande'])) {
    $idAnimal   = (int)($_POST['idAnimal']   ?? 0);
    $idCommande = (int)($_POST['idCommande'] ?? 0);
    $animal     = $animalModel->getById($idAnimal);

    if (!$animal || (int)$animal['idUser'] !== $sellerId) {
        $_SESSION['flash_erreur'] = "Action non autorisée.";
    } else {
        try {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE animal SET statut = 'disponible' WHERE idAnimal = :id")
                ->execute(['id' => $idAnimal]);
            $commandeModel->changerStatut($idCommande, 'annule');
            $pdo->commit();
            $_SESSION['flash_succes'] = "Commande annulée — animal remis en disponible.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash_erreur'] = "Erreur technique. Réessayez.";
        }
    }
    header("Location: /PetMarket/?page=seller");
    exit();
}

// ── Marquer vendu sans commande (fallback) ───────────────────
if (isset($_POST['marquer_vendu'])) {
    $id     = (int)($_POST['idAnimal'] ?? 0);
    $animal = $animalModel->getById($id);

    if (!$animal || (int)$animal['idUser'] !== $sellerId) {
        $_SESSION['flash_erreur'] = "Action non autorisée.";
    } elseif ($animal['statut'] !== 'reserve') {
        $_SESSION['flash_erreur'] = "Seul un animal réservé peut être marqué comme vendu.";
    } else {
        $pdo->prepare("UPDATE animal SET statut = 'vendu' WHERE idAnimal = :id")
            ->execute(['id' => $id]);
        $_SESSION['flash_succes'] = "Animal marqué comme vendu.";
    }
    header("Location: /PetMarket/?page=seller");
    exit();
}

// ── Remettre en disponible (fallback) ────────────────────────
if (isset($_POST['marquer_dispo'])) {
    $id     = (int)($_POST['idAnimal'] ?? 0);
    $animal = $animalModel->getById($id);

    if (!$animal || (int)$animal['idUser'] !== $sellerId) {
        $_SESSION['flash_erreur'] = "Action non autorisée.";
    } else {
        $pdo->prepare("UPDATE animal SET statut = 'disponible' WHERE idAnimal = :id")
            ->execute(['id' => $id]);
        $_SESSION['flash_succes'] = "Animal remis en disponible.";
    }
    header("Location: /PetMarket/?page=seller");
    exit();
}

// ── Données pour la vue ───────────────────────────────────────
$animaux    = $animalModel->getBySeller($sellerId);
$categories = $categorieModel->getAll();

$commandesRecues = $commandeModel->getByVendeur($sellerId);
$commandesParAnimal = [];
foreach ($commandesRecues as $cmd) {
    if ($cmd['statut'] === 'en_attente') {
        $commandesParAnimal[(int)$cmd['idAnimal']] = $cmd;
    }
}

$animauxDisponibles = array_filter($animaux, fn($a) => $a['statut'] === 'disponible');
$animauxReserves    = array_filter($animaux, fn($a) => $a['statut'] === 'reserve');
$animauxVendus      = array_filter($animaux, fn($a) => $a['statut'] === 'vendu');

require_once __DIR__ . "/../views/seller/animals.php";
