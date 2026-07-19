<?php
class Panier
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère tous les items du panier d'un utilisateur
     * Retourne : [['idAnimal' => X, 'quantite' => Y], ...]
     */
    public function getItemsByUser($userId)
    {
        $sql = "SELECT idAnimal, quantite FROM panier WHERE idUser = :idUser";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idUser' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute un item ou incrémente sa quantité si déjà présent
     */
    public function addItem($userId, $idAnimal, $qty = 1)
    {
        $sql = "INSERT INTO panier (idUser, idAnimal, quantite)
                VALUES (:idUser, :idAnimal, :quantite)
                ON DUPLICATE KEY UPDATE quantite = quantite + VALUES(quantite)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'idUser'   => $userId,
            'idAnimal' => $idAnimal,
            'quantite' => $qty
        ]);
    }

    /**
     * Définit une quantité exacte pour un item.
     * Si l'item n'existe pas encore, il est créé.
     * Utilisé par panierController pour ajouter / retirer proprement.
     */
    public function setQuantite($userId, $idAnimal, $quantite)
    {
        $sql = "INSERT INTO panier (idUser, idAnimal, quantite)
                VALUES (:idUser, :idAnimal, :quantite)
                ON DUPLICATE KEY UPDATE quantite = :quantite";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'idUser'   => $userId,
            'idAnimal' => $idAnimal,
            'quantite' => (int) $quantite
        ]);
    }

    /**
     * Supprime un item précis du panier d'un utilisateur
     * Alias expressif de removeItem() — utilisé par panierController
     */
    public function supprimer($userId, $idAnimal)
    {
        $sql = "DELETE FROM panier WHERE idUser = :idUser AND idAnimal = :idAnimal";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'idUser'   => $userId,
            'idAnimal' => $idAnimal
        ]);
    }

    /**
     * Vide entièrement le panier d'un utilisateur
     * Alias expressif de clearByUser() — utilisé par panierController
     */
    public function viderParUser($userId)
    {
        $sql = "DELETE FROM panier WHERE idUser = :idUser";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['idUser' => $userId]);
    }

    /**
     * Supprime un item (nom original conservé pour compatibilité)
     */
    public function removeItem($userId, $idAnimal)
    {
        return $this->supprimer($userId, $idAnimal);
    }

    /**
     * Vide le panier (nom original conservé pour compatibilité)
     */
    public function clearByUser($userId)
    {
        return $this->viderParUser($userId);
    }
}