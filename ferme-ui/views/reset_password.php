<?php ob_start(); ?>
<div class="container">
  <h1>Nouveau mot de passe</h1>
  <?php if (!empty($errors)): ?><div class="alert alert-error"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div><?php endif; ?>
  <form method="POST" action="index.php?action=reset_password&amp;token=<?= e($token) ?>">
    <?= csrf_field() ?>
    <label for="password">Nouveau mot de passe (8 caractères minimum)</label>
    <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
    <label for="password_confirm">Confirmer le mot de passe</label>
    <input type="password" id="password_confirm" name="password_confirm" required minlength="8" autocomplete="new-password">
    <button type="submit">Enregistrer le nouveau mot de passe</button>
  </form>
</div>
<?php $content = ob_get_clean(); $title = 'Réinitialiser le mot de passe - Fermes'; require __DIR__ . '/layout.php'; ?>
