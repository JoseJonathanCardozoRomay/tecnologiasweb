<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/TutorModel.php';
require_once __DIR__ . '/../../models/TutoriaModel.php';

$tutorModel = new TutorModel($pdo);
$tutoriaModel = new TutoriaModel($pdo);

$idUsuario = $_SESSION['id_usuario'] ?? 0;
$tutor = $tutorModel->obtenerPorUsuario($idUsuario);

if (!$tutor) {
    // Si no tiene registro en tutores, lo creamos automáticamente
    $pdo->prepare("INSERT INTO tutores (id_usuario, especialidad) VALUES (?, 'Docente UPDS')")->execute([$idUsuario]);
    $tutor = $tutorModel->obtenerPorUsuario($idUsuario);
}

$idTutor = $tutor['id_tutor'];
$misTutorias = $tutoriaModel->obtenerPorTutor($idTutor);
$misMaterias = $tutorModel->obtenerMaterias($idTutor);
$misHorarios = $tutorModel->obtenerDisponibilidad($idTutor);

$pendientes = count(array_filter($misTutorias, fn($t) => $t['estado'] === 'pendiente'));
$confirmadas = count(array_filter($misTutorias, fn($t) => $t['estado'] === 'confirmada'));
$realizadas = count(array_filter($misTutorias, fn($t) => $t['estado'] === 'realizada'));

$tituloPagina = 'Panel del Docente Tutor - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<div class="row g-4">
  <div class="col-12">
    <div class="card card-custom p-4 text-white shadow" style="background: linear-gradient(135deg, #1e3a5f 0%, #0d6efd 100%) !important;">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
          <h2 class="fw-bold mb-1">¡Bienvenido(a), Prof. <?= htmlspecialchars($_SESSION['nombre']) ?>! 👋</h2>
          <p class="mb-0 text-white-50">Portal Docente de Tutorías Académicas &bull; <?= htmlspecialchars($tutor['especialidad'] ?? 'Docencia') ?></p>
        </div>
        <div class="d-flex gap-2">
          <a href="/controllers/tutores_disponibilidad.php?id=<?= $idTutor ?>" class="btn btn-light text-primary fw-semibold d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-clock-history"></i>
            <span>Mis Horarios y Materias</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Métricas en vivo -->
  <div class="col-md-4">
    <div class="card card-custom p-4 text-center border-start border-warning border-4">
      <div class="text-warning fs-1 mb-2"><i class="bi bi-clock"></i></div>
      <h3 class="fw-bold mb-0 text-dark"><?= $pendientes ?></h3>
      <p class="text-muted small mb-0">Solicitudes Pendientes</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-custom p-4 text-center border-start border-info border-4">
      <div class="text-info fs-1 mb-2"><i class="bi bi-calendar-check"></i></div>
      <h3 class="fw-bold mb-0 text-dark"><?= $confirmadas ?></h3>
      <p class="text-muted small mb-0">Sesiones Agendadas/Confirmadas</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-custom p-4 text-center border-start border-success border-4">
      <div class="text-success fs-1 mb-2"><i class="bi bi-check2-circle"></i></div>
      <h3 class="fw-bold mb-0 text-dark"><?= $realizadas ?></h3>
      <p class="text-muted small mb-0">Tutorías Realizadas con Éxito</p>
    </div>
  </div>

  <!-- Listado de tutorías asignadas -->
  <div class="col-12">
    <div class="card card-custom shadow-sm overflow-hidden">
      <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-calendar-week text-primary"></i>
          <span>Mis Sesiones de Tutoría</span>
        </h5>
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
            <?php foreach ($misTutorias as $t): ?>
              <?php
                $badgeEstado = 'bg-warning text-dark';
                if ($t['estado'] === 'confirmada') $badgeEstado = 'bg-info text-white';
                if ($t['estado'] === 'realizada') $badgeEstado = 'bg-success text-white';
                if ($t['estado'] === 'cancelada') $badgeEstado = 'bg-danger text-white';
              ?>
              <tr>
                <td class="ps-4">
                  <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($t['fecha'])) ?></div>
                  <small class="text-muted"><?= substr($t['hora_inicio'], 0, 5) ?> - <?= substr($t['hora_fin'], 0, 5) ?></small>
                </td>
                <td>
                  <div class="fw-semibold text-primary"><?= htmlspecialchars($t['nombre_materia']) ?></div>
                  <?php if (!empty($t['observaciones'])): ?>
                    <small class="text-muted text-truncate d-block" style="max-width: 200px;" title="<?= htmlspecialchars($t['observaciones']) ?>">
                      Obs: <?= htmlspecialchars($t['observaciones']) ?>
                    </small>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="fw-medium text-dark"><?= htmlspecialchars($t['est_nombre'] . ' ' . $t['est_apellido']) ?></div>
                  <small class="text-muted"><?= htmlspecialchars($t['est_correo']) ?></small>
                </td>
                <td>
                  <span class="badge bg-light text-dark border">
                    <?= ucfirst($t['modalidad']) ?>
                  </span>
                  <?php if (!empty($t['lugar_o_enlace'])): ?>
                    <div class="small text-muted text-truncate" style="max-width: 140px;"><?= htmlspecialchars($t['lugar_o_enlace']) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge rounded-pill px-3 py-1 <?= $badgeEstado ?>">
                    <?= ucfirst($t['estado']) ?>
                  </span>
                  <?php if (!empty($t['calificacion'])): ?>
                    <div class="text-warning small mt-1">
                      <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi bi-star<?= $i <= $t['calificacion'] ? '-fill' : '' ?>"></i>
                      <?php endfor; ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td class="text-end pe-4">
                  <div class="btn-group" role="group">
                    <?php if ($t['estado'] === 'pendiente'): ?>
                      <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=confirmada" 
                         class="btn btn-sm btn-success d-flex align-items-center gap-1" title="Aceptar y confirmar">
                        <i class="bi bi-check-circle"></i> Aceptar
                      </a>
                      <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=cancelada" 
                         class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Rechazar esta solicitud?');" title="Rechazar">
                        <i class="bi bi-x-circle"></i>
                      </a>
                    <?php elseif ($t['estado'] === 'confirmada'): ?>
                      <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=realizada" 
                         class="btn btn-sm btn-primary d-flex align-items-center gap-1" title="Marcar como realizada">
                        <i class="bi bi-check2-all"></i> Marcar Realizada
                      </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($misTutorias)): ?>
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="bi bi-calendar-check fs-1 d-block mb-2 text-secondary"></i>
                  Aún no tienes solicitudes de tutorías asignadas.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
