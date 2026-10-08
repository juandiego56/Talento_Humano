<?php
/** Uso: View::partial('layouts._paginacion', ['pagina'=>$pagina,'paginas'=>$paginas,'total'=>$total,'porPagina'=>$porPagina]); */
if (($paginas ?? 1) < 1) return;
$desde = $total > 0 ? (($pagina - 1) * $porPagina) + 1 : 0;
$hasta = min($total, $pagina * $porPagina);
$ventana = [];
for ($i = 1; $i <= $paginas; $i++) {
    if ($i === 1 || $i === $paginas || abs($i - $pagina) <= 2) $ventana[] = $i;
}
?>
<div class="paginacion">
  <div class="paginacion-info">Mostrando <?= $desde ?>–<?= $hasta ?> de <?= $total ?></div>
  <?php if ($paginas > 1): ?>
  <div class="paginacion-nav">
    <?php if ($pagina > 1): ?><a href="<?= View::e(View::urlPagina($pagina - 1)) ?>" class="pg">‹ Anterior</a><?php endif; ?>
    <?php $prev = 0; foreach ($ventana as $p): ?>
      <?php if ($p - $prev > 1): ?><span class="pg-gap">…</span><?php endif; ?>
      <a href="<?= View::e(View::urlPagina($p)) ?>" class="pg <?= $p === $pagina ? 'activa' : '' ?>"><?= $p ?></a>
    <?php $prev = $p; endforeach; ?>
    <?php if ($pagina < $paginas): ?><a href="<?= View::e(View::urlPagina($pagina + 1)) ?>" class="pg">Siguiente ›</a><?php endif; ?>
  </div>
  <?php endif; ?>
</div>
