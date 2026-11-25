<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ProduitModel.php';

class ProduitController
{
    public const CATEGORIES = ['Légumes', 'Fruits', 'Céréales', 'Légumineuses', 'Huiles', 'Produits laitiers', 'Autre'];
    public const UNITES = ['kg', 'L', 'pièce'];
    public const SEUIL_FAIBLE = 50;

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    
    /** @return ProduitModel[] */
    public function findAll(int $fermeId = 0, string $q = ''): array
    {
        $sql = 'SELECT p.*, f.nom AS ferme_nom FROM produits p
                JOIN fermes f ON f.id = p.ferme_id WHERE 1=1';
        $params = [];
        if ($fermeId > 0) {
            $sql .= ' AND p.ferme_id = :ferme';
            $params['ferme'] = $fermeId;
        }
        if ($q !== '') {
            $sql .= ' AND (p.nom LIKE :q1 OR p.categorie LIKE :q2)';
            $params['q1'] = $params['q2'] = '%' . $q . '%';
        }
        $stmt = $this->db->prepare($sql . ' ORDER BY p.id ASC');
        $stmt->execute($params);
        return array_map(fn(array $r) => ProduitModel::fromArray($r), $stmt->fetchAll());
    }

    public function findById(int $id): ?ProduitModel
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, f.nom AS ferme_nom FROM produits p
             JOIN fermes f ON f.id = p.ferme_id WHERE p.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? ProduitModel::fromArray($row) : null;
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM produits')->fetchColumn();
    }

    private function fermeExists(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM fermes WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return (bool) $stmt->fetch();
    }

   
    public static function statut(ProduitModel $p): array
    {
        if ($p->getQuantiteStock() <= 0) {
            return ['Rupture', 'off'];
        }
        if ($p->getQuantiteStock() <= self::SEUIL_FAIBLE) {
            return ['Stock faible', 'warn'];
        }
        return ['En stock', ''];
    }

    
    public function validate(array $post): array
    {
        $errors = [];
        $nom   = trim($post['nom'] ?? '');
        $prix  = str_replace(',', '.', trim($post['prix'] ?? ''));
        $stock = filter_var($post['quantite_stock'] ?? '', FILTER_VALIDATE_INT);
        $date  = trim($post['date_recolte'] ?? '');

        if (mb_strlen($nom) < 2 || mb_strlen($nom) > 100) {
            $errors[] = "Le nom du produit doit contenir entre 2 et 100 caractères.";
        }
        if (!in_array($post['categorie'] ?? '', self::CATEGORIES, true)) {
            $errors[] = "Catégorie invalide.";
        }
        if (!in_array($post['unite'] ?? '', self::UNITES, true)) {
            $errors[] = "Unité invalide.";
        }
        if (!is_numeric($prix) || (float) $prix <= 0) {
            $errors[] = "Le prix doit être supérieur à 0.";
        }
        if ($stock === false || $stock < 0) {
            $errors[] = "Le stock doit être un entier positif ou nul.";
        }
        if ($date !== '') {
            $d = DateTime::createFromFormat('Y-m-d', $date);
            if (!$d || $d->format('Y-m-d') !== $date) {
                $errors[] = "Date de récolte invalide.";
            }
        }
        if (!$this->fermeExists((int) ($post['ferme_id'] ?? 0))) {
            $errors[] = "Veuillez choisir une ferme existante.";
        }
        return $errors;
    }

    public function fromPost(array $post, int $id = 0): ProduitModel
    {
        $p = new ProduitModel();
        $p->setId($id);
        $p->setNom(trim($post['nom'] ?? ''));
        $p->setCategorie($post['categorie'] ?? '');
        $p->setPrix((float) str_replace(',', '.', trim($post['prix'] ?? '0')));
        $p->setQuantiteStock((int) ($post['quantite_stock'] ?? 0));
        $p->setUnite($post['unite'] ?? '');
        $p->setDateRecolte(trim($post['date_recolte'] ?? ''));
        $p->setFermeId((int) ($post['ferme_id'] ?? 0));
        return $p;
    }

    
    public function save(ProduitModel $p): void
    {
        $params = [
            'nom'       => $p->getNom(),
            'categorie' => $p->getCategorie(),
            'prix'      => $p->getPrix(),
            'stock'     => $p->getQuantiteStock(),
            'unite'     => $p->getUnite(),
            'date'      => $p->getDateRecolte() !== '' ? $p->getDateRecolte() : null,
            'ferme'     => $p->getFermeId(),
        ];
        if ($p->getId() > 0) {
            $params['id'] = $p->getId();
            $this->db->prepare(
                'UPDATE produits SET nom = :nom, categorie = :categorie, prix = :prix,
                 quantite_stock = :stock, unite = :unite, date_recolte = :date, ferme_id = :ferme
                 WHERE id = :id'
            )->execute($params);
        } else {
            $this->db->prepare(
                'INSERT INTO produits (nom, categorie, prix, quantite_stock, unite, date_recolte, ferme_id)
                 VALUES (:nom, :categorie, :prix, :stock, :unite, :date, :ferme)'
            )->execute($params);
        }
    }

    
    public function delete(int $id): void
    {
        $this->db->prepare('DELETE FROM produits WHERE id = :id')->execute(['id' => $id]);
    }
}
