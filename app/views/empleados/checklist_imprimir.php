<?php
/**
 * Réplica del formato oficial FO-TH-027 "Lista de Chequeo Vinculaciones Laborales" V9.
 */
$chk = function (bool $on): string {
    return '<span class="hv-mini-chk '.($on?'on':'').'" style="margin-right:0"><span class="sq">'.($on?'✕':'').'</span></span>';
};
?>

<style>
  /* Formato compacto de ahorro de papel: reduce márgenes y tamaños solo en esta hoja */
  @media print {
    .hoja-doc { padding: 10px !important; }
    body { font-size: 11px; }
  }
  .hv-tbl .val, .hv-tbl .lbl, .hv-tbl .banner { padding: 3px 6px !important; font-size: 11px !important; line-height: 1.25; }
  .hv-title { font-size: 14px !important; margin: 6px 0 !important; }
  .hv-topbar { margin-bottom: 6px !important; }
  .hv-note { font-size: 9.5px !important; margin-top: 6px !important; }
  .hv-firmas { margin-top: 10px !important; }
</style>

<div class="hv-topbar">
  <div class="hv-brand">
    GESTIÓN DEL TALENTO HUMANO
    <span>Lista de Chequeo Vinculaciones Laborales</span>
  </div>
  <div class="hv-code-box">
    <div><b>Código</b> <span>FO-TH-027</span></div>
    <div><b>Versión</b> <span>09</span></div>
    <div><b>Fecha</b> <span>Noviembre 2025</span></div>
  </div>
</div>
<div class="hv-title">Lista de Chequeo Vinculaciones Laborales</div>

<table class="hv-tbl" style="margin-bottom:14px">
  <tr>
    <td class="lbl" style="width:180px">Nombre del colaborador</td>
    <td class="val"><?= View::e($empleado['nombres'].' '.$empleado['apellidos']) ?></td>
  </tr>
  <tr>
    <td class="lbl">No. de identificación</td>
    <td class="val"><?= View::e($empleado['numero_documento']) ?></td>
  </tr>
</table>

<table class="hv-tbl">
  <tr>
    <td class="banner" style="text-align:center;width:36px">ITEM</td>
    <td class="banner">DOCUMENTO</td>
    <td class="banner" style="text-align:center;width:40px">SI</td>
    <td class="banner" style="text-align:center;width:40px">NO</td>
    <td class="banner" style="width:180px">OBSERVACIONES</td>
  </tr>
  <?php foreach ($items as $i => $it): ?>
  <tr>
    <td class="val" style="text-align:center"><?= $i + 1 ?></td>
    <td class="val">
      <?= View::e($it['nombre']) ?><?= $it['descripcion'] ? ' <span style="color:var(--muted);font-size:10.5px">('.View::e($it['descripcion']).')</span>' : '' ?>
      <?php if ($it['archivo_path']): ?>
        <div style="font-size:10px;color:var(--primary);margin-top:2px">📎 <?= View::e($it['archivo_nombre_original']) ?></div>
      <?php endif; ?>
    </td>
    <td class="val" style="text-align:center"><?= $chk((bool)$it['entregado']) ?></td>
    <td class="val" style="text-align:center"><?= $chk(!$it['entregado']) ?></td>
    <td class="val"><?= $it['observaciones'] ? View::e($it['observaciones']) : '' ?></td>
  </tr>
  <?php endforeach; ?>
</table>

<div class="hv-note" style="text-align:center;margin-top:10px">
  Tenga en cuenta que todos los documentos marcados en esta lista de chequeo deben ser soportados con documento adjunto.
</div>

<div class="hv-firmas">
  <div class="hv-firma">
    <div class="linea">Revisó en archivo: <?= View::e($firma['reviso_en_archivo'] ?? '') ?></div>
  </div>
  <div class="hv-firma">
    <div class="linea">Firma: <?= View::e($firma['firma_reviso'] ?? '') ?></div>
  </div>
</div>
