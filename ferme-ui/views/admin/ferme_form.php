<?php

ob_start();
$edit = $ferme->getId() > 0;
?>
<div class="card">
  <?php if (!empty($errors)): ?>
    <div class="alert alert-error"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div>
  <?php endif; ?>
  <form class="f" method="post" action="index.php?action=admin_ferme_form<?= $edit ? '&id=' . $ferme->getId() : '' ?>">
    <?= csrf_field() ?>
    <label>Nom de la ferme
      <input type="text" name="nom" required minlength="3" maxlength="100" value="<?= e($ferme->getNom()) ?>" placeholder="Ex : Ferme El Baraka">
    </label>
    <label>Emplacement
      <input type="text" name="emplacement" required maxlength="150" value="<?= e($ferme->getEmplacement()) ?>" placeholder="Ville, gouvernorat">
    </label>
    <div class="duo">
      <label>Aire (hectares)
        <input type="number" name="aire" required min="0.1" step="0.1" value="<?= $edit ? e((string) $ferme->getAire()) : '' ?>">
      </label>
      <label>Type d'agriculture
        <select name="type_agriculture" required>
          <option value="">Choisir...</option>
          <?php foreach (FermeController::TYPES as $t): ?>
            <option value="<?= e($t) ?>" <?= $ferme->getTypeAgriculture() === $t ? 'selected' : '' ?>><?= e($t) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <label>Description <small>500 caractères maximum</small>
      <textarea name="description" rows="5" maxlength="500"><?= e($ferme->getDescription()) ?></textarea>
    </label>
    <div class="actions">
      <button class="btn" type="submit"><?= $edit ? 'Mettre à jour' : 'Enregistrer la ferme' ?></button>
      <a class="btn sec" href="index.php?action=admin_fermes">Annuler</a>
    </div>
  </form>
</div>
<?php
$content = ob_get_clean();
$title = $edit ? 'Modifier la ferme' : 'Ajouter une ferme';
$activePage = 'fermes';
require __DIR__ . '/../layout_admin.php';
