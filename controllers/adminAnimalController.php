<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/app.php";
require_once __DIR__ . "/../models/animal.php";
require_once __DIR__ . "/../models/categorie.php";
require_once __DIR__ . "/../models/user.php";

if (!isset($_SESSION['user_id']) || !($_SESSION['user_is_admin'] ?? false)) {
    header("Location: /PetMarket/?page=login");
    exit();
}

$animalModel    = new Animal($pdo);
$categorieModel = new Categorie($pdo);
$userModel      = new User($pdo);
$erreur         = "";

// ── Upload photo ─────────────────────────────────────────────
function traiterPhoto($champ, &$erreur) {
    if (!isset($_FILES[$champ]) || $_FILES[$champ]['error'] !== 0) {
        $erreur = "Photo obligatoire.";
        return null;
    }
    $extensionsOk = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($_FILES[$champ]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $extensionsOk)) {
        $erreur = "Format invalide. Utilise JPG, PNG ou GIF.";
        return null;
    }
    $nomFichier  = uniqid("animal_") . "." . $ext;
    $destination = __DIR__ . "/../public/images/" . $nomFichier;
    if (!move_uploaded_file($_FILES[$champ]['tmp_name'], $destination)) {
        $erreur = "Erreur lors de l'upload.";
        return null;
    }
    return $nomFichier;
}

// ════════════════════════════════════════════════════════════
//  ANIMAUX
// ════════════════════════════════════════════════════════════

if (isset($_POST['creer_animal'])) {
    $nom         = trim($_POST['nom']          ?? "");
    $prix        = (int)($_POST['prix']        ?? 0);
    $description = trim($_POST['description']  ?? "");
    $idCategorie = (int)($_POST['idCategorie'] ?? 0);

    if (empty($nom) || $prix <= 0 || $idCategorie <= 0) {
        $erreur = "Tous les champs obligatoires doivent être remplis.";
    } else {
        $photo = traiterPhoto("photo", $erreur);
        if ($photo) {
            // ✅ CORRECTION : l'admin poste en son propre nom
            $animalModel->create($nom, $prix, $description, $photo, $idCategorie, $_SESSION['user_id']);
            $_SESSION['flash_succes'] = "Animal ajouté !";
            header("Location: /PetMarket/?page=admin");
            exit();
        }
    }
}

if (isset($_POST['modifier_animal'])) {
    $id          = (int)($_POST['idAnimal']    ?? 0);
    $nom         = trim($_POST['nom']          ?? "");
    $prix        = (int)($_POST['prix']        ?? 0);
    $description = trim($_POST['description']  ?? "");
    $idCategorie = (int)($_POST['idCategorie'] ?? 0);

    if (empty($nom) || $prix <= 0 || $idCategorie <= 0) {
        $erreur = "Tous les champs obligatoires doivent être remplis.";
    } else {
        $animal = $animalModel->getById($id);
        $photo  = $animal['photo'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $nouvellePhoto = traiterPhoto("photo", $erreur);
            if ($nouvellePhoto) $photo = $nouvellePhoto;
        }
        if (empty($erreur)) {
            $animalModel->update($id, $nom, $prix, $description, $photo, $idCategorie);
            $_SESSION['flash_succes'] = "Animal modifié.";
            header("Location: /PetMarket/?page=admin");
            exit();
        }
    }
}

if (isset($_POST['supprimer_animal'])) {
    $id = (int)($_POST['idAnimal'] ?? 0);
    if ($id > 0) {
        $animalModel->delete($id);
        $_SESSION['flash_succes'] = "Animal supprimé.";
        header("Location: /PetMarket/?page=admin");
        exit();
    }
}

// ── Statut animal ────────────────────────────────────────────
if (isset($_POST['marquer_vendu'])) {
    $id = (int)($_POST['idAnimal'] ?? 0);
    if ($id > 0) {
        $pdo->prepare("UPDATE animal SET statut = 'vendu' WHERE idAnimal = :id")->execute(['id' => $id]);
        $_SESSION['flash_succes'] = "Animal marqué comme vendu.";
        header("Location: /PetMarket/?page=admin");
        exit();
    }
}

