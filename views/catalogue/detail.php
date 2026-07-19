<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../models/animal.php";
require_once __DIR__ . "/../../models/categorie.php";

$animalModel    = new Animal($pdo);
$categorieModel = new Categorie($pdo);
$id     = (int)($_GET['id'] ?? 0);
$animal = $animalModel->getById($id);

if (!$animal) {
    header("Location: /PetMarket/?page=catalogue");
    exit();
}

$categories   = $categorieModel->getAll();
$nomCategorie = "Non classé";
foreach ($categories as $cat) {
    if ((int)$cat['idCategorie'] === (int)$animal['idCategorie']) {
        $nomCategorie = $cat['libelle'];
        break;
    }
}

$similaires = $animalModel->getSimilaires($animal['idCategorie'], $id, 3);

require_once __DIR__ . "/../../models/panier.php";
$panierModel    = new Panier($pdo);
$dejaDansPanier = false;
if (isset($_SESSION['user_id'])) {
    $articlesPanier = $panierModel->getByUser((int)$_SESSION['user_id']);
    foreach ($articlesPanier as $a) {
        if ((int)$a['idAnimal'] === $id) {
            $dejaDansPanier = true;
            break;
        }
    }
}

$photo       = !empty($animal['photo'])
    ? '/PetMarket/public/images/' . htmlspecialchars($animal['photo'])
    : '/PetMarket/public/images/no-image.jpg';
$estDisponible = ($animal['statut'] ?? 'disponible') === 'disponible';

