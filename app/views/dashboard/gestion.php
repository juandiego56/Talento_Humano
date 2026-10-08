<div class="page-header">
  <div>
    <div class="page-subtitle">Resumen general de los 3 módulos del sistema</div>
  </div>
</div>

<?php $totalPendientes = $solicitudesPendientes + $verificacionPendiente + $nominasBorrador; ?>
<div class="card" style="border-left:4px solid var(--warning, #f59e0b)">
  <div class="card-header"><div class="card-title">Pendientes de tu atención</div></div>
  <?php if ($totalPendientes === 0): ?>
    <p class="text-muted">No hay nada pendiente de revisión en este momento.</p>
  <?php else: ?>
  <div class="dato-grid">
    <div class="dato-item">
      <div class="dato-label">Solicitudes de vinculación</div>
      <div class="dato-valor" style="font-size:24px"><?= $solicitudesPendientes ?></div>
      <?php if ($solicitudesPendientes > 0): ?>
        <a href="<?= APP_URL ?>/solicitudes-vinculacion?estado=pendiente" class="btn btn-outline btn-sm" style="margin-top:8px">Revisar</a>
      <?php endif; ?>
    </div>
    <div class="dato-item">
      <div class="dato-label">Documentos por verificar</div>
      <div class="dato-valor" style="font-size:24px"><?= $verificacionPendiente ?></div>
      <?php if ($verificacionPendiente > 0): ?>
        <a href="<?= APP_URL ?>/verificacion" class="btn btn-outline btn-sm" style="margin-top:8px">Revisar</a>
      <?php endif; ?>
    </div>
    <div class="dato-item">
      <div class="dato-label">Nóminas en borrador</div>
      <div class="dato-valor" style="font-size:24px"><?= $nominasBorrador ?></div>
      <?php if ($nominasBorrador > 0): ?>
        <a href="<?= APP_URL ?>/nomina" class="btn btn-outline btn-sm" style="margin-top:8px">Revisar</a>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php
// ── Gráficas del tablero (SVG generado en el servidor: sin librerías ni internet) ──
$cNavy = '#0a2540'; $cMain = '#2dd4bf'; $cSoft = '#2dd4bf'; $cBlue = '#2563eb'; $cGrey = '#cbd5e1';

/** Dona: $segs = [[etiqueta, valor, color], ...]. Con total 0 se dibuja solo el aro vacío. */
$donut = function (array $segs, string $centro, string $etq, string $aria): string {
    $r = 38; $c = 2 * M_PI * $r;
    $total = array_sum(array_column($segs, 1));
    $svg = '<svg viewBox="0 0 100 100" class="chart-donut" role="img" aria-label="' . View::e($aria) . '">'
         . '<circle cx="50" cy="50" r="' . $r . '" fill="none" stroke="#e2e8f0" stroke-width="12"/>';
    if ($total > 0) {
        $off = 0;
        foreach ($segs as [$l, $v, $col]) {
            if ($v <= 0) continue;
            $len = $c * $v / $total;
            $svg .= '<circle cx="50" cy="50" r="' . $r . '" fill="none" stroke="' . $col . '" stroke-width="12"'
                  . ' stroke-dasharray="' . round($len, 2) . ' ' . round($c - $len, 2) . '"'
                  . ' stroke-dashoffset="' . round(-$off, 2) . '" transform="rotate(-90 50 50)"/>';
            $off += $len;
        }
    }
    return $svg . '<text x="50" y="51" class="donut-num">' . View::e($centro) . '</text>'
                . '<text x="50" y="64" class="donut-lbl">' . View::e($etq) . '</text></svg>';
};

$pctChecklist = max(0, min(100, (float)$checklistProm));

$segPersonal = [
    ['Activos',   (int)$empStats['activos'],   $cMain],
    ['Inactivos', (int)$empStats['inactivos'], $cBlue],
];

$colEstadoAct = ['programada' => $cMain, 'en_curso' => $cBlue, 'finalizada' => $cNavy, 'cancelada' => $cGrey];
$segBienestar = []; $totalBienestar = 0;
foreach ($bienestarPorEstado as $b) {
    $segBienestar[] = [View::estadoActividadLabel($b['estado']), (int)$b['total'], $colEstadoAct[$b['estado']] ?? $cGrey];
    $totalBienestar += (int)$b['total'];
}

