<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
require_once __DIR__ . '/../../includes/auth.php';
requerirRol(['estudiante']);

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/EstudianteModel.php';
require_once __DIR__ . '/../../models/TutoriaModel.php';
require_once __DIR__ . '/../../models/ReunionModel.php';
require_once __DIR__ . '/../../models/InformeModel.php';
require_once __DIR__ . '/../../models/ComprobanteModel.php';

$tituloPagina = 'Mi Historial - Estudiante - UPDS';
$estudianteModel = new EstudianteModel($pdo);
$estudiante = $estudianteModel->obtenerPorUsuario((int) ($_SESSION['id_usuario'] ?? 0));
$mgCompletada = $estudiante ? (new TutoriaModel($pdo))->mgCompletada((int) $estudiante['id_estudiante']) : false;
include __DIR__ . '/../layouts/header.php';

$tutorias = [];
$reuniones = [];
$informes = [];
$comprobantes = [];

if ($estudiante) {
    $idEstudiante = (int) $estudiante['id_estudiante'];
    $tutorias = (new TutoriaModel($pdo))->obtenerPorEstudiante($idEstudiante);
    $reuniones = (new ReunionModel($pdo))->obtenerPorEstudiante($idEstudiante);
    $informes = (new InformeModel($pdo))->obtenerPorEstudiante($idEstudiante);
    $comprobantes = (new ComprobanteModel($pdo))->obtenerPorEstudiante($idEstudiante);
}

$asistencias = ['si' => ['success', 'Asistió'], 'no' => ['danger', 'No asistió'], 'tardanza' => ['warning', 'Llegó tarde']];
?>

<?php
$titulo = 'Mi Historial';
$descripcion = 'Todas tus tutorías, reuniones e informes de avance registrados en el sistema.';
$icono = 'bi-clock-history';
include __DIR__ . '/../partials/page_header.php';
?>

<ul class="nav nav-pills gap-2 mb-4" role="tablist">
  <li class="nav-item" role="presentation">
    <button type="button" class="nav-link active" id="tab-tutorias" data-bs-toggle="pill" data-bs-target="#pane-tutorias" role="tab" aria-selected="true"><i class="bi bi-calendar2-check me-1"></i>Tutorías (<?= count($tutorias) ?>)</button>
  </li>
  <li class="nav-item" role="presentation">
    <button type="button" class="nav-link" id="tab-reuniones" data-bs-toggle="pill" data-bs-target="#pane-reuniones" role="tab" aria-selected="false"><i class="bi bi-camera-video me-1"></i>Reuniones (<?= count($reuniones) ?>)</button>
  </li>
  <li class="nav-item" role="presentation">
    <button type="button" class="nav-link" id="tab-informes" data-bs-toggle="pill" data-bs-target="#pane-informes" role="tab" aria-selected="false"><i class="bi bi-file-earmark-text me-1"></i>Informes (<?= count($informes) ?>)</button>
  </li>
  <li class="nav-item" role="presentation">
    <button type="button" class="nav-link" id="tab-comprobantes" data-bs-toggle="pill" data-bs-target="#pane-comprobantes" role="tab" aria-selected="false"><i class="bi bi-cash-stack me-1"></i>Comprobantes (<?= count($comprobantes) ?>)</button>
  </li>
</ul>

