<?php
require_once __DIR__ . '/../../includes/auth.php';
requerirRol(['estudiante']);
require_once __DIR__ . '/../../includes/verificar_sesion.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/EstudianteModel.php';
require_once __DIR__ . '/../../models/TutoriaModel.php';
require_once __DIR__ . '/../../models/CarreraModel.php';
require_once __DIR__ . '/../../includes/lista_helper.php';

$estudianteModel = new EstudianteModel($pdo);
$tutoriaModel = new TutoriaModel($pdo);

$idUsuario = $_SESSION['id_usuario'] ?? 0;
$estudiante = $estudianteModel->obtenerPorUsuario($idUsuario);

if (!$estudiante) {
    $carreraModel = new CarreraModel($pdo);
    $carreras = $carreraModel->obtenerTodas();
    $idCarreraDefault = !empty($carreras) ? $carreras[0]['id_carrera'] : 1;
    $estudianteModel->guardarOActualizar($idUsuario, $idCarreraDefault, 1, 'RU-' . rand(10000, 99999));
    $estudiante = $estudianteModel->obtenerPorUsuario($idUsuario);
}

$idEstudiante = $estudiante['id_estudiante'];
$totalRegistros = $tutoriaModel->contarPorEstudiante($idEstudiante);
$pag = paginacionCalcular($totalRegistros, paginacionParametros(10));
$misTutorias = $tutoriaModel->obtenerPorEstudiantePaginadas($idEstudiante, $pag['por_pagina'], $pag['offset']);
$metricasEstudiante = $tutoriaModel->obtenerMetricasPorEstudiante($idEstudiante);

$pendientes = (int) ($metricasEstudiante['pendientes'] ?? 0);
$confirmadas = (int) ($metricasEstudiante['confirmadas'] ?? 0);
$realizadas = (int) ($metricasEstudiante['realizadas'] ?? 0);

