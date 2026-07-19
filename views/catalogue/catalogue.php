<?php
if (!isset($animaux)) {
    require_once __DIR__ . "/../../controllers/animalController.php";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue — PetMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/PetMarket/public/css/header.css">
    <link rel="stylesheet" href="/PetMarket/public/css/footer.css">
    <link rel="stylesheet" href="/PetMarket/public/css/catalogue.css">
    <style>
        /* Overlay statut sur les cartes */
        .carte { position: relative; overflow: hidden; }

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
        }
        .carte-overlay.reserve  { background: #e67e22; }
        .carte-overlay.vendu    { background: #e74c3c; }

        /* Image grisée si non disponible */
        .carte img.indispo { filter: grayscale(60%); opacity: .8; }
    </style>
</head>
<body>
<?php include __DIR__ . "/../layout/header.php"; ?>
<?php include __DIR__ . "/../layout/flash.php"; ?>

<main>
    <div class="catalogue-haut">
        <h1><i class="fa-solid fa-list"></i> Catalogue</h1>
        <p class="sous-titre">Trouvez votre compagnon idéal</p>
    </div>

    <form class="form-recherche" method="get" action="/PetMarket/?page=catalogue">
        <input type="text" name="recherche"
               placeholder="Rechercher un animal..."
               value="<?php echo htmlspecialchars($_GET['recherche'] ?? ''); ?>">

        <select name="idCategorie">
            <option value="0">Toutes les catégories</option>
            <?php foreach ($categories as $cat) : ?>
                <option value="<?php echo (int)$cat['idCategorie']; ?>"
                    <?php echo ((int)($_GET['idCategorie'] ?? 0) === (int)$cat['idCategorie']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['libelle']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="tri">
            <option value="recent"    <?php echo (($_GET['tri'] ?? 'recent') === 'recent')    ? 'selected' : ''; ?>>Plus récents</option>
            <option value="prix_asc"  <?php echo (($_GET['tri'] ?? 'recent') === 'prix_asc')  ? 'selected' : ''; ?>>Prix croissant</option>
            <option value="prix_desc" <?php echo (($_GET['tri'] ?? 'recent') === 'prix_desc') ? 'selected' : ''; ?>>Prix décroissant</option>
        </select>

        <button type="submit" class="btn-orange">
            <i class="fa-solid fa-magnifying-glass"></i> Rechercher
        </button>
        <a href="/PetMarket/?page=catalogue" class="btn-contour">
            <i class="fa-solid fa-rotate-left"></i> Réinitialiser
        </a>
    </form>

    <div class="grille">
        <?php if (!empty($animaux)) : ?>
            <?php foreach ($animaux as $animal) :
                $statut      = $animal['statut'] ?? 'disponible';
                $indispo     = $statut !== 'disponible';
            ?>
            <div class="carte">

                <!-- Badge RÉSERVÉ ou VENDU -->
                <?php if ($statut === 'reserve') : ?>
                    <div class="carte-overlay reserve"><i class="fa-solid fa-clock"></i> Réservé</div>
                <?php elseif ($statut === 'vendu') : ?>
                    <div class="carte-overlay vendu"><i class="fa-solid fa-ban"></i> Vendu</div>
                <?php endif; ?>

                <a href="/PetMarket/?page=detail&id=<?php echo (int)$animal['idAnimal']; ?>">
                    <img
                        src="/PetMarket/public/images/<?php echo htmlspecialchars($animal['photo'] ?: 'no-image.jpg'); ?>"
                        alt="<?php echo htmlspecialchars($animal['nom']); ?>"
                        class="<?php echo $indispo ? 'indispo' : ''; ?>"
                    >
                </a>

                <div class="carte-corps">
                    <?php if (!empty($animal['categorie_nom'])) : ?>
                        <span class="badge"><?php echo htmlspecialchars($animal['categorie_nom']); ?></span>
                    <?php endif; ?>
                    <h3>
                        <a href="/PetMarket/?page=detail&id=<?php echo (int)$animal['idAnimal']; ?>">
                            <?php echo htmlspecialchars($animal['nom']); ?>
                        </a>
                    </h3>
                    <p class="prix"><?php echo number_format((int)$animal['prix'], 0, ',', ' '); ?> FCFA</p>
                    <a href="/PetMarket/?page=detail&id=<?php echo (int)$animal['idAnimal']; ?>"
                       class="btn-<?php echo $indispo ? 'contour' : 'orange'; ?> btn-petit">
                        <i class="fa-solid fa-eye"></i> Voir l'annonce
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="message-vide">
                <i class="fa-solid fa-magnifying-glass"></i>
                <p>Aucun animal trouvé.</p>
                <a href="/PetMarket/?page=catalogue" class="btn-contour">Voir tous les animaux</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . "/../layout/footer.php"; ?>
</body>
</html>