<div class="tab-content">
  <div class="tab-pane fade show active" id="pane-tutorias" role="tabpanel" aria-labelledby="tab-tutorias">
    <div class="card card-custom shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4">Fecha y Horario</th>
              <th>Materia</th>
              <th>Docente Tutor</th>
              <th>Tipo</th>
              <th>Estado</th>
              <th class="pe-4">Inscritos</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tutorias as $t): ?>
              <tr>
                <td class="ps-4">
                  <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($t['fecha'])) ?></div>
                  <small class="text-muted"><?= substr($t['hora_inicio'], -8, 5) ?> - <?= substr($t['hora_fin'], -8, 5) ?></small>
                </td>
                <td class="fw-semibold text-primary"><?= htmlspecialchars($t['nombre_materia']) ?></td>
                <td>Prof. <?= htmlspecialchars($t['tutor_nombre'] . ' ' . $t['tutor_apellido']) ?></td>
                <td>
                  <?php if (($t['tipo'] ?? 'apoyo') === 'grado'): ?>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle"><i class="bi bi-mortarboard me-1"></i>Grado</span>
                  <?php else: ?>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle"><i class="bi bi-journal-bookmark me-1"></i>Apoyo</span>
                  <?php endif; ?>
                </td>
                <td><?= estado_badge($t['estado'], $t['estado_conclusion_nombre'] ?? null) ?></td>
                <td class="pe-4">
                  <?php if (($t['tipo'] ?? 'apoyo') === 'apoyo'): ?>
                    <span class="badge <?= $t['habilitada'] ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning-emphasis' ?>"><?= (int) ($t['inscritos'] ?? 1) ?> inscritos</span>
                  <?php else: ?>
                    <span class="text-muted small">—</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($tutorias)): ?>
              <tr><td colspan="6" class="text-center py-5 text-muted">Aún no tenés tutorías registradas.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="tab-pane fade" id="pane-reuniones" role="tabpanel" aria-labelledby="tab-reuniones">
    <div class="card card-custom shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4">Fecha</th>
              <th>Materia</th>
              <th>Docente</th>
              <th>Horario</th>
              <th>Asistencia</th>
              <th class="pe-4">Evidencia</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reuniones as $r): ?>
              <tr>
                <td class="ps-4 fw-semibold"><?= date('d/m/Y', strtotime($r['fecha'])) ?></td>
                <td><?= htmlspecialchars($r['nombre_materia']) ?></td>
                <td>Prof. <?= htmlspecialchars($r['tutor_nombre'] . ' ' . $r['tutor_apellido']) ?></td>
                <td class="text-muted"><?= substr($r['hora_inicio'], -8, 5) ?> - <?= substr($r['hora_fin'], -8, 5) ?></td>
                <td>
                  <?php [$claseAsist, $etqAsist] = $asistencias[$r['asistio_estudiante']] ?? ['neutral', ucfirst($r['asistio_estudiante'] ?? '')]; ?>
                  <span class="badge bg-<?= $claseAsist ?> bg-opacity-10 text-<?= $claseAsist ?> border border-<?= $claseAsist ?>-subtle"><?= $etqAsist ?></span>
                </td>
                <td class="pe-4">
                  <?php if (!empty($r['evidencia_url'])): ?>
                    <a href="/controllers/reuniones_evidencia.php?id=<?= (int) $r['id_reunion'] ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary rounded-2" title="Ver evidencia"><i class="bi bi-paperclip"></i>Ver</a>
                  <?php else: ?>
                    <span class="text-muted small">—</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($reuniones)): ?>
              <tr><td colspan="6" class="text-center py-5 text-muted">Aún no se registraron reuniones.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="tab-pane fade" id="pane-informes" role="tabpanel" aria-labelledby="tab-informes">
    <div class="card card-custom shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4">Informe</th>
              <th>Materia</th>
              <th>Docente</th>
              <th>% de avance</th>
              <th>Fecha límite</th>
              <th class="pe-4">Registrado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($informes as $inf): ?>
              <tr>
                <td class="ps-4 fw-semibold">Informe #<?= (int) $inf['numero_informe'] ?></td>
                <td><?= htmlspecialchars($inf['nombre_materia']) ?></td>
                <td>Prof. <?= htmlspecialchars($inf['tutor_nombre'] . ' ' . $inf['tutor_apellido']) ?></td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="max-width: 150px; height: 8px;">
                      <div class="progress-bar bg-success" style="width: <?= (int) $inf['porcentaje_avance'] ?>%"></div>
                    </div>
                    <span class="fw-semibold"><?= (int) $inf['porcentaje_avance'] ?>%</span>
                  </div>
                </td>
                <td class="text-muted small"><?= !empty($inf['fecha_limite']) ? date('d/m/Y', strtotime($inf['fecha_limite'])) : '—' ?></td>
                <td class="pe-4 text-muted small"><?= date('d/m/Y', strtotime($inf['fecha_registro'])) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($informes)): ?>
              <tr><td colspan="6" class="text-center py-5 text-muted">Aún no se registraron informes de avance.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="tab-pane fade" id="pane-comprobantes" role="tabpanel" aria-labelledby="tab-comprobantes">
    <div class="card card-custom shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4">Monto</th>
              <th>Fecha de pago</th>
              <th>Registrado</th>
              <th>Estado</th>
              <th class="pe-4">Archivo</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($comprobantes as $c): ?>
              <tr>
                <td class="ps-4 fw-semibold">Bs. <?= number_format((float) $c['monto'], 2) ?></td>
                <td><?= date('d/m/Y', strtotime($c['fecha_pago'])) ?></td>
                <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($c['fecha_registro'])) ?></td>
                <td>
                  <?= estado_badge($c['estado']) ?>
                  <?php if ($c['estado'] === 'rechazado' && !empty($c['motivo_rechazo'])): ?>
                    <small class="d-block text-danger"><?= htmlspecialchars($c['motivo_rechazo']) ?></small>
                  <?php endif; ?>
                </td>
                <td class="pe-4">
                  <?php if (archivo_disponible($c['ruta_archivo'], 'uploads/comprobantes')): ?>
                    <a href="/controllers/mg_comprobantes_descargar.php?id=<?= (int) $c['id_comprobante'] ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary rounded-2" title="Ver comprobante"><i class="bi bi-paperclip"></i>Ver</a>
                  <?php else: ?>
                    <span class="btn btn-sm btn-outline-secondary rounded-2 disabled" role="button" tabindex="-1" aria-disabled="true" title="Este comprobante no tiene archivo adjunto."><i class="bi bi-paperclip"></i>Ver</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($comprobantes)): ?>
              <tr><td colspan="5" class="text-center py-5 text-muted">Aún no registraste comprobantes. <a href="/controllers/mg_comprobantes_registrar.php">Ir a Modalidad de Grado</a></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>