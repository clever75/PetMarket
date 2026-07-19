<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../controllers/profilController.php";

// Infos manquantes → bannière d'alerte
$telephoneManquant = empty($user['telephone']);
$villeManquante    = empty($user['ville']);
$profilIncomplet   = $telephoneManquant || $villeManquante;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil — PetMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/PetMarket/public/css/header.css">
    <link rel="stylesheet" href="/PetMarket/public/css/footer.css">
    <link rel="stylesheet" href="/PetMarket/public/css/profil.css">
    <style>
        /* ── Bannière profil incomplet ───────────────────── */
        .banniere-incomplet {
            background: #fff3e0;
            border: 1px solid #e67e22;
            border-left: 4px solid #e67e22;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: .92rem;
        }
        .banniere-incomplet i.icone-alerte {
            color: #e67e22;
            font-size: 1.3rem;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .banniere-incomplet ul {
            margin: 6px 0 0 0;
            padding-left: 18px;
            color: #b7600a;
        }
        .banniere-incomplet a {
            color: #e67e22;
            font-weight: 600;
            text-decoration: underline;
            cursor: pointer;
        }

        /* ── Onglets ──────────────────────────────────────── */
        .profil-onglets {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            border-bottom: 2px solid #eee;
            padding-bottom: 0;
            flex-wrap: wrap;
        }
        .profil-onglet {
            padding: 10px 20px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: .95rem;
            color: #666;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            border-radius: 4px 4px 0 0;
            transition: color .2s, border-color .2s;
        }
        .profil-onglet:hover { color: #e67e22; }
        .profil-onglet.actif {
            color: #e67e22;
            border-bottom-color: #e67e22;
            font-weight: 600;
        }
        .profil-section { display: none; }
        .profil-section.active { display: block; }

        /* ── Champ incomplet (highlight) ─────────────────── */
        input.champ-manquant {
            border-color: #e67e22 !important;
            background: #fff8f3 !important;
        }
        .label-requis {
            color: #e67e22;
            font-size: .78rem;
            font-weight: normal;
            margin-left: 6px;
        }

        /* ── Tableau commandes ────────────────────────────── */
        .commandes-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .9rem;
        }
        .commandes-table th {
            background: #f8f8f8;
            padding: 10px 14px;
            text-align: left;
            font-weight: 600;
            color: #444;
            border-bottom: 2px solid #eee;
        }
        .commandes-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        .commandes-table tr:last-child td { border-bottom: none; }
        .commandes-table img {
            width: 48px; height: 48px;
            object-fit: cover;
            border-radius: 6px;
        }
        .badge-commande {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-en_attente { background:#fff3e0; color:#e67e22; }
        .badge-confirme   { background:#e8f5e9; color:#27ae60; }
        .badge-annule     { background:#fdecea; color:#e74c3c; }

        .commandes-stats {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .commandes-stat {
            background: #f8f8f8;
            border-radius: 10px;
            padding: 14px 22px;
            text-align: center;
            min-width: 100px;
        }
        .commandes-stat .cs-nombre {
            font-size: 1.6rem;
            font-weight: 700;
            color: #e67e22;
        }
        .commandes-stat .cs-label {
            font-size: .78rem;
            color: #888;
            margin-top: 2px;
        }
        .message-vide {
            text-align: center;
            padding: 40px 20px;
            color: #aaa;
        }
        .message-vide i { font-size: 2.5rem; margin-bottom: 12px; display: block; }
    </style>
</head>
<body>

    <?php include __DIR__ . "/../layout/header.php"; ?>
    <?php include __DIR__ . "/../layout/flash.php"; ?>

    <main>
        <div class="contenu">

            <h1><i class="fa-solid fa-user"></i> Mon profil</h1>

            <!-- ══ BANNIÈRE PROFIL INCOMPLET ════════════════ -->
            <?php if ($profilIncomplet && !($_SESSION['user_is_admin'] ?? false)) : ?>
            <div class="banniere-incomplet">
                <i class="fa-solid fa-triangle-exclamation icone-alerte"></i>
                <div>
                    <strong>Votre profil est incomplet.</strong>
                    Vous ne pourrez pas commander tant que les informations suivantes manquent :
                    <ul>
                        <?php if ($telephoneManquant) : ?>
                            <li>Numéro de téléphone <em>(le vendeur en a besoin pour vous contacter)</em></li>
                        <?php endif; ?>
                        <?php if ($villeManquante) : ?>
                            <li>Ville <em>(pour organiser la récupération de l'animal)</em></li>
                        <?php endif; ?>
                    </ul>
                    <a onclick="afficherSection('infos', document.querySelector('.profil-onglet'))">
                        → Compléter mes infos maintenant
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($erreur)) : ?>
                <div class="alerte-erreur">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo htmlspecialchars($erreur); ?>
                </div>
            <?php endif; ?>

            <div class="profil-grille">

                <!-- ── CARTE PROFIL ──────────────────────── -->
                <div class="profil-carte">
                    <div class="profil-avatar">
                        <?php echo strtoupper(substr($user['nom'], 0, 1)); ?>
                    </div>
                    <h2><?php echo htmlspecialchars($user['pseudo'] ?? $user['nom']); ?></h2>
                    <p class="profil-email">
                        <i class="fa-solid fa-envelope"></i>
                        <?php echo htmlspecialchars($user['email']); ?>
                    </p>
                    <?php if (!empty($user['telephone'])) : ?>
                        <p class="profil-info">
                            <i class="fa-solid fa-phone"></i>
                            <?php echo htmlspecialchars($user['telephone']); ?>
                        </p>
                    <?php else : ?>
                        <p class="profil-info" style="color:#e67e22;">
                            <i class="fa-solid fa-phone-slash"></i> Téléphone manquant
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($user['ville'])) : ?>
                        <p class="profil-info">
                            <i class="fa-solid fa-location-dot"></i>
                            <?php echo htmlspecialchars($user['ville']); ?>
                        </p>
                    <?php else : ?>
                        <p class="profil-info" style="color:#e67e22;">
                            <i class="fa-solid fa-location-dot"></i> Ville manquante
                        </p>
                    <?php endif; ?>
                    <span class="profil-role">
                        <?php if ($_SESSION['user_is_admin'] ?? false) : ?>
                            <i class="fa-solid fa-shield"></i> Administrateur
                        <?php elseif (($_SESSION['user_role'] ?? '') === 'vendeur') : ?>
                            <i class="fa-solid fa-store"></i> Vendeur
                        <?php else : ?>
                            <i class="fa-solid fa-user"></i> Acheteur
                        <?php endif; ?>
                    </span>
                    <?php if (!empty($commandes)) : ?>
                        <div style="margin-top:16px;font-size:.85rem;color:#888;">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <?php echo count($commandes); ?> commande(s)
                        </div>
                    <?php endif; ?>

                    <?php
                    $role          = $_SESSION['user_role']     ?? 'acheteur';
                    $estAdmin      = $_SESSION['user_is_admin'] ?? false;
                    $demandeEnCours= (int)($user['seller_request'] ?? 0) === 1;
                    ?>
                    <?php if (!$estAdmin && $role === 'acheteur') : ?>
                        <div style="margin-top:20px;border-top:1px solid #f0f0f0;padding-top:16px;">
                            <?php if ($demandeEnCours) : ?>
                                <div style="background:#fff3e0;border:1px solid #f0d0a0;border-radius:8px;
                                            padding:10px 14px;font-size:.83rem;color:#b7600a;text-align:center;">
                                    <i class="fa-solid fa-clock"></i>
                                    Demande vendeur en cours de validation…
                                </div>
                            <?php else : ?>
                                <form method="post" action="/PetMarket/?page=profil">
                                    <button type="submit" name="demander_vendeur"
                                            class="btn-orange"
                                            style="width:100%;justify-content:center;"
                                            onclick="return confirm('Envoyer une demande pour devenir vendeur ?')">
                                        <i class="fa-solid fa-store"></i> Devenir vendeur
                                    </button>
                                </form>
                                <p style="font-size:.75rem;color:#aaa;text-align:center;margin-top:6px;">
                                    Votre demande sera validée par un administrateur.
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ── CONTENU DROITE ────────────────────── -->
                <div class="profil-formulaires">

                    <!-- Onglets -->
                    <div class="profil-onglets">
                        <button class="profil-onglet actif" onclick="afficherSection('infos', this)">
                            <i class="fa-solid fa-user-pen"></i> Mes infos
                            <?php if ($profilIncomplet) : ?>
                                <span style="background:#e67e22;color:#fff;border-radius:10px;
                                             padding:1px 6px;font-size:.7rem;margin-left:4px;">!</span>
                            <?php endif; ?>
                        </button>
                        <button class="profil-onglet" onclick="afficherSection('mdp', this)">
                            <i class="fa-solid fa-lock"></i> Mot de passe
                        </button>
                        <?php if (($_SESSION['user_role'] ?? '') === 'acheteur' && !($_SESSION['user_is_admin'] ?? false)) : ?>
                        <button class="profil-onglet" onclick="afficherSection('commandes', this)">
                            <i class="fa-solid fa-bag-shopping"></i> Mes commandes
                            <?php if (!empty($commandes)) : ?>
                                <span style="background:#e67e22;color:#fff;border-radius:10px;
                                             padding:1px 7px;font-size:.72rem;margin-left:4px;">
                                    <?php echo count($commandes); ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <?php endif; ?>
                    </div>

                    <!-- ══ MES INFOS ══ -->
                    <div id="section-infos" class="profil-section active">
                        <div class="bloc">
                            <h2><i class="fa-solid fa-user-pen"></i> Mes informations</h2>
                            <form method="post" action="/PetMarket/?page=profil">
                                <div class="champ">
                                    <label><i class="fa-solid fa-user"></i> Nom complet</label>
                                    <input type="text" name="nom"
                                           value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                                </div>
                                <div class="champ">
                                    <label><i class="fa-solid fa-at"></i> Pseudo</label>
                                    <input type="text" name="pseudo"
                                           value="<?php echo htmlspecialchars($user['pseudo'] ?? ''); ?>"
                                           placeholder="Ton pseudo">
                                </div>
                                <div class="champ">
                                    <label>
                                        <i class="fa-solid fa-phone"></i> Téléphone
                                        <?php if ($telephoneManquant) : ?>
                                            <span class="label-requis">— requis pour commander</span>
                                        <?php endif; ?>
                                    </label>
                                    <input type="tel" name="telephone"
                                           value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>"
                                           placeholder="Ex: 90 00 00 00"
                                           class="<?php echo $telephoneManquant ? 'champ-manquant' : ''; ?>">
                                </div>
                                <div class="champ">
                                    <label>
                                        <i class="fa-solid fa-location-dot"></i> Ville
                                        <?php if ($villeManquante) : ?>
                                            <span class="label-requis">— requis pour commander</span>
                                        <?php endif; ?>
                                    </label>
                                    <input type="text" name="ville"
                                           value="<?php echo htmlspecialchars($user['ville'] ?? ''); ?>"
                                           placeholder="Ex: Lomé"
                                           class="<?php echo $villeManquante ? 'champ-manquant' : ''; ?>">
                                </div>
                                <div class="champ">
                                    <label><i class="fa-solid fa-envelope"></i> Email</label>
                                    <input type="email"
                                           value="<?php echo htmlspecialchars($user['email']); ?>"
                                           disabled>
                                    <small>L'email ne peut pas être modifié.</small>
                                </div>
                                <button type="submit" name="modifier_profil" class="btn-orange">
                                    <i class="fa-solid fa-floppy-disk"></i> Enregistrer
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- ══ MOT DE PASSE ══ -->
                    <div id="section-mdp" class="profil-section">
                        <div class="bloc">
                            <h2><i class="fa-solid fa-lock"></i> Changer le mot de passe</h2>
                            <form method="post" action="/PetMarket/?page=profil"
                                  onsubmit="return verifierMdp()">
                                <div class="champ">
                                    <label>Ancien mot de passe</label>
                                    <div class="champ-mdp">
                                        <input type="password" id="ancien" name="ancien_mdp" required>
                                        <button type="button" class="btn-oeil" onclick="toggleMdp('ancien', this)">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="champ">
                                    <label>Nouveau mot de passe</label>
                                    <div class="champ-mdp">
                                        <input type="password" id="nouveau" name="nouveau_mdp" required>
                                        <button type="button" class="btn-oeil" onclick="toggleMdp('nouveau', this)">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="champ">
                                    <label>Confirmer le nouveau mot de passe</label>
                                    <div class="champ-mdp">
                                        <input type="password" id="confirm" name="confirm_mdp" required>
                                        <button type="button" class="btn-oeil" onclick="toggleMdp('confirm', this)">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                    <p id="erreur-mdp" class="erreur-inline" style="display:none;">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        Les mots de passe ne correspondent pas.
                                    </p>
                                </div>
                                <button type="submit" name="changer_mdp" class="btn-orange">
                                    <i class="fa-solid fa-key"></i> Changer le mot de passe
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- ══ MES COMMANDES ══ -->
                    <?php if (($_SESSION['user_role'] ?? '') === 'acheteur' && !($_SESSION['user_is_admin'] ?? false)) : ?>
                    <div id="section-commandes" class="profil-section">
                        <div class="bloc">
                            <h2><i class="fa-solid fa-bag-shopping"></i> Mes commandes</h2>
                            <?php if (!empty($commandes)) :
                                $nbAttente    = count(array_filter($commandes, fn($c) => $c['statut'] === 'en_attente'));
                                $nbConfirme   = count(array_filter($commandes, fn($c) => $c['statut'] === 'confirme'));
                                $totalDepense = array_sum(array_column(
                                    array_filter($commandes, fn($c) => $c['statut'] !== 'annule'), 'prix'
                                ));
                            ?>
                            <div class="commandes-stats">
                                <div class="commandes-stat">
                                    <div class="cs-nombre"><?php echo count($commandes); ?></div>
                                    <div class="cs-label">Total</div>
                                </div>
                                <?php if ($nbAttente > 0) : ?>
                                <div class="commandes-stat">
                                    <div class="cs-nombre" style="color:#e67e22;"><?php echo $nbAttente; ?></div>
                                    <div class="cs-label">En attente</div>
                                </div>
                                <?php endif; ?>
                                <?php if ($nbConfirme > 0) : ?>
                                <div class="commandes-stat">
                                    <div class="cs-nombre" style="color:#27ae60;"><?php echo $nbConfirme; ?></div>
                                    <div class="cs-label">Confirmées</div>
                                </div>
                                <?php endif; ?>
                                <div class="commandes-stat">
                                    <div class="cs-nombre" style="font-size:1.1rem;">
                                        <?php echo number_format($totalDepense, 0, ',', ' '); ?> FCFA
                                    </div>
                                    <div class="cs-label">Dépensé</div>
                                </div>
                            </div>

                            <table class="commandes-table">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Animal</th>
                                        <th>Catégorie</th>
                                        <th>Prix</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($commandes as $cmd) :
                                        $statut = $cmd['statut'];
                                        $labels = [
                                            'en_attente' => ['<i class="fa-solid fa-clock"></i> En attente',      'badge-en_attente'],
                                            'confirme'   => ['<i class="fa-solid fa-circle-check"></i> Confirmé', 'badge-confirme'],
                                            'annule'     => ['<i class="fa-solid fa-ban"></i> Annulé',            'badge-annule'],
                                        ];
                                        $info = $labels[$statut] ?? ['Inconnu', 'badge-en_attente'];
                                    ?>
                                    <tr>
                                        <td>
                                            <img src="/PetMarket/public/images/<?php echo htmlspecialchars($cmd['animal_photo'] ?? 'no-image.jpg'); ?>"
                                                 alt="<?php echo htmlspecialchars($cmd['animal_nom']); ?>">
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($cmd['animal_nom']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($cmd['categorie_nom'] ?? '—'); ?></td>
                                        <td><?php echo number_format((int)$cmd['prix'], 0, ',', ' '); ?> FCFA</td>
                                        <td style="color:#888;font-size:.85rem;">
                                            <?php echo date('d/m/Y', strtotime($cmd['date_commande'])); ?>
                                        </td>
                                        <td>
                                            <span class="badge-commande <?php echo $info[1]; ?>">
                                                <?php echo $info[0]; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <p style="margin-top:18px;font-size:.85rem;color:#888;line-height:1.7;">
                                <i class="fa-solid fa-circle-info" style="color:#e67e22;"></i>
                                Après votre commande, le vendeur vous contactera via
                                <strong>WhatsApp</strong>, <strong>Flooz</strong> ou <strong>T-Money</strong>
                                pour finaliser le paiement.
                            </p>

                            <?php else : ?>
                            <div class="message-vide">
                                <i class="fa-solid fa-bag-shopping"></i>
                                <p>Vous n'avez pas encore passé de commande.</p>
                                <a href="/PetMarket/?page=catalogue" class="btn-orange"
                                   style="margin-top:12px;display:inline-block;">
                                    <i class="fa-solid fa-list"></i> Voir le catalogue
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div><!-- /.profil-formulaires -->
            </div><!-- /.profil-grille -->

        </div><!-- /.contenu -->
    </main>

    <?php include __DIR__ . "/../layout/footer.php"; ?>

    <script>
        function afficherSection(nom, bouton) {
            document.querySelectorAll('.profil-section').forEach(function(s) {
                s.classList.remove('active');
            });
            document.querySelectorAll('.profil-onglet').forEach(function(b) {
                b.classList.remove('actif');
            });
            document.getElementById('section-' + nom).classList.add('active');
            if (bouton) bouton.classList.add('actif');
        }

        window.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash === '#commandes') {
                var btn = document.querySelectorAll('.profil-onglet')[2];
                if (btn) afficherSection('commandes', btn);
            }
            // Ouvre "Mes infos" si le profil est incomplet
            <?php if ($profilIncomplet) : ?>
            // Scroll vers la bannière
            var banniere = document.querySelector('.banniere-incomplet');
            if (banniere) banniere.scrollIntoView({ behavior: 'smooth', block: 'start' });
            <?php endif; ?>
        });

        function toggleMdp(idChamp, bouton) {
            var champ = document.getElementById(idChamp);
            var icone = bouton.querySelector('i');
            champ.type = champ.type === 'password' ? 'text' : 'password';
            icone.className = champ.type === 'password' ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
        }

        function verifierMdp() {
            var nouveau = document.getElementById('nouveau').value;
            var confirm = document.getElementById('confirm').value;
            var erreur  = document.getElementById('erreur-mdp');
            if (nouveau !== confirm) {
                erreur.style.display = 'block';
                return false;
            }
            erreur.style.display = 'none';
            return true;
        }
    </script>

</body>
</html>