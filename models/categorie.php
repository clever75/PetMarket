<?php
// models/categorie.php

class Categorie
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        return $this->pdo->query(
            "SELECT * FROM categorie ORDER BY libelle ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM categorie WHERE idCategorie = :id LIMIT 1"
        );
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByLibelle($libelle)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM categorie WHERE LOWER(libelle) = LOWER(:libelle) LIMIT 1"
        );
        $stmt->execute(['libelle' => trim($libelle)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($libelle)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categorie (libelle) VALUES (:libelle)"
        );
        $stmt->execute(['libelle' => $libelle]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update($id, $libelle)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE categorie SET libelle = :libelle WHERE idCategorie = :id"
        );
        $stmt->execute(['libelle' => $libelle, 'id' => (int)$id]);
        return $stmt->rowCount() > 0;
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM categorie WHERE idCategorie = :id"
        );
        $stmt->execute(['id' => (int)$id]);
        return $stmt->rowCount() > 0;
    }

    public function aDesAnimaux($id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM animal WHERE idCategorie = :id"
        );
        $stmt->execute(['id' => (int)$id]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