if (isset($_POST['marquer_dispo'])) {
    $id = (int)($_POST['idAnimal'] ?? 0);
    if ($id > 0) {
        $pdo->prepare("UPDATE animal SET statut = 'disponible' WHERE idAnimal = :id")->execute(['id' => $id]);
        $_SESSION['flash_succes'] = "Animal remis en disponible.";
        header("Location: /PetMarket/?page=admin");
        exit();
    }
}

// ════════════════════════════════════════════════════════════
//  CATÉGORIES
// ════════════════════════════════════════════════════════════

if (isset($_POST['creer_categorie'])) {
    $libelle = trim($_POST['libelle'] ?? "");
    if (empty($libelle)) {
        $erreur = "Le nom de la catégorie est obligatoire.";
    } else {
        $categorieModel->create($libelle);
        $_SESSION['flash_succes'] = "Catégorie ajoutée !";
        header("Location: /PetMarket/?page=admin#section-categories");
        exit();
    }
}

if (isset($_POST['modifier_categorie'])) {
    $id      = (int)($_POST['idCategorie'] ?? 0);
    $libelle = trim($_POST['libelle']      ?? "");
    if (empty($libelle)) {
        $erreur = "Le nom est obligatoire.";
    } else {
        $categorieModel->update($id, $libelle);
        $_SESSION['flash_succes'] = "Catégorie modifiée.";
        header("Location: /PetMarket/?page=admin#section-categories");
        exit();
    }
}

if (isset($_POST['supprimer_categorie'])) {
    $id = (int)($_POST['idCategorie'] ?? 0);
    if ($categorieModel->aDesAnimaux($id)) {
        $_SESSION['flash_erreur'] = "Impossible — des animaux utilisent cette catégorie.";
    } else {
        $categorieModel->delete($id);
        $_SESSION['flash_succes'] = "Catégorie supprimée.";
    }
    header("Location: /PetMarket/?page=admin#section-categories");
    exit();
}

// ════════════════════════════════════════════════════════════
//  UTILISATEURS
// ════════════════════════════════════════════════════════════

// Valider une demande vendeur
if (isset($_POST['valider_vendeur'])) {
    $idUser = (int)($_POST['idUser'] ?? 0);
    if ($idUser > 0) {
        $userModel->validerVendeur($idUser);
        $_SESSION['flash_succes'] = "Vendeur validé.";
        header("Location: /PetMarket/?page=admin#section-utilisateurs");
        exit();
    }
}

// Révoquer un vendeur → repasse en acheteur
if (isset($_POST['revoquer_vendeur'])) {
    $idUser = (int)($_POST['idUser'] ?? 0);
    if ($idUser > 0 && $idUser !== (int)$_SESSION['user_id']) {
        $userModel->revoquerVendeur($idUser);
        $_SESSION['flash_succes'] = "Vendeur révoqué — repassé en acheteur.";
    } else {
        $_SESSION['flash_erreur'] = "Action impossible.";
    }
    header("Location: /PetMarket/?page=admin#section-utilisateurs");
    exit();
}

// Supprimer un utilisateur
if (isset($_POST['supprimer_user'])) {
    $idUser = (int)($_POST['idUser'] ?? 0);
    if ($idUser > 0 && $idUser !== (int)$_SESSION['user_id']) {
        $userModel->delete($idUser);
        $_SESSION['flash_succes'] = "Utilisateur supprimé.";
    } else {
        $_SESSION['flash_erreur'] = "Vous ne pouvez pas vous supprimer vous-même.";
    }
    header("Location: /PetMarket/?page=admin#section-utilisateurs");
    exit();
}

// ════════════════════════════════════════════════════════════
//  DONNÉES POUR LA VUE
// ════════════════════════════════════════════════════════════
$animaux         = $animalModel->getAllAvecCategorie();
$categories      = $categorieModel->getAll();
$demandesVendeur = $userModel->getDemandesVendeur();
$utilisateurs    = $userModel->getAll();

require_once __DIR__ . "/../views/admin/animals.php";