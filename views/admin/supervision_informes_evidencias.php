<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Supervision de Informes y Evidencias - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$migas = [
    ['texto' => 'Administracion', 'url' => '/controllers/usuarios_listar.php'],
    ['texto' => 'Supervision', 'actual' => true],
];
$titulo = 'Supervision de Informes y Evidencias';
$descripcion = 'Vista unificada de todos los informes de avance de tutores, evidencias de reuniones de estudiantes y documentos de expedientes.';
$icono = 'bi-eye-fill';
include __DIR__ . '/../partials/page_header.php';
?>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-4">
    <div class="card card-custom p-3 text-center border-start border-primary border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Informes de avance</small>
      <h3 class="fw-bold mb-0 text-primary"><?= count($informes) ?></h3>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="card card-custom p-3 text-center border-start border-success border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Evidencias de reuniones</small>
      <h3 class="fw-bold mb-0 text-success"><?= count($evidencias) ?></h3>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="card card-custom p-3 text-center border-start border-accent border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Documentos de expediente</small>
      <h3 class="fw-bold mb-0 text-info"><?= count($documentosExpediente) ?></h3>
    </div>
  </div>
</div>

<ul class="nav nav-tabs mb-4" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="tab-informes" data-bs-toggle="tab" data-bs-target="#pane-informes" type="button" role="tab" aria-controls="pane-informes" aria-selected="true">
      <i class="bi bi-file-earmark-check me-1"></i>Informes de avance (<?= count($informes) ?>)
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="tab-evidencias" data-bs-toggle="tab" data-bs-target="#pane-evidencias" type="button" role="tab" aria-controls="pane-evidencias" aria-selected="false">
      <i class="bi bi-camera-video me-1"></i>Evidencias de reuniones (<?= count($evidencias) ?>)
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="tab-expedientes" data-bs-toggle="tab" data-bs-target="#pane-expedientes" type="button" role="tab" aria-controls="pane-expedientes" aria-selected="false">
      <i class="bi bi-folder2-open me-1"></i>Documentos de expediente (<?= count($documentosExpediente) ?>)
    </button>
  </li>
</ul>

