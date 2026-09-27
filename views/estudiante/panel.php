<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
require_once __DIR__ . '/../../includes/auth.php';
requerirRol(['estudiante']);

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/EstudianteModel.php';
require_once __DIR__ . '/../../models/TutoriaModel.php';
require_once __DIR__ . '/../../models/ConfiguracionModel.php';
require_once __DIR__ . '/../../includes/flash.php';

$estudianteModel = new EstudianteModel($pdo);
$estudiante = $estudianteModel->obtenerPorUsuario((int) ($_SESSION['id_usuario'] ?? 0));

$tutoriaModel = new TutoriaModel($pdo);
$configuracion = new ConfiguracionModel($pdo);

if (!$estudiante) {
    $metricas = [
        'total' => 0, 'pendiente' => 0, 'confirmada' => 0, 'realizada' => 0, 'cancelada' => 0,
    ];
    $tutorias = [];
    $accesoMG = false;
    $mgCompletada = false;
    $mgConcluida = null;
} else {
    $metricas = $tutoriaModel->obtenerMetricasPorEstudiante((int) $estudiante['id_estudiante']);
    $tutorias = $tutoriaModel->obtenerPorEstudiante((int) $estudiante['id_estudiante']);
    $accesoMG = $estudianteModel->puedeAccederMG((int) $estudiante['id_estudiante'], $configuracion);
    $mgCompletada = $tutoriaModel->mgCompletada((int) $estudiante['id_estudiante']);
    $mgConcluida = $tutoriaModel->mgConcluida((int) $estudiante['id_estudiante']);
}

$tituloPagina = 'Mis Tutorías - Estudiante - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$titulo = 'Bienvenido, ' . ($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? '');
$descripcion = 'Solicita tutorías con los docentes disponibles y sigue el estado de tus sesiones.';
$icono = 'bi-calendar2-check';
$accion = $mgCompletada
    ? ''
    : '<a href="/controllers/tutorias_solicitar.php" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2"><i class="bi bi-calendar-plus"></i>Solicitar tutoría</a>';
include __DIR__ . '/../partials/page_header.php';
?>

<?php if ($mgCompletada): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-3 border-success-subtle" role="alert">
    <div class="d-flex align-items-start gap-3">
      <i class="bi bi-patch-check-fill text-success fs-3" aria-hidden="true"></i>
      <div>
        <h5 class="alert-heading fw-bold mb-1">Modalidad de Grado Completada</h5>
        <p class="mb-1">Tu tutoría de grado finalizó con resultado <strong><?= htmlspecialchars($mgConcluida['estado_conclusion_nombre'] ?? 'Finalizada') ?></strong>. Ya no podés solicitar ni agregar nuevas tutorías.</p>
        <a href="/views/estudiante/historial.php" class="alert-link fw-semibold">Consultá tu historial académico completo <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<?php if (!$estudiante): ?>
  <div class="alert alert-warning alert-dismissible fade show rounded-3" role="alert">
    <strong>Tu perfil de estudiante está incompleto.</strong> Para solicitar tutorías debes estar registrado como estudiante. Contacta al administrador del sistema.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <?php
    $kpiClase = 'primary'; $kpiIcono = 'bi-calendar3'; $kpiValor = $metricas['total'] ?? 0; $kpiEtiqueta = 'Tutorías solicitadas';
    include __DIR__ . '/../partials/kpi_card.php';
    ?>
  </div>
  <div class="col-6 col-md-3">
    <?php
    $kpiClase = 'warning'; $kpiIcono = 'bi-clock'; $kpiValor = $metricas['pendiente'] ?? 0; $kpiEtiqueta = 'Pendientes';
    include __DIR__ . '/../partials/kpi_card.php';
    ?>
  </div>
  <div class="col-6 col-md-3">
    <?php
    $kpiClase = 'info'; $kpiIcono = 'bi-check2'; $kpiValor = $metricas['confirmada'] ?? 0; $kpiEtiqueta = 'Confirmadas';
    include __DIR__ . '/../partials/kpi_card.php';
    ?>
  </div>
  <div class="col-6 col-md-3">
    <?php
    $kpiClase = 'success'; $kpiIcono = 'bi-check-circle'; $kpiValor = $metricas['realizada'] ?? 0; $kpiEtiqueta = 'Realizadas';
    include __DIR__ . '/../partials/kpi_card.php';
    ?>
  </div>
</div>

