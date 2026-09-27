<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Expediente MG N° ' . (int) $expediente['id_expediente_mg'] . ' - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';

$nombreModalidad = $expediente['modalidad'];
$requiereTutor = (int) $expediente['modalidad_requiere_tutor'];
$estadoActual = $expediente['estado'];
?>

<?php
$titulo = 'Expediente MG N° ' . (int) $expediente['id_expediente_mg'];
$descripcion = 'Ficha completa del expediente con asignación de tutor (HU-024/025).';
$icono = 'bi-folder2-open';
$contador = mgExpEstadoLabel($estadoActual);
$colorContador = 'info';
$migas = [
    ['texto' => 'Expedientes MG', 'url' => '/controllers/mg_expedientes_listar.php'],
    ['texto' => 'Expediente N° ' . (int) $expediente['id_expediente_mg'], 'actual' => true],
];
$accion = '<a href="/controllers/mg_expedientes_listar.php" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"><i class="bi bi-arrow-left"></i> Volver</a>';
include __DIR__ . '/../../partials/page_header.php';
?>

<!-- Tarjetas resumen del expediente -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card card-custom h-100">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3">
          <span class="avatar avatar-student" aria-hidden="true"><?= htmlspecialchars(mb_substr($expediente['estudiante'], 0, 2)) ?></span>
          <div>
            <div class="text-muted text-uppercase small fw-semibold">Estudiante</div>
            <div class="fw-bold"><?= htmlspecialchars($expediente['estudiante']) ?></div>
            <div class="small text-muted">
              <?= htmlspecialchars($expediente['estudiante_ru']) ?> · @<?= htmlspecialchars($expediente['estudiante_correo']) ?>
            </div>
          </div>
        </div>
        <hr>
        <div class="d-flex flex-column gap-1 small">
          <span><i class="bi bi-mortarboard me-1 text-primary"></i><?= htmlspecialchars($expediente['carrera']) ?></span>
          <span class="text-muted">Semestre <?= (int) $expediente['estudiante_semestre'] ?> · <?= (int) $expediente['estudiante_materias'] ?> materias completadas</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card card-custom h-100">
      <div class="card-body">
        <div class="text-muted text-uppercase small fw-semibold mb-2">Modalidad y cohorte</div>
        <div class="fw-bold fs-6 d-flex align-items-center gap-2">
          <i class="bi bi-diagram-3 text-primary"></i><?= htmlspecialchars($nombreModalidad) ?>
        </div>
        <div class="small text-muted mb-2">Flujo: <?= htmlspecialchars(mgFlujoLabel($expediente['modalidad_flujo'])) ?></div>
        <div class="d-flex flex-wrap gap-2 mt-2">
          <span class="badge bg-light text-dark border px-2 py-1">
            <i class="bi bi-calendar-range me-1 text-primary"></i>Cohorte <?= htmlspecialchars($expediente['cohorte']) ?>
          </span>
          <?php if ($requiereTutor): ?>
            <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle px-2 py-1"><i class="bi bi-person-check me-1"></i>Requiere tutor</span>
          <?php else: ?>
            <span class="badge bg-light text-muted border px-2 py-1"><i class="bi bi-person-dash me-1"></i>Sin tutor</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card card-custom h-100">
      <div class="card-body">
        <div class="text-muted text-uppercase small fw-semibold mb-2">Solicitud</div>
        <div class="small d-flex flex-column gap-1">
          <span><i class="bi bi-clock me-1 text-primary"></i><?= htmlspecialchars(date('d/m/Y H:i', strtotime($expediente['fecha_solicitud']))) ?></span>
          <span class="text-muted"><?= $expediente['solicitud_detalle'] ? htmlspecialchars($expediente['solicitud_detalle']) : 'Sin detalle adicional.' ?></span>
          <?php if ($estadoActual === 'validado' && $expediente['validador_nombre']): ?>
            <span class="text-success small"><i class="bi bi-person-check-fill me-1"></i>Validado por <?= htmlspecialchars($expediente['validador_nombre']) ?></span>
          <?php endif; ?>
          <?php if ($expediente['resolucion_admin']): ?>
            <span class="text-muted">Resolución: <?= htmlspecialchars($expediente['resolucion_admin']) ?></span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Avance del expediente -->
