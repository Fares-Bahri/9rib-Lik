<?php
class UserModel
{
    private int $id = 0;
    private string $nom = '';
    private string $prenom = '';
    private string $email = '';
    private string $password = '';
    private string $role = 'user';
    private string $dateCreation = '';

    public function __construct() {}

    public function getId(): int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getNomComplet(): string { return trim($this->prenom . ' ' . $this->nom); }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getRole(): string { return $this->role; }
    public function getDateCreation(): string { return $this->dateCreation; }

    public function setId(int $id): void { $this->id = $id; }
    public function setNom(string $nom): void { $this->nom = $nom; }
    public function setPrenom(string $prenom): void { $this->prenom = $prenom; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setPassword(string $password): void { $this->password = $password; }
    public function setRole(string $role): void { $this->role = $role; }
    public function setDateCreation(string $d): void { $this->dateCreation = $d; }

    public static function fromArray(array $row): self
    {
        $u = new self();
        $u->setId((int) ($row['id'] ?? 0));
        $u->setNom((string) ($row['nom'] ?? ''));
        $u->setPrenom((string) ($row['prenom'] ?? ''));
        $u->setEmail((string) ($row['email'] ?? ''));
        $u->setPassword((string) ($row['password'] ?? ''));
        $u->setRole((string) ($row['role'] ?? 'user'));
        $u->setDateCreation((string) ($row['date_creation'] ?? ''));
        return $u;
    }
}
