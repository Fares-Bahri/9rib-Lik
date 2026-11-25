<?php

ob_start();
?>
<div class="stats">
  <div class="stat"><span>Fermes</span><b><?= $totalFermes ?></b></div>
  <div class="stat"><span>Superficie totale</span><b><?= e((string) $totalAire) ?> ha</b></div>
  <div class="stat"><span>Produits</span><b><?= $totalProduits ?></b></div>
</div>
<div class="card">
  <div class="bar">
    <form class="filtre" method="get" action="index.php">
      <input type="hidden" name="action" value="admin_fermes">
      <input type="search" name="q" value="<?= e($q) ?>" placeholder="Rechercher une ferme..." aria-label="Rechercher">
      <button class="btn sec" type="submit">Rechercher</button>
    </form>
    <a class="btn" href="index.php?action=admin_ferme_form">Ajouter une ferme</a>
  </div>
  <table>
    <thead><tr><th>ID</th><th>Nom</th><th>Emplacement</th><th>Aire (ha)</th><th>Type</th><th>Produits</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($fermes as $f): ?>
      <tr>
        <td><?= $f->getId() ?></td>
        <td><?= e($f->getNom()) ?></td>
        <td><?= e($f->getEmplacement()) ?></td>
        <td><?= e((string) $f->getAire()) ?></td>
        <td><span class="badge"><?= e($f->getTypeAgriculture()) ?></span></td>
        <td><?= $f->getNbProduits() ?></td>
        <td>
          <a class="btn sm sec" href="index.php?action=ferme&id=<?= $f->getId() ?>">Voir</a>
          <a class="btn sm sec" href="index.php?action=admin_ferme_form&id=<?= $f->getId() ?>">Modifier</a>
          <form class="inline" method="post" action="index.php?action=admin_ferme_delete" data-confirm="Supprimer cette ferme et tous ses produits ?">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $f->getId() ?>">
            <button class="btn sm del" type="submit">Supprimer</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($fermes)): ?><tr><td colspan="7">Aucune ferme.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
$title = 'Gestion des fermes';
$activePage = 'fermes';
require __DIR__ . '/../layout_admin.php';
