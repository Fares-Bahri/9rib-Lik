<?php ob_start(); ?>
<div class="container">
  <h1>Mot de passe oublié</h1>
  <?php if (!empty($errors)): ?><div class="alert alert-error"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div><?php endif; ?>
  <?php if ($message = flash()): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
  <form method="POST" action="index.php?action=forgot_password">
    <?= csrf_field() ?>
    <label for="email">Adresse email</label>
    <input type="email" id="email" name="email" required autocomplete="email">
    <button type="submit">Recevoir le lien</button>
  </form>
  <div class="switch-link"><a href="index.php?action=login">Retour à la connexion</a></div>
</div>
<?php $content = ob_get_clean(); $title = 'Mot de passe oublié - Fermes'; require __DIR__ . '/layout.php'; ?>
