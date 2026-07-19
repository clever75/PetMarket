<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos — PetMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/PetMarket/public/css/header.css">
    <link rel="stylesheet" href="/PetMarket/public/css/footer.css">
    <link rel="stylesheet" href="/PetMarket/public/css/about.css">
</head>
<body>
<?php include __DIR__ . "/../layout/header.php"; ?>
<main class="about-page">
    <div class="contenu">
        <h1>À propos de <span class="orange">PetMarket</span></h1>
        <p class="intro">Une plateforme simple pour trouver et vendre des animaux de compagnie à Lomé, Togo.</p>
        <div class="grille-3">
            <div class="bloc">
                <div class="icone"><i class="fa-solid fa-shield-halved"></i></div>
                <h3>Vendeurs vérifiés</h3>
                <p>Chaque vendeur est validé par notre équipe avant de publier.</p>
            </div>
            <div class="bloc">
                <div class="icone"><i class="fa-solid fa-paw"></i></div>
                <h3>Animaux variés</h3>
                <p>Chiens, chats, oiseaux et plus encore.</p>
            </div>
            <div class="bloc">
                <div class="icone"><i class="fa-solid fa-location-dot"></i></div>
                <h3>Basé à Lomé</h3>
                <p>Une plateforme locale pensée pour le marché togolais.</p>
            </div>
        </div>
        <div class="comment-ca-marche">
            <h2>Comment ça marche ?</h2>
            <div class="etapes">
                <div class="etape">
                    <div class="numero">1</div>
                    <h3>Créez un compte</h3>
                    <p>Inscrivez-vous en tant qu'acheteur ou demandez le statut vendeur.</p>
                </div>
                <div class="etape">
                    <div class="numero">2</div>
                    <h3>Parcourez le catalogue</h3>
                    <p>Filtrez par catégorie et trouvez l'animal idéal.</p>
                </div>
                <div class="etape">
                    <div class="numero">3</div>
                    <h3>Contactez le vendeur</h3>
                    <p>Ajoutez au panier et finalisez votre achat.</p>
                </div>
            </div>
        </div>
        <div class="centrer">
            <a href="/PetMarket/?page=catalogue" class="btn-orange"><i class="fa-solid fa-list"></i> Voir le catalogue</a>
            <a href="/PetMarket/?page=login" class="btn-contour"><i class="fa-solid fa-user-plus"></i> Créer un compte</a>
        </div>
    </div>
</main>
<?php include __DIR__ . "/../layout/footer.php"; ?>
</body>
</html>