<div class="page-header">
  <div>
    <div class="page-title">Nómina — <?= View::periodoLabel($nomina['periodo']) ?></div>
    <div class="page-subtitle">
      Generada el <?= View::fecha($nomina['fecha_generacion']) ?>
      <?php if (!empty($nomina['corte_academico'])): ?>
        · Corte académico: <strong><?= View::corteAcademicoLabel($nomina['corte_academico']) ?></strong>
      <?php endif; ?>
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
    <span class="stat-icon" style="color:var(--success)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <circle cx="12" cy="12" r="9"/>
        <path stroke-linecap="round" d="M12 8v8M8 12h8"/>
      </svg>
    </span>
    <div class="stat-num" style="font-size:18px"><?= View::money($nomina['total_devengado']) ?></div>
    <div class="stat-label">Total devengado</div>
  </div>
  <div class="stat-card stat-alerta">
    <span class="stat-icon" style="color:var(--warning)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <circle cx="12" cy="12" r="9"/>
        <path stroke-linecap="round" d="M8 12h8"/>
      </svg>
    </span>
    <div class="stat-num" style="font-size:18px"><?= View::money($nomina['total_deducciones']) ?></div>
    <div class="stat-label">Total deducciones</div>
  </div>
  <div class="stat-card stat-checklist">
    <span class="stat-icon" style="color:var(--info)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </span>
    <div class="stat-num" style="font-size:18px"><?= View::money($nomina['total_neto']) ?></div>
    <div class="stat-label">Neto a pagar</div>
  </div>
  <div class="stat-card stat-personal">
    <span class="stat-icon" style="color:var(--blue)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
      </svg>
    </span>
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