<div class="tab-content">
  <div class="tab-pane fade show active" id="pane-informes" role="tabpanel" aria-labelledby="tab-informes">
    <div class="card card-custom shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4">Informe</th>
              <th>Tutor</th>
              <th>Estudiante</th>
              <th>Materia</th>
              <th>Tutoría</th>
              <th>% Avance</th>
              <th>Fecha</th>
              <th class="pe-4">Ver</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($informes)): ?>
              <tr><td colspan="8" class="text-center py-5 text-muted">No hay informes de avance registrados.</td></tr>
            <?php else: ?>
              <?php foreach ($informes as $inf): ?>
                <tr>
                  <td class="ps-4 fw-semibold">#<?= (int) $inf['numero_informe'] ?></td>
                  <td>Prof. <?= htmlspecialchars($inf['tutor_nombre'] . ' ' . $inf['tutor_apellido']) ?></td>
                  <td><?= htmlspecialchars($inf['estudiante_nombre'] . ' ' . $inf['estudiante_apellido']) ?></td>
                  <td class="fw-semibold text-primary"><?= htmlspecialchars($inf['nombre_materia']) ?></td>
                  <td>#<?= (int) $inf['id_tutoria'] ?> <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1 ms-1"><?= ucfirst($inf['tipo'] ?? 'apoyo') ?></span></td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="progress flex-grow-1" style="max-width: 120px; height: 8px;">
                        <div class="progress-bar bg-success" style="width: <?= (int) $inf['porcentaje_avance'] ?>%"></div>
                      </div>
                      <span class="fw-semibold"><?= (int) $inf['porcentaje_avance'] ?>%</span>
                    </div>
                  </td>
                  <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($inf['fecha_registro'])) ?></td>
                  <td class="pe-4">
                    <a href="/controllers/expediente_documentos.php?id=<?= (int) $inf['id_tutoria'] ?>" class="btn btn-sm btn-outline-primary rounded-2" title="Ver expediente"><i class="bi bi-eye"></i></a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="tab-pane fade" id="pane-evidencias" role="tabpanel" aria-labelledby="tab-evidencias">
    <div class="card card-custom shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4">Reunión</th>
              <th>Tutor</th>
              <th>Estudiante</th>
              <th>Materia</th>
              <th>Fecha</th>
              <th>Asistencia</th>
              <th class="pe-4">Evidencia</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($evidencias)): ?>
              <tr><td colspan="7" class="text-center py-5 text-muted">No hay evidencias de reuniones registradas.</td></tr>
            <?php else: ?>
              <?php foreach ($evidencias as $r):
                $asistencias = ['si' => ['success', 'Asistió'], 'no' => ['danger', 'No asistió'], 'tardanza' => ['warning', 'Llegó tarde']];
                [$claseAsist, $etqAsist] = $asistencias[$r['asistio_estudiante']] ?? ['neutral', ucfirst($r['asistio_estudiante'] ?? '')];
              ?>
                <tr>
                  <td class="ps-4 fw-semibold">#<?= (int) $r['id_reunion'] ?></td>
                  <td>Prof. <?= htmlspecialchars($r['tutor_nombre'] . ' ' . $r['tutor_apellido']) ?></td>
                  <td><?= htmlspecialchars($r['estudiante_nombre'] . ' ' . $r['estudiante_apellido']) ?></td>
                  <td><?= htmlspecialchars($r['nombre_materia']) ?></td>
                  <td class="text-muted small"><?= date('d/m/Y', strtotime($r['fecha'])) ?> <?= substr($r['hora_inicio'], 0, 5) ?>-<?= substr($r['hora_fin'], 0, 5) ?></td>
                  <td>
                    <span class="badge bg-<?= $claseAsist ?> bg-opacity-10 text-<?= $claseAsist ?> border border-<?= $claseAsist ?>-subtle"><?= $etqAsist ?></span>
                  </td>
                  <td class="pe-4">
                    <a href="/controllers/reuniones_evidencia.php?id=<?= (int) $r['id_reunion'] ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary rounded-2 download-btn" title="Ver evidencia"><i class="bi bi-paperclip"></i>Ver</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="tab-pane fade" id="pane-expedientes" role="tabpanel" aria-labelledby="tab-expedientes">
    <div class="card card-custom shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4">Documento</th>
              <th>Origen</th>
              <th>Destinatario</th>
              <th>Tutoría</th>
              <th>Fecha</th>
              <th class="pe-4">Descargar</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($documentosExpediente)): ?>
              <tr><td colspan="6" class="text-center py-5 text-muted">No hay documentos de expediente registrados.</td></tr>
            <?php else: ?>
              <?php foreach ($documentosExpediente as $d):
                $extensionDoc = strtolower((string) pathinfo((string) $d['ruta_archivo'], PATHINFO_EXTENSION));
              ?>
                <tr>
                  <td class="ps-4">
                    <div class="fw-medium text-truncate" title="<?= htmlspecialchars($d['nombre_original']) ?>"><?= htmlspecialchars($d['nombre_original']) ?></div>
                    <small class="text-muted">
                      <?= $d['origen_nombre'] . ' ' . $d['origen_apellido'] ?> → <?= $d['dest_nombre'] . ' ' . $d['dest_apellido'] ?>
                    </small>
                  </td>
                  <td><?= htmlspecialchars($d['origen_nombre'] . ' ' . $d['origen_apellido']) ?></td>
                  <td><?= htmlspecialchars($d['dest_nombre'] . ' ' . $d['dest_apellido']) ?></td>
                  <td>#<?= (int) $d['id_tutoria'] ?> <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1 ms-1"><?= ucfirst($d['tipo'] ?? 'apoyo') ?></span></td>
                  <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($d['fecha'])) ?></td>
                  <td class="pe-4">
                    <a href="/controllers/expediente_documento_descargar.php?id=<?= (int) $d['id_documento'] ?>&ver=descargar" class="btn btn-sm btn-primary rounded-2 download-btn" title="Descargar"><i class="bi bi-download"></i></a>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-2 js-ver-documento download-btn" data-url="/controllers/expediente_documento_descargar.php?id=<?= (int) $d['id_documento'] ?>&ver=inline" data-nombre="<?= htmlspecialchars($d['nombre_original'], ENT_QUOTES, 'UTF-8') ?>" title="Previsualizar"><i class="bi bi-eye"></i></button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script>
(function () {
  document.addEventListener('click', function (evento) {
    const btn = evento.target.closest('.download-btn');
    if (!btn) return;
    btn.classList.add('loading');
    setTimeout(function () { btn.classList.remove('loading'); }, 1200);
  });
})();
</script>