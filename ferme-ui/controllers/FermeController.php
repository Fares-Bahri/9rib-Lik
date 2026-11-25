<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/FermeModel.php';

class FermeController
{
    public const TYPES = ['Maraîchage', 'Céréaliculture', 'Arboriculture', 'Élevage', 'Biologique', 'Mixte'];

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    
    /** @return FermeModel[] */
    public function findAll(string $q = '', string $type = ''): array
    {
        $sql = 'SELECT f.*, (SELECT COUNT(*) FROM produits p WHERE p.ferme_id = f.id) AS nb_produits
                FROM fermes f WHERE 1=1';
        $params = [];
        if ($q !== '') {
            $sql .= ' AND (f.nom LIKE :q1 OR f.emplacement LIKE :q2)';
            $params['q1'] = $params['q2'] = '%' . $q . '%';
        }
        if ($type !== '') {
            $sql .= ' AND f.type_agriculture = :type';
            $params['type'] = $type;
        }
        $stmt = $this->db->prepare($sql . ' ORDER BY f.id ASC');
        $stmt->execute($params);
        return array_map(fn(array $r) => FermeModel::fromArray($r), $stmt->fetchAll());
    }

    public function findById(int $id): ?FermeModel
    {
        $stmt = $this->db->prepare(
            'SELECT f.*, (SELECT COUNT(*) FROM produits p WHERE p.ferme_id = f.id) AS nb_produits
             FROM fermes f WHERE f.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? FermeModel::fromArray($row) : null;
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM fermes')->fetchColumn();
    }

    public function totalAire(): float
    {
        return (float) $this->db->query('SELECT COALESCE(SUM(aire), 0) FROM fermes')->fetchColumn();
    }

    
    public function validate(array $post): array
    {
        $errors = [];
        $nom = trim($post['nom'] ?? '');
        $emp = trim($post['emplacement'] ?? '');
        $aire = str_replace(',', '.', trim($post['aire'] ?? ''));
        $type = $post['type_agriculture'] ?? '';
        $desc = trim($post['description'] ?? '');

        if (mb_strlen($nom) < 3 || mb_strlen($nom) > 100) {
            $errors[] = "Le nom doit contenir entre 3 et 100 caractères.";
        }
        if ($emp === '' || mb_strlen($emp) > 150) {
            $errors[] = "L'emplacement est obligatoire (150 caractères maximum).";
        }
        if (!is_numeric($aire) || (float) $aire <= 0) {
            $errors[] = "L'aire doit être un nombre supérieur à 0.";
        }
        if (!in_array($type, self::TYPES, true)) {
            $errors[] = "Type d'agriculture invalide.";
        }
        if (mb_strlen($desc) > 500) {
            $errors[] = "La description ne doit pas dépasser 500 caractères.";
        }
        return $errors;
    }

    public function fromPost(array $post, int $id = 0): FermeModel
    {
        $f = new FermeModel();
        $f->setId($id);
        $f->setNom(trim($post['nom'] ?? ''));
        $f->setEmplacement(trim($post['emplacement'] ?? ''));
        $f->setAire((float) str_replace(',', '.', trim($post['aire'] ?? '0')));
        $f->setTypeAgriculture($post['type_agriculture'] ?? '');
        $f->setDescription(trim($post['description'] ?? ''));
        return $f;
    }

    /
    public function save(FermeModel $f): void
    {
        $p = [
            'nom'         => $f->getNom(),
            'emplacement' => $f->getEmplacement(),
            'aire'        => $f->getAire(),
            'type'        => $f->getTypeAgriculture(),
            'description' => $f->getDescription(),
        ];
        if ($f->getId() > 0) {
            $p['id'] = $f->getId();
            $this->db->prepare(
                'UPDATE fermes SET nom = :nom, emplacement = :emplacement, aire = :aire,
                 type_agriculture = :type, description = :description WHERE id = :id'
            )->execute($p);
        } else {
            $this->db->prepare(
                'INSERT INTO fermes (nom, emplacement, aire, type_agriculture, description)
                 VALUES (:nom, :emplacement, :aire, :type, :description)'
            )->execute($p);
        }
    }

    
    public function delete(int $id): void
    {
        $this->db->prepare('DELETE FROM fermes WHERE id = :id')->execute(['id' => $id]);
    }
}
