<?php
if (!isset($animaux)) {
    require_once __DIR__ . "/../../controllers/adminAnimalController.php";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — PetMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/PetMarket/public/css/admin.css">
</head>
<body>

<header>
    <div class="header-inner">
        <a href="/PetMarket/?page=accueil" class="logo"><i class="fa-solid fa-paw"></i> PetMarket</a>
        <span class="admin-badge"><i class="fa-solid fa-gauge"></i> Administration</span>
        <div style="display:flex;gap:10px;">
            <a href="/PetMarket/?page=catalogue" class="btn-contour"><i class="fa-solid fa-list"></i> Catalogue</a>
            <a href="/PetMarket/?page=logout" class="btn-contour"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
        </div>
    </div>
</header>

<main class="admin-page">

    <h1><i class="fa-solid fa-gauge"></i> Tableau de bord</h1>
    <p class="sous-titre">Bienvenue, <?php echo htmlspecialchars($_SESSION['user_nom']); ?></p>

    <?php include __DIR__ . "/../layout/flash.php"; ?>

    <?php if (!empty($erreur)) : ?>
        <div class="alerte-erreur"><i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($erreur); ?></div>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════════════
         STATS
         ═══════════════════════════════════════════════════ -->
    <div class="stats">
        <div class="stat">
            <p class="nombre"><?php echo count($animaux); ?></p>
            <p class="libelle"><i class="fa-solid fa-paw"></i> Animaux</p>
        </div>
        <div class="stat">
            <p class="nombre"><?php echo count(array_filter($animaux, fn($a) => ($a['statut'] ?? 'disponible') === 'disponible')); ?></p>
            <p class="libelle"><i class="fa-solid fa-check"></i> Disponibles</p>
        </div>
        <div class="stat">
            <p class="nombre"><?php echo count(array_filter($animaux, fn($a) => ($a['statut'] ?? '') === 'reserve')); ?></p>
            <p class="libelle"><i class="fa-solid fa-clock"></i> Réservés</p>
        </div>
        <div class="stat">
            <p class="nombre"><?php echo count(array_filter($animaux, fn($a) => ($a['statut'] ?? '') === 'vendu')); ?></p>
            <p class="libelle"><i class="fa-solid fa-tag"></i> Vendus</p>
        </div>
        <div class="stat">
            <p class="nombre"><?php echo count($utilisateurs); ?></p>
            <p class="libelle"><i class="fa-solid fa-users"></i> Utilisateurs</p>
        </div>
        <div class="stat" style="<?php echo count($demandesVendeur) > 0 ? 'border-color:#e67e22;' : ''; ?>">
            <p class="nombre" style="<?php echo count($demandesVendeur) > 0 ? 'color:#e67e22;' : ''; ?>">
                <?php echo count($demandesVendeur); ?>
            </p>
            <p class="libelle"><i class="fa-solid fa-store"></i> Demandes vendeur</p>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════
         AJOUTER UN ANIMAL
         ═══════════════════════════════════════════════════ -->
    <div class="bloc">
        <h2><i class="fa-solid fa-plus"></i> Ajouter un animal</h2>
        <form method="post" enctype="multipart/form-data" action="/PetMarket/?page=admin">
            <div class="form-ligne">
                <div class="champ"><label>Nom *</label><input type="text" name="nom" placeholder="Ex: Milo" required></div>
                <div class="champ"><label>Prix (FCFA) *</label><input type="number" name="prix" placeholder="Ex: 15000" required></div>
                <div class="champ">
                    <label>Catégorie *</label>
                    <select name="idCategorie" required>
                        <option value="">Choisir...</option>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?php echo (int)$cat['idCategorie']; ?>"><?php echo htmlspecialchars($cat['libelle']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="champ"><label>Photo *</label><input type="file" name="photo" accept="image/*" required></div>
            </div>
            <div class="champ">
                <label>Description (optionnel)</label>
                <textarea name="description" placeholder="Décrivez l'animal..." rows="3"></textarea>
            </div>
            <button type="submit" name="creer_animal" class="btn-orange"><i class="fa-solid fa-plus"></i> Ajouter</button>
        </form>
    </div>

    <!-- ═══════════════════════════════════════════════════
         LISTE DES ANIMAUX
         ═══════════════════════════════════════════════════ -->
    <div class="bloc">
        <h2><i class="fa-solid fa-list"></i> Animaux (<?php echo count($animaux); ?>)</h2>
        <?php if (!empty($animaux)) : ?>
        <table>
            <thead>
                <tr><th>Photo</th><th>Nom</th><th>Catégorie</th><th>Prix</th><th>Statut</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($animaux as $animal) :
                    $statut = $animal['statut'] ?? 'disponible';
                ?>
                <tr>
                    <td><img class="photo-table"
                             src="/PetMarket/public/images/<?php echo htmlspecialchars($animal['photo'] ?: 'no-image.jpg'); ?>"
                             alt=""
                             style="<?php echo $statut !== 'disponible' ? 'filter:grayscale(50%);' : ''; ?>">
                    </td>
                    <td><?php echo htmlspecialchars($animal['nom']); ?></td>
                    <td><?php echo htmlspecialchars($animal['categorie_nom'] ?? '—'); ?></td>
                    <td><?php echo number_format((int)$animal['prix'], 0, ',', ' '); ?> FCFA</td>
                    <td>
                        <?php if ($statut === 'vendu') : ?>
                            <span class="badge-vendu"><i class="fa-solid fa-ban"></i> Vendu</span>
                        <?php elseif ($statut === 'reserve') : ?>
                            <span class="badge-reserve"><i class="fa-solid fa-clock"></i> Réservé</span>
                        <?php else : ?>
                            <span class="badge-dispo"><i class="fa-solid fa-check"></i> Disponible</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn-modifier" onclick="afficherModif('animal-<?php echo (int)$animal['idAnimal']; ?>')">
                            <i class="fa-solid fa-pen"></i> Modifier
                        </button>

                        <?php if ($statut === 'disponible') : ?>
                            <form method="post" action="/PetMarket/?page=admin" style="display:inline;">
                                <input type="hidden" name="idAnimal" value="<?php echo (int)$animal['idAnimal']; ?>">
                                <button type="submit" name="marquer_vendu" class="btn-vendu">
                                    <i class="fa-solid fa-tag"></i> Vendu
                                </button>
                            </form>
                        <?php elseif ($statut === 'reserve') : ?>
                            <form method="post" action="/PetMarket/?page=admin" style="display:inline;">
                                <input type="hidden" name="idAnimal" value="<?php echo (int)$animal['idAnimal']; ?>">
                                <button type="submit" name="marquer_vendu" class="btn-vendu">
                                    <i class="fa-solid fa-circle-check"></i> Confirmer vendu
                                </button>
                            </form>
                            <form method="post" action="/PetMarket/?page=admin" style="display:inline;">
                                <input type="hidden" name="idAnimal" value="<?php echo (int)$animal['idAnimal']; ?>">
                                <button type="submit" name="marquer_dispo" class="btn-dispo">
                                    <i class="fa-solid fa-rotate-left"></i> Annuler résa
                                </button>
                            </form>
                        <?php else : ?>
                            <form method="post" action="/PetMarket/?page=admin" style="display:inline;">
                                <input type="hidden" name="idAnimal" value="<?php echo (int)$animal['idAnimal']; ?>">
                                <button type="submit" name="marquer_dispo" class="btn-dispo">
                                    <i class="fa-solid fa-rotate-left"></i> Remettre dispo
                                </button>
                            </form>
                        <?php endif; ?>

                        <form method="post" action="/PetMarket/?page=admin"
                              style="display:inline;" onsubmit="return confirm('Supprimer cet animal ?')">
                            <input type="hidden" name="idAnimal" value="<?php echo (int)$animal['idAnimal']; ?>">
                            <button type="submit" name="supprimer_animal" class="btn-supprimer">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <!-- Formulaire modification inline -->
                <tr id="animal-<?php echo (int)$animal['idAnimal']; ?>" style="display:none;">
                    <td colspan="6">
                        <form method="post" enctype="multipart/form-data" action="/PetMarket/?page=admin" class="form-modif">
                            <input type="hidden" name="idAnimal" value="<?php echo (int)$animal['idAnimal']; ?>">
                            <div class="form-ligne">
                                <div class="champ"><label>Nom</label><input type="text" name="nom" value="<?php echo htmlspecialchars($animal['nom']); ?>" required></div>
                                <div class="champ"><label>Prix</label><input type="number" name="prix" value="<?php echo (int)$animal['prix']; ?>" required></div>
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
                                <div class="champ"><label>Nouvelle photo (optionnel)</label><input type="file" name="photo" accept="image/*"></div>
                            </div>
                            <div class="champ">
                                <label>Description</label>
                                <textarea name="description" rows="3"><?php echo htmlspecialchars($animal['description'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" name="modifier_animal" class="btn-orange"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
                            <button type="button" class="btn-contour" onclick="afficherModif('animal-<?php echo (int)$animal['idAnimal']; ?>')">Annuler</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else : ?>
            <p class="message-vide">Aucun animal pour le moment.</p>
        <?php endif; ?>
    </div>

    <!-- ═══════════════════════════════════════════════════
         CATÉGORIES
         ═══════════════════════════════════════════════════ -->
    <div class="bloc" id="section-categories">
        <h2><i class="fa-solid fa-tags"></i> Catégories (<?php echo count($categories); ?>)</h2>
        <form method="post" action="/PetMarket/?page=admin" class="form-inline">
            <input type="text" name="libelle" placeholder="Nouvelle catégorie..." required>
            <button type="submit" name="creer_categorie" class="btn-orange"><i class="fa-solid fa-plus"></i> Ajouter</button>
        </form>
        <?php if (!empty($categories)) : ?>
        <table style="margin-top:16px;">
            <thead><tr><th>Nom</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($categories as $cat) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($cat['libelle']); ?></td>
                    <td>
                        <button class="btn-modifier" onclick="afficherModif('cat-<?php echo (int)$cat['idCategorie']; ?>')">
                            <i class="fa-solid fa-pen"></i> Modifier
                        </button>
                        <form method="post" action="/PetMarket/?page=admin"
                              style="display:inline;" onsubmit="return confirm('Supprimer cette catégorie ?')">
                            <input type="hidden" name="idCategorie" value="<?php echo (int)$cat['idCategorie']; ?>">
                            <button type="submit" name="supprimer_categorie" class="btn-supprimer"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <tr id="cat-<?php echo (int)$cat['idCategorie']; ?>" style="display:none;">
                    <td colspan="2">
                        <form method="post" action="/PetMarket/?page=admin" class="form-inline">
                            <input type="hidden" name="idCategorie" value="<?php echo (int)$cat['idCategorie']; ?>">
                            <input type="text" name="libelle" value="<?php echo htmlspecialchars($cat['libelle']); ?>" required>
                            <button type="submit" name="modifier_categorie" class="btn-orange"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
                            <button type="button" class="btn-contour" onclick="afficherModif('cat-<?php echo (int)$cat['idCategorie']; ?>')">Annuler</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- ═══════════════════════════════════════════════════
         UTILISATEURS
         ═══════════════════════════════════════════════════ -->
    <div class="bloc" id="section-utilisateurs">
        <h2><i class="fa-solid fa-users"></i> Utilisateurs (<?php echo count($utilisateurs); ?>)</h2>

        <?php if (!empty($demandesVendeur)) : ?>
            <div style="background:#fff3e0;border:1px solid #e67e22;border-radius:8px;padding:12px 16px;margin-bottom:16px;">
                <p style="font-weight:bold;color:#e67e22;margin-bottom:8px;">
                    <i class="fa-solid fa-clock"></i>
                    <?php echo count($demandesVendeur); ?> demande(s) vendeur en attente
                </p>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Inscription</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $u) :
                    $estAdmin    = (strtolower($u['email']) === strtolower(ADMIN_EMAIL));
                    $estSoi      = (int)$u['idUser'] === (int)$_SESSION['user_id'];
                    $role        = $u['role'] ?? 'acheteur';
                    $demandeEnCours = (int)($u['seller_request'] ?? 0) === 1;
                ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($u['pseudo'] ?? $u['nom']); ?>
                        <?php if ($estSoi) : ?>
                            <span style="font-size:.75rem;color:#888;"> (vous)</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                        <?php if ($estAdmin) : ?>
                            <span class="badge-admin"><i class="fa-solid fa-shield"></i> Admin</span>
                        <?php elseif ($role === 'vendeur') : ?>
                            <span class="badge-vendeur"><i class="fa-solid fa-store"></i> Vendeur</span>
                        <?php elseif ($demandeEnCours) : ?>
                            <span class="badge-reserve"><i class="fa-solid fa-clock"></i> Demande vendeur</span>
                        <?php else : ?>
                            <span class="badge-acheteur"><i class="fa-solid fa-user"></i> Acheteur</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo !empty($u['date_inscription']) ? date('d/m/Y', strtotime($u['date_inscription'])) : '—'; ?></td>
                    <td>
                        <?php if (!$estAdmin && !$estSoi) : ?>

                            <?php if ($demandeEnCours) : ?>
                                <!-- Valider la demande vendeur -->
                                <form method="post" action="/PetMarket/?page=admin" style="display:inline;">
                                    <input type="hidden" name="idUser" value="<?php echo (int)$u['idUser']; ?>">
                                    <button type="submit" name="valider_vendeur" class="btn-valider">
                                        <i class="fa-solid fa-check"></i> Valider vendeur
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($role === 'vendeur') : ?>
                                <!-- Révoquer le vendeur -->
                                <form method="post" action="/PetMarket/?page=admin"
                                      style="display:inline;"
                                      onsubmit="return confirm('Révoquer ce vendeur ? Il redeviendra acheteur.')">
                                    <input type="hidden" name="idUser" value="<?php echo (int)$u['idUser']; ?>">
                                    <button type="submit" name="revoquer_vendeur" class="btn-vendu">
                                        <i class="fa-solid fa-store-slash"></i> Révoquer
                                    </button>
                                </form>
                            <?php endif; ?>

                            <!-- Supprimer l'utilisateur -->
                            <form method="post" action="/PetMarket/?page=admin"
                                  style="display:inline;"
                                  onsubmit="return confirm('Supprimer cet utilisateur ? Cette action est irréversible.')">
                                <input type="hidden" name="idUser" value="<?php echo (int)$u['idUser']; ?>">
                                <button type="submit" name="supprimer_user" class="btn-supprimer">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                        <?php else : ?>
                            <span style="color:#aaa;font-size:.85rem;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</main>

<script>
function afficherModif(id) {
    var ligne = document.getElementById(id);
    ligne.style.display = ligne.style.display === 'none' ? 'table-row' : 'none';
}
</script>

</body>
</html>