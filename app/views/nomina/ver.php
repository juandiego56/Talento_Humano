<div class="page-header">
  <div>
    <div class="page-title">Nómina — <?= View::periodoLabel($nomina['periodo']) ?></div>
    <div class="page-subtitle">
      Generada el <?= View::fecha($nomina['fecha_generacion']) ?>
      <span class="badge <?= View::estadoNominaBadge($nomina['estado']) ?>" style="margin-left:8px"><?= View::estadoNominaLabel($nomina['estado']) ?></span>
    </div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/nomina" class="btn btn-outline">← Volver</a>
    <?php if ($nomina['estado'] === 'borrador' && Auth::puedeGestionar()): ?>
      <form method="POST" action="<?= APP_URL ?>/nomina/<?= $nomina['id'] ?>/pagar" onsubmit="return confirm('¿Marcar esta nómina como pagada?')">
        <button type="submit" class="btn btn-primary">Marcar como pagada</button>
      </form>
    <?php endif; ?>
    <?php if ($nomina['estado'] !== 'anulada' && Auth::esAdmin()): ?>
      <form method="POST" action="<?= APP_URL ?>/nomina/<?= $nomina['id'] ?>/anular" onsubmit="return confirm('¿Anular esta nómina?')">
        <button type="submit" class="btn btn-outline" style="color:var(--danger);border-color:#fecaca">Anular</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<div class="stats-grid">
  <div class="stat-card stat-nomina">
    <span class="stat-icon">➕</span>
    <div class="stat-num" style="font-size:18px"><?= View::money($nomina['total_devengado']) ?></div>
    <div class="stat-label">Total devengado</div>
  </div>
  <div class="stat-card stat-alerta">
    <span class="stat-icon">➖</span>
    <div class="stat-num" style="font-size:18px"><?= View::money($nomina['total_deducciones']) ?></div>
    <div class="stat-label">Total deducciones</div>
  </div>
  <div class="stat-card stat-checklist">
    <span class="stat-icon">💰</span>
    <div class="stat-num" style="font-size:18px"><?= View::money($nomina['total_neto']) ?></div>
    <div class="stat-label">Neto a pagar</div>
  </div>
  <div class="stat-card stat-personal">
    <span class="stat-icon">👥</span>
    <div class="stat-num"><?= count($detalle) ?></div>
    <div class="stat-label">Empleados incluidos</div>
  </div>
</div>

<div class="card">
  <table>
    <thead><tr><th>Empleado</th><th>Cargo</th><th>Días</th><th>Devengado</th><th>Deducciones</th><th>Neto</th><th></th></tr></thead>
    <tbody>
      <?php if (!$detalle): ?><tr><td colspan="7" class="text-muted">Sin empleados en esta nómina.</td></tr><?php endif; ?>
      <?php foreach ($detalle as $d): ?>
      <tr>
        <td><?= View::e($d['nombres'].' '.$d['apellidos']) ?><br><span style="font-size:11px;color:var(--muted)"><?= View::e($d['numero_documento']) ?></span></td>
        <td><?= View::e($d['cargo'] ?? '—') ?></td>
        <td><?= (int)$d['dias_trabajados'] ?></td>
        <td><?= View::money($d['total_devengado']) ?></td>
        <td><?= View::money($d['total_deducciones']) ?></td>
        <td><strong><?= View::money($d['neto_pagar']) ?></strong></td>
        <td><a href="<?= APP_URL ?>/nomina/<?= $nomina['id'] ?>/detalle/<?= $d['id'] ?>" class="btn btn-outline btn-sm">Colilla de pago</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
