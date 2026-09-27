<?php
if (!isset($reuniones) || !isset($seguimientos)) {
    header('Location: /controllers/reuniones_registrar.php');
    exit;
}
require_once __DIR__ . '/../../models/ReunionModel.php';
require_once __DIR__ . '/../../includes/csrf.php';
$tituloPagina = 'Reuniones - Tutor - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$migas = [
    ['texto' => 'Mi panel', 'url' => '/views/tutor/panel.php'],
    ['texto' => 'Reuniones', 'actual' => true],
];
$titulo = 'Reuniones de tutoría';
$descripcion = 'Registra las reuniones y, una vez realizadas, completa el seguimiento con asistencia, cumplimiento e informe final.';
$icono = 'bi-calendar2-week-fill';
$accion = '<a href="/views/tutor/panel.php" class="btn btn-outline-secondary rounded-3 px-3"><i class="bi bi-arrow-left me-1"></i>Volver al panel</a>';
include __DIR__ . '/../partials/page_header.php';
?>

<?php if (!empty($errores)): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
    <strong>No se pudo completar la operación:</strong>
    <ul class="mb-0 mt-1">
      <?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<ul class="nav nav-tabs mb-3" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="tab-agendar" data-bs-toggle="tab" data-bs-target="#panelAgendar" type="button" role="tab" aria-controls="panelAgendar" aria-selected="true">
      <i class="bi bi-calendar-plus me-1"></i>Agendar reunión
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="tab-seguimiento" data-bs-toggle="tab" data-bs-target="#panelSeguimiento" type="button" role="tab" aria-controls="panelSeguimiento" aria-selected="false">
      <i class="bi bi-clipboard2-check me-1"></i>Seguimiento y observaciones
      <?php $conSeguimiento = count($seguimientos); ?>
      <?php if ($conSeguimiento > 0): ?>
        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary-subtle ms-1"><?= $conSeguimiento ?></span>
      <?php endif; ?>
    </button>
  </li>
</ul>

