<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($erreur))      $erreur      = "";
if (!isset($ongletActif)) $ongletActif = "connexion";
if (!isset($inscriptionValues)) {
    $inscriptionValues = [
        'nom'       => '',
        'pseudo'    => '',
        'email'     => '',
        'telephone' => '',
        'ville'     => '',
        'role'      => 'acheteur',
    ];
}
$flashSucces = $_SESSION['flash_succes'] ?? "";
unset($_SESSION['flash_succes']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — PetMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/PetMarket/public/css/login.css">
</head>
<body>
<header>
    <div class="header-inner">
        <a href="/PetMarket/?page=accueil" class="logo">
            <i class="fa-solid fa-paw"></i> PetMarket
        </a>
    </div>
</header>

<main class="login-page">
    <div class="login-boite">

        <?php if (!empty($flashSucces)) : ?>
            <div class="alerte-succes" style="background:#eafaf1;border:1px solid #27ae60;color:#1e7e45;border-radius:8px;padding:12px 14px;margin-bottom:16px;">
                <i class="fa-solid fa-circle-check"></i>
                <?php echo htmlspecialchars($flashSucces); ?>
            </div>
        <?php endif; ?>

        <div class="onglets">
            <button class="onglet <?php echo $ongletActif === 'connexion'  ? 'actif' : ''; ?>"
                    onclick="afficherOnglet('connexion')">
                <i class="fa-solid fa-right-to-bracket"></i> Connexion
            </button>
            <button class="onglet <?php echo $ongletActif === 'inscription' ? 'actif' : ''; ?>"
                    onclick="afficherOnglet('inscription')">
                <i class="fa-solid fa-user-plus"></i> Inscription
            </button>
        </div>

        <!-- ══ CONNEXION ══ -->
        <div id="form-connexion" class="form-section"
             <?php echo $ongletActif === 'inscription' ? 'style="display:none;"' : ''; ?>>
            <h2>Se connecter</h2>
            <?php if (!empty($erreur) && $ongletActif === 'connexion') : ?>
                <div class="alerte-erreur">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo htmlspecialchars($erreur); ?>
                </div>
            <?php endif; ?>
            <form method="post" action="/PetMarket/?page=login">
                <div class="champ">
                    <label><i class="fa-solid fa-envelope"></i> Email</label>
                    <input type="email" name="email" placeholder="ton@email.com" required>
                </div>
                <div class="champ">
                    <label><i class="fa-solid fa-lock"></i> Mot de passe</label>
                    <div class="champ-mdp">
                        <input type="password" id="mdp-co" name="password" placeholder="••••••••" required>
                        <button type="button" class="btn-oeil" onclick="toggleMdp('mdp-co', this)">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" name="connexion" class="btn-orange btn-block">
                    <i class="fa-solid fa-right-to-bracket"></i> Se connecter
                </button>
            </form>
            <p class="lien-switch">Pas encore de compte ?
                <a href="#" onclick="afficherOnglet('inscription')">S'inscrire</a>
            </p>
        </div>

        <!-- ══ INSCRIPTION ══ -->
        <div id="form-inscription" class="form-section"
             <?php echo $ongletActif === 'connexion' ? 'style="display:none;"' : ''; ?>>
            <h2>Créer un compte</h2>

            <?php if (!empty($erreur) && $ongletActif === 'inscription') : ?>
                <div class="alerte-erreur">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo htmlspecialchars($erreur); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/PetMarket/?page=login" onsubmit="return validerInscription()">

                <!-- Infos de base -->
                <div class="champ">
                    <label><i class="fa-solid fa-user"></i> Nom complet *</label>
                    <input type="text" name="nom" placeholder="Ton nom complet"
                           value="<?php echo htmlspecialchars($inscriptionValues['nom']); ?>" required>
                </div>
                <div class="champ">
                    <label><i class="fa-solid fa-at"></i> Pseudo (optionnel)</label>
                    <input type="text" name="pseudo" placeholder="Ton pseudo"
                           value="<?php echo htmlspecialchars($inscriptionValues['pseudo']); ?>">
                </div>
                <div class="champ">
                    <label><i class="fa-solid fa-envelope"></i> Email *</label>
                    <input type="email" name="email" placeholder="ton@email.com"
                           value="<?php echo htmlspecialchars($inscriptionValues['email']); ?>" required>
                </div>

                <!-- Coordonnées — obligatoires -->
                <div class="champ">
                    <label>
                        <i class="fa-solid fa-phone"></i> Téléphone *
                        <span style="font-size:.78rem;color:#e67e22;font-weight:normal;">
                            — nécessaire pour être contacté
                        </span>
                    </label>
                    <input type="tel" name="telephone" placeholder="Ex: 90 00 00 00"
                           pattern="[0-9\s\+\-]{6,20}"
                           value="<?php echo htmlspecialchars($inscriptionValues['telephone']); ?>" required>
                </div>
                <div class="champ">
                    <label>
                        <i class="fa-solid fa-location-dot"></i> Ville *
                        <span style="font-size:.78rem;color:#e67e22;font-weight:normal;">
                            — pour la récupération de l'animal
                        </span>
                    </label>
                    <input type="text" name="ville" placeholder="Ex: Lomé"
                           value="<?php echo htmlspecialchars($inscriptionValues['ville']); ?>" required>
                </div>

                <!-- Mot de passe -->
                <div class="champ">
                    <label><i class="fa-solid fa-lock"></i> Mot de passe * <span style="font-size:.78rem;color:#aaa;">(6 caractères min.)</span></label>
                    <div class="champ-mdp">
                        <input type="password" id="mdp-ins" name="password" placeholder="••••••••"
                               minlength="6" required>
                        <button type="button" class="btn-oeil" onclick="toggleMdp('mdp-ins', this)">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="champ">
                    <label><i class="fa-solid fa-lock"></i> Confirmer le mot de passe *</label>
                    <div class="champ-mdp">
                        <input type="password" id="mdp-confirm" placeholder="••••••••" required>
                        <button type="button" class="btn-oeil" onclick="toggleMdp('mdp-confirm', this)">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <p id="erreur-mdp" class="erreur-inline" style="display:none;">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        Les mots de passe ne correspondent pas.
                    </p>
                </div>

                <!-- Rôle -->
                <div class="champ">
                    <label><i class="fa-solid fa-briefcase"></i> Tu veux être ?</label>
                    <select name="role">
                        <option value="acheteur" <?php echo $inscriptionValues['role'] === 'acheteur' ? 'selected' : ''; ?>>Acheteur</option>
                        <option value="vendeur" <?php echo $inscriptionValues['role'] === 'vendeur' ? 'selected' : ''; ?>>Vendeur (demande validation admin)</option>
                    </select>
                </div>

                <button type="submit" name="inscription" class="btn-orange btn-block">
                    <i class="fa-solid fa-user-plus"></i> Créer mon compte
                </button>
            </form>

            <p class="lien-switch">Déjà un compte ?
                <a href="#" onclick="afficherOnglet('connexion')">Se connecter</a>
            </p>
        </div>

    </div>
</main>

<script>
function afficherOnglet(nom) {
    document.getElementById('form-connexion').style.display   = nom === 'connexion'   ? 'block' : 'none';
    document.getElementById('form-inscription').style.display = nom === 'inscription' ? 'block' : 'none';
    var onglets = document.querySelectorAll('.onglet');
    onglets[0].classList.toggle('actif', nom === 'connexion');
    onglets[1].classList.toggle('actif', nom === 'inscription');
}

function toggleMdp(idChamp, bouton) {
    var champ = document.getElementById(idChamp);
    var icone = bouton.querySelector('i');
    if (champ.type === 'password') {
        champ.type = 'text';
        icone.className = 'fa-solid fa-eye-slash';
    } else {
        champ.type = 'password';
        icone.className = 'fa-solid fa-eye';
    }
}

function validerInscription() {
    var mdp    = document.getElementById('mdp-ins').value;
    var conf   = document.getElementById('mdp-confirm').value;
    var erreur = document.getElementById('erreur-mdp');
    if (mdp !== conf) {
        erreur.style.display = 'block';
        document.getElementById('mdp-confirm').focus();
        return false;
    }
    erreur.style.display = 'none';
    return true;
}
</script>
</body>
</html>
