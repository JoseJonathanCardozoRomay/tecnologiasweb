<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Evaluación de Defensa MG - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<?php
$titulo = 'Evaluación de Defensa';
$descripcion = 'Captura de calificaciones por tribunal y promedio automático (HU-031).';
$icono = 'bi-clipboard2-check';
$contador = '';
$accion = '<a href="/controllers/mg_defensas_listar.php?cohorte=' . (int) $defensa['id_cohorte_mg'] . '" class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3"><i class="bi bi-arrow-left"></i><span class="fw-semibold">Volver</span></a>';
include __DIR__ . '/../../partials/page_header.php';
?>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card card-custom shadow-sm">
      <div class="card-header bg-white border-0 py-3">
        <h2 class="h6 mb-0 fw-bold">Defensa programada</h2>
      </div>
      <div class="card-body pt-0">
        <div class="row g-3 small">
          <div class="col-md-4">
            <div class="text-muted text-uppercase" style="font-size: 0.7rem;">Estudiante</div>
            <div class="fw-bold text-dark"><?= htmlspecialchars($defensa['estudiante_apellido'] . ' ' . $defensa['estudiante_nombre']) ?></div>
            <div class="text-muted"><?= htmlspecialchars($defensa['registro_universitario']) ?></div>
          </div>
          <div class="col-md-4">
            <div class="text-muted text-uppercase" style="font-size: 0.7rem;">Modalidad</div>
            <div class="fw-semibold"><?= htmlspecialchars($defensa['modalidad_nombre']) ?></div>
            <div class="text-muted"><?= htmlspecialchars($defensa['nombre_periodo']) ?></div>
          </div>
          <div class="col-md-4">
            <div class="text-muted text-uppercase" style="font-size: 0.7rem;">Fecha y hora</div>
            <div class="fw-semibold"><?= htmlspecialchars($defensa['fecha_defensa']) ?></div>
            <div class="text-muted"><?= htmlspecialchars($defensa['hora_inicio']) ?> – <?= htmlspecialchars($defensa['hora_fin']) ?> · <?= htmlspecialchars((string) $defensa['ambiente_nombre']) ?></div>
          </div>
        </div>
      </div>
    </div>

    <div class="card card-custom shadow-sm mt-4">
      <div class="card-header bg-white border-0 py-3">
        <h2 class="h6 mb-0 fw-bold">Notas del tribunal</h2>
      </div>
      <div class="card-body pt-0">
        <form method="POST" action="/controllers/mg_defensa_evaluar.php?id=<?= (int) $id ?>">
          <?= csrf_campo(); ?>
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem;">
                <tr>
                  <th class="ps-3">Rol</th>
                  <th>Docente</th>
                  <th style="width: 150px;">Nota (0-100)</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($defensa['tribunales'] as $t): ?>
                  <tr>
                    <td class="ps-3">
                      <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1 text-capitalize"><?= htmlspecialchars($t['rol']) ?></span>
                    </td>
                    <td>
                      <div class="fw-semibold"><?= htmlspecialchars($t['apellido'] . ' ' . $t['nombre']) ?></div>
                      <?php if ($t['especialidad']): ?>
                        <div class="small text-muted"><?= htmlspecialchars($t['especialidad']) ?></div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <input type="number" step="0.01" min="0" max="100" class="form-control rounded-3"
                             name="nota[<?= (int) $t['id_tribunal_defensa_mg'] ?>]"
                             value="<?= htmlspecialchars(isset($notasActuales[$t['id_tribunal_defensa_mg']]) ? $notasActuales[$t['id_tribunal_defensa_mg']] : ($t['nota'] !== null ? (string) $t['nota'] : '')) ?>"
                             required>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="alert alert-info small rounded-3 mt-3 mb-0">
            El promedio se calcula automáticamente al guardar.
            Nota de aprobación: <strong><?= number_format($notaAprob, 0) ?>/100</strong>
            (<code>parámetro nota_aprobacion_mg</code>).
          </div>

          <?php if (isset($errores) && !empty($errores)): ?>
            <div class="alert alert-danger small rounded-3 mt-3 mb-0">
              <ul class="mb-0 ps-3">
                <?php foreach ($errores as $e): ?>
                  <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-4 rounded-3">
              <i class="bi bi-check2-circle me-1"></i> Guardar evaluación
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card card-custom shadow-sm">
      <div class="card-header bg-white border-0 py-3">
        <h2 class="h6 mb-0 fw-bold">Resultado</h2>
      </div>
      <div class="card-body pt-0">
        <div class="d-flex flex-column gap-2">
          <div class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
            <span class="text-muted small">Estado defensa</span><?= mgDefensaEstadoBadge($defensa['estado']) ?>
          </div>
          <div class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
            <span class="text-muted small">Nota final (promedio)</span>
            <span class="fw-bold fs-5 <?= $defensa['nota_final'] !== null && (float) $defensa['nota_final'] >= $notaAprob ? 'text-success' : 'text-secondary' ?>">
              <?= $defensa['nota_final'] !== null ? number_format((float) $defensa['nota_final'], 2) : '—' ?>
            </span>
          </div>
          <div class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
            <span class="text-muted small">Resultado</span><?= mgResultadoBadge($defensa['resultado']) ?>
          </div>
        </div>
        <p class="small text-muted mt-3 mb-0">
          Al guardar la evaluación, el expediente pasa a estado
          <strong>concluido</strong> (aprobado) o <strong>cerrado</strong> (reprobado).
        </p>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>