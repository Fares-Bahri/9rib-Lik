<?php

ob_start();
?>
<div class="card">
  <div class="bar">
    <form class="filtre" method="get" action="index.php">
      <input type="hidden" name="action" value="admin_produits">
      <input type="search" name="q" value="<?= e($q) ?>" placeholder="Rechercher un produit..." aria-label="Rechercher">
      <select name="ferme_id" aria-label="Filtrer par ferme">
        <option value="0">Toutes les fermes</option>
        <?php foreach ($fermes as $f): ?>
          <option value="<?= $f->getId() ?>" <?= $fermeId === $f->getId() ? 'selected' : '' ?>><?= e($f->getNom()) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn sec" type="submit">Filtrer</button>
    </form>
    <a class="btn" href="index.php?action=admin_produit_form">Ajouter un produit</a>
  </div>
  <table>
    <thead><tr><th>ID</th><th>Ferme</th><th>Produit</th><th>Catégorie</th><th>Prix</th><th>Stock</th><th>Récolte</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($produits as $p): [$label, $cls] = ProduitController::statut($p); ?>
      <tr>
        <td><?= $p->getId() ?></td>
        <td><?= e($p->getFermeNom()) ?></td>
        <td><?= e($p->getNom()) ?></td>
        <td><?= e($p->getCategorie()) ?></td>
        <td><?= number_format($p->getPrix(), 2, ',', ' ') ?> TND/<?= e($p->getUnite()) ?></td>
        <td><?= $p->getQuantiteStock() ?> <?= e($p->getUnite()) ?></td>
        <td><?= e($p->getDateRecolte()) ?></td>
        <td><span class="badge <?= e($cls) ?>"><?= e($label) ?></span></td>
        <td>
          <a class="btn sm sec" href="index.php?action=admin_produit_form&id=<?= $p->getId() ?>">Modifier</a>
          <form class="inline" method="post" action="index.php?action=admin_produit_delete" data-confirm="Supprimer ce produit ?">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $p->getId() ?>">
            <button class="btn sm del" type="submit">Supprimer</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($produits)): ?><tr><td colspan="9">Aucun produit.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
$title = 'Gestion des produits et stocks';
$activePage = 'produits';
require __DIR__ . '/../layout_admin.php';
