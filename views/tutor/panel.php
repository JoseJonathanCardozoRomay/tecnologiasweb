<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
require_once __DIR__ . '/../../includes/auth.php';
requerirRol(['tutor']);

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/TutorModel.php';
require_once __DIR__ . '/../../models/TutoriaModel.php';
require_once __DIR__ . '/../../includes/flash.php';

$tutorModel = new TutorModel($pdo);
$tutor = $tutorModel->obtenerPorUsuario((int) ($_SESSION['id_usuario'] ?? 0));

if (!$tutor) {
    flash_set('error', 'Todavía no tienes un perfil de tutor. Contacta al administrador.');
    header('Location: /views/login/login.php');
    exit;
}

$tutoriaModel = new TutoriaModel($pdo);
$metricas = $tutoriaModel->obtenerMetricasPorTutor((int) $tutor['id_tutor']);
$tutorias = $tutoriaModel->obtenerPorTutor((int) $tutor['id_tutor']);
$estadosConclusion = $tutoriaModel->obtenerEstadosConclusion();
$materias = $tutor['materias'] ?? [];
$disponibilidad = $tutor['disponibilidad'] ?? [];

$tituloPagina = 'Mi Panel - Tutor - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$titulo = 'Bienvenido, Prof. ' . $tutor['nombre'] . ' ' . $tutor['apellido'];
$descripcion = 'Gestiona tus tutorías, materias y disponibilidad horaria desde este panel.';
$icono = 'bi-grid-1x2';
$accion = '<a href="/controllers/tutores_disponibilidad.php" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2"><i class="bi bi-clock-history"></i>Actualizar horarios y materias</a>';
include __DIR__ . '/../partials/page_header.php';
?>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <?php
    $kpiClase = 'primary'; $kpiIcono = 'bi-journal-bookmark'; $kpiValor = count($materias); $kpiEtiqueta = 'Materias a cargo';
    include __DIR__ . '/../partials/kpi_card.php';
    ?>
  </div>
  <div class="col-6 col-md-3">
    <?php
    $kpiClase = 'warning'; $kpiIcono = 'bi-clock-history'; $kpiValor = count($disponibilidad); $kpiEtiqueta = 'Bloques horarios';
    include __DIR__ . '/../partials/kpi_card.php';
    ?>
  </div>
  <div class="col-6 col-md-3">
    <?php
    $kpiClase = 'info'; $kpiIcono = 'bi-inbox'; $kpiValor = ($metricas['pendiente'] ?? 0) + ($metricas['confirmada'] ?? 0); $kpiEtiqueta = 'Solicitudes por atender';
    include __DIR__ . '/../partials/kpi_card.php';
    ?>
  </div>
  <div class="col-6 col-md-3">
    <?php
    $kpiClase = 'success'; $kpiIcono = 'bi-check-circle'; $kpiValor = $metricas['realizada'] ?? 0; $kpiEtiqueta = 'Tutorías realizadas';
    include __DIR__ . '/../partials/kpi_card.php';
    ?>
  </div>
</div>

<div class="card card-custom shadow-sm">
  <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-calendar3 me-1 text-primary"></i>Solicitudes y tutorías</h5>
    <div class="d-flex flex-wrap gap-2">
      <span class="badge bg-accent bg-opacity-10 text-accent border border-accent-subtle"><?= $metricas['pendiente'] ?? 0 ?> pendientes</span>
      <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle"><?= $metricas['realizada'] ?? 0 ?> realizadas</span>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <th class="ps-4">Fecha y Horario</th>
          <th>Materia</th>
          <th>Estudiante</th>
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
              $emptyTitulo = 'Aún no tienes tutorías';
              $emptyTexto = 'Cuando un estudiante solicite una sesión con tu disponibilidad, aparecerá aquí.';
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
              <div class="fw-medium text-dark"><?= htmlspecialchars($t['estudiante_nombre'] . ' ' . $t['estudiante_apellido']) ?></div>
              <?php if (!empty($t['observaciones'])): ?><small class="text-muted text-truncate d-inline-block" style="max-width: 160px;" title="<?= htmlspecialchars($t['observaciones']) ?>"><?= htmlspecialchars($t['observaciones']) ?></small><?php endif; ?>
            </td>
            <td>
              <?php if ($t['modalidad'] === 'virtual'): ?>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1"><i class="bi bi-camera-video me-1"></i>Virtual</span>
              <?php else: ?>
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1"><i class="bi bi-geo-alt me-1"></i>Presencial</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge rounded-pill border px-3 py-1 <?= $badgeEstado ?>"><?= ucfirst($t['estado']) ?><?= (!empty($t['estado_conclusion_nombre']) && $t['estado'] === 'finalizada') ? ' — ' . htmlspecialchars($t['estado_conclusion_nombre']) : '' ?></span>
              <?php if (($t['tipo'] ?? 'apoyo') === 'grado'): ?><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1 ms-1"><i class="bi bi-mortarboard me-1"></i>Grado</span><?php endif; ?>
            </td>
            <td class="text-end pe-4">
              <div class="btn-group" role="group">
                <?php if ($t['estado'] === 'pendiente'): ?>
                  <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=confirmada" onclick="enviarPostSeguro(this.href); return false;" class="btn btn-outline-success btn-sm" title="Aceptar tutoría"><i class="bi bi-check-lg"></i></a>
                  <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=cancelada" onclick="confirmarCancelacion(this.href); return false;" class="btn btn-outline-accent btn-sm" title="Rechazar tutoría"><i class="bi bi-slash-circle"></i></a>
                <?php elseif ($t['estado'] === 'confirmada'): ?>
                  <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=realizada" onclick="enviarPostSeguro(this.href); return false;" class="btn btn-outline-primary btn-sm" title="Marcar como realizada"><i class="bi bi-check2-all"></i></a>
                  <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=cancelada" onclick="confirmarCancelacion(this.href); return false;" class="btn btn-outline-accent btn-sm" title="Cancelar tutoría"><i class="bi bi-slash-circle"></i></a>
                <?php elseif (($t['tipo'] ?? 'apoyo') === 'grado' && $t['estado'] !== 'finalizada' && $t['estado'] !== 'cancelada'): ?>
                  <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=finalizada" onclick="confirmarFinalizacion(this.href, <?= htmlspecialchars(json_encode($estadosConclusion), ENT_QUOTES, 'UTF-8') ?>); return false;" class="btn btn-outline-primary btn-sm" title="Finalizar Modalidad de Grado"><i class="bi bi-mortarboard"></i>&nbsp;Finalizar MG</a>
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