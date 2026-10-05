<div class="page-header">
  <div>
    <div class="page-title"><?= View::e($solicitud['nombres_candidato'].' '.$solicitud['apellidos_candidato']) ?></div>
    <div class="page-subtitle">
      <span class="badge <?= View::estadoSolicitudBadge($solicitud['estado']) ?>"><?= View::estadoSolicitudLabel($solicitud['estado']) ?></span>
    </div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/solicitudes-vinculacion" class="btn btn-outline">← Volver</a>
  </div>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Datos de la solicitud</div></div>
  <div class="dato-grid">
    <div class="dato-item"><div class="dato-label">Programa académico</div><div class="dato-valor"><?= View::e($solicitud['programa_nombre']) ?></div></div>
    <div class="dato-item"><div class="dato-label">Solicitado por</div><div class="dato-valor"><?= View::e($solicitud['solicitante_nombre']) ?></div></div>
    <div class="dato-item"><div class="dato-label">Fecha de solicitud</div><div class="dato-valor"><?= View::fecha($solicitud['fecha_solicitud']) ?></div></div>
    <div class="dato-item"><div class="dato-label">Cargo sugerido</div><div class="dato-valor"><?= View::e($solicitud['cargo_sugerido'] ?: '—') ?></div></div>
    <div class="dato-item"><div class="dato-label">Nivel de formación requerido</div><div class="dato-valor"><?= View::nivelEducativoLabelOrVacio($solicitud['nivel_educativo_requerido']) ?: '—' ?></div></div>
    <div class="dato-item"><div class="dato-label">Dedicación docente</div><div class="dato-valor"><?= View::tipoVinculacionDocenteLabel($solicitud['tipo_vinculacion_docente']) ?></div></div>
    <div class="dato-item"><div class="dato-label">Fecha requerida</div><div class="dato-valor"><?= View::fecha($solicitud['fecha_requerida']) ?></div></div>
  </div>
  <div style="margin-top:14px">
    <div class="dato-label" style="margin-bottom:4px">Justificación</div>
    <p style="color:var(--txt2)"><?= nl2br(View::e($solicitud['justificacion'])) ?></p>
  </div>
</div>

<?php if ($solicitud['estado'] !== 'pendiente'): ?>
<div class="card">
  <div class="card-header"><div class="card-title">Respuesta de Talento Humano</div></div>
  <div class="dato-grid">
    <div class="dato-item"><div class="dato-label">Atendida por</div><div class="dato-valor"><?= View::e($solicitud['atendida_por_nombre'] ?? '—') ?></div></div>
    <div class="dato-item"><div class="dato-label">Fecha de respuesta</div><div class="dato-valor"><?= View::fecha($solicitud['fecha_respuesta']) ?></div></div>
  </div>
  <?php if ($solicitud['respuesta']): ?>
  <div style="margin-top:14px">
    <div class="dato-label" style="margin-bottom:4px">Comentario</div>
    <p style="color:var(--txt2)"><?= nl2br(View::e($solicitud['respuesta'])) ?></p>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($solicitud['estado'] === 'pendiente' && Auth::puedeGestionar()): ?>
<div class="card">
  <div class="card-header"><div class="card-title">Responder solicitud</div></div>
  <form method="POST" id="formRespuestaSolicitud">
    <label>Comentario (opcional)
      <textarea name="respuesta" rows="3" placeholder="Observaciones para el Director de Programa"></textarea>
    </label>
    <div class="page-actions" style="margin-top:10px">
      <button type="submit" formaction="<?= APP_URL ?>/solicitudes-vinculacion/<?= $solicitud['id'] ?>/aprobar" class="btn btn-primary">Aprobar</button>
      <button type="submit" formaction="<?= APP_URL ?>/solicitudes-vinculacion/<?= $solicitud['id'] ?>/rechazar" class="btn btn-outline" style="color:var(--danger);border-color:#fecaca">Rechazar</button>
    </div>
  </form>
</div>
<?php endif; ?>