// Données de l'animal pour le JS (injection sécurisée)
$animalNom   = addslashes(htmlspecialchars($animal['nom']));
$animalPhoto = addslashes(htmlspecialchars($animal['photo'] ?: 'no-image.jpg'));
$animalPrix  = (int)$animal['prix'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($animal['nom']); ?> — PetMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/PetMarket/public/css/header.css">
    <link rel="stylesheet" href="/PetMarket/public/css/footer.css">
    <link rel="stylesheet" href="/PetMarket/public/css/detail.css">
</head>
<body>
    <?php include __DIR__ . "/../layout/header.php"; ?>
    <?php include __DIR__ . "/../layout/flash.php"; ?>

    <main>
        <div class="fil-ariane">
            <a href="/PetMarket/?page=accueil">Accueil</a> ›
            <a href="/PetMarket/?page=catalogue">Catalogue</a> ›
            <?php echo htmlspecialchars($animal['nom']); ?>
        </div>

        <div class="detail-grille">
            <div class="detail-photo">
                <img
                    src="<?php echo $photo; ?>"
                    alt="<?php echo htmlspecialchars($animal['nom']); ?>"
                    style="<?php echo !$estDisponible ? 'filter:grayscale(60%);opacity:.8;' : ''; ?>"
                >
                <?php if (!$estDisponible) : ?>
                    <div class="badge-statut badge-<?php echo htmlspecialchars($animal['statut']); ?>">
                        <?php echo strtoupper(htmlspecialchars($animal['statut'])); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="detail-info">
                <span class="badge"><?php echo htmlspecialchars($nomCategorie); ?></span>
                <h1><?php echo htmlspecialchars($animal['nom']); ?></h1>
                <p class="prix-grand"><?php echo number_format($animalPrix, 0, ',', ' '); ?> FCFA</p>

                <?php if (!empty($animal['age'])) : ?>
                    <p class="age"><i class="fa-solid fa-cake-candles"></i> Âge : <?php echo htmlspecialchars($animal['age']); ?></p>
                <?php endif; ?>

                <?php if (!empty($animal['description'])) : ?>
                    <p class="description"><?php echo nl2br(htmlspecialchars($animal['description'])); ?></p>
                <?php else : ?>
                    <p class="description vide">Aucune description disponible.</p>
                <?php endif; ?>

                <!-- ZONE PANIER -->
                <div class="actions" id="zone-panier">
                    <?php if (!$estDisponible) : ?>
                        <button class="btn-orange btn-block" disabled style="opacity:.5;cursor:not-allowed;">
                            <i class="fa-solid fa-ban"></i>
                            <?php echo ucfirst(htmlspecialchars($animal['statut'])); ?> — indisponible
                        </button>

                    <?php elseif (isset($_SESSION['user_id'])) : ?>
                        <?php if ($dejaDansPanier) : ?>
                            <button class="btn-contour btn-block" onclick="retirerDuPanier(<?php echo $id; ?>)">
                                <i class="fa-solid fa-cart-arrow-down"></i> Retirer du panier
                            </button>
                        <?php else : ?>
                            <button class="btn-orange btn-block" onclick="ajouterAuPanier(<?php echo $id; ?>)">
                                <i class="fa-solid fa-cart-plus"></i> Ajouter au panier
                            </button>
                        <?php endif; ?>

                    <?php else : ?>
                        <a href="/PetMarket/?page=login" class="btn-contour btn-block">
                            <i class="fa-solid fa-lock"></i> Connectez-vous pour acheter
                        </a>
                    <?php endif; ?>
                </div>

                <a href="/PetMarket/?page=catalogue" class="lien-retour">
                    <i class="fa-solid fa-arrow-left"></i> Retour au catalogue
                </a>
            </div>
        </div>

        <!-- ANIMAUX SIMILAIRES -->
        <?php if (!empty($similaires)) : ?>
            <div class="similaires">
                <h2>Animaux similaires</h2>
                <div class="grille-similaires">
                    <?php foreach ($similaires as $sim) : ?>
                        <div class="carte">
                            <a href="/PetMarket/?page=detail&id=<?php echo (int)$sim['idAnimal']; ?>">
                                <img
                                    src="/PetMarket/public/images/<?php echo htmlspecialchars($sim['photo'] ?: 'no-image.jpg'); ?>"
                                    alt="<?php echo htmlspecialchars($sim['nom']); ?>"
                                >
                            </a>
                            <div class="carte-corps">
                                <h3><?php echo htmlspecialchars($sim['nom']); ?></h3>
                                <p class="prix"><?php echo number_format((int)$sim['prix'], 0, ',', ' '); ?> FCFA</p>
                                <a href="/PetMarket/?page=detail&id=<?php echo (int)$sim['idAnimal']; ?>"
                                   class="btn-contour btn-petit">
                                    <i class="fa-solid fa-eye"></i> Voir
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . "/../layout/footer.php"; ?>

    <script>
    // Données de l'animal injectées depuis PHP
    const ANIMAL_ID    = <?php echo $id; ?>;
    const ANIMAL_NOM   = "<?php echo $animalNom; ?>";
    const ANIMAL_PHOTO = "<?php echo $animalPhoto; ?>";
    const ANIMAL_PRIX  = <?php echo $animalPrix; ?>;

    // ── Ajouter au panier ────────────────────────────────────
    async function ajouterAuPanier(id) {
        const rep  = await fetch('/PetMarket/?page=panier', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=ajouter&idAnimal=' + id
        });
        const data = await rep.json();

        if (data.succes) {
            // 1. Changer le bouton → "Retirer du panier"
            document.getElementById('zone-panier').innerHTML =
                '<button class="btn-contour btn-block" onclick="retirerDuPanier(' + id + ')">' +
                '<i class="fa-solid fa-cart-arrow-down"></i> Retirer du panier</button>';

            // 2. Mettre à jour le badge header
            var badge = document.getElementById('badge-panier');
            if (badge) {
                badge.textContent   = data.total;
                badge.style.display = 'flex';
            }

            // 3. Ajouter l'article dans le tiroir panier
            var contenu = document.getElementById('tiroir-contenu');
            if (contenu) {
                // Supprimer le message "panier vide" s'il existe
                var vide = contenu.querySelector('.tiroir-vide');
                if (vide) vide.remove();

                // Ajouter la carte seulement si pas déjà présente
                if (!document.getElementById('tiroir-article-' + id)) {
                    var div = document.createElement('div');
                    div.className = 'tiroir-article';
                    div.id        = 'tiroir-article-' + id;
                    div.innerHTML =
                        '<img src="/PetMarket/public/images/' + ANIMAL_PHOTO + '" alt="' + ANIMAL_NOM + '">' +
                        '<div class="tiroir-article-info">' +
                            '<p class="tiroir-article-nom">' + ANIMAL_NOM + '</p>' +
                            '<p class="tiroir-article-prix">' + ANIMAL_PRIX.toLocaleString('fr-FR') + ' FCFA</p>' +
                        '</div>' +
                        '<button class="tiroir-supprimer" onclick="supprimerDuPanier(' + id + ')" title="Retirer">' +
                            '<i class="fa-solid fa-trash"></i>' +
                        '</button>';
                    contenu.appendChild(div);
                }
            }

            // 4. Afficher le pied du tiroir et mettre à jour le total
            var pied = document.getElementById('tiroir-pied');
            if (pied) {
                pied.style.display = 'block';
            } else {
                // Le pied n'existait pas (panier était vide) — le créer
                var tiroir = document.getElementById('panier-tiroir');
                var newPied = document.createElement('div');
                newPied.className = 'tiroir-pied';
                newPied.id        = 'tiroir-pied';
                newPied.innerHTML =
                    '<div class="tiroir-total">' +
                        '<span>Total</span>' +
                        '<span id="tiroir-total">' + (data.total_prix || ANIMAL_PRIX).toLocaleString('fr-FR') + ' FCFA</span>' +
                    '</div>' +
                    '<button class="btn-commander" id="btn-commander" onclick="passerCommande()">' +
                        '<i class="fa-solid fa-check"></i> Commander' +
                    '</button>' +
                    '<p class="tiroir-securite"><i class="fa-solid fa-shield-halved"></i> Paiement sécurisé</p>';
                tiroir.appendChild(newPied);
            }

            // Mettre à jour le montant total
            var totalEl = document.getElementById('tiroir-total');
            if (totalEl && data.total_prix !== undefined) {
                totalEl.textContent = data.total_prix.toLocaleString('fr-FR') + ' FCFA';
            }
        }
    }

    // ── Retirer du panier ────────────────────────────────────
    async function retirerDuPanier(id) {
        const rep  = await fetch('/PetMarket/?page=panier', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=supprimer&idAnimal=' + id
        });
        const data = await rep.json();

        if (data.succes) {
            // 1. Rebascule vers "Ajouter au panier"
            document.getElementById('zone-panier').innerHTML =
                '<button class="btn-orange btn-block" onclick="ajouterAuPanier(' + id + ')">' +
                '<i class="fa-solid fa-cart-plus"></i> Ajouter au panier</button>';

            // 2. Mettre à jour le badge
            var badge = document.getElementById('badge-panier');
            if (badge) {
                if (data.total > 0) {
                    badge.textContent   = data.total;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }

            // 3. Retirer la carte du tiroir
            var article = document.getElementById('tiroir-article-' + id);
            if (article) article.remove();

            // 4. Mettre à jour le total ou cacher le pied si vide
            if (data.total === 0) {
                var pied = document.getElementById('tiroir-pied');
                if (pied) pied.style.display = 'none';

                var contenu = document.getElementById('tiroir-contenu');
                if (contenu) {
                    contenu.innerHTML =
                        '<div class="tiroir-vide">' +
                        '<i class="fa-solid fa-cart-shopping"></i>' +
                        '<p>Votre panier est vide</p>' +
                        '<a href="/PetMarket/?page=catalogue" onclick="fermerPanier()" class="btn-orange">Voir le catalogue</a>' +
                        '</div>';
                }
            } else {
                var totalEl = document.getElementById('tiroir-total');
                if (totalEl && data.total_prix !== undefined) {
                    totalEl.textContent = data.total_prix.toLocaleString('fr-FR') + ' FCFA';
                }
            }
        }
    }
    </script>
</body>
</html>