<div class="tab-content">
  <div class="tab-pane fade show active" id="panelAgendar" role="tabpanel" aria-labelledby="tab-agendar" tabindex="0">
    <div class="row g-3">
      <div class="col-lg-5">
        <div class="card card-custom shadow-sm h-100">
          <div class="card-header bg-white py-3">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-plus-circle me-1 text-primary"></i>Nueva reunión</h5>
          </div>
          <div class="card-body">
            <p class="text-muted small">
              La asistencia y las observaciones se registran <strong>después</strong> de realizar la reunión, en la pestaña
              <em>Seguimiento y observaciones</em>.
            </p>
            <form method="POST" action="reuniones_registrar.php" enctype="multipart/form-data" autocomplete="off">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="accion" value="registrar">

              <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small text-uppercase">Tutoría *</label>
                <select name="id_tutoria" id="select-tutoria" class="form-select rounded-3 py-2" required>
                  <option value="">Selecciona una tutoría activa</option>
                  <?php foreach ($tutoriasAceptadas as $t): ?>
                    <option value="<?= (int) $t['id_tutoria'] ?>" <?= ((int) ($id_tutoria ?? 0) === (int) $t['id_tutoria']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars(trim($t['estudiante_nombre'] . ' ' . $t['estudiante_apellido'])) ?> — <?= htmlspecialchars($t['nombre_materia']) ?> (<?= date('d/m/Y', strtotime($t['fecha'])) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php if (empty($tutoriasAceptadas)): ?>
                  <div class="form-text text-danger">No tienes tutorías aceptadas o en proceso.</div>
                <?php endif; ?>
              </div>

              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label fw-semibold text-secondary small text-uppercase">Fecha *</label>
                  <input type="date" name="fecha" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['fecha'] ?? date('Y-m-d')) ?>" max="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-semibold text-secondary small text-uppercase">Inicio *</label>
                  <input type="time" name="hora_inicio" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['hora_inicio'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-semibold text-secondary small text-uppercase">Fin *</label>
                  <input type="time" name="hora_fin" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['hora_fin'] ?? '') ?>" required>
                </div>
              </div>

              <div class="mb-3 mt-3">
                <label class="form-label fw-semibold text-secondary small text-uppercase">Lugar o enlace</label>
                <input type="text" name="lugar_o_enlace" class="form-control rounded-3 py-2" maxlength="200" placeholder="Salón 4 / https://meet.upds.edu.bo/..." value="<?= htmlspecialchars($_POST['lugar_o_enlace'] ?? '') ?>">
              </div>

              

              <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small text-uppercase">Temas tratados</label>
                <textarea name="observaciones" class="form-control rounded-3 py-2" rows="2" maxlength="500" placeholder="Agenda de la reunión (opcional)"><?= htmlspecialchars($_POST['observaciones'] ?? '') ?></textarea>
              </div>

              <button type="submit" class="btn btn-primary w-100 rounded-3 py-2" <?= empty($tutoriasAceptadas) ? 'disabled' : '' ?>>
                <i class="bi bi-plus-square me-1"></i>Registrar reunión
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-7">
        <?php require __DIR__ . '/../partials/tutor_reuniones_tabla.php'; ?>
      </div>
    </div>
  </div>

  <div class="tab-pane fade" id="panelSeguimiento" role="tabpanel" aria-labelledby="tab-seguimiento" tabindex="0">
    <div class="card card-custom shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clipboard2-check me-1 text-primary"></i>Seguimiento de reuniones realizadas</h5>
      </div>
      <div class="card-body p-0">
        <?php if (empty($reuniones)): ?>
          <div class="p-4">
            <?php
            $emptyTitulo = 'Sin reuniones registradas';
            $emptyTexto = 'Cuando registres una reunión podrás aquí anotar la asistencia, el cumplimiento y las observaciones, y generar el informe final.';
            include __DIR__ . '/../partials/empty_state.php';
            ?>
          </div>
        <?php else: ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($reuniones as $r):
              $seg = $seguimientos[(int) $r['id_reunion']] ?? null;
              $realizada = ReunionModel::reunionRealizada($r);
            ?>
              <li class="list-group-item py-3">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                  <div class="min-w-0">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      <span class="badge bg-light border text-dark">#<?= (int) $r['id_reunion'] ?></span>
                      <strong class="text-dark"><?= htmlspecialchars((string) ($r['estudiantes_nombres'] ?? 'Sin estudiante')) ?></strong>
                      <span class="text-muted small"><?= htmlspecialchars($r['nombre_materia'] ?? 'Sin materia') ?></span>
                    </div>
                    <div class="text-muted small mt-1">
                      <?= date('d/m/Y', strtotime($r['fecha'])) ?> ·
                      <?= substr((string) $r['hora_inicio'], 0, 5) ?>–<?= substr((string) $r['hora_fin'], 0, 5) ?> h
                      <?php if (!empty($r['lugar_o_enlace'])): ?> · <?= htmlspecialchars($r['lugar_o_enlace']) ?><?php endif; ?>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <?php if ($seg): ?>
                        <span class="badge border px-3 py-1 <?= ReunionModel::claseAsistencia($seg['asistencia']) ?>">
                          <?= htmlspecialchars(ReunionModel::etiquetaAsistencia($seg['asistencia'])) ?>
                        </span>
                        <span class="badge border px-3 py-1 <?= ReunionModel::claseCumplimiento($seg['cumplimiento']) ?>">
                          <?= htmlspecialchars(ReunionModel::etiquetaCumplimiento($seg['cumplimiento'])) ?>
                        </span>
                      <?php else: ?>
                        <span class="badge border px-3 py-1 bg-warning bg-opacity-10 text-warning-emphasis border-warning-subtle">
                          <i class="bi bi-exclamation-circle me-1"></i>Seguimiento pendiente
                        </span>
                      <?php endif; ?>
                      <button type="button" class="btn btn-sm btn-primary rounded-3"
                              data-bs-toggle="modal" data-bs-target="#modalSeguimiento"
                              data-reunion="<?= htmlspecialchars(json_encode([
                                  'id'            => (int) $r['id_reunion'],
                                  'tutoria'       => (int) $r['id_tutoria'],
                                  'estudiante'    => (string) ($r['estudiantes_nombres'] ?? ''),
                                  'materia'       => (string) ($r['nombre_materia'] ?? ''),
                                  'fecha'         => date('d/m/Y', strtotime($r['fecha'])),
                                  'horario'       => substr((string) $r['hora_inicio'], 0, 5) . '-' . substr((string) $r['hora_fin'], 0, 5),
                                  'lugar'         => (string) ($r['lugar_o_enlace'] ?? ''),
                                  'temas'         => (string) ($r['observaciones'] ?? ''),
                                  'evidencia'     => !empty($r['evidencia_url']),
                                  'informe'       => !empty($r['informe_url']),
                                  'asistencia'    => (string) ($seg['asistencia'] ?? 'si'),
                                  'cumplimiento'  => (string) ($seg['cumplimiento'] ?? 'parcial'),
                                  'observaciones' => (string) ($seg['observaciones'] ?? ''),
                                  'compromisos'   => (string) ($seg['compromisos'] ?? ''),
                                  'registrado'    => !empty($seg),
                              ], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>">
                        <i class="bi bi-pencil-square me-1"></i><?= $seg ? 'Editar' : 'Registrar' ?>
                      </button>
                      <?php if ($seg): ?>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Descargar informe de la reunión">
                          <a href="/controllers/reuniones_informe.php?id_reunion=<?= (int) $r['id_reunion'] ?>&formato=pdf"
                             class="btn btn-outline-primary rounded-3" title="Generar el informe final en PDF">
                            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                          </a>
                          <a href="/controllers/reuniones_informe.php?id_reunion=<?= (int) $r['id_reunion'] ?>&formato=doc"
                             class="btn btn-outline-primary rounded-3" title="Generar el informe final en Word">
                            <i class="bi bi-file-earmark-word me-1"></i>DOC
                          </a>
                        </div>
                      <?php endif; ?>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-labelledby="modalSeguimientoTitulo" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-4 border-0 shadow">
      <form method="POST" action="/controllers/reuniones_registrar.php" autocomplete="off" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="accion" value="seguimiento">
        <input type="hidden" name="id_reunion" id="segIdReunion" value="">
        <input type="hidden" name="id_tutoria" id="segIdTutoria" value="<?= (int) ($id_tutoria ?? 0) ?>">

        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="modalSeguimientoTitulo">
            <i class="bi bi-clipboard2-check me-1 text-primary"></i>Seguimiento de la reunión
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <div class="bg-light border rounded-3 p-3 mb-4">
            <div class="row g-2 small">
              <div class="col-sm-6"><span class="text-muted">Estudiante:</span> <strong id="segEstudiante">—</strong></div>
              <div class="col-sm-6"><span class="text-muted">Materia:</span> <strong id="segMateria">—</strong></div>
              <div class="col-sm-6"><span class="text-muted">Fecha:</span> <strong id="segFecha">—</strong></div>
              <div class="col-sm-6"><span class="text-muted">Horario:</span> <strong id="segHorario">—</strong></div>
              <div class="col-12"><span class="text-muted">Lugar:</span> <strong id="segLugar">—</strong></div>
              <div class="col-12" id="segTemasFila"><span class="text-muted">Temas tratados:</span> <span id="segTemas">—</span></div>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Asistencia del estudiante *</label>
            <div class="row g-2" id="segAsistenciaGroup">
              <?php foreach (['si' => 'Sí asistió', 'tardanza' => 'Tardanza', 'no' => 'No asistió'] as $valor => $etiqueta): ?>
                <div class="col-4">
                  <input class="btn-check" type="radio" name="asistencia" id="segAsist<?= $valor ?>" value="<?= $valor ?>" <?= $valor === 'si' ? 'checked' : '' ?>>
                  <label class="btn btn-outline-secondary w-100 rounded-3" for="segAsist<?= $valor ?>"><?= htmlspecialchars($etiqueta) ?></label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Cumplimiento de la reunión *</label>
            <div class="row g-2" id="segCumplimientoGroup">
              <?php foreach (['completo' => 'Completo', 'parcial' => 'Parcial', 'pendiente' => 'No cumplido'] as $valor => $etiqueta): ?>
                <div class="col-4">
                  <input class="btn-check" type="radio" name="cumplimiento" id="segCumpl<?= $valor ?>" value="<?= $valor ?>" checked>
                  <label class="btn btn-outline-secondary w-100 rounded-3" for="segCumpl<?= $valor ?>"><?= htmlspecialchars($etiqueta) ?></label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold text-secondary small text-uppercase" for="segObservaciones">Observaciones de la reunión</label>
            <textarea name="observaciones" id="segObservaciones" class="form-control rounded-3 py-2" rows="3" maxlength="1000"
                      placeholder="Desarrollo de la sesión, avances, dificultades detectadas..."></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold text-secondary small text-uppercase" for="segCompromisos">Compromisos acordados</label>
            <textarea name="compromisos" id="segCompromisos" class="form-control rounded-3 py-2" rows="3" maxlength="1000"
                      placeholder="Un compromiso por línea, con su fecha de entrega"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Evidencia (JPG, PNG o PDF · máx 5MB)</label>
            <input type="file" name="evidencia" id="segEvidenciaInput" class="form-control rounded-3 py-2" accept=".jpg,.jpeg,.png,.pdf,application/pdf,image/jpeg,image/png">
            <div class="form-text">Sube la captura o documento que respalde la reunión (opcional).</div>
          </div>
        </div>

        <div class="modal-footer">
          <span class="small text-muted me-auto" id="segEstado">—</span>
          <button type="button" class="btn btn-outline-secondary rounded-3 px-3" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary rounded-3 px-3">
            <i class="bi bi-save me-1"></i>Guardar seguimiento
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  function validarEvidencia(input) {
    const archivo = input.files[0];
    if (!archivo) return;
    const ext = archivo.name.split('.').pop().toLowerCase();
    const permitidas = ['jpg', 'jpeg', 'png', 'pdf'];
    if (!permitidas.includes(ext)) {
      mostrarToast('La evidencia debe ser JPG, PNG o PDF.', 'danger');
      input.value = '';
    } else if (archivo.size > 5 * 1024 * 1024) {
      mostrarToast('El archivo no puede superar los 5MB.', 'danger');
      input.value = '';
    }
  }

  document.getElementById('segEvidenciaInput')?.addEventListener('change', function (e) {
    validarEvidencia(e.target);
  });

  const marcar = function (prefijo, valor) {
    const activo = document.getElementById(prefijo + valor);
    if (activo) activo.checked = true;
  };

  document.querySelectorAll('[data-reunion]').forEach((boton) => {
    boton.addEventListener('click', function () {
      let datos;
      try {
        datos = JSON.parse(boton.dataset.reunion);
      } catch (e) {
        return;
      }
      if (!datos) return;

      document.getElementById('segIdReunion').value = datos.id;
      document.getElementById('segIdTutoria').value = datos.tutoria || 0;
      document.getElementById('segEstudiante').textContent = datos.estudiante || '—';
      document.getElementById('segMateria').textContent = datos.materia || '—';
      document.getElementById('segFecha').textContent = datos.fecha || '—';
      document.getElementById('segHorario').textContent = datos.horario || '—';
      document.getElementById('segLugar').textContent = datos.lugar || 'No indicado';
      document.getElementById('segTemas').textContent = datos.temas || '—';
      document.getElementById('segObservaciones').value = datos.observaciones || '';
      document.getElementById('segCompromisos').value = datos.compromisos || '';
      document.getElementById('segEvidenciaInput').value = '';
      document.getElementById('segEstado').innerHTML = datos.registrado
        ? '<i class="bi bi-check-circle me-1"></i>Seguimiento registrado. Podés editarlo.'
        : '<i class="bi bi-exclamation-circle me-1"></i>Primer registro de seguimiento.';

      marcar('segAsist', datos.asistencia || 'si');
      marcar('segCumpl', datos.cumplimiento || 'parcial');
    });
  });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
