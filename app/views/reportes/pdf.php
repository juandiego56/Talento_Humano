<h2 style="margin-top:0">Reporte de Personal</h2>
<p style="color:#666;font-size:13px">
  Generado el <?= date('d/m/Y H:i') ?>
  <?php if ($programaNombre): ?> · Programa: <strong><?= View::e($programaNombre) ?></strong><?php endif; ?>
  <?php if ($filtros['estado'] !== ''): ?> · Estado: <strong><?= View::e(ucfirst($filtros['estado'])) ?></strong><?php endif; ?>
</p>

<h3>Cualificación docente (nivel de formación)</h3>
<?php if (!$cualificacion): ?>
  <p style="color:#666">No hay docentes que coincidan con el filtro seleccionado.</p>
<?php else: ?>
  <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
    <thead>
      <tr style="border-bottom:2px solid #333">
        <th style="text-align:left;padding:4px">Nivel de formación</th>
        <th style="text-align:right;padding:4px">Docentes</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($cualificacion as $c): ?>
      <tr style="border-bottom:1px solid #ddd">
        <td style="padding:4px"><?= View::e($c['etiqueta']) ?></td>
        <td style="padding:4px;text-align:right"><?= $c['total'] ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<h3>Personal (<?= count($empleados) ?>)</h3>
<table style="width:100%;border-collapse:collapse;font-size:12px">
  <thead>
    <tr style="border-bottom:2px solid #333">
      <th style="text-align:left;padding:4px">Nombre</th>
      <th style="text-align:left;padding:4px">Documento</th>
      <th style="text-align:left;padding:4px">Cargo</th>
      <th style="text-align:left;padding:4px">Área</th>
      <th style="text-align:left;padding:4px">Programa</th>
      <th style="text-align:left;padding:4px">Nivel de formación</th>
      <th style="text-align:left;padding:4px">Dedicación</th>
      <th style="text-align:left;padding:4px">Estado</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!$empleados): ?><tr><td colspan="8" style="padding:8px;color:#666">Sin resultados.</td></tr><?php endif; ?>
    <?php foreach ($empleados as $e): ?>
    <tr style="border-bottom:1px solid #ddd">
      <td style="padding:4px"><?= View::e($e['nombres'] . ' ' . $e['apellidos']) ?></td>
      <td style="padding:4px"><?= View::e($e['numero_documento']) ?></td>
      <td style="padding:4px"><?= View::e($e['cargo'] ?? '—') ?></td>
      <td style="padding:4px"><?= View::e($e['area'] ?? '—') ?></td>
      <td style="padding:4px"><?= View::e($e['programa'] ?? '—') ?></td>
      <td style="padding:4px"><?= $e['nivel_educativo'] ? View::nivelEducativoLabel($e['nivel_educativo']) : '—' ?></td>
      <td style="padding:4px"><?= View::tipoVinculacionDocenteLabel($e['tipo_vinculacion_docente']) ?></td>
      <td style="padding:4px"><?= View::e(ucfirst($e['estado'])) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
