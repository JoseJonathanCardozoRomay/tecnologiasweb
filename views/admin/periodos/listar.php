<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Periodos Académicos - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
 <div>
    <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
      <i class="bi bi-calendar-range text-primary"></i>
      <span>Periodos Académicos</span>
    </h2>
    <p class="text-muted mb-0">Define el rango de fechas dentro del cual los estudiantes podrán agendar tutorías.</p>
 </div>
 <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#modalPeriodo" onclick="prepararCrear()">
    <i class="bi bi-plus-circle-fill"></i>
    <span class="fw-semibold">Nuevo Periodo</span>
 </button>
</div>

<?php if (!empty($errores)): ?>
 <div class="alert alert-danger py-2 px-3 rounded-3 shadow-sm mb-4">
    <ul class="mb-0 ps-3 small">
      <?php foreach ($errores as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
 </div>
<?php endif; ?>

<div class="card card-custom shadow-sm overflow-hidden">
 <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <th class="ps-4">Código</th>
          <th>Nombre</th>
          <th>Fecha Inicio</th>
          <th>Fecha Fin</th>
          <th>Estado</th>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($periodos as $p): ?>
          <?php
          $fechaActual = date('Y-m-d');
          $periodoCulminado = $p['fecha_fin'] < $fechaActual;
          $periodoVigente = $p['fecha_inicio'] <= $fechaActual && $p['fecha_fin'] >= $fechaActual;
          ?>
          <tr>
            <td class="ps-4"><span class="badge badge-accent px-2 py-1"><?= htmlspecialchars($p['codigo']) ?></span></td>
            <td class="fw-semibold text-dark"><?= htmlspecialchars($p['nombre']) ?></td>
            <td><i class="bi bi-calendar-event text-success me-1"></i><?= date('d/m/Y', strtotime($p['fecha_inicio'])) ?></td>
            <td><i class="bi bi-calendar-x text-danger me-1"></i><?= date('d/m/Y', strtotime($p['fecha_fin'])) ?></td>
            <td>
              <?php if ($periodoCulminado): ?>
                <span class="badge bg-dark bg-opacity-10 text-dark border-dark-subtle px-2 py-1"><i class="bi bi-check-circle me-1"></i>Culminado</span>
              <?php elseif ((int) $p['activo'] === 1): ?>
                <span class="badge bg-success bg-opacity-10 text-success border-success-subtle px-2 py-1"><i class="bi bi-check2-circle me-1"></i>Activo</span>
              <?php else: ?>
                <span class="badge bg-secondary bg-opacity-10 text-secondary border-secondary-subtle px-2 py-1"><i class="bi bi-pause-circle me-1"></i>Inactivo</span>
              <?php endif; ?>
            </td>
            <td class="text-end pe-4">
              <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary btn-sm" title="Editar"
                        onclick='prepararEditar(<?= json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                  <i class="bi bi-pencil-fill"></i>
                </button>
                <?php if (!$periodoCulminado): ?>
                  <form method="POST" class="d-inline">
                    <?php require_once __DIR__ . '/../../../includes/csrf.php'; echo csrf_campo(); ?>
                    <input type="hidden" name="accion" value="<?= (int) $p['activo'] === 1 ? 'desactivar' : 'activar' ?>">
                    <input type="hidden" name="id_periodo" value="<?= $p['id_periodo'] ?>">
                    <button type="submit" class="btn btn-outline-<?= (int) $p['activo'] === 1 ? 'warning' : 'success' ?> btn-sm rounded-end-2"
                            title="<?= (int) $p['activo'] === 1 ? 'Desactivar' : 'Activar' ?>">
                      <i class="bi <?= (int) $p['activo'] === 1 ? 'bi-pause-fill' : 'bi-play-fill' ?>"></i>
                    </button>
                  </form>
                <?php else: ?>
                  <button type="button" class="btn btn-outline-secondary btn-sm rounded-end-2" disabled title="Periodo culminado - no se puede modificar">
                    <i class="bi bi-lock-fill"></i>
                  </button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($periodos)): ?>
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">
              <i class="bi bi-calendar-range fs-1 d-block mb-2 text-secondary"></i>
              No hay periodos registrados. Crea el primero para habilitar la solicitud de tutorías.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
 </div>
</div>

<!-- Modal de creación/edición -->
<div class="modal fade" id="modalPeriodo" tabindex="-1">
 <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <form method="POST" autocomplete="off">
        <?php require_once __DIR__ . '/../../../includes/csrf.php'; echo csrf_campo(); ?>
        <input type="hidden" name="accion" id="accionPeriodo" value="crear">
        <input type="hidden" name="id_periodo" id="idPeriodo" value="">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold" id="tituloModalPeriodo">Nuevo Periodo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold small text-uppercase text-secondary">Código *</label>
            <input type="text" name="codigo" id="codigoPeriodo" class="form-control" maxlength="20" placeholder="Ej: I-2026" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold small text-uppercase text-secondary">Nombre *</label>
            <input type="text" name="nombre" id="nombrePeriodo" class="form-control" maxlength="100" placeholder="Ej: 2026-1 Primer Semestre" required>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold small text-uppercase text-secondary">Fecha Inicio *</label>
              <input type="date" name="fecha_inicio" id="fechaInicioPeriodo" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small text-uppercase text-secondary">Fecha Fin *</label>
              <input type="date" name="fecha_fin" id="fechaFinPeriodo" class="form-control" required>
            </div>
          </div>
          <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" name="activo" id="activoPeriodo" value="1" checked>
            <label class="form-check-label" for="activoPeriodo">Periodo activo (los estudiantes podrán agendar dentro de este rango)</label>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
 </div>
</div>

<script>
function prepararCrear() {
  document.getElementById('accionPeriodo').value = 'crear';
  document.getElementById('idPeriodo').value = '';
  document.getElementById('codigoPeriodo').value = '';
  document.getElementById('nombrePeriodo').value = '';
  document.getElementById('fechaInicioPeriodo').value = '';
  document.getElementById('fechaFinPeriodo').value = '';
  document.getElementById('activoPeriodo').checked = true;
  document.getElementById('tituloModalPeriodo').textContent = 'Nuevo Periodo';
}
function prepararEditar(p) {
  document.getElementById('accionPeriodo').value = 'editar';
  document.getElementById('idPeriodo').value = p.id_periodo;
  document.getElementById('codigoPeriodo').value = p.codigo;
  document.getElementById('nombrePeriodo').value = p.nombre;
  document.getElementById('fechaInicioPeriodo').value = p.fecha_inicio;
  document.getElementById('fechaFinPeriodo').value = p.fecha_fin;
  document.getElementById('activoPeriodo').checked = parseInt(p.activo, 10) === 1;
  document.getElementById('tituloModalPeriodo').textContent = 'Editar Periodo';
  new bootstrap.Modal(document.getElementById('modalPeriodo')).show();
}
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
