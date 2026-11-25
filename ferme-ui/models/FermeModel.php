<?php
class FermeModel
{
    private int $id = 0;
    private string $nom = '';
    private string $emplacement = '';
    private float $aire = 0.0;
    private string $typeAgriculture = '';
    private string $description = '';
    private int $nbProduits = 0; 

    public function __construct() {}

    public function getId(): int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getEmplacement(): string { return $this->emplacement; }
    public function getAire(): float { return $this->aire; }
    public function getTypeAgriculture(): string { return $this->typeAgriculture; }
    public function getDescription(): string { return $this->description; }
    public function getNbProduits(): int { return $this->nbProduits; }

    public function setId(int $id): void { $this->id = $id; }
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setEmplacement(string $emplacement): void { $this->emplacement = $emplacement; }
    public function setAire(float $aire): void { $this->aire = $aire; }
    public function setTypeAgriculture(string $type): void { $this->typeAgriculture = $type; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setNbProduits(int $nb): void { $this->nbProduits = $nb; }

    public static function fromArray(array $row): self
    {
        $f = new self();
        $f->setId((int) ($row['id'] ?? 0));
        $f->setNom((string) ($row['nom'] ?? ''));
        $f->setEmplacement((string) ($row['emplacement'] ?? ''));
        $f->setAire((float) ($row['aire'] ?? 0));
        $f->setTypeAgriculture((string) ($row['type_agriculture'] ?? ''));
        $f->setDescription((string) ($row['description'] ?? ''));
        $f->setNbProduits((int) ($row['nb_produits'] ?? 0));
        return $f;
    }
}
