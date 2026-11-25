<?php

$flash = flash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Ferme') ?></title>
<link rel="stylesheet" href="assets/css/front.css">
</head>
<body>
<nav class="nav">
  <b>Du champ à l'assiette</b>
  <div>
    <?php if (AuthController::isAuthenticated()): ?>
      <a href="index.php?action=fermes">Fermes</a>
      <?php if (AuthController::hasRole('admin')): ?><a href="index.php?action=admin_fermes">Espace admin</a><?php endif; ?>
      <a href="index.php?action=logout" class="btn">Déconnexion</a>
    <?php else: ?>
      <a href="index.php?action=login">Connexion</a>
      <a href="index.php?action=register" class="btn">Inscription</a>
    <?php endif; ?>
  </div>
</nav>
<?php if ($flash): ?><div class="alert alert-success" style="margin:0 6vw"><?= e($flash) ?></div><?php endif; ?>
<?= $content ?>
<footer>Projet Technologies Web 2A · Gestion des fermes</footer>
<script src="assets/js/app.js"></script>
</body>
</html>
