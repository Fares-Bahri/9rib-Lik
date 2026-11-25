<?php

$activePage = $activePage ?? 'fermes';
$flash = flash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Administration') ?></title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="wrap">
  <aside class="side">
    <div class="brand">● Admin Fermes</div>
    <a href="index.php?action=admin_fermes" class="<?= $activePage === 'fermes' ? 'on' : '' ?>">Fermes</a>
    <a href="index.php?action=admin_produits" class="<?= $activePage === 'produits' ? 'on' : '' ?>">Produits</a>
    <a href="index.php?action=fermes">Retour au site public</a>
    <div class="foot">
      <?= e($user ? $user->getNomComplet() : '') ?><br>
      <small>ADMINISTRATEUR</small><br>
      <a href="index.php?action=logout" style="padding:8px 0;border:0">Se déconnecter</a>
    </div>
  </aside>
  <div class="main">
    <div class="top"><h1><?= e($title ?? '') ?></h1><span class="env">BACKOFFICE</span></div>
    <div class="content">
      <?php if ($flash): ?><div class="alert alert-success"><?= e($flash) ?></div><?php endif; ?>
      <?= $content ?>
    </div>
  </div>
</div>
<script src="assets/js/app.js"></script>
</body>
</html>
