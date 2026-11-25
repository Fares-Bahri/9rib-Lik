<?php

ob_start();
$old = $old ?? [];
?>
<div class="container">
  <h1>Créer un compte</h1>
  <?php if (!empty($errors)): ?>
    <div class="alert alert-error"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div>
  <?php endif; ?>
  <form method="POST" action="index.php?action=register">
    <?= csrf_field() ?>
    <label for="nom">Nom</label>
    <input type="text" id="nom" name="nom" required minlength="2" maxlength="50" value="<?= e($old['nom'] ?? '') ?>">
    <label for="prenom">Prénom</label>
    <input type="text" id="prenom" name="prenom" required minlength="2" maxlength="50" value="<?= e($old['prenom'] ?? '') ?>">
    <label for="email">Adresse email</label>
    <input type="email" id="email" name="email" required value="<?= e($old['email'] ?? '') ?>">
    <label for="password">Mot de passe (8 caractères minimum)</label>
    <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
    <label for="password_confirm">Confirmer le mot de passe</label>
    <input type="password" id="password_confirm" name="password_confirm" required minlength="8" autocomplete="new-password">
    <button type="submit">Créer mon compte</button>
  </form>
  <div class="switch-link">Déjà un compte ? <a href="index.php?action=login">Se connecter</a></div>
</div>
<?php
$content = ob_get_clean();
$title = 'Inscription - Fermes';
require __DIR__ . '/layout.php';
