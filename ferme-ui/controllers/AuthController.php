<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    
    public static function isAuthenticated(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function hasRole(string $role): bool
    {
        return self::isAuthenticated() && ($_SESSION['role'] ?? null) === $role;
    }

    public static function requireAuth(): void
    {
        if (!self::isAuthenticated()) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireAuth();
        if (($_SESSION['role'] ?? null) !== $role) {
            http_response_code(403);
            die('Accès refusé : vous n\'avez pas les permissions nécessaires.');
        }
    }

    
    public static function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        if (!$token || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    
    private function existsByEmail(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM utilisateur WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetch();
    }

    private function insertUser(UserModel $user): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO utilisateur (nom, prenom, email, password, role) VALUES (:nom, :prenom, :email, :password, :role)'
        );
        return $stmt->execute([
            'nom'      => $user->getNom(),
            'prenom'   => $user->getPrenom(),
            'email'    => $user->getEmail(),
            'password' => $user->getPassword(),
            'role'     => $user->getRole(),
        ]);
    }

    private function findByEmail(string $email): ?UserModel
    {
        $stmt = $this->db->prepare('SELECT * FROM utilisateur WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? UserModel::fromArray($row) : null;
    }

    private function createPasswordReset(string $email): ?string
    {
        if (!$this->findByEmail($email)) return null;
        $token = bin2hex(random_bytes(32));
        $stmt = $this->db->prepare('DELETE FROM password_resets WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $stmt = $this->db->prepare('INSERT INTO password_resets (email, token_hash, expires_at) VALUES (:email, :token_hash, DATE_ADD(UTC_TIMESTAMP(), INTERVAL 1 HOUR))');
        $stmt->execute(['email' => $email, 'token_hash' => hash('sha256', $token)]);
        return $token;
    }

    public function requestPasswordReset(array $post): array
    {
        if (!self::verifyCsrfToken($post['csrf_token'] ?? null)) return ['Jeton de sécurité invalide. Veuillez réessayer.'];
        $email = trim($post['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return ['Veuillez saisir une adresse email valide.'];
        $token = $this->createPasswordReset($email);
        if ($token !== null) {
            $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'), '/\\');
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $link = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $baseUrl . '/index.php?action=reset_password&token=' . urlencode($token);
            @mail($email, 'Réinitialisation de votre mot de passe', "Pour choisir un nouveau mot de passe, ouvrez ce lien (valable 1 heure) :\n\n$link\n", "Content-Type: text/plain; charset=UTF-8\r\n");
        }
        return [];
    }

    public function resetPassword(array $post, string $token): array
    {
        if (!self::verifyCsrfToken($post['csrf_token'] ?? null)) return ['Jeton de sécurité invalide. Veuillez réessayer.'];
        $password = $post['password'] ?? '';
        if (strlen($password) < 8) return ['Le mot de passe doit contenir au moins 8 caractères.'];
        if ($password !== ($post['password_confirm'] ?? '')) return ['Les mots de passe ne correspondent pas.'];
        $stmt = $this->db->prepare('SELECT email FROM password_resets WHERE token_hash = :token_hash AND expires_at > UTC_TIMESTAMP() LIMIT 1');
        $stmt->execute(['token_hash' => hash('sha256', $token)]);
        $reset = $stmt->fetch();
        if (!$reset) return ['Ce lien est invalide ou a expiré. Demandez un nouveau lien.'];
        $stmt = $this->db->prepare('UPDATE utilisateur SET password = :password WHERE email = :email');
        $stmt->execute(['password' => password_hash($password, PASSWORD_DEFAULT), 'email' => $reset['email']]);
        $stmt = $this->db->prepare('DELETE FROM password_resets WHERE email = :email');
        $stmt->execute(['email' => $reset['email']]);
        return [];
    }

    public function findById(int $id): ?UserModel
    {
        $stmt = $this->db->prepare('SELECT * FROM utilisateur WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? UserModel::fromArray($row) : null;
    }

   
    public function register(array $post): array
    {
        $errors = [];
        if (!self::verifyCsrfToken($post['csrf_token'] ?? null)) {
            $errors[] = "Jeton de sécurité invalide. Veuillez réessayer.";
            return $errors;
        }

        $nom             = trim($post['nom'] ?? '');
        $prenom          = trim($post['prenom'] ?? '');
        $email           = trim($post['email'] ?? '');
        $password        = $post['password'] ?? '';
        $passwordConfirm = $post['password_confirm'] ?? '';

        if ($nom === '' || $prenom === '' || $email === '' || $password === '') {
            $errors[] = "Tous les champs sont obligatoires.";
        }
        if (!preg_match('/^[\p{L}\s\'\-]{2,50}$/u', $nom) || !preg_match('/^[\p{L}\s\'\-]{2,50}$/u', $prenom)) {
            $errors[] = "Nom et prénom : 2 à 50 caractères (lettres, espaces, ' ou -).";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Adresse email invalide.";
        }
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
        if ($password !== $passwordConfirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        if (!empty($errors)) {
            return $errors;
        }
        if ($this->existsByEmail($email)) {
            $errors[] = "Cet email est déjà utilisé.";
            return $errors;
        }

        $user = new UserModel();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        $user->setRole('user');

        if (!$this->insertUser($user)) {
            $errors[] = "Erreur lors de la création du compte.";
        }
        return $errors;
    }

    
    public function login(array $post): array
    {
        $errors = [];
        if (!self::verifyCsrfToken($post['csrf_token'] ?? null)) {
            $errors[] = "Jeton de sécurité invalide. Veuillez réessayer.";
            return $errors;
        }

        $email    = trim($post['email'] ?? '');
        $password = $post['password'] ?? '';
        if ($email === '' || $password === '') {
            $errors[] = "Veuillez saisir votre email et votre mot de passe.";
            return $errors;
        }

        $user = $this->findByEmail($email);
        if (!$user || !password_verify($password, $user->getPassword())) {
            $errors[] = "Email ou mot de passe incorrect."; 
            return $errors;
        }

        session_regenerate_id(true);
        $_SESSION['user_id']       = $user->getId();
        $_SESSION['username']      = $user->getNomComplet();
        $_SESSION['role']          = $user->getRole();
        $_SESSION['last_activity'] = time();
        return [];
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }
}
