<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Informes de Avance - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$migas = [
    ['texto' => 'Mi panel', 'url' => '/views/tutor/panel.php'],
    ['texto' => 'Informes de Avance', 'actual' => true],
];
$titulo = 'Informes de Avance';
$descripcion = 'Registra los informes de avance de cada tutoría. El porcentaje acumulado se muestra como evidencia del progreso.';
$icono = 'bi-clipboard2-check-fill';
$accion = '<a href="/views/tutor/panel.php" class="btn btn-outline-secondary rounded-3 px-3"><i class="bi bi-arrow-left me-1"></i>Volver al panel</a>';
include __DIR__ . '/../partials/page_header.php';
?>

<?php if (!empty($errores)): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
    <strong>No se pudo registrar el informe:</strong>
    <ul class="mb-0 mt-1">
      <?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-5">
    <div class="card card-custom shadow-sm h-100">
      <div class="card-header bg-white py-3">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-plus me-1 text-primary"></i>Nuevo informe</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="informes_registrar.php" autocomplete="off">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

          <div class="mb-3">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Tutoría *</label>
            <select name="id_tutoria" class="form-select rounded-3 py-2" required>
              <option value="">Selecciona una tutoría activa</option>
              <?php foreach ($tutoriasProceso as $t): ?>
                <option value="<?= $t['id_tutoria'] ?>" <?= ((int) ($id_tutoria ?? 0) === (int) $t['id_tutoria']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($t['estudiante_nombre'] . ' ' . $t['estudiante_apellido']) ?> — <?= htmlspecialchars($t['nombre_materia']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold text-secondary small text-uppercase">N.º de informe (automático)</label>
              <div class="form-control rounded-3 py-2 bg-light border-secondary-subtle fw-semibold text-primary" id="display-numero-informe">
                Informe #<span id="siguienteNumeroInforme"><?= $id_tutoria > 0 ? $siguienteNumeroInforme : '—' ?></span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold text-secondary small text-uppercase">Porcentaje de avance *</label>
              <div class="input-group">
                <input type="number" name="porcentaje_avance" id="porcentaje-input" class="form-control rounded-start-3 py-2" min="0" max="100" value="<?= htmlspecialchars($_POST['porcentaje_avance'] ?? '') ?>" required>
                <span class="input-group-text rounded-end-3 bg-light">%</span>
              </div>
            </div>
          </div>

          <div class="mb-3 mt-3">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Fecha límite</label>
            <input type="date" name="fecha_limite" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['fecha_limite'] ?? '') ?>">
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Descripción del avance *</label>
            <textarea name="descripcion_avance" class="form-control rounded-3 py-2" rows="3" maxlength="1000" required placeholder="Qué se logró en este informe"><?= htmlspecialchars($_POST['descripcion_avance'] ?? '') ?></textarea>
          </div>

          <button type="submit" class="btn btn-primary w-100 rounded-3 py-2"><i class="bi bi-file-earmark-check me-1"></i>Registrar informe</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <?php if ($id_tutoria > 0): ?>
      <div class="card card-custom shadow-sm mb-3">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold text-dark">Avance acumulado</span>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-1 fs-6"><?= $porcentajeAcumulado ?>%</span>
          </div>
          <div class="progress rounded-pill" style="height: 12px;">
            <div class="progress-bar progress-bar-striped" role="progressbar" style="width: <?= $porcentajeAcumulado ?>%; background: #01af9f;" aria-valuenow="<?= $porcentajeAcumulado ?>" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <small class="text-muted"><?= $totalRegistrados ?> informe(s) registrado(s). Meta: 100%.</small>
        </div>
      </div>
    <?php endif; ?>

    <div class="card card-custom shadow-sm">
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-text me-1 text-primary"></i>Informes registrados</h5>
        <?php if ($id_tutoria > 0): ?><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">Tutoría #<?= $id_tutoria ?></span><?php endif; ?>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4">Informe</th>
              <th>Avance</th>
              <th>Registrado</th>
              <th>Descripción</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($informes)): ?>
              <tr>
                <td colspan="4" class="text-center py-5">
                  <?php
                  $emptyTitulo = ($id_tutoria > 0) ? 'Sin informes registrados' : 'Selecciona una tutoría';
                  $emptyTexto = ($id_tutoria > 0) ? 'Aún no hay informes de avance para esta tutoría.' : 'Elige una tutoría activa a la izquierda para ver sus informes.';
                  include __DIR__ . '/../partials/empty_state.php';
                  ?>
                </td>
              </tr>
            <?php endif; ?>
            <?php foreach ($informes as $inf): ?>
              <tr>
                <td class="ps-4">
                  <span class="fw-bold text-dark">Informe #<?= $inf['numero_informe'] ?></span>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1 rounded-pill" style="height: 8px;">
                      <div class="progress-bar" role="progressbar" style="width: <?= $inf['porcentaje_avance'] ?>%; background: #01af9f;" aria-valuenow="<?= $inf['porcentaje_avance'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="small fw-semibold text-primary"><?= $inf['porcentaje_avance'] ?>%</span>
                  </div>
                </td>
                <td class="text-muted small"><?= date('d/m/Y', strtotime($inf['fecha_registro'])) ?></td>
                <td class="text-muted small text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($inf['descripcion_avance']) ?>"><?= htmlspecialchars($inf['descripcion_avance']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>