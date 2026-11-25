<?php

ob_start();
$edit = $produit->getId() > 0;
?>
<div class="card">
  <?php if (!empty($errors)): ?>
    <div class="alert alert-error"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div>
  <?php endif; ?>
  <form class="f" method="post" action="index.php?action=admin_produit_form<?= $edit ? '&id=' . $produit->getId() : '' ?>">
    <?= csrf_field() ?>
    <label>Ferme productrice
      <select name="ferme_id" required>
        <option value="">Choisir...</option>
        <?php foreach ($fermes as $f): ?>
          <option value="<?= $f->getId() ?>" <?= $produit->getFermeId() === $f->getId() ? 'selected' : '' ?>><?= e($f->getNom()) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Nom du produit
      <input type="text" name="nom" required minlength="2" maxlength="100" value="<?= e($produit->getNom()) ?>" placeholder="Ex : Tomates">
    </label>
    <div class="duo">
      <label>Catégorie
        <select name="categorie" required>
          <option value="">Choisir...</option>
          <?php foreach (ProduitController::CATEGORIES as $c): ?>
            <option <?= $produit->getCategorie() === $c ? 'selected' : '' ?>><?= e($c) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Unité
        <select name="unite" required>
          <option value="">Choisir...</option>
          <?php foreach (ProduitController::UNITES as $u): ?>
            <option <?= $produit->getUnite() === $u ? 'selected' : '' ?>><?= e($u) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div class="duo">
      <label>Prix (TND)
        <input type="number" name="prix" required min="0.01" step="0.01" value="<?= $edit ? e((string) $produit->getPrix()) : '' ?>">
      </label>
      <label>Quantité en stock
        <input type="number" name="quantite_stock" required min="0" step="1" value="<?= $edit ? $produit->getQuantiteStock() : '' ?>">
      </label>
    </div>
    <label>Date de récolte
      <input type="date" name="date_recolte" value="<?= e($produit->getDateRecolte()) ?>">
    </label>
    <div class="actions">
      <button class="btn" type="submit"><?= $edit ? 'Mettre à jour' : 'Enregistrer le produit' ?></button>
      <a class="btn sec" href="index.php?action=admin_produits">Annuler</a>
    </div>
  </form>
</div>
<?php
$content = ob_get_clean();
$title = $edit ? 'Modifier le produit' : 'Ajouter un produit';
$activePage = 'produits';
require __DIR__ . '/../layout_admin.php';
