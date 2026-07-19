<?php
class User {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE idUser = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function emailExiste($email) {
        $stmt = $this->pdo->prepare("SELECT idUser FROM utilisateur WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ? true : false;
    }

    // ── Tous les utilisateurs (hors admin) ──────────────────
    public function getAll() {
        $sql = "SELECT * FROM utilisateur ORDER BY date_inscription DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    // ── Inscription ──────────────────────────────────────────
    public function inscrire($nom, $pseudo, $email, $motDePasse, $role = 'acheteur', $telephone = '', $ville = '') {
        $hash            = password_hash($motDePasse, PASSWORD_DEFAULT);
        $demandeVendeur  = ($role === 'vendeur') ? 1 : 0;

        $sql = "INSERT INTO utilisateur (nom, pseudo, telephone, ville, email, password, role, seller_request)
                VALUES (:nom, :pseudo, :telephone, :ville, :email, :password, 'acheteur', :seller_request)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nom'            => $nom,
            'pseudo'         => $pseudo ?: $nom,
            'telephone'      => $telephone,
            'ville'          => $ville,
            'email'          => $email,
            'password'       => $hash,
            'seller_request' => $demandeVendeur
        ]);
        return $this->pdo->lastInsertId();
    }

    // ── Connexion ────────────────────────────────────────────
    public function connecter($email, $motDePasse) {
        $user = $this->getByEmail($email);
        if (!$user) return false;
        if (!password_verify($motDePasse, $user['password'])) return false;
        return $user;
    }

    // ── Demandes vendeur en attente ──────────────────────────
    public function getDemandesVendeur() {
        $sql = "SELECT * FROM utilisateur WHERE seller_request = 1 ORDER BY idUser DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    // ── Valider un vendeur ───────────────────────────────────
    public function validerVendeur($idUser) {
        $sql  = "UPDATE utilisateur SET role = 'vendeur', seller_request = 0 WHERE idUser = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idUser]);
        return $stmt->rowCount() > 0;
    }

    // ── Révoquer un vendeur → repasse en acheteur ───────────
    public function revoquerVendeur($idUser) {
        $sql  = "UPDATE utilisateur SET role = 'acheteur', seller_request = 0 WHERE idUser = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idUser]);
        return $stmt->rowCount() > 0;
    }

    // ── Supprimer un utilisateur ─────────────────────────────
    public function delete($idUser) {
        $stmt = $this->pdo->prepare("DELETE FROM utilisateur WHERE idUser = :id");
        $stmt->execute(['id' => $idUser]);
        return $stmt->rowCount() > 0;
    }

    // ── Modifier le profil ───────────────────────────────────
    public function updateProfil($idUser, $nom, $pseudo, $telephone, $ville) {
        $sql  = "UPDATE utilisateur SET nom = :nom, pseudo = :pseudo,
                     telephone = :telephone, ville = :ville
                 WHERE idUser = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nom'       => $nom,
            'pseudo'    => $pseudo ?: $nom,
            'telephone' => $telephone,
            'ville'     => $ville,
            'id'        => $idUser
        ]);
        return $stmt->rowCount() > 0;
    }
}