<div class="card card-custom shadow-sm mb-4">
  <div class="card-body">
    <div class="d-flex align-items-center gap-2 mb-3">
      <i class="bi bi-arrows-expand text-primary"></i>
      <h2 class="h6 mb-0">Avance del estado</h2>
    </div>
    <form method="POST" action="/controllers/mg_expediente_estado.php" class="row g-3 align-items-end">
      <?php require_once __DIR__ . '/../../../includes/csrf.php'; echo csrf_campo(); ?>
      <input type="hidden" name="id_expediente_mg" value="<?= (int) $expediente['id_expediente_mg'] ?>">
      <div class="col-md-3">
        <label for="estado-exp" class="form-label fw-semibold text-secondary small text-uppercase">Estado</label>
        <select id="estado-exp" name="estado" class="form-select rounded-3">
          <?php foreach (['solicitado', 'validado', 'tutor_asignado', 'en_curso', 'concluido', 'cerrado'] as $est): ?>
            <option value="<?= $est ?>" <?= $estadoActual === $est ? 'selected' : '' ?>><?= mgExpEstadoLabel($est) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label for="resolucion-exp" class="form-label fw-semibold text-secondary small text-uppercase">Resolución / observación</label>
        <input type="text" id="resolucion-exp" name="resolucion_admin" class="form-control rounded-3"
               maxlength="255" value="<?= htmlspecialchars($expediente['resolucion_admin'] ?? '') ?>"
               placeholder="Referencia de la resolución (opcional)">
      </div>
      <div class="col-md-3">
        <button type="submit" class="btn btn-outline-primary w-100 py-2 rounded-3 d-flex align-items-center justify-content-center gap-2">
          <i class="bi bi-check2-circle"></i><span>Actualizar estado</span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Pestañas: Asignación de tutor + Historial -->
