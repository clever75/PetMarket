<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nbPanier = 0;
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . "/../../config/database.php";
    require_once __DIR__ . "/../../models/panier.php";
    $panierModelHeader = new Panier($pdo);
    $articlesHeader    = $panierModelHeader->getByUser((int)$_SESSION['user_id']);
    $nbPanier          = count($articlesHeader);
}
$premiereLettre = isset($_SESSION['user_nom']) ? strtoupper(substr($_SESSION['user_nom'], 0, 1)) : 'U';
?>

<header>
    <div class="header-inner">

        <a href="/PetMarket/?page=accueil" class="logo">
            <i class="fa-solid fa-paw"></i> PetMarket
        </a>

        <nav>
            <a href="/PetMarket/?page=accueil"><i class="fa-solid fa-house"></i> Accueil</a>
            <a href="/PetMarket/?page=catalogue"><i class="fa-solid fa-list"></i> Catalogue</a>
            <a href="/PetMarket/?page=about"><i class="fa-solid fa-circle-info"></i> À propos</a>
        </nav>

        <div class="header-droite">
            <?php if (isset($_SESSION['user_id'])) : ?>

                <button class="btn-panier" onclick="ouvrirPanier()" title="Mon panier">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="badge-panier" id="badge-panier"
                          style="<?php echo $nbPanier > 0 ? '' : 'display:none;'; ?>">
                        <?php echo $nbPanier; ?>
                    </span>
                </button>

                <div class="user-menu">
                    <button class="avatar" onclick="toggleMenu()" title="Mon compte">
                        <?php echo $premiereLettre; ?>
                    </button>
                    <div class="dropdown" id="dropdown">
                        <div class="dropdown-entete">
                            <div class="dropdown-avatar"><?php echo $premiereLettre; ?></div>
                            <div>
                                <p class="dropdown-nom"><?php echo htmlspecialchars($_SESSION['user_pseudo'] ?? $_SESSION['user_nom']); ?></p>
                                <p class="dropdown-email"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
                            </div>
                        </div>
                        <div class="dropdown-separateur"></div>
                        <a href="/PetMarket/?page=profil"><i class="fa-solid fa-user-pen"></i> Mon profil</a>
                        <?php if ($_SESSION['user_is_admin'] ?? false) : ?>
                            <a href="/PetMarket/?page=admin"><i class="fa-solid fa-gauge"></i> Administration</a>
                        <?php elseif (($_SESSION['user_role'] ?? '') === 'vendeur') : ?>
                            <a href="/PetMarket/?page=seller"><i class="fa-solid fa-store"></i> Mes annonces</a>
                        <?php endif; ?>
                        <div class="dropdown-separateur"></div>
                        <a href="/PetMarket/?page=logout" class="logout">
                            <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                        </a>
                    </div>
                </div>

            <?php else : ?>
                <a href="/PetMarket/?page=login" class="btn-orange">
                    <i class="fa-solid fa-right-to-bracket"></i> Connexion
                </a>
            <?php endif; ?>
        </div>

    </div>
</header>

<!-- OVERLAY + TIROIR PANIER -->
<div class="panier-overlay" id="panier-overlay" onclick="fermerPanier()"></div>

<div class="panier-tiroir" id="panier-tiroir">

    <div class="tiroir-entete">
        <h2><i class="fa-solid fa-cart-shopping"></i> Mon panier</h2>
        <button class="tiroir-fermer" onclick="fermerPanier()">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="tiroir-contenu" id="tiroir-contenu">
        <?php
        if (isset($_SESSION['user_id'])) :
            $panierModel    = new Panier($pdo);
            $articlesTiroir = $panierModel->getByUser((int)$_SESSION['user_id']);
            $totalTiroir    = 0;
            foreach ($articlesTiroir as $a) {
                $totalTiroir += (int)$a['prix'];
            }

            if (!empty($articlesTiroir)) :
                foreach ($articlesTiroir as $article) :
        ?>
                <div class="tiroir-article" id="tiroir-article-<?php echo (int)$article['idAnimal']; ?>">
                    <img
                        src="/PetMarket/public/images/<?php echo htmlspecialchars($article['photo'] ?? 'no-image.jpg'); ?>"
                        alt="<?php echo htmlspecialchars($article['nom']); ?>"
                    >
                    <div class="tiroir-article-info">
                        <p class="tiroir-article-nom"><?php echo htmlspecialchars($article['nom']); ?></p>
                        <p class="tiroir-article-prix"><?php echo number_format((int)$article['prix'], 0, ',', ' '); ?> FCFA</p>
                    </div>
                    <button class="tiroir-supprimer"
                            onclick="supprimerDuPanier(<?php echo (int)$article['idAnimal']; ?>)"
                            title="Retirer">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
        <?php
                endforeach;
            else :
        ?>
                <div class="tiroir-vide">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <p>Votre panier est vide</p>
                    <a href="/PetMarket/?page=catalogue" onclick="fermerPanier()" class="btn-orange">
                        Voir le catalogue
                    </a>
                </div>
        <?php
            endif;
        endif;
        ?>
    </div>

    <?php if (!empty($articlesTiroir)) : ?>
    <div class="tiroir-pied" id="tiroir-pied">
        <div class="tiroir-total">
            <span>Total</span>
            <span id="tiroir-total"><?php echo number_format($totalTiroir, 0, ',', ' '); ?> FCFA</span>
        </div>
        <button class="btn-commander" id="btn-commander" onclick="passerCommande()">
            <i class="fa-solid fa-check"></i> Commander
        </button>
        <p class="tiroir-securite"><i class="fa-solid fa-shield-halved"></i> Paiement sécurisé</p>
    </div>
    <?php endif; ?>

