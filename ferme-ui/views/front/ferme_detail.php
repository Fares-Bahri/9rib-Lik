<?php

ob_start();
?>
<main class="detail">
  <a href="index.php?action=fermes">Retour aux fermes</a>
  <h1 style="margin-top:16px"><?= e($ferme->getNom()) ?></h1>
  <span class="tag"><?= e($ferme->getTypeAgriculture()) ?></span>

  <div class="fiche">
    <div><small>Emplacement</small><strong><?= e($ferme->getEmplacement()) ?></strong></div>
    <div><small>Aire</small><strong><?= e((string) $ferme->getAire()) ?> ha</strong></div>
    <div><small>Type d'agriculture</small><strong><?= e($ferme->getTypeAgriculture()) ?></strong></div>
  </div>

  <h2>Description</h2>
  <p><?= nl2br(e($ferme->getDescription())) ?></p>

  <h2>Produits disponibles</h2>
  <?php if (empty($produits)): ?>
    <p class="empty" style="padding:0">Cette ferme n'a pas encore de produit.</p>
  <?php else: ?>
  <ul class="prods">
    <?php foreach ($produits as $p): [$label, $cls] = ProduitController::statut($p); ?>
    <li class="<?= $cls === 'off' ? 'rupture' : '' ?>">
      <span><strong><?= e($p->getNom()) ?></strong> · <?= e($p->getCategorie()) ?></span>
      <span><?= number_format($p->getPrix(), 2, ',', ' ') ?> TND / <?= e($p->getUnite()) ?> · <?= e($label) ?> (<?= $p->getQuantiteStock() ?> <?= e($p->getUnite()) ?>)</span>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</main>
<?php
$content = ob_get_clean();
$title = $ferme->getNom() . ' - Fermes';
require __DIR__ . '/../layout.php';
