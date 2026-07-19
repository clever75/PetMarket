<?php
if (!isset($animaux)) {
    require_once __DIR__ . "/../../controllers/sellerAnimalController.php";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes annonces — PetMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/PetMarket/public/css/seller.css">
    <style>
        /* ── Carte acheteur dans le tableau ──────────────── */
        .acheteur-info {
            background: #fff8f0;
            border: 1px solid #f0d9b5;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: .85rem;
            line-height: 1.9;
            min-width: 190px;
        }
        .acheteur-info p { margin: 0; }
        .acheteur-info .ai-nom {
            font-weight: 700;
            color: #333;
            font-size: .92rem;
        }
        .acheteur-info a {
            color: #e67e22;
            text-decoration: none;
        }
        .acheteur-info a:hover { text-decoration: underline; }

        /* ── Boutons confirmer / annuler ─────────────────── */
        .btn-confirmer {
            background: #27ae60;
            color: #fff;
            border: none;
            padding: 7px 13px;
            border-radius: 6px;
            cursor: pointer;
            font-size: .82rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background .2s;
        }
        .btn-confirmer:hover { background: #219150; }

        .btn-annuler {
            background: #fdecea;
            color: #e74c3c;
            border: 1px solid #f5c6c2;
            padding: 7px 13px;
            border-radius: 6px;
            cursor: pointer;
            font-size: .82rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background .2s;
        }
        .btn-annuler:hover { background: #f9d0cd; }

        /* ── Ligne réservation ───────────────────────────── */
        .tr-reserve td { background: #fffaf5; }

        /* ── Badge commande dans historique ─────────────── */
        .badge-cmd {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-cmd-confirme { background: #e8f5e9; color: #27ae60; }
        .badge-cmd-annule   { background: #fdecea; color: #e74c3c; }
        .badge-cmd-attente  { background: #fff3e0; color: #e67e22; }

        /* ── Historique commandes confirmées ─────────────── */
        .historique-total {
            font-size: .9rem;
            color: #555;
            margin-bottom: 16px;
            padding: 10px 14px;
            background: #f0faf4;
            border-radius: 8px;
            border-left: 3px solid #27ae60;
        }
        .historique-total strong { color: #27ae60; }
    </style>
</head>
<body>
<?php include __DIR__ . "/header_seller.php"; ?>
<main class="admin-page">
    <h1><i class="fa-solid fa-store"></i> Mes annonces</h1>
    <p class="sous-titre">Bonjour, <?php echo htmlspecialchars($_SESSION['user_pseudo'] ?? $_SESSION['user_nom']); ?></p>

    <?php include __DIR__ . "/../layout/flash.php"; ?>

    <?php if (!$profilVendeurOk) : ?>
    <div style="background:#fff3e0;border:1px solid #e67e22;border-left:4px solid #e67e22;
                border-radius:8px;padding:14px 18px;margin-bottom:20px;
                display:flex;align-items:flex-start;gap:12px;">
        <i class="fa-solid fa-triangle-exclamation" style="color:#e67e22;font-size:1.3rem;margin-top:2px;flex-shrink:0;"></i>
        <div>
            <strong>Votre profil est incomplet.</strong>
            Vous ne pouvez pas publier d'annonce tant que ces informations manquent :
            <ul style="margin:6px 0 0;padding-left:18px;color:#b7600a;">
                <?php if (empty($vendeurInfos['telephone'])) : ?>
                    <li>Numéro de téléphone <em>(les acheteurs doivent pouvoir vous joindre)</em></li>
                <?php endif; ?>
                <?php if (empty($vendeurInfos['ville'])) : ?>
                    <li>Ville <em>(les acheteurs veulent savoir où se trouve l'animal)</em></li>
                <?php endif; ?>
            </ul>
            <a href="/PetMarket/?page=profil" style="color:#e67e22;font-weight:600;">
                → Compléter mon profil
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

    <!-- ══ STATS ══════════════════════════════════════════════ -->
    <div class="stats">
        <div class="stat">
            <p class="nombre"><?php echo count($animauxDisponibles); ?></p>
            <p class="libelle"><i class="fa-solid fa-check"></i> Disponibles</p>
        </div>
        <div class="stat" style="<?php echo count($animauxReserves) > 0 ? 'border-color:#e67e22;' : ''; ?>">
            <p class="nombre" style="<?php echo count($animauxReserves) > 0 ? 'color:#e67e22;' : ''; ?>">
                <?php echo count($animauxReserves); ?>
            </p>
            <p class="libelle"><i class="fa-solid fa-clock"></i> Réservés</p>
        </div>
        <div class="stat">
            <p class="nombre"><?php echo count($animauxVendus); ?></p>
            <p class="libelle"><i class="fa-solid fa-tag"></i> Vendus</p>
        </div>
        <div class="stat">
            <p class="nombre"><?php echo count($categories); ?></p>
            <p class="libelle"><i class="fa-solid fa-tags"></i> Catégories</p>
        </div>
    </div>

    <!-- ══ RÉSERVATIONS EN ATTENTE ════════════════════════════ -->
    <?php if (!empty($animauxReserves)) : ?>
    <div class="bloc bloc-alerte">
        <h2>
            <i class="fa-solid fa-clock" style="color:#e67e22;"></i>
            Réservations en attente
            <span class="badge-nombre"><?php echo count($animauxReserves); ?></span>
        </h2>
        <p style="color:#666;font-size:.9rem;margin-bottom:20px;">
            Contactez l'acheteur pour finaliser le paiement, puis <strong>confirmez</strong> la commande
            — ou <strong>annulez</strong> si l'acheteur ne répond pas.
        </p>

        <table>
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Animal</th>
                    <th>Prix</th>
                    <th>Acheteur</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($animauxReserves as $animal) :
                    $idAnimal = (int)$animal['idAnimal'];
                    $cmd      = $commandesParAnimal[$idAnimal] ?? null;
                ?>
                <tr class="tr-reserve">
                    <td>
                        <img class="photo-table"
                             src="/PetMarket/public/images/<?php echo htmlspecialchars($animal['photo'] ?: 'no-image.jpg'); ?>"
                             alt="">
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($animal['nom']); ?></strong><br>
                        <span style="font-size:.8rem;color:#888;">
                            <?php echo htmlspecialchars($animal['categorie_nom'] ?? ''); ?>
                        </span>
                    </td>
                    <td>
                        <strong><?php echo number_format((int)$animal['prix'], 0, ',', ' '); ?> FCFA</strong>
                    </td>
                    <td>
                        <?php if ($cmd) : ?>
                            <div class="acheteur-info">
                                <p class="ai-nom">
                                    <i class="fa-solid fa-user"></i>
                                    <?php echo htmlspecialchars($cmd['acheteur_pseudo'] ?: $cmd['acheteur_nom']); ?>
                                    <?php if ($cmd['acheteur_pseudo'] && $cmd['acheteur_pseudo'] !== $cmd['acheteur_nom']) : ?>
                                        <span style="color:#aaa;font-weight:400;font-size:.8rem;">
                                            (<?php echo htmlspecialchars($cmd['acheteur_nom']); ?>)
                                        </span>
                                    <?php endif; ?>
                                </p>
                                <?php if (!empty($cmd['acheteur_tel'])) : ?>
                                <p>
                                    <i class="fa-solid fa-phone" style="color:#27ae60;"></i>
                                    <a href="tel:<?php echo htmlspecialchars($cmd['acheteur_tel']); ?>">
                                        <?php echo htmlspecialchars($cmd['acheteur_tel']); ?>
                                    </a>
                                    &nbsp;
                                    <a href="https://wa.me/<?php echo preg_replace('/\D/', '', $cmd['acheteur_tel']); ?>"
                                       target="_blank"
                                       style="color:#25d366;"
                                       title="Contacter sur WhatsApp">
                                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                                    </a>
                                </p>
                                <?php else : ?>
                                <p style="color:#aaa;font-size:.8rem;">
                                    <i class="fa-solid fa-phone-slash"></i> Pas de téléphone renseigné
                                </p>
                                <?php endif; ?>
                                <p>
                                    <i class="fa-solid fa-envelope" style="color:#e67e22;"></i>
                                    <a href="mailto:<?php echo htmlspecialchars($cmd['acheteur_email']); ?>">
                                        <?php echo htmlspecialchars($cmd['acheteur_email']); ?>
                                    </a>
                                </p>
                                <p style="color:#aaa;font-size:.78rem;margin-top:4px;">
                                    <i class="fa-solid fa-calendar"></i>
                                    Commandé le <?php echo date('d/m/Y à H:i', strtotime($cmd['date_commande'])); ?>
                                </p>
                            </div>
                        <?php else : ?>
                            <span style="color:#aaa;font-size:.85rem;">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                Commande introuvable
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($cmd) : ?>
                            <!-- ✅ Confirmer : paiement reçu -->
                            <form method="post" action="/PetMarket/?page=seller"
                                  style="display:block;margin-bottom:8px;"
                                  onsubmit="return confirm('Confirmer la vente de <?php echo addslashes(htmlspecialchars($animal['nom'])); ?> ? Le paiement a bien été reçu.')">
                                <input type="hidden" name="idAnimal"   value="<?php echo $idAnimal; ?>">
                                <input type="hidden" name="idCommande" value="<?php echo (int)$cmd['idCommande']; ?>">
                                <button type="submit" name="confirmer_commande" class="btn-confirmer">
                                    <i class="fa-solid fa-circle-check"></i> Confirmer (payé)
                                </button>
                            </form>
                            <!-- ❌ Annuler : acheteur ne répond pas -->
                            <form method="post" action="/PetMarket/?page=seller"
                                  onsubmit="return confirm('Annuler cette commande et remettre <?php echo addslashes(htmlspecialchars($animal['nom'])); ?> en disponible ?')">
                                <input type="hidden" name="idAnimal"   value="<?php echo $idAnimal; ?>">
                                <input type="hidden" name="idCommande" value="<?php echo (int)$cmd['idCommande']; ?>">
                                <button type="submit" name="annuler_commande" class="btn-annuler">
                                    <i class="fa-solid fa-ban"></i> Annuler
                                </button>
                            </form>
                        <?php else : ?>
                            <!-- Pas de commande enregistrée — fallback anciens boutons -->
                            <form method="post" action="/PetMarket/?page=seller"
                                  style="display:block;margin-bottom:8px;">
                                <input type="hidden" name="idAnimal" value="<?php echo $idAnimal; ?>">
                                <button type="submit" name="marquer_vendu" class="btn-vendu">
                                    <i class="fa-solid fa-circle-check"></i> Marquer vendu
                                </button>
                            </form>
                            <form method="post" action="/PetMarket/?page=seller"
                                  onsubmit="return confirm('Annuler et remettre en disponible ?')">
                                <input type="hidden" name="idAnimal" value="<?php echo $idAnimal; ?>">
                                <button type="submit" name="marquer_dispo" class="btn-contour btn-petit">
                                    <i class="fa-solid fa-rotate-left"></i> Remettre dispo
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- ══ PUBLIER UNE ANNONCE ════════════════════════════════ -->
    <div class="bloc">
        <h2><i class="fa-solid fa-plus"></i> Publier une annonce</h2>
        <form method="post" enctype="multipart/form-data" action="/PetMarket/?page=seller">
            <div class="form-ligne">
                <div class="champ">
                    <label>Nom *</label>
                    <input type="text" name="nom" placeholder="Ex: Milo" required>
                </div>
                <div class="champ">
                    <label>Prix (FCFA) *</label>
                    <input type="number" name="prix" placeholder="Ex: 15000" required>
                </div>
                <div class="champ">
                    <label>Catégorie *</label>
                    <select name="idCategorie" id="idCategorieAnnonce" required>
                        <option value="">Choisir...</option>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?php echo (int)$cat['idCategorie']; ?>">
                                <?php echo htmlspecialchars($cat['libelle']); ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="0">Autre categorie...</option>
                    </select>
                    <input type="text"
                           name="nouvelleCategorie"
                           id="nouvelleCategorieAnnonce"
                           placeholder="Ex: Hamster"
                           maxlength="50"
                           style="display:none;margin-top:8px;">
                </div>
                <div class="champ">
                    <label>Photo *</label>
                    <input type="file" name="photo" accept="image/*" required>
                </div>
            </div>
            <div class="champ">
                <label>Description (optionnel)</label>
                <textarea name="description" placeholder="Décrivez votre animal..." rows="3"></textarea>
            </div>
            <?php if ($profilVendeurOk) : ?>
            <button type="submit" name="creer" class="btn-orange">
                <i class="fa-solid fa-paper-plane"></i> Publier
            </button>
            <?php else : ?>
            <button type="button" class="btn-orange" disabled
                    style="opacity:.45;cursor:not-allowed;"
                    title="Complétez votre profil avant de publier">
                <i class="fa-solid fa-lock"></i> Complétez votre profil pour publier
            </button>
            <?php endif; ?>
        </form>
    </div>

    <!-- ══ ANNONCES DISPONIBLES ═══════════════════════════════ -->
    <div class="bloc">
        <h2><i class="fa-solid fa-list"></i> Annonces disponibles (<?php echo count($animauxDisponibles); ?>)</h2>
        <?php if (!empty($animauxDisponibles)) : ?>
        <table>
            <thead>
                <tr><th>Photo</th><th>Nom</th><th>Catégorie</th><th>Prix</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($animauxDisponibles as $animal) : ?>
                <tr>
                    <td><img class="photo-table"
                             src="/PetMarket/public/images/<?php echo htmlspecialchars($animal['photo'] ?: 'no-image.jpg'); ?>"
                             alt=""></td>
                    <td><?php echo htmlspecialchars($animal['nom']); ?></td>
                    <td><?php echo htmlspecialchars($animal['categorie_nom'] ?? '—'); ?></td>
                    <td><?php echo number_format((int)$animal['prix'], 0, ',', ' '); ?> FCFA</td>
                    <td>
                        <button class="btn-modifier"
                                onclick="afficherModif('animal-<?php echo (int)$animal['idAnimal']; ?>')">
                            <i class="fa-solid fa-pen"></i> Modifier
                        </button>
                        <form method="post" action="/PetMarket/?page=seller"
                              style="display:inline;"
                              onsubmit="return confirm('Supprimer cette annonce ?')">
                            <input type="hidden" name="idAnimal" value="<?php echo (int)$animal['idAnimal']; ?>">
                            <button type="submit" name="supprimer" class="btn-supprimer">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <!-- Formulaire modification inline -->
                <tr id="animal-<?php echo (int)$animal['idAnimal']; ?>" style="display:none;">
                    <td colspan="5">
                        <form method="post" enctype="multipart/form-data"
                              action="/PetMarket/?page=seller" class="form-modif">
                            <input type="hidden" name="idAnimal" value="<?php echo (int)$animal['idAnimal']; ?>">
                            <div class="form-ligne">
                                <div class="champ">
                                    <label>Nom</label>
                                    <input type="text" name="nom"
                                           value="<?php echo htmlspecialchars($animal['nom']); ?>" required>
                                </div>
                                <div class="champ">
                                    <label>Prix</label>
                                    <input type="number" name="prix"
                                           value="<?php echo (int)$animal['prix']; ?>" required>
                                </div>
                                <div class="champ">
                                    <label>Catégorie</label>
                                    <select name="idCategorie" required>
                                        <?php foreach ($categories as $cat) : ?>
                                            <option value="<?php echo (int)$cat['idCategorie']; ?>"
                                                <?php echo ((int)$cat['idCategorie'] === (int)$animal['idCategorie']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['libelle']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="champ">
                                    <label>Nouvelle photo (optionnel)</label>
                                    <input type="file" name="photo" accept="image/*">
                                </div>
                            </div>
                            <div class="champ">
                                <label>Description</label>
                                <textarea name="description" rows="3"><?php echo htmlspecialchars($animal['description'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" name="modifier" class="btn-orange">
                                <i class="fa-solid fa-floppy-disk"></i> Enregistrer
                            </button>
                            <button type="button" class="btn-contour"
                                    onclick="afficherModif('animal-<?php echo (int)$animal['idAnimal']; ?>')">
                                Annuler
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else : ?>
            <p class="message-vide">
                <i class="fa-solid fa-megaphone"></i> Aucune annonce disponible.
            </p>
        <?php endif; ?>
    </div>

    <!-- ══ HISTORIQUE DES VENTES ══════════════════════════════ -->
    <?php if (!empty($animauxVendus)) :
        // Calcul du total des ventes confirmées
        $totalVentes = 0;
        $commandesConfirmees = array_filter($commandesRecues, fn($c) => $c['statut'] === 'confirme');
        foreach ($commandesConfirmees as $c) { $totalVentes += (int)$c['prix']; }
    ?>
    <div class="bloc">
        <h2><i class="fa-solid fa-tag"></i> Historique des ventes (<?php echo count($animauxVendus); ?>)</h2>

        <?php if ($totalVentes > 0) : ?>
        <div class="historique-total">
            <i class="fa-solid fa-coins"></i>
            Total des ventes confirmées :
            <strong><?php echo number_format($totalVentes, 0, ',', ' '); ?> FCFA</strong>
        </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr><th>Photo</th><th>Nom</th><th>Catégorie</th><th>Prix</th><th>Acheteur</th><th>Statut</th></tr>
            </thead>
            <tbody>
                <?php
                // Indexer les commandes confirmées/annulées par idAnimal
                $histoParAnimal = [];
                foreach ($commandesRecues as $c) {
                    if (in_array($c['statut'], ['confirme', 'annule'])) {
                        $histoParAnimal[(int)$c['idAnimal']] = $c;
                    }
                }
                foreach ($animauxVendus as $animal) :
                    $histo = $histoParAnimal[(int)$animal['idAnimal']] ?? null;
                ?>
                <tr style="opacity:.8;">
                    <td>
                        <img class="photo-table"
                             src="/PetMarket/public/images/<?php echo htmlspecialchars($animal['photo'] ?: 'no-image.jpg'); ?>"
                             alt=""
                             style="filter:grayscale(50%);">
                    </td>
                    <td><?php echo htmlspecialchars($animal['nom']); ?></td>
                    <td><?php echo htmlspecialchars($animal['categorie_nom'] ?? '—'); ?></td>
                    <td><?php echo number_format((int)$animal['prix'], 0, ',', ' '); ?> FCFA</td>
                    <td style="font-size:.85rem;">
                        <?php if ($histo) : ?>
                            <strong><?php echo htmlspecialchars($histo['acheteur_pseudo'] ?: $histo['acheteur_nom']); ?></strong><br>
                            <span style="color:#888;"><?php echo htmlspecialchars($histo['acheteur_email']); ?></span>
                            <?php if (!empty($histo['acheteur_tel'])) : ?>
                            <br><span style="color:#27ae60;"><?php echo htmlspecialchars($histo['acheteur_tel']); ?></span>
                            <?php endif; ?>
                        <?php else : ?>
                            <span style="color:#aaa;">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($histo && $histo['statut'] === 'confirme') : ?>
                            <span class="badge-cmd badge-cmd-confirme">
                                <i class="fa-solid fa-circle-check"></i> Confirmé
                            </span>
                        <?php else : ?>
                            <span class="badge-vendu">
                                <i class="fa-solid fa-circle-check"></i> Vendu
                            </span>
                        <?php endif; ?>
                        <?php if ($histo) : ?>
                        <br>
                        <span style="color:#aaa;font-size:.75rem;">
                            <?php echo date('d/m/Y', strtotime($histo['date_commande'])); ?>
                        </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</main>

<script>
function afficherModif(id) {
    var ligne = document.getElementById(id);
    ligne.style.display = ligne.style.display === 'none' ? 'table-row' : 'none';
}

var selectCategorieAnnonce = document.getElementById('idCategorieAnnonce');
var inputNouvelleCategorie = document.getElementById('nouvelleCategorieAnnonce');
if (selectCategorieAnnonce && inputNouvelleCategorie) {
    function afficherNouvelleCategorie() {
        var afficher = selectCategorieAnnonce.value === '0';
        inputNouvelleCategorie.style.display = afficher ? 'block' : 'none';
        inputNouvelleCategorie.required = afficher;
        if (!afficher) {
            inputNouvelleCategorie.value = '';
        }
    }

    selectCategorieAnnonce.addEventListener('change', afficherNouvelleCategorie);
    afficherNouvelleCategorie();
}
</script>
</body>
</html>