</div>

<script>
function ouvrirPanier() {
    document.getElementById('panier-tiroir').classList.add('ouvert');
    document.getElementById('panier-overlay').classList.add('visible');
    document.body.style.overflow = 'hidden';
}

function fermerPanier() {
    document.getElementById('panier-tiroir').classList.remove('ouvert');
    document.getElementById('panier-overlay').classList.remove('visible');
    document.body.style.overflow = '';
}

function toggleMenu() {
    document.getElementById('dropdown').classList.toggle('ouvert');
}

document.addEventListener('click', function(e) {
    var menu = document.querySelector('.user-menu');
    if (menu && !menu.contains(e.target)) {
        var d = document.getElementById('dropdown');
        if (d) d.classList.remove('ouvert');
    }
});

// ── Supprimer un article du tiroir ───────────────────────────
async function supprimerDuPanier(id) {
    const rep  = await fetch('/PetMarket/?page=panier', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=supprimer&idAnimal=' + id
    });
    const data = await rep.json();

    if (data.succes) {
        // 1. Retirer la carte du tiroir
        var article = document.getElementById('tiroir-article-' + id);
        if (article) article.remove();

        // 2. Mettre à jour le badge
        var badge = document.getElementById('badge-panier');
        if (data.total > 0) {
            badge.textContent   = data.total;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }

        // 3. Mettre à jour le total
        if (data.total_prix !== undefined) {
            var totalEl = document.getElementById('tiroir-total');
            if (totalEl) totalEl.textContent = data.total_prix.toLocaleString('fr-FR') + ' FCFA';
        }

        // 4. Si panier vide → message vide + cacher le pied
        if (data.total === 0) {
            document.getElementById('tiroir-contenu').innerHTML =
                '<div class="tiroir-vide">' +
                '<i class="fa-solid fa-cart-shopping"></i>' +
                '<p>Votre panier est vide</p>' +
                '<a href="/PetMarket/?page=catalogue" onclick="fermerPanier()" class="btn-orange">Voir le catalogue</a>' +
                '</div>';
            var pied = document.getElementById('tiroir-pied');
            if (pied) pied.style.display = 'none';
        }

        // 5. ── SYNCHRO detail.php ──────────────────────────────
        // Si on est sur la page detail de CET animal, rebascule le bouton
        var zonePanier = document.getElementById('zone-panier');
        if (zonePanier && typeof ANIMAL_ID !== 'undefined' && ANIMAL_ID === id) {
            zonePanier.innerHTML =
                '<button class="btn-orange btn-block" onclick="ajouterAuPanier(' + id + ')">' +
                '<i class="fa-solid fa-cart-plus"></i> Ajouter au panier</button>';
        }
    }
}

// ── Commander → statut 'reserve' + message confirmation ─────
async function passerCommande() {
    var btn = document.getElementById('btn-commander');
    btn.disabled  = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> En cours...';

    const rep  = await fetch('/PetMarket/?page=commande', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: ''
    });
    const data = await rep.json();

    if (data.succes) {
        // Vider le badge
        var badge = document.getElementById('badge-panier');
        if (badge) badge.style.display = 'none';

        // Cacher le pied
        var pied = document.getElementById('tiroir-pied');
        if (pied) pied.style.display = 'none';

        // Message de confirmation
        document.getElementById('tiroir-contenu').innerHTML =
            '<div class="tiroir-vide">' +
            '<i class="fa-solid fa-circle-check" style="color:#27ae60;font-size:3rem;"></i>' +
            '<p style="font-weight:bold;font-size:1.1rem;margin-top:14px;">Commande enregistrée !</p>' +
            '<p style="font-size:.9rem;color:#555;margin-top:10px;line-height:1.8;">' +
                'Votre demande a bien été prise en compte.<br>' +
                'Le vendeur va vous contacter pour finaliser :<br><br>' +
                '<i class="fa-brands fa-whatsapp" style="color:#25d366;"></i> <strong>WhatsApp</strong><br>' +
                '<i class="fa-solid fa-mobile-screen"></i> <strong>Flooz / T-Money</strong><br>' +
                '<i class="fa-solid fa-location-dot"></i> Récupération à <strong>Lomé, Togo</strong>' +
            '</p>' +
            '<a href="/PetMarket/?page=catalogue" onclick="fermerPanier()" ' +
            'class="btn-orange" style="margin-top:16px;display:inline-block;">' +
            '<i class="fa-solid fa-list"></i> Voir le catalogue</a>' +
            '</div>';

        // Rebascule le bouton sur detail.php si on y est
        var zonePanier = document.getElementById('zone-panier');
        if (zonePanier && typeof ANIMAL_ID !== 'undefined') {
            zonePanier.innerHTML =
                '<button class="btn-orange btn-block" disabled style="opacity:.5;cursor:not-allowed;">' +
                '<i class="fa-solid fa-ban"></i> Réservé — indisponible</button>';
        }

    } else {
        btn.disabled  = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Commander';
        alert(data.message || 'Une erreur est survenue.');
    }
}
</script>