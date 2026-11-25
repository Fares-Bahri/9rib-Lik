<?php
class ProduitModel
{
    private int $id = 0;
    private string $nom = '';
    private string $categorie = '';
    private float $prix = 0.0;
    private int $quantiteStock = 0;
    private string $unite = '';
    private string $dateRecolte = '';
    private int $fermeId = 0;
    private string $fermeNom = ''; 
    public function __construct() {}

    public function getId(): int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getCategorie(): string { return $this->categorie; }
    public function getPrix(): float { return $this->prix; }
    public function getQuantiteStock(): int { return $this->quantiteStock; }
    public function getUnite(): string { return $this->unite; }
    public function getDateRecolte(): string { return $this->dateRecolte; }
    public function getFermeId(): int { return $this->fermeId; }
    public function getFermeNom(): string { return $this->fermeNom; }

    public function setId(int $id): void { $this->id = $id; }
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setCategorie(string $categorie): void { $this->categorie = $categorie; }
    public function setPrix(float $prix): void { $this->prix = $prix; }
    public function setQuantiteStock(int $q): void { $this->quantiteStock = $q; }
    public function setUnite(string $unite): void { $this->unite = $unite; }
    public function setDateRecolte(string $d): void { $this->dateRecolte = $d; }
    public function setFermeId(int $id): void { $this->fermeId = $id; }
    public function setFermeNom(string $nom): void { $this->fermeNom = $nom; }

    public static function fromArray(array $row): self
    {
        $p = new self();
        $p->setId((int) ($row['id'] ?? 0));
        $p->setNom((string) ($row['nom'] ?? ''));
        $p->setCategorie((string) ($row['categorie'] ?? ''));
        $p->setPrix((float) ($row['prix'] ?? 0));
        $p->setQuantiteStock((int) ($row['quantite_stock'] ?? 0));
        $p->setUnite((string) ($row['unite'] ?? ''));
        $p->setDateRecolte((string) ($row['date_recolte'] ?? ''));
        $p->setFermeId((int) ($row['ferme_id'] ?? 0));
        $p->setFermeNom((string) ($row['ferme_nom'] ?? ''));
        return $p;
    }
}
