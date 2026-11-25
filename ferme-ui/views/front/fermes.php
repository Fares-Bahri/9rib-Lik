<?php

ob_start();
?>
<header class="hero">
  <h1>Chaque achat soutient une ferme près de chez vous</h1>
  <p>Découvrez les producteurs partenaires, leurs pratiques et leurs produits.</p>
  <div class="flux"><span class="p">Producteurs</span>➜<span class="r">Points relais</span>➜<span class="c">Consommateurs</span></div>
</header>

<form class="filtres" method="get" action="index.php">
  <input type="hidden" name="action" value="fermes">
  <input type="search" name="q" value="<?= e($q) ?>" placeholder="Rechercher une ferme" aria-label="Rechercher une ferme">
  <select name="type" aria-label="Type d'agriculture">
    <option value="">Tous les types</option>
    <?php foreach (FermeController::TYPES as $t): ?>
      <option value="<?= e($t) ?>" <?= $type === $t ? 'selected' : '' ?>><?= e($t) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit">Filtrer</button>
</form>

<?php if (empty($fermes)): ?>
  <p class="empty">Aucune ferme ne correspond à votre recherche.</p>
<?php else: ?>
<main class="grille">
  <?php foreach ($fermes as $f): ?>
  <article class="carte">
    <span class="tag"><?= e($f->getTypeAgriculture()) ?></span>
    <h3><?= e($f->getNom()) ?></h3>
    <div class="lieu"><?= e($f->getEmplacement()) ?> · <?= e((string) $f->getAire()) ?> ha</div>
    <p><?= e(mb_strimwidth($f->getDescription(), 0, 110, '…')) ?></p>
    <span class="tag orange"><?= $f->getNbProduits() ?> produit(s)</span>
    <a class="lien" href="index.php?action=ferme&id=<?= $f->getId() ?>">Voir la ferme</a>
  </article>
  <?php endforeach; ?>
</main>
<?php endif; ?>
<?php
$content = ob_get_clean();
$title = 'Nos fermes partenaires';
require __DIR__ . '/../layout.php';
