<?php
// models/panier.php

class Panier
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère tous les items du panier AVEC détails animal
     * (nom, prix, photo, statut) — utilisé par header, commande, panier
     */
    public function getByUser($userId)
    {
        $sql = "SELECT p.idAnimal, p.quantite,
                       a.nom, a.prix, a.photo, a.statut
                FROM panier p
                JOIN animal a ON a.idAnimal = p.idAnimal
                WHERE p.idUser = :idUser";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idUser' => (int)$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère uniquement ids/quantités sans JOIN
     * Utilisé par loginController pour charger la session
     */
    public function getItemsByUser($userId)
    {
        $sql = "SELECT idAnimal, quantite FROM panier WHERE idUser = :idUser";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idUser' => (int)$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute un item ou incrémente la quantité
     */
    public function addItem($userId, $idAnimal, $qty = 1)
    {
        $sql = "INSERT INTO panier (idUser, idAnimal, quantite)
                VALUES (:idUser, :idAnimal, :quantite)
                ON DUPLICATE KEY UPDATE quantite = quantite + VALUES(quantite)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'idUser'   => (int)$userId,
            'idAnimal' => (int)$idAnimal,
            'quantite' => (int)$qty,
        ]);
    }

    /**
     * Définit une quantité exacte (ajouter sans doublon)
     */
    public function setQuantite($userId, $idAnimal, $quantite)
    {
        $sql = "INSERT INTO panier (idUser, idAnimal, quantite)
                VALUES (:idUser, :idAnimal, :quantite)
                ON DUPLICATE KEY UPDATE quantite = :quantite";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'idUser'   => (int)$userId,
            'idAnimal' => (int)$idAnimal,
            'quantite' => (int)$quantite,
        ]);
    }

    /**
     * Supprime un item précis
     */
    public function supprimer($userId, $idAnimal)
    {
        $sql = "DELETE FROM panier WHERE idUser = :idUser AND idAnimal = :idAnimal";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'idUser'   => (int)$userId,
            'idAnimal' => (int)$idAnimal,
        ]);
    }

    /**
     * Vide entièrement le panier
     * Disponible sous 3 noms pour compatibilité totale avec tout le code existant
     */
    public function vider($userId)
    {
        $sql  = "DELETE FROM panier WHERE idUser = :idUser";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['idUser' => (int)$userId]);
    }

    public function viderParUser($userId)          { return $this->vider($userId); }
    public function clearByUser($userId)           { return $this->vider($userId); }
    public function removeItem($userId, $idAnimal) { return $this->supprimer($userId, $idAnimal); }
}