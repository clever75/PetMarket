<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../models/animal.php";

$animalModel    = new Animal($pdo);
$animauxAccueil = $animalModel->getDerniersDisponibles(4);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetMarket — Accueil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/PetMarket/public/css/header.css">
    <link rel="stylesheet" href="/PetMarket/public/css/footer.css">
    <link rel="stylesheet" href="/PetMarket/public/css/index0.css">
</head>
<body>

<?php include __DIR__ . "/../layout/header.php"; ?>
<?php include __DIR__ . "/../layout/flash.php"; ?>

<main>

    <!-- ═══════════════════════════════════════════════════
         HERO
         ═══════════════════════════════════════════════════ -->
    <section class="hero">
        <div class="hero-texte">
            <h1>Trouvez votre <span class="orange">compagnon idéal</span></h1>
            <p>Des animaux proposés par des vendeurs sérieux à Lomé, Togo.</p>
            <div class="hero-boutons">
                <a href="/PetMarket/?page=catalogue" class="btn-orange">
                    <i class="fa-solid fa-list"></i> Voir le catalogue
                </a>
                <a href="/PetMarket/?page=about" class="btn-contour">
                    <i class="fa-solid fa-circle-info"></i> En savoir plus
                </a>
            </div>
        </div>
        <div class="hero-image">
            <img src="/PetMarket/public/images/hero.jpg" alt="Animaux de compagnie">
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════
         ANIMAUX DISPONIBLES
         ═══════════════════════════════════════════════════ -->
    <section class="section-animaux">
        <div class="contenu">
            <h2>Animaux disponibles</h2>
            <p class="sous-titre">Quelques animaux qui attendent un foyer</p>

            <?php if (!empty($animauxAccueil)) : ?>
                <div class="grille">
                    <?php foreach ($animauxAccueil as $animal) :
                        $statut  = $animal['statut'] ?? 'disponible';
                        $indispo = $statut !== 'disponible';
                    ?>
                    <div class="carte" style="position:relative;">

                        <?php if ($statut === 'reserve') : ?>
                            <div class="carte-overlay reserve">
                                <i class="fa-solid fa-clock"></i> Réservé
                            </div>
                        <?php elseif ($statut === 'vendu') : ?>
                            <div class="carte-overlay vendu">
                                <i class="fa-solid fa-ban"></i> Vendu
                            </div>
                        <?php endif; ?>

                        <a href="/PetMarket/?page=detail&id=<?php echo (int)$animal['idAnimal']; ?>">
                            <img
                                src="/PetMarket/public/images/<?php echo htmlspecialchars($animal['photo'] ?: 'no-image.jpg'); ?>"
                                alt="<?php echo htmlspecialchars($animal['nom']); ?>"
                                style="<?php echo $indispo ? 'filter:grayscale(60%);opacity:.8;' : ''; ?>"
                            >
                        </a>

                        <div class="carte-corps">
                            <h3><?php echo htmlspecialchars($animal['nom']); ?></h3>
                            <p class="prix"><?php echo number_format((int)$animal['prix'], 0, ',', ' '); ?> FCFA</p>
                            <a href="/PetMarket/?page=detail&id=<?php echo (int)$animal['idAnimal']; ?>"
                               class="btn-<?php echo $indispo ? 'contour' : 'orange'; ?> btn-petit">
                                <i class="fa-solid fa-eye"></i> Voir
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="centrer">
                    <a href="/PetMarket/?page=catalogue" class="btn-contour">
                        <i class="fa-solid fa-arrow-right"></i> Voir tous les animaux
                    </a>
                </div>

            <?php else : ?>
                <div class="message-vide">
                    <i class="fa-solid fa-paw"></i>
                    <p>Aucun animal disponible pour le moment.</p>
                    <a href="/PetMarket/?page=about" class="btn-contour">
                        <i class="fa-solid fa-circle-info"></i> En savoir plus sur PetMarket
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════
         POURQUOI PETMARKET
         ═══════════════════════════════════════════════════ -->
    <section class="section-pourquoi">
        <div class="contenu">
            <h2>Pourquoi PetMarket ?</h2>
            <p class="sous-titre">Simple, rapide et de confiance</p>
            <div class="grille-3">
                <div class="avantage">
                    <div class="icone"><i class="fa-solid fa-shield-halved"></i></div>
                    <h3>Vendeurs vérifiés</h3>
                    <p>Chaque vendeur est validé par notre équipe avant de publier une annonce.</p>
                </div>
                <div class="avantage">
                    <div class="icone"><i class="fa-solid fa-paw"></i></div>
                    <h3>Animaux variés</h3>
                    <p>Chiens, chats, oiseaux — trouvez celui qui vous convient.</p>
                </div>
                <div class="avantage">
                    <div class="icone"><i class="fa-solid fa-location-dot"></i></div>
                    <h3>Basé à Lomé</h3>
                    <p>Une plateforme locale pensée pour le Togo.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════
         COMMENT ÇA MARCHE
         ═══════════════════════════════════════════════════ -->
    <section class="section-comment">
        <div class="contenu">
            <h2>Comment ça marche ?</h2>
            <p class="sous-titre">En 3 étapes simples</p>
            <div class="etapes">
                <div class="etape">
                    <div class="numero">1</div>
                    <h3>Créez un compte</h3>
                    <p>Inscrivez-vous en tant qu'acheteur ou demandez le statut vendeur.</p>
                </div>
                <div class="etape">
                    <div class="numero">2</div>
                    <h3>Parcourez le catalogue</h3>
                    <p>Filtrez par catégorie, triez par prix et trouvez l'animal idéal.</p>
                </div>
                <div class="etape">
                    <div class="numero">3</div>
                    <h3>Commandez</h3>
                    <p>Ajoutez au panier, commandez et contactez le vendeur pour le paiement.</p>
                </div>
            </div>
            <?php if (!isset($_SESSION['user_id'])) : ?>
                <div class="centrer" style="margin-top:24px;">
                    <a href="/PetMarket/?page=login" class="btn-orange">
                        <i class="fa-solid fa-user-plus"></i> Créer un compte
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php include __DIR__ . "/../layout/footer.php"; ?>

<style>
/* Overlay statut sur les cartes de l'accueil */
.carte-overlay {
    position: absolute;
    top: 12px; left: 12px;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: .78rem;
    font-weight: bold;
    color: #fff;
    letter-spacing: .5px;
    text-transform: uppercase;
    pointer-events: none;
    z-index: 1;
}
.carte-overlay.reserve { background: #e67e22; }
.carte-overlay.vendu   { background: #e74c3c; }
</style>

</body>
</html>