$mesesCorto = [1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic'];
$moneyCorto = fn($v) => $v >= 1000000 ? number_format($v / 1000000, 1, ',', '.') . ' M' : number_format($v / 1000, 0, ',', '.') . ' k';
$maxNomina = $nominaSerie ? max(array_map(fn($n) => (float)$n['total_neto'], $nominaSerie)) : 0;
?>
<div class="chart-grid">

  <div class="chart-card">
    <div class="chart-title">Personal por estado</div>
    <div class="chart-body"><?= $donut($segPersonal, (string)(int)$empStats['activos'], 'activos', 'Empleados por estado') ?></div>
    <ul class="chart-legend">
      <?php foreach ($segPersonal as [$l, $v, $col]): ?>
        <li><span class="sw" style="background:<?= $col ?>"></span><?= $l ?><b><?= $v ?></b></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="chart-card">
    <div class="chart-title">Lista de chequeo</div>
    <div class="chart-body"><?= $donut([['Avance', $pctChecklist, $cBlue], ['Pendiente', 100 - $pctChecklist, '#e2e8f0']], $checklistProm . '%', 'promedio', 'Avance promedio de la lista de chequeo') ?></div>
    <ul class="chart-legend">
      <li><span class="sw" style="background:<?= $cBlue ?>"></span>Documentos completados<b><?= $checklistProm ?>%</b></li>
      <li><span class="sw" style="background:#e2e8f0"></span>Pendientes<b><?= round(100 - $pctChecklist, 1) ?>%</b></li>
    </ul>
  </div>

  <div class="chart-card">
    <div class="chart-title">Nómina neta por periodo</div>
    <div class="chart-body chart-body-bars">
      <?php if (!$nominaSerie): ?>
        <p class="chart-empty">Sin nóminas generadas</p>
      <?php else: $n = count($nominaSerie); $slot = 40; $x0 = (240 - $n * $slot) / 2; ?>
        <svg viewBox="0 0 240 130" class="chart-bars" role="img" aria-label="Nómina neta de los últimos periodos">
          <line x1="0" y1="100" x2="240" y2="100" stroke="#e2e8f0"/>
          <?php foreach ($nominaSerie as $i => $nm):
              $v = (float)$nm['total_neto'];
              $h = $maxNomina > 0 ? max(2, 72 * $v / $maxNomina) : 2;
              $x = $x0 + $i * $slot + 8; $y = 100 - $h;
              $ultima = ($i === $n - 1);
              [$yy, $mm] = array_pad(explode('-', $nm['periodo']), 2, '');
          ?>
            <rect x="<?= $x ?>" y="<?= round($y, 1) ?>" width="24" height="<?= round($h, 1) ?>" rx="3" fill="<?= $ultima ? $cBlue : $cSoft ?>"/>
            <text x="<?= $x + 12 ?>" y="<?= round($y - 4, 1) ?>" class="bar-val"><?= $moneyCorto($v) ?></text>
            <text x="<?= $x + 12 ?>" y="114" class="bar-lbl"><?= $mesesCorto[(int)$mm] ?? $mm ?></text>
          <?php endforeach; ?>
        </svg>
      <?php endif; ?>
    </div>
    <ul class="chart-legend">
      <?php if ($ultimaNomina): ?>
        <li>Última (<?= View::periodoLabel($ultimaNomina['periodo']) ?>)<b><?= View::money($ultimaNomina['total_neto']) ?></b></li>
      <?php else: ?>
        <li>Aún no hay nóminas para graficar</li>
      <?php endif; ?>
    </ul>
  </div>

  <div class="chart-card">
    <div class="chart-title">Actividades de bienestar</div>
    <div class="chart-body"><?= $donut($segBienestar, (string)$totalBienestar, 'actividades', 'Actividades de bienestar por estado') ?></div>
    <ul class="chart-legend">
      <?php if (!$segBienestar): ?><li>Aún no hay actividades</li><?php endif; ?>
      <?php foreach ($segBienestar as [$l, $v, $col]): ?>
        <li><span class="sw" style="background:<?= $col ?>"></span><?= $l ?><b><?= $v ?></b></li>
      <?php endforeach; ?>
    </ul>
  </div>

</div>

<div class="form-row" style="align-items:start">

  <div class="card" style="grid-column: span 2">
    <div class="card-header"><div class="card-title">Empleados recientes</div>
      <a href="<?= APP_URL ?>/empleados" class="btn btn-outline btn-sm">Ver todos</a>
    </div>
    <table>
      <thead><tr><th>Nombre</th><th>Cargo</th><th>Área</th><th>Ingreso</th><th>Estado</th></tr></thead>
      <tbody>
        <?php if (!$empleadosRecientes): ?>
          <tr><td colspan="5" class="text-muted">Aún no hay empleados registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($empleadosRecientes as $e): ?>
        <tr>
          <td><a href="<?= APP_URL ?>/empleados/<?= $e['id'] ?>"><?= View::e($e['nombres'].' '.$e['apellidos']) ?></a></td>
          <td><?= View::e($e['cargo'] ?? '—') ?></td>
          <td><?= View::e($e['area'] ?? '—') ?></td>
          <td><?= View::fecha($e['fecha_ingreso']) ?></td>
          <td><span class="badge <?= View::estadoEmpleadoBadge($e['estado']) ?>"><?= View::estadoEmpleadoLabel($e['estado']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Empleados por área</div></div>
    <?php if (!$porArea): ?>
      <p class="text-muted">Sin datos.</p>
    <?php endif; ?>
    <?php foreach ($porArea as $a): ?>
      <div style="margin-bottom:10px">
        <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-bottom:3px">
          <span><?= View::e($a['area']) ?></span><strong><?= (int)$a['total'] ?></strong>
        </div>
        <div class="progress-bar" style="width:100%">
          <div class="progress-fill" style="width:<?= min(100, (int)$a['total'] * 12) ?>%"></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<div class="card">
  <div class="card-header"><div class="card-title">Próximas actividades de bienestar</div>
    <a href="<?= APP_URL ?>/bienestar" class="btn btn-outline btn-sm">Ver todas</a>
  </div>
  <table>
    <thead><tr><th>Actividad</th><th>Tipo</th><th>Fecha</th><th>Lugar</th><th>Estado</th></tr></thead>
    <tbody>
      <?php if (!$proximasActividades): ?>
        <tr><td colspan="5" class="text-muted">No hay actividades próximas programadas.</td></tr>
      <?php endif; ?>
      <?php foreach ($proximasActividades as $a): ?>
      <tr>
        <td><a href="<?= APP_URL ?>/bienestar/<?= $a['id'] ?>"><?= View::e($a['nombre']) ?></a></td>
        <td><span class="badge badge-tipo-<?= $a['tipo'] ?>"><?= View::tipoActividadLabel($a['tipo']) ?></span></td>
        <td><?= View::fecha($a['fecha_inicio']) ?></td>
        <td><?= View::e($a['lugar'] ?? '—') ?></td>
        <td><span class="badge <?= View::estadoActividadBadge($a['estado']) ?>"><?= View::estadoActividadLabel($a['estado']) ?></span></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