<div class="card card-custom shadow-sm">
  <div class="card-header bg-white py-0 border-0">
    <ul class="nav nav-tabs card-header-tabs pt-3" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active px-4" id="tab-asignacion-tab" data-bs-toggle="tab" data-bs-target="#tab-asignacion" type="button" role="tab" aria-controls="tab-asignacion" aria-selected="true">
          <i class="bi bi-person-plus me-1"></i>Asignación de tutor
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link px-4" id="tab-historial-tab" data-bs-toggle="tab" data-bs-target="#tab-historial" type="button" role="tab" aria-controls="tab-historial" aria-selected="false">
          <i class="bi bi-clock-history me-1"></i>Historial de asignaciones
          <?php $totalHistorial = count($historial); if ($totalHistorial > 0): ?>
            <span class="badge bg-secondary rounded-pill"><?= $totalHistorial ?></span>
          <?php endif; ?>
        </button>
      </li>
    </ul>
  </div>

  <div class="tab-content">
    <!-- TAB 1: Asignación de tutor -->
    <div class="tab-pane fade show active p-4" id="tab-asignacion" role="tabpanel" aria-labelledby="tab-asignacion-tab">
      <?php if (!$requiereTutor): ?>
        <div class="alert alert-info d-flex gap-2 align-items-center mb-0 rounded-3">
          <i class="bi bi-info-circle-fill"></i>
          <span>Esta modalidad (<strong><?= htmlspecialchars($nombreModalidad) ?></strong>) no requiere asignación de tutor. El expediente se gestiona sin esta etapa.</span>
        </div>

      <?php elseif ($asignacionActiva): ?>
        <div class="d-flex flex-column gap-3">
          <div class="alert alert-success d-flex flex-wrap align-items-center justify-content-between gap-2 rounded-3 mb-0">
            <div class="d-flex align-items-center gap-3">
              <span class="avatar avatar-tutor" aria-hidden="true"><?= htmlspecialchars(mb_substr($asignacionActiva['tutor_nombre'], 0, 2)) ?></span>
              <div>
                <div class="fw-bold"><?= htmlspecialchars($asignacionActiva['tutor_nombre']) ?></div>
                <div class="small text-muted"><?= htmlspecialchars($asignacionActiva['tutor_especialidad']) ?> · <?= htmlspecialchars($asignacionActiva['tutor_correo']) ?></div>
                <div class="small text-muted">Asignado el <?= htmlspecialchars(date('d/m/Y H:i', strtotime($asignacionActiva['fecha_asignacion']))) ?></div>
              </div>
            </div>
            <div class="d-flex flex-column gap-2 align-items-end">
              <span class="status-badge status-active">Asignación activa</span>
              <a href="/controllers/mg_carta_tutor.php?id_expediente=<?= (int) $expediente['id_expediente_mg'] ?>" class="btn btn-outline-primary btn-sm rounded-3" target="_blank">
                <i class="bi bi-envelope-paper me-1"></i>Carta de asignación
              </a>
              <form method="POST" action="/controllers/mg_expediente_finalizar_asignacion.php">
                <?php require_once __DIR__ . '/../../../includes/csrf.php'; echo csrf_campo(); ?>
                <input type="hidden" name="id_asignacion_tutor" value="<?= (int) $asignacionActiva['id_asignacion_tutor'] ?>">
                <input type="hidden" name="id_expediente_mg" value="<?= (int) $expediente['id_expediente_mg'] ?>">
                <button type="button" class="btn btn-outline-warning btn-sm rounded-3"
                        onclick="confirmarTransicion('/controllers/mg_expediente_finalizar_asignacion.php', 'Se finalizará la asignación actual. El registro se conservará en el historial.', 'warning')">
                  <i class="bi bi-person-x me-1"></i>Finalizar asignación
                </button>
              </form>
            </div>
          </div>
          <div class="text-muted small">Para reasignar, primero finaliza la asignación actual y luego selecciona un nuevo tutor del panel.
            Adicionalmente, el expediente debe estar en estado <em>solicitado</em> o <em>validado</em> para registrar una nueva asignación.</div>
        </div>

      <?php else: ?>
        <div class="d-flex flex-column gap-4">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
              <h3 class="h6 mb-1">Tutores con afinidad a la carrera del estudiante</h3>
              <p class="small text-muted mb-0">
                Candidatos ordenados por afinidad de materias (<?= htmlspecialchars($expediente['carrera']) ?>).
                Carga recomendada: <strong><?= (int) $cargaRecomendada ?></strong> · carga máxima: <strong><?= (int) $cargaMaxima ?></strong> expedientes activos.
                <?php if ($requiereActa): ?>Se registrará el acta de aceptación automáticamente.<?php endif; ?>
              </p>
            </div>
          </div>

          <form method="POST" action="/controllers/mg_expediente_asignar.php" id="form-asignar-tutor">
            <?php require_once __DIR__ . '/../../../includes/csrf.php'; echo csrf_campo(); ?>
            <input type="hidden" name="id_expediente_mg" value="<?= (int) $expediente['id_expediente_mg'] ?>">

            <?php if (empty($tutoresAfines)): ?>
              <div class="alert alert-warning rounded-3 mb-0">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                No hay tutores registrados con materias de afinidad para la carrera <strong><?= htmlspecialchars($expediente['carrera']) ?></strong>.
                Verifica el catálogo de tutores y sus materias vinculadas.
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-3">
                  <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                      <th class="ps-3" style="width: 40px;">Sel.</th>
                      <th>Tutor</th>
                      <th>Materias de afinidad</th>
                      <th style="width: 200px;">Carga actual</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($tutoresAfines as $cand): ?>
                      <?php
                      $carga = (int) $cand['carga_actual'];
                      $deshabilitado = $carga >= $cargaMaxima;
                      $sobreCarga = $carga >= $cargaRecomendada;
                      $afinidad = (int) $cand['afinidad'];
                      ?>
                      <tr class="<?= $deshabilitado ? 'opacity-50' : '' ?>">
                        <td class="ps-3">
                          <input class="form-check-input" type="radio" name="id_tutor" value="<?= (int) $cand['id_tutor'] ?>"
                                 <?= $deshabilitado ? 'disabled' : '' ?> required>
                        </td>
                        <td>
                          <div class="fw-bold d-flex align-items-center gap-2">
                            <i class="bi bi-person-video3 text-primary opacity-75"></i>
                            <?= htmlspecialchars($cand['tutor_nombre']) ?>
                            <?php if ($afinidad >= 2): ?>
                              <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">Alta afinidad</span>
                            <?php endif; ?>
                          </div>
                          <div class="small text-muted"><?= htmlspecialchars($cand['tutor_especialidad']) ?></div>
                        </td>
                        <td>
                          <?php if ($cand['materias_afines'] !== ''): ?>
                            <div class="d-flex flex-wrap gap-1">
                              <?php foreach (array_slice(explode(', ', $cand['materias_afines']), 0, 4) as $materia): ?>
                                <span class="badge bg-light text-dark border px-2 py-1"><?= htmlspecialchars($materia) ?></span>
                              <?php endforeach; ?>
                              <?php if (count(explode(', ', $cand['materias_afines'])) > 4): ?>
                                <span class="small text-muted">+<?= count(explode(', ', $cand['materias_afines'])) - 4 ?> más</span>
                              <?php endif; ?>
                            </div>
                          <?php else: ?>
                            <span class="text-muted small">—</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 8px;">
                              <div class="progress-bar <?= $deshabilitado ? 'bg-danger' : ($sobreCarga ? 'bg-warning' : 'bg-success') ?>"
                                   style="width: <?= min(100, round($carga / max(1, $cargaMaxima) * 100)) ?>%"></div>
                            </div>
                            <span class="badge <?= $deshabilitado ? 'bg-danger-subtle text-danger border' : ($sobreCarga ? 'bg-warning-subtle text-warning border' : 'bg-light text-dark border') ?> px-2 py-1">
                              <?= $carga ?> / <?= (int) $cargaRecomendada ?>
                            </span>
                          </div>
                          <?php if ($deshabilitado): ?>
                            <div class="small text-danger fw-semibold mt-1"><i class="bi bi-exclamation-circle me-1"></i>Carga máxima alcanzada</div>
                          <?php elseif ($sobreCarga): ?>
                            <div class="small text-warning fw-semibold mt-1"><i class="bi bi-exclamation-triangle me-1"></i>Sobre la carga recomendada</div>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

              <div class="row g-3 align-items-end">
                <div class="col-md-8">
                  <label for="observaciones-asig" class="form-label fw-semibold text-secondary small text-uppercase">Observaciones de la asignación</label>
                  <input type="text" id="observaciones-asig" name="observaciones" class="form-control rounded-3" maxlength="255"
                         placeholder="Motivo o nota de la asignación (opcional)">
                </div>
                <div class="col-md-4">
                  <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-person-plus-fill"></i><span>Asignar tutor</span>
                  </button>
                </div>
              </div>
            <?php endif; ?>
          </form>
        </div>
      <?php endif; ?>
    </div>

    <!-- TAB 2: Historial de asignaciones -->
    <div class="tab-pane fade p-4" id="tab-historial" role="tabpanel" aria-labelledby="tab-historial-tab">
      <?php if (empty($historial)): ?>
        <div class="text-center py-5 text-muted">
          <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary"></i>
          Aún no hay asignaciones de tutor registradas para este expediente.
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
              <tr>
                <th class="ps-3">#</th>
                <th>Tutor</th>
                <th>Operador</th>
                <th>Fecha de asignación</th>
                <th>Estado</th>
                <th class="pe-3">Observaciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($historial as $h): ?>
                <tr>
                  <td class="ps-3 text-muted fw-semibold"><?= (int) $h['id_asignacion_tutor'] ?></td>
                  <td>
                    <div class="fw-bold"><?= htmlspecialchars($h['tutor_nombre']) ?></div>
                    <div class="small text-muted"><?= htmlspecialchars($h['tutor_especialidad']) ?></div>
                  </td>
                  <td class="small"><?= htmlspecialchars($h['operador_nombre']) ?></td>
                  <td class="small"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($h['fecha_asignacion']))) ?></td>
                  <td>
                    <?php if ($h['estado'] === 'activa'): ?>
                      <span class="status-badge status-active">Activa</span>
                    <?php else: ?>
                      <span class="status-badge status-inactive">Finalizada</span>
                    <?php endif; ?>
                  </td>
                  <td class="pe-3 small text-muted"><?= $h['observaciones'] ? htmlspecialchars($h['observaciones']) : '—' ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>