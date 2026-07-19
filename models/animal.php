<?php
// models/animal.php

class Animal
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // ────────────────────────────────────────────────────────
    //  CATALOGUE PUBLIC — disponibles seulement
    // ────────────────────────────────────────────────────────

    public function getAllDisponibles($tri = 'recent')
    {
        $ordre = $this->getOrdre($tri);
        $sql   = "SELECT animal.*, categorie.libelle AS categorie_nom
                  FROM animal
                  LEFT JOIN categorie ON animal.idCategorie = categorie.idCategorie
                  WHERE animal.statut = 'disponible'
                  ORDER BY $ordre";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function rechercher($texte, $idCategorie = null, $tri = 'recent')
    {
        $ordre = $this->getOrdre($tri);
        if ($idCategorie) {
            $sql  = "SELECT animal.*, categorie.libelle AS categorie_nom
                     FROM animal
                     LEFT JOIN categorie ON animal.idCategorie = categorie.idCategorie
                     WHERE animal.statut = 'disponible'
                       AND (animal.nom LIKE :texte OR categorie.libelle LIKE :texte)
                       AND animal.idCategorie = :idCategorie
                     ORDER BY $ordre";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'texte'       => '%' . $texte . '%',
                'idCategorie' => $idCategorie,
            ]);
        } else {
            $sql  = "SELECT animal.*, categorie.libelle AS categorie_nom
                     FROM animal
                     LEFT JOIN categorie ON animal.idCategorie = categorie.idCategorie
                     WHERE animal.statut = 'disponible'
                       AND (animal.nom LIKE :texte OR categorie.libelle LIKE :texte)
                     ORDER BY $ordre";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['texte' => '%' . $texte . '%']);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDerniersDisponibles($limite = 4)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM animal
             WHERE statut = 'disponible'
             ORDER BY date_ajout DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSimilaires($idCategorie, $idAnimalExclu, $limite = 3)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM animal
             WHERE idCategorie = :idCategorie
               AND idAnimal   != :idAnimal
               AND statut      = 'disponible'
             ORDER BY date_ajout DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':idCategorie', $idCategorie,   PDO::PARAM_INT);
        $stmt->bindValue(':idAnimal',    $idAnimalExclu, PDO::PARAM_INT);
        $stmt->bindValue(':limite',      $limite,        PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ────────────────────────────────────────────────────────
    //  ADMIN — tous les animaux (tous statuts)
    // ────────────────────────────────────────────────────────

    public function getAllAvecCategorie($tri = 'recent')
    {
        $ordre = $this->getOrdre($tri);
        $sql   = "SELECT animal.*, categorie.libelle AS categorie_nom
                  FROM animal
                  LEFT JOIN categorie ON animal.idCategorie = categorie.idCategorie
                  ORDER BY $ordre";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        return $this->pdo->query(
            "SELECT * FROM animal ORDER BY date_ajout DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    // ────────────────────────────────────────────────────────
    //  VENDEUR — ses propres annonces (tous statuts)
    // ────────────────────────────────────────────────────────

    public function getBySeller($idUser)
    {
        $stmt = $this->pdo->prepare(
            "SELECT animal.*, categorie.libelle AS categorie_nom
             FROM animal
             LEFT JOIN categorie ON animal.idCategorie = categorie.idCategorie
             WHERE animal.idUser = :idUser
             ORDER BY animal.date_ajout DESC"
        );
        $stmt->execute(['idUser' => (int)$idUser]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ────────────────────────────────────────────────────────
    //  CRUD
    // ────────────────────────────────────────────────────────

    public function getById($id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM animal WHERE idAnimal = :id LIMIT 1"
        );
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nom, $prix, $description, $photo, $idCategorie, $idUser = null)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO animal (nom, prix, description, photo, idCategorie, idUser, statut)
             VALUES (:nom, :prix, :description, :photo, :idCategorie, :idUser, 'disponible')"
        );
        $stmt->execute([
            'nom'         => $nom,
            'prix'        => (int)$prix,
            'description' => $description,
            'photo'       => $photo,
            'idCategorie' => (int)$idCategorie,
            'idUser'      => $idUser ? (int)$idUser : null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update($id, $nom, $prix, $description, $photo, $idCategorie)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE animal
             SET nom = :nom, prix = :prix, description = :description,
                 photo = :photo, idCategorie = :idCategorie
             WHERE idAnimal = :id"
        );
        return $stmt->execute([
            'id'          => (int)$id,
            'nom'         => $nom,
            'prix'        => (int)$prix,
            'description' => $description,
            'photo'       => $photo,
            'idCategorie' => (int)$idCategorie,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM animal WHERE idAnimal = :id"
        );
        return $stmt->execute(['id' => (int)$id]);
    }

    // ────────────────────────────────────────────────────────
    //  UTILITAIRE PRIVÉ
    // ────────────────────────────────────────────────────────

    private function getOrdre($tri)
    {
        $ordres = [
            'recent'    => 'animal.date_ajout DESC',
            'prix_asc'  => 'animal.prix ASC',
            'prix_desc' => 'animal.prix DESC',
        ];
        return $ordres[$tri] ?? 'animal.date_ajout DESC';
    }
}