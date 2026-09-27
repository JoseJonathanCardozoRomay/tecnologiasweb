<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Alertas de Cohorte - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<?php
$titulo = 'Panel de Alertas Calculadas';
$descripcion = 'Alertas A1-A9 calculadas por demanda para el Coordinador (HU-033/038).';
$icono = 'bi-exclamation-triangle';
$contador = count($alertas);
$accion = '<button class="btn btn-outline-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3" onclick="location.reload()"><i class="bi bi-arrow-clockwise"></i><span class="fw-semibold">Recalcular</span></button>';
include __DIR__ . '/../../partials/page_header.php';
?>

<?php if ($alertas): ?>
  <div class="d-flex flex-column gap-3">
    <?php foreach ($alertas as $i => $a): ?>
      <div class="card card-custom shadow-sm">
        <div class="card-body py-3 d-flex align-items-start gap-3">
          <span class="badge bg-dark text-white fs-6 px-2 py-1 rounded-3" style="min-width: 44px; text-align: center;"><?= htmlspecialchars($a['codigo']) ?></span>
          <div class="flex-grow-1">
            <div class="d-flex flex-wrap align-items-center gap-2">
              <strong class="text-dark"><?= htmlspecialchars($a['titulo']) ?></strong>
              <?= mgAlertasNivelBadge($a['nivel']) ?>
            </div>
            <div class="small text-muted mt-1"><?= htmlspecialchars($a['mensaje']) ?></div>
            <?php if ($a['referencias']): ?>
              <div class="d-flex flex-wrap gap-1 mt-2">
                <?php foreach (array_slice($a['referencias'], 0, 4) as $r): ?>
                  <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle fw-normal">
                    <?= htmlspecialchars(($a['codigo'] === 'A7' ? $r['registro_universitario'] . ' · ' : '') . (($r['apellido'] ?? '') . ' ' . ($r['nombre'] ?? ''))) ?>
                  </span>
                <?php endforeach; ?>
                <?php if (count($a['referencias']) > 4): ?>
                  <span class="badge bg-light text-secondary border fw-normal">+<?= count($a['referencias']) - 4 ?></span>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
          <button type="button" class="btn btn-outline-success btn-sm flex-shrink-0 rounded-3"
                  data-bs-toggle="modal" data-bs-target="#modalAtender"
                  data-codigo="<?= htmlspecialchars($a['codigo']) ?>"
                  data-titulo="<?= htmlspecialchars($a['titulo']) ?>"
                  data-mensaje="<?= htmlspecialchars($a['mensaje']) ?>"
                  data-referencia="<?= (int) ($a['referencias'][0]['id_expediente_mg'] ?? $a['referencias'][0]['id_estudiante'] ?? $a['referencias'][0]['id_tutor'] ?? $a['referencias'][0]['id_defensa_mg'] ?? 0) ?>">
            <i class="bi bi-check2-circle me-1"></i> Marcar atendida
          </button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <div class="card card-custom shadow-sm">
    <div class="card-body text-center py-5">
      <i class="bi bi-emoji-smile fs-1 d-block mb-2 text-success"></i>
      <p class="mb-0 text-muted">No hay alertas vigentes. Todo se encuentra en orden.</p>
    </div>
  </div>
<?php endif; ?>

<?php if ($historial): ?>
  <div class="card card-custom shadow-sm mt-4">
    <div class="card-header bg-white border-0 py-3">
      <h2 class="h6 mb-0 fw-bold">Alerta atendidas (historial)</h2>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem;">
          <tr>
            <th class="ps-4">Código</th>
            <th>Título</th>
            <th>Atendida por</th>
            <th>Nota</th>
            <th class="pe-4">Fecha</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($historial as $h): ?>
            <tr>
              <td class="ps-4"><span class="badge bg-dark text-white"><?= htmlspecialchars($h['codigo']) ?></span></td>
              <td class="small"><?= htmlspecialchars($h['titulo']) ?></td>
              <td class="small"><?= htmlspecialchars(($h['atendido_por_nombre'] ?? '') . ' ' . ($h['atendido_por_apellido'] ?? '')) ?></td>
              <td class="small text-muted"><?= htmlspecialchars($h['nota']) ?></td>
              <td class="pe-4 small text-muted"><?= htmlspecialchars((string) $h['fecha_atencion']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

<div class="modal fade" id="modalAtender" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="/controllers/mg_alertas_atender.php" class="modal-content">
      <?= csrf_campo(); ?>
      <input type="hidden" name="codigo" id="at-codigo">
      <input type="hidden" name="titulo" id="at-titulo">
      <input type="hidden" name="mensaje" id="at-mensaje">
      <input type="hidden" name="id_referencia" id="at-referencia">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAtenderLabel">Marcar alerta como atendida</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p class="small text-muted mb-3" id="at-resumen"></p>
        <label class="form-label fw-semibold text-secondary small text-uppercase mb-1" for="at-nota">Nota de atención</label>
        <textarea id="at-nota" name="nota" rows="3" class="form-control rounded-3" maxlength="255" required
                  placeholder="Describe la acción tomada para esta alerta..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success rounded-3"><i class="bi bi-check2-circle me-1"></i> Confirmar atención</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var modal = document.getElementById('modalAtender');
  modal.addEventListener('show.bs.modal', function (event) {
    var btn = event.relatedTarget;
    document.getElementById('at-codigo').value = btn.dataset.codigo || '';
    document.getElementById('at-titulo').value = btn.dataset.titulo || '';
    document.getElementById('at-mensaje').value = btn.dataset.mensaje || '';
    document.getElementById('at-referencia').value = btn.dataset.referencia || '0';
    document.getElementById('at-resumen').textContent =
      (btn.dataset.codigo || '') + ' — ' + (btn.dataset.titulo || '');
  });
});
</script>