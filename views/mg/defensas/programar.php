<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = ($esEdicion ? 'Editar' : 'Programar') . ' Defensa MG - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<?php
$titulo = $esEdicion ? 'Editar Defensa Programada' : 'Programar Nueva Defensa';
$descripcion = 'Agenda la defensa validando disponibilidad de ambiente y agenda de docentes (HU-029).';
$icono = 'bi-calendar2-plus';
$contador = '';
$accion = '<a href="/controllers/mg_defensas_listar.php' . ($esEdicion ? '?cohorte=' . (int) $defensaEdit['id_cohorte_mg'] : '') . '" class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3"><i class="bi bi-arrow-left"></i><span class="fw-semibold">Volver</span></a>';
include __DIR__ . '/../../partials/page_header.php';
?>

<form method="POST" action="/controllers/mg_defensas_programar.php<?= $esEdicion ? '?id=' . (int) $idDefensa : '' ?>" id="form-defensa">
  <?= csrf_campo(); ?>
  <?php if ($esEdicion): ?>
    <input type="hidden" name="id_cohorte_mg" value="<?= (int) $defensaEdit['id_cohorte_mg'] ?>">
    <input type="hidden" name="id_expediente_mg" value="<?= (int) $defensaEdit['id_expediente_mg'] ?>">
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card card-custom shadow-sm">
        <div class="card-header bg-white border-0 py-3">
          <h2 class="h6 mb-0 fw-bold">Datos de la defensa</h2>
        </div>
        <div class="card-body pt-0">
          <div class="row g-3">
            <?php if ($esEdicion): ?>
              <div class="col-12">
                <label class="form-label fw-semibold text-secondary small text-uppercase mb-1">Expediente (fijo)</label>
                <div class="bg-light rounded-3 p-3">
                  <div class="fw-bold text-dark">
                    <?= htmlspecialchars($defensaEdit['estudiante_apellido'] . ' ' . $defensaEdit['estudiante_nombre']) ?>
                  </div>
                  <div class="small text-muted">
                    <?= htmlspecialchars($defensaEdit['registro_universitario']) ?> ·
                    <?= htmlspecialchars($defensaEdit['modalidad_nombre']) ?> ·
                    <?= htmlspecialchars($defensaEdit['nombre_periodo']) ?>
                  </div>
                </div>
              </div>
            <?php else: ?>
              <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small text-uppercase mb-1" for="sel-df-cohorte">Cohorte</label>
                <select id="sel-df-cohorte" name="id_cohorte_mg" class="form-select rounded-3" onchange="this.form.submit()">
                  <?php foreach ($cohortes as $c): ?>
                    <option value="<?= (int) $c['id_cohorte_mg'] ?>" <?= (int) $c['id_cohorte_mg'] === $selCohorte ? 'selected' : '' ?>>
                      <?= htmlspecialchars($c['nombre_periodo']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small text-uppercase mb-1" for="sel-df-exp">Expediente</label>
                <select id="sel-df-exp" name="id_expediente_mg" class="form-select rounded-3" required>
                  <option value="">Seleccionar expediente...</option>
                  <?php foreach ($candidatos as $cand): ?>
                    <option value="<?= (int) $cand['id_expediente_mg'] ?>"
                      <?= (int) $cand['id_expediente_mg'] === $selExpediente ? 'selected' : '' ?>>
                      <?= htmlspecialchars($cand['registro_universitario'] . ' · ' . $cand['estudiante_apellido'] . ' ' . $cand['estudiante_nombre'] . ' · ' . $cand['modalidad_nombre']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            <?php endif; ?>

            <div class="col-md-4">
              <label class="form-label fw-semibold text-secondary small text-uppercase mb-1" for="in-fecha-df">Fecha</label>
              <input type="date" id="in-fecha-df" name="fecha_defensa" value="<?= htmlspecialchars($sFecha) ?>" class="form-control rounded-3" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold text-secondary small text-uppercase mb-1" for="in-hini-df">Hora inicio</label>
              <input type="time" id="in-hini-df" name="hora_inicio" value="<?= htmlspecialchars($sHIni) ?>" class="form-control rounded-3" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold text-secondary small text-uppercase mb-1" for="in-hfin-df">Hora fin</label>
              <input type="time" id="in-hfin-df" name="hora_fin" value="<?= htmlspecialchars($sHFin) ?>" class="form-control rounded-3" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold text-secondary small text-uppercase mb-1" for="sel-df-amb">Ambiente / Aula</label>
              <select id="sel-df-amb" name="id_ambiente_mg" class="form-select rounded-3" required>
                <option value="">Seleccionar ambiente...</option>
                <?php foreach ($ambientes as $a): ?>
                  <option value="<?= (int) $a['id_ambiente_mg'] ?>" <?= (int) $a['id_ambiente_mg'] === $selAmbiente ? 'selected' : '' ?>>
                    <?= htmlspecialchars($a['nombre'] . ' · ' . $a['ubicacion'] . ' (cap. ' . (int) $a['capacidad'] . ')') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold text-secondary small text-uppercase mb-1" for="in-obs-df">Observaciones</label>
              <input type="text" id="in-obs-df" name="observaciones" value="<?= htmlspecialchars($sObs) ?>" maxlength="255" class="form-control rounded-3">
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card card-custom shadow-sm">
        <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
          <h2 class="h6 mb-0 fw-bold">Tribunales (afines a la carrera)</h2>
          <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1" id="contador-tribunales">0 / <?= (int) $maxTribunales ?></span>
        </div>
        <div class="card-body pt-0">
          <div class="alert alert-warning small rounded-3 py-2">
            Se requieren de <strong><?= (int) $minTribunales ?></strong> a <strong><?= (int) $maxTribunales ?></strong> docentes
            afines a la carrera del estudiante. Se validará que el ambiente y la agenda de cada docente y del tutor
            no se crucen con otra defensa.
          </div>
          <?php if ($tutorActivo): ?>
            <div class="small text-muted mb-2">Tutor del expediente (no puede integrar el tribunal): <strong><?= htmlspecialchars($defensaEdit ? $defensaEdit['tutor_apellido'] . ' ' . $defensaEdit['tutor_nombre'] : '') ?></strong></div>
          <?php endif; ?>

          <?php if (empty($docentesAfin)): ?>
            <div class="alert alert-danger small rounded-3 mb-0">
              No se encontraron docentes afines a la carrera de este estudiante.
              Regístralos en <em>gestión académica → docentes/materias</em> antes de programar.
            </div>
          <?php else: ?>
            <div class="d-flex flex-column gap-2" style="max-height: 380px; overflow-y: auto;">
              <?php foreach ($docentesAfin as $d): ?>
                <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 cursor-pointer" style="cursor: pointer;">
                  <input type="checkbox" name="tribunales[]" value="<?= (int) $d['id_tutor'] ?>"
                         class="form-check-input tribunal-check"
                         <?= in_array((int) $d['id_tutor'], $selTribunales, true) ? 'checked' : '' ?>>
                  <span class="flex-grow-1">
                    <span class="d-block fw-semibold small"><?= htmlspecialchars($d['apellido'] . ' ' . $d['nombre']) ?></span>
                    <?php if ($d['especialidad']): ?>
                      <span class="d-block text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($d['especialidad']) ?></span>
                    <?php endif; ?>
                  </span>
                </label>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <?php if (!empty($errores)): ?>
    <div class="alert alert-danger mt-4 rounded-3">
      <strong>No se pudo guardar la defensa:</strong>
      <ul class="mb-0 mt-1 ps-3">
        <?php foreach ($errores as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="d-flex justify-content-end gap-2 mt-4">
    <a href="/controllers/mg_defensas_listar.php<?= $esEdicion ? '?cohorte=' . (int) $defensaEdit['id_cohorte_mg'] : '' ?>" class="btn btn-outline-secondary px-4 rounded-3">Cancelar</a>
    <button type="submit" class="btn btn-primary px-4 rounded-3">
      <i class="bi bi-check2-circle me-1"></i> <?= $esEdicion ? 'Guardar cambios' : 'Programar defensa' ?>
    </button>
  </div>
</form>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>

<script>
(function () {
  var max = <?= (int) $maxTribunales ?>;
  var contador = document.getElementById('contador-tribunales');
  var checks = document.querySelectorAll('.tribunal-check');

  function actualizar() {
    var n = document.querySelectorAll('.tribunal-check:checked').length;
    contador.textContent = n + ' / ' + max;
  }
  checks.forEach(function (c) {
    c.addEventListener('change', function () {
      var n = document.querySelectorAll('.tribunal-check:checked').length;
      if (this.checked && n > max) {
        this.checked = false;
        alert('Máximo ' + max + ' tribunales por defensa.');
      }
      actualizar();
    });
  });
  actualizar();
})();
</script>