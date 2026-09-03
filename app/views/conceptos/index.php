<div class="page-header">
  <div>
    <div class="page-title">Conceptos de Nómina</div>
    <div class="page-subtitle">Devengados y deducciones que se pueden aplicar en la nómina</div>
  </div>
</div>

<div class="form-row" style="align-items:start">
  <div class="card" style="grid-column: span 2">
    <table>
      <thead><tr><th>Concepto</th><th>Tipo</th><th>Cálculo</th><th>Automático</th><th></th></tr></thead>
      <tbody>
        <?php if (!$conceptos): ?><tr><td colspan="5" class="text-muted">Sin conceptos registrados.</td></tr><?php endif; ?>
        <?php foreach ($conceptos as $c): ?>
        <tr style="<?= $c['activo'] ? '' : 'opacity:.45' ?>">
          <td><?= View::e($c['nombre']) ?></td>
          <td><span class="badge <?= $c['tipo']==='devengado' ? 'badge-aprobado' : 'badge-en-revision' ?>"><?= $c['tipo']==='devengado'?'Devengado':'Deducción' ?></span></td>
          <td>
            <?php if ($c['es_porcentaje']): ?>
              <?= (float)$c['porcentaje'] ?>% del salario básico
            <?php elseif ($c['valor_fijo']): ?>
              <?= View::money($c['valor_fijo']) ?> fijo
            <?php else: ?>
              Valor manual por periodo
            <?php endif; ?>
          </td>
          <td><?= $c['automatico'] ? '<span class="badge badge-generado">Sí</span>' : '<span class="badge badge-borrador">No</span>' ?></td>
          <td>
            <?php if ($c['activo']): ?>
            <form method="POST" action="<?= APP_URL ?>/conceptos/<?= $c['id'] ?>/eliminar" onsubmit="return confirm('¿Desactivar este concepto?')">
              <button type="submit" class="btn btn-sm btn-outline">Desactivar</button>
            </form>
            <?php else: ?>
              <span class="badge badge-borrador">Inactivo</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Nuevo concepto</div></div>
    <form method="POST" action="<?= APP_URL ?>/conceptos/guardar" id="formConcepto">
      <label>Nombre<input type="text" name="nombre" required></label>
      <label>Tipo
        <select name="tipo" required>
          <option value="devengado">Devengado</option>
          <option value="deduccion">Deducción</option>
        </select>
      </label>
      <label class="check-group" style="display:flex;align-items:center;gap:6px">
        <input type="checkbox" name="es_porcentaje" id="chkPorcentaje" onchange="
          document.getElementById('wrapPorcentaje').style.display=this.checked?'block':'none';
          document.getElementById('wrapFijo').style.display=this.checked?'none':'block';
        ">
        ¿Es un porcentaje del salario básico?
      </label>
      <div id="wrapPorcentaje" style="display:none">
        <label>Porcentaje (%)<input type="number" name="porcentaje" step="0.01" min="0" max="100"></label>
      </div>
      <div id="wrapFijo">
        <label>Valor fijo (opcional)<input type="number" name="valor_fijo" step="1000" min="0"></label>
      </div>
      <label class="check-group" style="display:flex;align-items:center;gap:6px">
        <input type="checkbox" name="automatico"> Aplicar automáticamente al generar nómina
      </label>
      <button type="submit" class="btn btn-primary" style="margin-top:10px;width:100%">Crear concepto</button>
    </form>
  </div>
</div>
