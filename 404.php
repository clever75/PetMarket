<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable — PetMarket</title>
    <link rel="stylesheet" href="/PetMarket/public/css/header.css">
    <link rel="stylesheet" href="/PetMarket/public/css/footer.css">
    <link rel="stylesheet" href="/PetMarket/public/css/404.css">
</head>
<body>

<?php include __DIR__ . "/views/layout/header.php"; ?>

<main class="page-404">
    <div class="contenu-404">
        <p class="grand-chiffre">404</p>
        <h1>Page introuvable</h1>
        <p>La page que tu cherches n'existe pas ou a été déplacée.</p>
        <div class="boutons-404">
            <a href="/PetMarket/views/acceuil/index.php" class="btn-orange">Retour à l'accueil</a>
            <a href="/PetMarket/views/catalogue/catalogue.php" class="btn-contour">Voir le catalogue</a>
        </div>
    </div>
</main>

<?php include __DIR__ . "/views/layout/footer.php"; ?>

</body>
</html>