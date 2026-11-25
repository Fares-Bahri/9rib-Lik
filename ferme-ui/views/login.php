<?php

ob_start();
?>
<div class="container">
  <h1>Connexion</h1>
  <?php if (!empty($errors)): ?>
    <div class="alert alert-error"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div>
  <?php endif; ?>
  <?php if (!empty($_GET['registered'])): ?>
    <div class="alert alert-success">Compte créé avec succès, vous pouvez vous connecter.</div>
  <?php endif; ?>
  <?php if ($message = flash()): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
  <form method="POST" action="index.php?action=login">
    <?= csrf_field() ?>
    <label for="email">Adresse email</label>
    <input type="email" id="email" name="email" required autocomplete="email">
    <label for="password">Mot de passe</label>
    <input type="password" id="password" name="password" required autocomplete="current-password">
    <div class="switch-link"><a href="index.php?action=forgot_password">Mot de passe oublié ?</a></div>
    <button type="submit">Se connecter</button>
  </form>
  <div class="switch-link">Pas encore de compte ? <a href="index.php?action=register">S'inscrire</a></div>
</div>
<?php
$content = ob_get_clean();
$title = 'Connexion - Fermes';
require __DIR__ . '/layout.php';