$tituloPagina = 'Portal del Estudiante - Tutorías UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<div class="row g-4">
  <div class="col-12">
    <div class="card card-custom p-4 text-white shadow" style="background: linear-gradient(135deg, #0d5c3a 0%, #198754 100%) !important;">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
          <h2 class="fw-bold mb-1">¡Hola, <?= htmlspecialchars($_SESSION['nombre']) ?>! 👋</h2>
          <p class="mb-0 text-white-50">Carrera: <?= htmlspecialchars($estudiante['nombre_carrera']) ?> &bull; Semestre <?= $estudiante['semestre'] ?> &bull; R.U: <?= htmlspecialchars($estudiante['registro_universitario']) ?></p>
        </div>
        <div>
          <a href="/controllers/tutorias_solicitar.php" class="btn btn-warning text-dark fw-bold d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3">
            <i class="bi bi-calendar-plus-fill"></i>
            <span>+ Solicitar Nueva Tutoría</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Métricas del estudiante -->
  <div class="col-md-4">
    <div class="card card-custom p-4 text-center border-start border-warning border-4">
      <div class="text-warning fs-1 mb-2"><i class="bi bi-hourglass-split"></i></div>
      <h3 class="fw-bold mb-0 text-dark"><?= $pendientes ?></h3>
      <p class="text-muted small mb-0">Solicitudes en Espera de Confirmación</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-custom p-4 text-center border-start border-info border-4">
      <div class="text-info fs-1 mb-2"><i class="bi bi-calendar-event"></i></div>
      <h3 class="fw-bold mb-0 text-dark"><?= $confirmadas ?></h3>
      <p class="text-muted small mb-0">Tutorías Confirmadas / Próximas</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-custom p-4 text-center border-start border-success border-4">
      <div class="text-success fs-1 mb-2"><i class="bi bi-award"></i></div>
      <h3 class="fw-bold mb-0 text-dark"><?= $realizadas ?></h3>
      <p class="text-muted small mb-0">Tutorías Completadas</p>
    </div>
  </div>

  <!-- Historial de Tutorías -->
  <div class="col-12">
    <div class="card card-custom shadow-sm overflow-hidden">
      <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-clock-history text-primary"></i>
          <span>Mis Solicitudes y Tutorías</span>
        </h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4">Fecha y Horario</th>
              <th>Materia</th>
              <th>Docente Tutor</th>
              <th>Modalidad / Lugar</th>
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
                </td>
                <td>
                  <div class="fw-medium text-dark">Prof. <?= htmlspecialchars($t['tut_nombre'] . ' ' . $t['tut_apellido']) ?></div>
                  <small class="text-muted"><?= htmlspecialchars($t['tut_correo']) ?></small>
                </td>
                <td>
                  <span class="badge bg-light text-dark border">
                    <?= ucfirst($t['modalidad']) ?>
                  </span>
                  <?php if (!empty($t['lugar_o_enlace'])): ?>
                    <div class="small text-muted text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($t['lugar_o_enlace']) ?>">
                      <?= htmlspecialchars($t['lugar_o_enlace']) ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge rounded-pill px-3 py-1 <?= $badgeEstado ?>">
                    <?= ucfirst($t['estado']) ?>
                  </span>
                </td>
                <td class="text-end pe-4">
                  <?php if ($t['estado'] === 'realizada'): ?>
                    <?php if (!empty($t['calificacion'])): ?>
                      <div class="text-warning small" title="Calificación enviada: <?= $t['calificacion'] ?>/5">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                          <i class="bi bi-star<?= $i <= $t['calificacion'] ? '-fill' : '' ?>"></i>
                        <?php endfor; ?>
                      </div>
                    <?php else: ?>
                      <button type="button" class="btn btn-warning btn-sm d-inline-flex align-items-center gap-1"
                              data-bs-toggle="modal" data-bs-target="#modalEvaluar_<?= $t['id_tutoria'] ?>">
                        <i class="bi bi-star"></i> Calificar
                      </button>

                      <!-- Modal de Evaluación -->
                      <div class="modal fade text-start" id="modalEvaluar_<?= $t['id_tutoria'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content rounded-4 border-0 shadow">
                            <form action="/controllers/tutorias_evaluar.php" method="POST">
                              <?php require_once __DIR__ . '/../../includes/csrf.php'; echo csrf_campo(); ?>
                              <input type="hidden" name="id_tutoria" value="<?= $t['id_tutoria'] ?>">
                              <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold">Calificar Tutoría de <?= htmlspecialchars($t['nombre_materia']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>
                              <div class="modal-body">
                                <p class="text-muted small">Tu retroalimentación ayuda a mejorar el apoyo académico de los tutores.</p>
                                
                                <div class="mb-3 text-center">
                                  <label class="form-label fw-semibold small text-uppercase text-secondary d-block">Calificación (1 a 5 estrellas)</label>
                                  <select name="calificacion" class="form-select form-select-lg text-center fw-bold text-warning border-warning" required>
                                    <option value="5">⭐⭐⭐⭐⭐ 5 - Excelente</option>
                                    <option value="4">⭐⭐⭐⭐ 4 - Muy Buena</option>
                                    <option value="3">⭐⭐⭐ 3 - Regular</option>
                                    <option value="2">⭐⭐ 2 - Necesita Mejorar</option>
                                    <option value="1">⭐ 1 - Deficiente</option>
                                  </select>
                                </div>

                                <div class="mb-3">
                                  <label class="form-label fw-semibold small text-uppercase text-secondary">Comentario u Observaciones</label>
                                  <textarea name="comentario" class="form-control" rows="3" placeholder="¿Qué te pareció la sesión? ¿Se resolvieron tus dudas?"></textarea>
                                </div>
                              </div>
                              <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-success">Guardar Calificación</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                  <?php elseif ($t['estado'] === 'pendiente'): ?>
                      <a href="/controllers/tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=cancelada" 
                        class="btn btn-outline-danger btn-sm" onclick="confirmarEliminacion(this.href, '¿Deseas cancelar esta solicitud?'); return false;">
                      Cancelar
                    </a>
                  <?php else: ?>
                    <span class="text-muted small">-</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($misTutorias)): ?>
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="bi bi-calendar-plus fs-1 d-block mb-2 text-secondary"></i>
                  Aún no has solicitado tutorías. ¡Haz clic en <strong>+ Solicitar Nueva Tutoría</strong> para agendar tu primera sesión!
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <?php $mostrarSelector = false; include __DIR__ . '/../partials/paginacion.php'; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
