<?php
// controllers/animalController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/animal.php";
require_once __DIR__ . "/../models/categorie.php";

$animalModel    = new Animal($pdo);
$categorieModel = new Categorie($pdo);

$recherche   = trim($_GET['recherche'] ?? "");
$idCategorie = (int)($_GET['idCategorie'] ?? 0);
$tri         = $_GET['tri'] ?? 'recent';

$trisAutorises = ['recent', 'prix_asc', 'prix_desc'];
if (!in_array($tri, $trisAutorises)) {
    $tri = 'recent';
}

// Catalogue public → uniquement les animaux disponibles
if ($recherche !== "" || $idCategorie > 0) {
    $animaux = $animalModel->rechercher($recherche, $idCategorie ?: null, $tri);
} else {
    $animaux = $animalModel->getAllDisponibles($tri);
}

$categories = $categorieModel->getAll();
require_once __DIR__ . "/../views/catalogue/catalogue.php";