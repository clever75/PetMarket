<!-- views/seller/header_seller.php -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Compte initial des réservations en attente (chargement serveur)
require_once __DIR__ . "/../../config/database.php";
$nbReservations = 0;
if (isset($_SESSION['user_id'])) {
    $stmtInit = $pdo->prepare("
        SELECT COUNT(*) AS nb
        FROM commande c
        JOIN animal a ON a.idAnimal = c.idAnimal
        WHERE a.idUser = :id
          AND c.statut = 'en_attente'
          AND a.statut = 'reserve'
    ");
    $stmtInit->execute(['id' => (int)$_SESSION['user_id']]);
    $nbReservations = (int)($stmtInit->fetch()['nb'] ?? 0);
}
?>
<style>
    /* ── Header vendeur ───────────────────────────────────────── */
    header {
        background: #fff;
        border-bottom: 1px solid #eee;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .header-inner {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        height: 64px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .logo {
        font-weight: 800;
        font-size: 1.2rem;
        color: #e67e22;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .seller-badge {
        background: #fff3e0;
        color: #e67e22;
        border: 1px solid #f0d0a0;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: .8rem;
        font-weight: 600;
    }

    .header-inner>div:last-child {
        margin-left: auto;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    /* ── Bouton notif ─────────────────────────────────────────── */
    .btn-notif {
        position: relative;
        background: none;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 8px 14px;
        cursor: pointer;
        font-size: .9rem;
        color: #555;
        display: flex;
        align-items: center;
        gap: 7px;
        text-decoration: none;
        transition: background .15s, border-color .15s;
    }

    .btn-notif:hover {
        background: #fff8f0;
        border-color: #e67e22;
        color: #e67e22;
    }

    .btn-notif.active {
        background: #fff3e0;
        border-color: #e67e22;
        color: #e67e22;
        font-weight: 600;
    }

    /* Badge rouge sur le bouton notif */
    .badge-notif {
        position: absolute;
        top: -7px;
        right: -7px;
        background: #e74c3c;
        color: #fff;
        font-size: .68rem;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        border: 2px solid #fff;
        animation: pulse-badge .6s ease;
    }

    @keyframes pulse-badge {
        0% {
            transform: scale(0.5);
            opacity: 0;
        }

        60% {
            transform: scale(1.2);
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* ── Dropdown notifications ───────────────────────────────── */
    .notif-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 340px;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, .12);
        z-index: 200;
        display: none;
        overflow: hidden;
    }

    .notif-dropdown.ouvert {
        display: block;
    }

    .notif-entete {
        padding: 14px 16px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .notif-entete h3 {
        margin: 0;
        font-size: .95rem;
        color: #333;
    }

    .notif-liste {
        max-height: 320px;
        overflow-y: auto;
    }

    .notif-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f8f8f8;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        transition: background .15s;
    }

    .notif-item:hover {
        background: #fffaf5;
    }

    .notif-item:last-child {
        border-bottom: none;
    }

    .notif-item img {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 8px;
        flex-shrink: 0;
    }

    .notif-item-body {
        flex: 1;
    }

    .notif-item-titre {
        font-weight: 600;
        font-size: .88rem;
        color: #333;
        margin-bottom: 2px;
    }

    .notif-item-detail {
        font-size: .8rem;
        color: #888;
        line-height: 1.5;
    }

    .notif-item-date {
        font-size: .75rem;
        color: #bbb;
        margin-top: 3px;
    }

    .notif-vide {
        padding: 30px 16px;
        text-align: center;
        color: #aaa;
        font-size: .88rem;
    }

    .notif-vide i {
        font-size: 1.8rem;
        display: block;
        margin-bottom: 8px;
    }

    .notif-footer {
        padding: 12px 16px;
        border-top: 1px solid #f0f0f0;
        text-align: center;
    }

    .notif-footer a {
        color: #e67e22;
        font-size: .85rem;
        font-weight: 600;
        text-decoration: none;
    }

    .notif-footer a:hover {
        text-decoration: underline;
    }

    /* ── Toast notification (nouvelle commande) ───────────────── */
    .toast-notif {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #fff;
        border: 1px solid #f0d0a0;
        border-left: 4px solid #e67e22;
        border-radius: 10px;
        box-shadow: 0 6px 24px rgba(0, 0, 0, .15);
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 999;
        max-width: 320px;
        animation: slideInToast .4s ease;
        cursor: pointer;
    }

    .toast-notif:hover {
        background: #fff8f0;
    }

    @keyframes slideInToast {
        from {
            transform: translateX(120%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .toast-notif .toast-icone {
        font-size: 1.6rem;
        color: #e67e22;
        flex-shrink: 0;
    }

    .toast-notif .toast-texte p {
        margin: 0;
    }

    .toast-notif .toast-titre {
        font-weight: 700;
        font-size: .9rem;
        color: #333;
    }

    .toast-notif .toast-sous {
        font-size: .82rem;
        color: #888;
        margin-top: 2px;
    }

    .toast-fermer {
        position: absolute;
        top: 8px;
        right: 8px;
        background: none;
        border: none;
        cursor: pointer;
        color: #bbb;
        font-size: .85rem;
    }

    .toast-fermer:hover {
        color: #888;
    }
</style>

<header>
    <div class="header-inner">
        <a href="/PetMarket/?page=accueil" class="logo">
            <i class="fa-solid fa-paw"></i> PetMarket
        </a>
        <span class="seller-badge">
            <i class="fa-solid fa-store"></i> Espace vendeur
        </span>

        <div>
            <!-- ── Bouton notifications ── -->
            <div style="position:relative;">
                <button class="btn-notif <?php echo $nbReservations > 0 ? 'active' : ''; ?>"
                    id="btn-notif"
                    onclick="toggleNotifDropdown()"
                    title="Mes notifications">
                    <i class="fa-solid fa-bell"></i>
                    Notifications
                    <!-- Badge -->
                    <span class="badge-notif" id="badge-notif"
                        style="<?php echo $nbReservations > 0 ? '' : 'display:none;'; ?>">
                        <?php echo $nbReservations; ?>
                    </span>
                </button>

                <!-- Dropdown -->
                <div class="notif-dropdown" id="notif-dropdown">
                    <div class="notif-entete">
                        <h3><i class="fa-solid fa-bell" style="color:#e67e22;"></i> Réservations en attente</h3>
                        <span id="notif-count-label" style="font-size:.8rem;color:#888;">
                            <?php echo $nbReservations; ?> en attente
                        </span>
                    </div>
                    <div class="notif-liste" id="notif-liste">
                        <!-- Rempli par JS -->
                        <div class="notif-vide" id="notif-chargement">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                            Chargement...
                        </div>
                    </div>
                    <div class="notif-footer">
                        <a href="/PetMarket/?page=seller">
                            <i class="fa-solid fa-arrow-right"></i> Voir toutes mes réservations
                        </a>
                    </div>
                </div>
            </div>

            <a href="/PetMarket/?page=catalogue" class="btn-contour">
                <i class="fa-solid fa-list"></i> Catalogue
            </a>
            <a href="/PetMarket/?page=logout" class="btn-contour">
                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
            </a>
        </div>
    </div>
</header>

<script>
    // ── État local ────────────────────────────────────────────────
    var dernierNb = <?php echo $nbReservations; ?>;
    var derniereDate = null;
    var dropdownOuvert = false;
    var POLL_INTERVAL = 30000; // 30 secondes

    // ── Toggle dropdown ───────────────────────────────────────────
    function toggleNotifDropdown() {
        dropdownOuvert = !dropdownOuvert;
        document.getElementById('notif-dropdown').classList.toggle('ouvert', dropdownOuvert);
        if (dropdownOuvert) chargerNotifs();
    }

    // Fermer en cliquant ailleurs
    document.addEventListener('click', function(e) {
        var zone = document.getElementById('btn-notif').parentElement;
        if (!zone.contains(e.target)) {
            document.getElementById('notif-dropdown').classList.remove('ouvert');
            dropdownOuvert = false;
        }
    });

    // ── Charger le détail des notifs dans le dropdown ─────────────
    async function chargerNotifs() {
        try {
            const rep = await fetch('/PetMarket/?page=notif_detail');
            const data = await rep.json();
            if (!data.succes) return;

            var liste = document.getElementById('notif-liste');

            if (!data.reservations || data.reservations.length === 0) {
                liste.innerHTML =
                    '<div class="notif-vide">' +
                    '<i class="fa-solid fa-check-circle" style="color:#27ae60;"></i>' +
                    '<p>Aucune réservation en attente</p>' +
                    '</div>';
                return;
            }

            var html = '';
            data.reservations.forEach(function(r) {
                var dateStr = new Date(r.date_commande).toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: 'short',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                html +=
                    '<div class="notif-item">' +
                    '<img src="/PetMarket/public/images/' + escapeHtml(r.animal_photo || 'no-image.jpg') + '" alt="">' +
                    '<div class="notif-item-body">' +
                    '<div class="notif-item-titre">' +
                    '<i class="fa-solid fa-paw" style="color:#e67e22;font-size:.8rem;"></i> ' +
                    escapeHtml(r.animal_nom) +
                    '</div>' +
                    '<div class="notif-item-detail">' +
                    '<i class="fa-solid fa-user"></i> ' + escapeHtml(r.acheteur_pseudo || r.acheteur_nom) +
                    (r.acheteur_tel ?
                        ' &nbsp;<i class="fa-solid fa-phone" style="color:#27ae60;"></i> ' + escapeHtml(r.acheteur_tel) :
                        ' <span style="color:#e74c3c;font-size:.75rem;">(pas de tél)</span>'
                    ) +
                    '</div>' +
                    '<div class="notif-item-detail">' +
                    '<strong>' + Number(r.prix).toLocaleString('fr-FR') + ' FCFA</strong>' +
                    '</div>' +
                    '<div class="notif-item-date"><i class="fa-solid fa-clock"></i> ' + dateStr + '</div>' +
                    '</div>' +
                    '</div>';
            });
            liste.innerHTML = html;

        } catch (e) {
            console.error('Erreur chargement notifs:', e);
        }
    }

    // ── Polling toutes les 30s ────────────────────────────────────
    async function pollNotifications() {
        try {
            const rep = await fetch('/PetMarket/?page=notif');
            const data = await rep.json();
            if (!data.succes) return;

            var nb = data.nb;
            var badge = document.getElementById('badge-notif');
            var btn = document.getElementById('btn-notif');

            // Mise à jour badge
            if (nb > 0) {
                badge.textContent = nb;
                badge.style.display = 'flex';
                btn.classList.add('active');
            } else {
                badge.style.display = 'none';
                btn.classList.remove('active');
            }

            // Mise à jour label dans le dropdown
            document.getElementById('notif-count-label').textContent = nb + ' en attente';

            // Nouvelle commande depuis le dernier poll ?
            if (nb > dernierNb && dernierNb !== null) {
                var nouvelles = nb - dernierNb;
                afficherToast(nouvelles);
                // Actualiser le dropdown si ouvert
                if (dropdownOuvert) chargerNotifs();
            }

            dernierNb = nb;
            derniereDate = data.derniere;

        } catch (e) {
            // Silencieux — pas de spam console en prod
        }
    }

    // ── Toast nouvelle commande ───────────────────────────────────
    function afficherToast(nb) {
        // Supprimer un toast existant
        var ancien = document.getElementById('toast-notif');
        if (ancien) ancien.remove();

        var toast = document.createElement('div');
        toast.className = 'toast-notif';
        toast.id = 'toast-notif';
        toast.onclick = function() {
            window.location.href = '/PetMarket/?page=seller';
        };
        toast.innerHTML =
            '<button class="toast-fermer" onclick="event.stopPropagation();this.parentElement.remove();">' +
            '<i class="fa-solid fa-xmark"></i>' +
            '</button>' +
            '<div class="toast-icone"><i class="fa-solid fa-bell"></i></div>' +
            '<div class="toast-texte">' +
            '<p class="toast-titre">Nouvelle réservation !</p>' +
            '<p class="toast-sous">' +
            (nb > 1 ? nb + ' nouvelles réservations sont' : 'Une réservation est') +
            ' en attente de votre confirmation.' +
            '</p>' +
            '</div>';
        document.body.appendChild(toast);

        // Disparaît automatiquement après 8 secondes
        setTimeout(function() {
            if (toast.parentElement) {
                toast.style.transition = 'opacity .4s, transform .4s';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(120%)';
                setTimeout(function() {
                    if (toast.parentElement) toast.remove();
                }, 400);
            }
        }, 8000);
    }

    // ── Utilitaire XSS ───────────────────────────────────────────
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // ── Lancement ─────────────────────────────────────────────────
    // Premier poll après 30s, puis toutes les 30s
    setInterval(pollNotifications, POLL_INTERVAL);
</script>