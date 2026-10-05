<div class="page-header">
  <div>
    <div class="page-title">Colilla de Pago</div>
    <div class="page-subtitle"><?= View::e($detalle['nombres'].' '.$detalle['apellidos']) ?> — <?= View::periodoLabel($nomina['periodo']) ?></div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/nomina/<?= $nomina['id'] ?>" class="btn btn-outline">← Volver a la nómina</a>
    <button onclick="window.print()" class="btn btn-outline">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:14px;height:14px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M7 8V3h10v5M7 17H5a2 2 0 01-2-2v-4a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2h-2M7 13h10v8H7v-8z"/>
      </svg>
      Imprimir
    </button>
  </div>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Datos del empleado</div></div>
  <div class="dato-grid">
    <div class="dato-item"><div class="dato-label">Documento</div><div class="dato-valor"><?= View::e($detalle['numero_documento']) ?></div></div>
    <div class="dato-item"><div class="dato-label">Cargo</div><div class="dato-valor"><?= View::e($detalle['cargo'] ?? '—') ?></div></div>
    <div class="dato-item"><div class="dato-label">Área</div><div class="dato-valor"><?= View::e($detalle['area'] ?? '—') ?></div></div>
    <div class="dato-item"><div class="dato-label">Periodo</div><div class="dato-valor"><?= View::periodoLabel($nomina['periodo']) ?></div></div>
    <div class="dato-item"><div class="dato-label">Días trabajados</div><div class="dato-valor"><?= (int)$detalle['dias_trabajados'] ?></div></div>
    <div class="dato-item"><div class="dato-label">Salario básico</div><div class="dato-valor"><?= View::money($detalle['salario_base']) ?></div></div>
  </div>
</div>

<div class="form-row" style="align-items:start">
  <div class="card">
    <div class="card-header"><div class="card-title" style="color:var(--success)">Devengados</div></div>
    <table>
      <tbody>
        <?php $totalDev = 0; foreach ($conceptos as $c): if ($c['tipo'] !== 'devengado') continue; $totalDev += $c['valor']; ?>
        <tr>
          <td><?= View::e($c['nombre_concepto']) ?></td>
          <td style="text-align:right"><?= View::money($c['valor']) ?></td>
          <td style="width:30px">
            <?php if ($nomina['estado']==='borrador' && Auth::puedeGestionar()): ?>
            <form method="POST" action="<?= APP_URL ?>/nomina/<?= $nomina['id'] ?>/detalle/<?= $detalle['id'] ?>/concepto/<?= $c['id'] ?>/eliminar" onsubmit="return confirm('¿Eliminar este concepto?')">
              <button type="submit" class="btn btn-outline btn-sm" style="padding:2px 8px">✕</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title" style="color:var(--danger)">Deducciones</div></div>
    <table>
      <tbody>
        <?php $totalDed = 0; foreach ($conceptos as $c): if ($c['tipo'] !== 'deduccion') continue; $totalDed += $c['valor']; ?>
        <tr>
          <td><?= View::e($c['nombre_concepto']) ?></td>
          <td style="text-align:right"><?= View::money($c['valor']) ?></td>
          <td style="width:30px">
            <?php if ($nomina['estado']==='borrador' && Auth::puedeGestionar()): ?>
            <form method="POST" action="<?= APP_URL ?>/nomina/<?= $nomina['id'] ?>/detalle/<?= $detalle['id'] ?>/concepto/<?= $c['id'] ?>/eliminar" onsubmit="return confirm('¿Eliminar este concepto?')">
              <button type="submit" class="btn btn-outline btn-sm" style="padding:2px 8px">✕</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="colilla-total">
  <span class="colilla-label">NETO A PAGAR</span>
  <span class="colilla-valor"><?= View::money($detalle['neto_pagar']) ?></span>
</div>

<?php if ($nomina['estado'] === 'borrador' && Auth::puedeGestionar()): ?>
<div class="card" style="margin-top:20px">
  <div class="card-header"><div class="card-title">Agregar concepto manual</div></div>
  <form method="POST" action="<?= APP_URL ?>/nomina/<?= $nomina['id'] ?>/detalle/<?= $detalle['id'] ?>/actualizar" class="form-row">
    <label>Concepto
      <select name="concepto_id" required>
        <?php foreach ($catalogo as $c): ?>
          <option value="<?= $c['id'] ?>"><?= View::e($c['nombre']) ?> (<?= $c['tipo']==='devengado'?'Devengado':'Deducción' ?>)</option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Valor
      <input type="number" name="valor" min="1" step="1000" required placeholder="Ej: 150000">
    </label>
    <label style="align-self:end">
      <button type="submit" class="btn btn-primary" style="width:100%">Agregar a la colilla</button>
    </label>
  </form>
  <p class="text-muted" style="margin-top:8px;font-size:12px">Usa esto para horas extra, bonificaciones, comisiones o descuentos adicionales de este empleado en el periodo.</p>
</div>
<?php endif; ?>
