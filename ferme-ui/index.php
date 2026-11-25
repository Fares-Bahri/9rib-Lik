<?php
declare(strict_types=1);


ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');

session_start();


$sessionLifetime = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionLifetime)) {
    $_SESSION = [];
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();


header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'");


require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/FermeController.php';
require_once __DIR__ . '/controllers/ProduitController.php';

$authController    = new AuthController();
$fermeController   = new FermeController();
$produitController = new ProduitController();


function e(?string $s): string { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
function csrf_field(): string { return '<input type="hidden" name="csrf_token" value="' . e(AuthController::getCsrfToken()) . '">'; }
function redirect(string $action): void { header('Location: index.php?action=' . $action); exit; }
function flash(?string $msg = null): ?string
{
    if ($msg !== null) { $_SESSION['flash'] = $msg; return null; }
    $m = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $m;
}
function isPost(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }


$action = $_GET['action'] ?? 'home';

switch ($action) {

    
    case 'home':
        if (AuthController::isAuthenticated()) {
            redirect(($_SESSION['role'] ?? '') === 'admin' ? 'admin_fermes' : 'fermes');
        }
        redirect('login');

    
    case 'register':
        if (AuthController::isAuthenticated()) { redirect('home'); }
        $errors = [];
        $old = [];
        if (isPost()) {
            $errors = $authController->register($_POST);
            $old = ['nom' => $_POST['nom'] ?? '', 'prenom' => $_POST['prenom'] ?? '', 'email' => $_POST['email'] ?? ''];
            if (empty($errors)) {
                header('Location: index.php?action=login&registered=1');
                exit;
            }
        }
        require __DIR__ . '/views/register.php';
        break;

    
    case 'login':
        if (AuthController::isAuthenticated()) { redirect('home'); }
        $errors = [];
        if (isPost()) {
            $errors = $authController->login($_POST);
            if (empty($errors)) { redirect('home'); }
        }
        require __DIR__ . '/views/login.php';
        break;

    case 'forgot_password':
        $errors = [];
        if (isPost()) {
            $errors = $authController->requestPasswordReset($_POST);
            if (empty($errors)) flash('Si cette adresse correspond à un compte, un lien de réinitialisation a été envoyé.');
        }
        require __DIR__ . '/views/forgot_password.php';
        break;

    case 'reset_password':
        $token = (string) ($_GET['token'] ?? '');
        $errors = [];
        if (isPost()) {
            $errors = $authController->resetPassword($_POST, $token);
            if (empty($errors)) { flash('Mot de passe modifié. Vous pouvez vous connecter.'); redirect('login'); }
        }
        require __DIR__ . '/views/reset_password.php';
        break;

    case 'logout':
        $authController->logout();
        redirect('login');

    
    case 'fermes':
        AuthController::requireAuth();
        $q = trim($_GET['q'] ?? '');
        $type = (string) ($_GET['type'] ?? '');
        $fermes = $fermeController->findAll($q, $type);
        require __DIR__ . '/views/front/fermes.php';
        break;

    case 'ferme':
        AuthController::requireAuth();
        $ferme = $fermeController->findById((int) ($_GET['id'] ?? 0));
        if (!$ferme) { http_response_code(404); die('404 - Ferme introuvable'); }
        $produits = $produitController->findAll($ferme->getId());
        require __DIR__ . '/views/front/ferme_detail.php';
        break;

    
    case 'admin_fermes':
        AuthController::requireRole('admin');
        $user = $authController->findById((int) $_SESSION['user_id']);
        $q = trim($_GET['q'] ?? '');
        $fermes = $fermeController->findAll($q);
        $totalFermes = $fermeController->countAll();
        $totalAire = $fermeController->totalAire();
        $totalProduits = $produitController->countAll();
        require __DIR__ . '/views/admin/fermes.php';
        break;

    case 'admin_ferme_form': 
        AuthController::requireRole('admin');
        $user = $authController->findById((int) $_SESSION['user_id']);
        $id = (int) ($_GET['id'] ?? 0);
        $ferme = $id > 0 ? $fermeController->findById($id) : new FermeModel();
        if (!$ferme) { http_response_code(404); die('404 - Ferme introuvable'); }
        $errors = [];
        if (isPost()) {
            $ferme = $fermeController->fromPost($_POST, $id);
            if (!AuthController::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = "Jeton de sécurité invalide. Veuillez réessayer.";
            } else {
                $errors = $fermeController->validate($_POST);
            }
            if (empty($errors)) {
                $fermeController->save($ferme);
                flash($id > 0 ? 'Ferme modifiée avec succès.' : 'Ferme ajoutée avec succès.');
                redirect('admin_fermes');
            }
        }
        require __DIR__ . '/views/admin/ferme_form.php';
        break;

    case 'admin_ferme_delete':
        AuthController::requireRole('admin');
        if (isPost() && AuthController::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $fermeController->delete((int) ($_POST['id'] ?? 0));
            flash('Ferme supprimée (ses produits aussi).');
        }
        redirect('admin_fermes');

    // ================= BACKOFFICE : PRODUITS (CRUD) =================
    case 'admin_produits':
        AuthController::requireRole('admin');
        $user = $authController->findById((int) $_SESSION['user_id']);
        $fermeId = (int) ($_GET['ferme_id'] ?? 0);
        $q = trim($_GET['q'] ?? '');
        $produits = $produitController->findAll($fermeId, $q);
        $fermes = $fermeController->findAll();
        require __DIR__ . '/views/admin/produits.php';
        break;

    case 'admin_produit_form':
        AuthController::requireRole('admin');
        $user = $authController->findById((int) $_SESSION['user_id']);
        $id = (int) ($_GET['id'] ?? 0);
        $produit = $id > 0 ? $produitController->findById($id) : new ProduitModel();
        if (!$produit) { http_response_code(404); die('404 - Produit introuvable'); }
        $fermes = $fermeController->findAll();
        $errors = [];
        if (isPost()) {
            $produit = $produitController->fromPost($_POST, $id);
            if (!AuthController::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                $errors[] = "Jeton de sécurité invalide. Veuillez réessayer.";
            } else {
                $errors = $produitController->validate($_POST);
            }
            if (empty($errors)) {
                $produitController->save($produit);
                flash($id > 0 ? 'Produit modifié avec succès.' : 'Produit ajouté avec succès.');
                redirect('admin_produits');
            }
        }
        require __DIR__ . '/views/admin/produit_form.php';
        break;

    case 'admin_produit_delete':
        AuthController::requireRole('admin');
        if (isPost() && AuthController::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $produitController->delete((int) ($_POST['id'] ?? 0));
            flash('Produit supprimé.');
        }
        redirect('admin_produits');

    default:
        http_response_code(404);
        echo '404 - Page not found';
        break;
}
