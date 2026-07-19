<?php
// models/commande.php

class Commande
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Crée une ligne de commande pour un animal.
     */
    public function creer($idUser, $idAnimal, $prix)
    {
        $sql = "INSERT INTO commande (idUser, idAnimal, prix, statut)
                VALUES (:idUser, :idAnimal, :prix, 'en_attente')";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'idUser'   => (int) $idUser,
            'idAnimal' => (int) $idAnimal,
            'prix'     => (int) $prix,
        ]);
    }

    /**
     * Toutes les commandes d'un acheteur, avec le détail de l'animal.
     */
    public function getByUser($idUser)
    {
        $sql = "SELECT c.idCommande, c.prix, c.statut, c.date_commande,
                       a.nom AS animal_nom, a.photo AS animal_photo,
                       cat.libelle AS categorie_nom
                FROM commande c
                JOIN animal    a   ON a.idAnimal    = c.idAnimal
                JOIN categorie cat ON cat.idCategorie = a.idCategorie
                WHERE c.idUser = :idUser
                ORDER BY c.date_commande DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idUser' => (int) $idUser]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Toutes les commandes reçues par un vendeur
     * (animaux dont idUser = $idVendeur).
     */
    public function getByVendeur($idVendeur)
    {
        $sql = "SELECT c.idCommande, c.prix, c.statut, c.date_commande,
                       a.idAnimal, a.nom AS animal_nom, a.photo AS animal_photo,
                       u.nom AS acheteur_nom, u.pseudo AS acheteur_pseudo,
                       u.telephone AS acheteur_tel, u.email AS acheteur_email
                FROM commande c
                JOIN animal      a ON a.idAnimal  = c.idAnimal
                JOIN utilisateur u ON u.idUser    = c.idUser
                WHERE a.idUser = :idVendeur
                ORDER BY c.date_commande DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idVendeur' => (int) $idVendeur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Toutes les commandes (vue admin).
     */
    public function getAll()
    {
        $sql = "SELECT c.idCommande, c.prix, c.statut, c.date_commande,
                       a.nom  AS animal_nom,  a.photo AS animal_photo,
                       u.nom  AS acheteur_nom, u.email AS acheteur_email,
                       v.nom  AS vendeur_nom,  v.email AS vendeur_email
                FROM commande c
                JOIN animal      a ON a.idAnimal = c.idAnimal
                JOIN utilisateur u ON u.idUser   = c.idUser
                LEFT JOIN utilisateur v ON v.idUser = a.idUser
                ORDER BY c.date_commande DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Change le statut d'une commande (confirme / annule).
     */
    public function changerStatut($idCommande, $statut)
    {
        $statutsOk = ['en_attente', 'confirme', 'annule'];
        if (!in_array($statut, $statutsOk)) return false;

        $stmt = $this->pdo->prepare(
            "UPDATE commande SET statut = :statut WHERE idCommande = :id"
        );
        return $stmt->execute([
            'statut' => $statut,
            'id'     => (int) $idCommande,
        ]);
    }
}