<?php if ($accesoMG): ?>
  <div class="row g-3 mb-4">
    <div class="col-12">
      <div class="card card-custom shadow-sm border-primary-subtle">
        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
          <div class="d-flex align-items-center gap-3">
            <span class="avatar avatar-admin" style="width: 52px; height: 52px; font-size: 1.5rem;" aria-hidden="true"><i class="bi bi-mortarboard"></i></span>
            <div>
              <h5 class="fw-bold text-dark mb-1"><i class="bi bi-patch-check-fill text-success me-1"></i>Modalidad de Grado disponible</h5>
              <p class="text-muted mb-0 small">Cumples los requisitos académicos y tu pago fue validado. Puedes solicitar tu tutoría de grado.</p>
            </div>
          </div>
          <div class="d-flex flex-wrap gap-2 flex-shrink-0">
            <?php if (!$mgCompletada): ?>
              <a href="/controllers/tutorias_solicitar.php?tipo=grado" class="btn btn-primary rounded-3 px-3"><i class="bi bi-mortarboard me-1"></i>Solicitar Grado</a>
            <?php endif; ?>
            <a href="/controllers/mg_comprobantes_registrar.php" class="btn btn-outline-primary rounded-3 px-3"><i class="bi bi-file-earmark-check me-1"></i>Mis comprobantes</a>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php else: ?>
  <div class="row g-3 mb-4">
    <div class="col-12">
      <div class="card card-custom shadow-sm">
        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
          <div class="d-flex align-items-center gap-3">
            <span class="avatar avatar-default" style="width: 52px; height: 52px; font-size: 1.5rem; filter: blur(0.5px);" aria-hidden="true"><i class="bi bi-mortarboard"></i></span>
            <div>
              <h5 class="fw-bold text-dark mb-1"><i class="bi bi-lock text-secondary me-1"></i>Modalidad de Grado</h5>
              <p class="text-muted mb-0 small">Completa los requisitos académicos y valida tu comprobante de pago para habilitar tu tutoría de grado.</p>
            </div>
          </div>
          <a href="/controllers/mg_comprobantes_registrar.php" class="btn btn-outline-secondary rounded-3 px-3 flex-shrink-0"><i class="bi bi-clipboard-check me-1"></i>Ver requisitos</a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="card card-custom shadow-sm">
  <div class="card-header bg-white py-3">
    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-list-check me-1 text-primary"></i>Mis tutorías</h5>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <th class="ps-4">Fecha y Horario</th>
          <th>Materia</th>
          <th>Docente Tutor</th>
          <th>Modalidad</th>
          <th>Estado</th>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($tutorias)): ?>
          <tr>
            <td colspan="6" class="text-center py-5">
              <?php
              $emptyTitulo = 'Todavía no tienes tutorías solicitadas';
              $emptyTexto = $mgCompletada
                  ? 'Tu Modalidad de Grado finalizó correctamente. Podés consultar tu historial desde el menú.'
                  : 'Busca la materia que necesites reforzar, elige un docente y agenda tu primera sesión.';
              $emptyAccion = $mgCompletada
                  ? '<a href="/views/estudiante/historial.php" class="btn btn-outline-primary rounded-3"><i class="bi bi-clock-history me-1"></i>Ver historial</a>'
                  : '<a href="/controllers/tutorias_solicitar.php" class="btn btn-primary rounded-3"><i class="bi bi-calendar-plus me-1"></i>Solicitar mi primera tutoría</a>';
              include __DIR__ . '/../partials/empty_state.php';
              ?>
            </td>
          </tr>
        <?php endif; ?>
        <?php foreach ($tutorias as $t):
            $badgeEstado = 'badge-accent';
            if ($t['estado'] === 'confirmada') $badgeEstado = 'bg-info bg-opacity-10 text-info-emphasis border-info-subtle';
            if ($t['estado'] === 'realizada') $badgeEstado = 'bg-success bg-opacity-10 text-success border-success-subtle';
            if ($t['estado'] === 'cancelada') $badgeEstado = 'bg-danger bg-opacity-10 text-danger border-danger-subtle';
          ?>
          <tr>
            <td class="ps-4">
              <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($t['fecha'])) ?></div>
              <small class="text-muted"><i class="bi bi-clock me-1"></i><?= substr($t['hora_inicio'], -8, 5) ?> - <?= substr($t['hora_fin'], -8, 5) ?></small>
            </td>
            <td><span class="fw-semibold text-primary"><?= htmlspecialchars($t['nombre_materia']) ?></span></td>
            <td>
              <div class="fw-medium text-dark">Prof. <?= htmlspecialchars($t['tutor_nombre'] . ' ' . $t['tutor_apellido']) ?></div>
              <?php if (!empty($t['especialidad'])): ?><small class="text-muted"><?= htmlspecialchars($t['especialidad']) ?></small><?php endif; ?>
            </td>
            <td>
              <?php if ($t['modalidad'] === 'virtual'): ?>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1"><i class="bi bi-camera-video me-1"></i>Virtual</span>
              <?php else: ?>
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1"><i class="bi bi-geo-alt me-1"></i>Presencial</span>
              <?php endif; ?>
            </td>
            <td><span class="badge rounded-pill border px-3 py-1 <?= $badgeEstado ?>"><?= ucfirst($t['estado']) ?><?= (!empty($t['estado_conclusion_nombre']) && $t['estado'] === 'finalizada') ? ' — ' . htmlspecialchars($t['estado_conclusion_nombre']) : '' ?></span></td>
            <td class="text-end pe-4">
              <div class="btn-group" role="group">
                <a href="/views/estudiante/historial.php?detalle=<?= (int) $t['id_tutoria'] ?>" class="btn btn-outline-primary btn-sm rounded-2" title="Ver detalle e informes"><i class="bi bi-eye me-1"></i>Ver detalle</a>
                <?php if ($t['estado'] === 'pendiente' && !$mgCompletada): ?>
                  <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=cancelada" onclick="confirmarCancelacion(this.href); return false;" class="btn btn-outline-accent btn-sm" title="Cancelar solicitud"><i class="bi bi-slash-circle"></i></a>
                <?php else: ?>
                  <span class="text-muted small"><i class="bi bi-dot"></i></span>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>