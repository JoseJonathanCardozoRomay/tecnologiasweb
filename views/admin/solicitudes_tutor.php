<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/flash.php';
require_once __DIR__ . '/../../models/SolicitudActualizacionModel.php';

$tituloPagina = 'Solicitudes de Actualización de Tutores - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$migas = [
    ['texto' => 'Tutores', 'url' => '/controllers/tutores_listar.php'],
    ['texto' => 'Solicitudes de actualización', 'actual' => true],
];
$titulo = 'Solicitudes de actualización de tutores';
$descripcion = 'Revisa y resuelve los requerimientos de materias, carreras u horarios enviados por los tutores.';
$icono = 'bi-send-check';
$accion = '';
include __DIR__ . '/../partials/page_header.php';
?>

<?php if ($mensaje = flash_get('success')): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
    <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($mensaje) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>
<?php if ($mensaje = flash_get('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
    <i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($mensaje) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-xl-8">
    <div class="card card-custom shadow-sm">
      <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="fw-bold text-dark mb-0">
          <i class="bi bi-inbox me-1 text-primary"></i>Requerimientos
          <?php if ($pendientes > 0): ?>
            <span class="badge bg-warning text-dark ms-2"><?= (int) $pendientes ?> pendiente<?= $pendientes === 1 ? '' : 's' ?></span>
          <?php endif; ?>
        </h5>
        <div class="btn-group btn-group-sm" role="group" aria-label="Filtrar por estado">
          <?php
          $filtros = ['' => 'Todas', 'pendiente' => 'Pendientes', 'aprobada' => 'Aprobadas', 'rechazada' => 'Rechazadas'];
          foreach ($filtros as $valor => $texto):
            $url = $valor === '' ? '/controllers/solicitudes_tutor_listar.php' : '/controllers/solicitudes_tutor_listar.php?estado=' . $valor;
          ?>
            <a class="btn btn-outline-secondary<?= ($estado ?? '') === $valor ? ' active' : '' ?>" href="<?= $url ?>"><?= $texto ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4">#</th>
              <th>Tutor</th>
              <th>Tipo</th>
              <th>Detalle</th>
              <th>Enviada</th>
              <th>Estado</th>
              <th class="pe-4 text-end">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($solicitudes)): ?>
              <tr>
                <td colspan="7" class="text-center py-5">
                  <?php
                  $emptyTitulo = 'No hay solicitudes que mostrar';
                  $emptyTexto = 'Cuando un tutor solicite actualizar materias, carreras u horarios aparecerá aquí.';
                  include __DIR__ . '/../partials/empty_state.php';
                  ?>
                </td>
              </tr>
            <?php endif; ?>
            <?php foreach ($solicitudes as $s): ?>
              <?php $seleccionada = $solicitudSeleccionada && (int) $solicitudSeleccionada['id_solicitud'] === (int) $s['id_solicitud']; ?>
              <tr class="<?= $seleccionada ? 'table-active' : '' ?>">
                <td class="ps-4 fw-bold text-dark">#<?= (int) $s['id_solicitud'] ?></td>
                <td>
                  <div class="fw-semibold text-dark"><?= htmlspecialchars(trim($s['tutor_nombre'] . ' ' . $s['tutor_apellido'])) ?></div>
                  <small class="text-muted"><?= htmlspecialchars($s['nombre_carrera'] ?? 'Sin carrera asignada') ?></small>
                </td>
                <td><span class="badge border px-3 py-1 border-primary-subtle bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars(SolicitudActualizacionModel::etiquetaTipo($s['tipo'])) ?></span></td>
                <td class="text-muted small text-truncate" style="max-width: 240px;" title="<?= htmlspecialchars($s['detalle']) ?>"><?= htmlspecialchars($s['detalle']) ?></td>
                <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($s['fecha_solicitud'])) ?></td>
                <td><span class="<?= SolicitudActualizacionModel::claseEstado($s['estado']) ?>"><?= htmlspecialchars(ucfirst($s['estado'])) ?></span></td>
                <td class="pe-4 text-end">
                  <a href="/controllers/solicitudes_tutor_listar.php?id=<?= (int) $s['id_solicitud'] ?><?= $estado ? '&estado=' . urlencode($estado) : '' ?>"
                     class="btn btn-sm btn-outline-primary rounded-3">
                    <?= $s['estado'] === 'pendiente' ? 'Resolver' : 'Ver' ?>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-xl-4">
    <?php if (!$solicitudSeleccionada): ?>
      <div class="card card-custom shadow-sm">
        <div class="card-body text-center py-5">
          <i class="bi bi-clipboard-check text-muted" style="font-size: 2.4rem;" aria-hidden="true"></i>
          <h6 class="fw-bold text-dark mt-3 mb-1">Selecciona una solicitud</h6>
          <p class="text-muted small mb-0">Elige un requerimiento de la lista para aprobarlo o rechazarlo con su motivo.</p>
        </div>
      </div>
    <?php else: ?>
      <?php $s = $solicitudSeleccionada; ?>
      <div class="card card-custom shadow-sm">
        <div class="card-header bg-white py-3">
          <h5 class="fw-bold text-dark mb-0">Solicitud #<?= (int) $s['id_solicitud'] ?></h5>
        </div>
        <div class="card-body p-4">
          <dl class="row mb-0 small">
            <dt class="col-5 text-muted fw-semibold">Tutor</dt>
            <dd class="col-7 text-dark"><?= htmlspecialchars(trim($s['tutor_nombre'] . ' ' . $s['tutor_apellido'])) ?></dd>

            <dt class="col-5 text-muted fw-semibold">Tipo</dt>
            <dd class="col-7 text-dark"><?= htmlspecialchars(SolicitudActualizacionModel::etiquetaTipo($s['tipo'])) ?></dd>

            <dt class="col-5 text-muted fw-semibold">Enviada</dt>
            <dd class="col-7 text-dark"><?= date('d/m/Y H:i', strtotime($s['fecha_solicitud'])) ?></dd>

            <dt class="col-5 text-muted fw-semibold">Estado</dt>
            <dd class="col-7"><span class="<?= SolicitudActualizacionModel::claseEstado($s['estado']) ?>"><?= htmlspecialchars(ucfirst($s['estado'])) ?></span></dd>
          </dl>

          <hr>

          <p class="text-muted small text-uppercase fw-semibold mb-1">Detalle del requerimiento</p>
          <p class="text-dark small" style="white-space: pre-line;"><?= htmlspecialchars($s['detalle']) ?></p>

          <?php if ($s['estado'] === 'pendiente'): ?>
            <hr>
            <form method="POST" action="/controllers/solicitudes_tutor_responder.php" id="formResponderSolicitud">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="id_solicitud" value="<?= (int) $s['id_solicitud'] ?>">

              <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small text-uppercase" for="respuestaSolicitud">Motivo (obligatorio si rechazas)</label>
                <textarea name="respuesta" id="respuestaSolicitud" class="form-control rounded-3 py-2" rows="3" maxlength="600"></textarea>
              </div>

              <div class="d-flex gap-2">
                <button type="submit" name="estado" value="aprobada" class="btn btn-success rounded-3 flex-grow-1">
                  <i class="bi bi-check-lg me-1"></i>Aprobar
                </button>
                <button type="submit" name="estado" value="rechazada" class="btn btn-outline-danger rounded-3 flex-grow-1">
                  <i class="bi bi-x-lg me-1"></i>Rechazar
                </button>
              </div>
            </form>
            <p class="text-muted small mt-3 mb-0">
              Al aprobar, el tutor recibe la notificación. Aplica el cambio real en
              <a href="/controllers/tutores_disponibilidad.php?id=<?= (int) $s['id_tutor'] ?>">Horarios y materias del tutor</a>.
            </p>
          <?php else: ?>
            <hr>
            <p class="text-muted small text-uppercase fw-semibold mb-1">Respuesta del administrador</p>
            <p class="text-dark small mb-2" style="white-space: pre-line;"><?= !empty($s['respuesta']) ? htmlspecialchars($s['respuesta']) : 'Sin comentarios.' ?></p>
            <?php if (!empty($s['fecha_respuesta'])): ?>
              <p class="text-muted small mb-0">
                Atendida el <?= date('d/m/Y H:i', strtotime($s['fecha_respuesta'])) ?>
                <?php if (!empty($s['responde_nombre'])): ?>
                  por <?= htmlspecialchars(trim($s['responde_nombre'] . ' ' . $s['responde_apellido'])) ?>
                <?php endif; ?>
              </p>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
document.getElementById('formResponderSolicitud')?.addEventListener('submit', function (event) {
  const estado = event.submitter ? event.submitter.value : '';
  const detalle = document.getElementById('respuestaSolicitud');
  if (estado === 'rechazada' && (!detalle.value || !detalle.value.trim())) {
    event.preventDefault();
    Swal.fire({
      icon: 'warning',
      title: 'Indica el motivo',
      text: 'El rechazo debe explicar al tutor el motivo para que pueda corregir su solicitud.',
      confirmButtonColor: '#dc3545'
    });
  }
});
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
