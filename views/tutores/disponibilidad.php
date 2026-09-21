<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Gestión de Tutor y Horarios - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$titulo = "Horarios y Especialidades: Prof. {$tutor['nombre']} {$tutor['apellido']}";
$descripcion = 'Configura la disponibilidad horaria semanal y las materias que domina este docente.';
$icono = 'bi-calendar-range';
$accion = '<a href="' . (($_SESSION['rol'] === 'tutor') ? '../views/tutor/panel.php' : 'tutores_listar.php') . '" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"><i class="bi bi-arrow-left"></i> Volver</a>';
include __DIR__ . '/../partials/page_header.php';
?>
<?php /* ' ' . $tutor['apellido'] ) */ ?>
  <?php if (!empty($errores)): ?>
  <div class="alert alert-danger py-2 px-3 rounded-3 mb-4">
    <ul class="mb-0 ps-3 small">
      <?php foreach ($errores as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="row g-4">
  <!-- Columna 1: Bloques Horarios Predefinidos -->
  <div class="col-lg-6">
    <div class="card card-custom p-4 h-100">
      <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-clock-history text-primary"></i>
        <span>Bloques Horarios Disponibles</span>
      </h5>
      <p class="text-muted small mb-3">Selecciona los bloques horarios definidos por coordinación que corresponden a tu disponibilidad.</p>

      <!-- Lista de bloques seleccionados -->
      <div class="mb-4">
        <?php if (!empty($bloquesSeleccionados)): ?>
          <div class="list-group list-group-flush">
            <?php foreach ($bloquesSeleccionados as $b): ?>
              <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                <div>
                  <span class="badge bg-primary px-2 py-1 me-2"><?= htmlspecialchars($b['nombre_bloque']) ?></span>
                  <span class="fw-semibold text-dark"><?= substr($b['hora_inicio'], 0, 5) ?> - <?= substr($b['hora_fin'], 0, 5) ?></span>
                  <?php if (!empty($b['descripcion'])): ?>
                    <small class="text-muted d-block"><?= htmlspecialchars($b['descripcion']) ?></small>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="p-3 bg-light rounded-3 text-center text-muted small">
            No has seleccionado bloques horarios. Selecciona uno o más a continuación.
          </div>
        <?php endif; ?>
      </div>

      <!-- Formulario para seleccionar bloques -->
      <div class="p-3 bg-light rounded-3 border">
        <h6 class="fw-bold mb-2 small text-uppercase text-secondary">Seleccionar Bloques Disponibles</h6>
        <?php if (!empty($bloquesDisponibles)): ?>
          <form method="POST">
            <?php echo csrf_campo(); ?>
            <input type="hidden" name="accion" value="guardar_bloques">
            <div class="mb-3" style="max-height: 220px; overflow-y: auto;">
              <?php foreach ($bloquesDisponibles as $bloque): ?>
                <div class="form-check py-1">
                  <input class="form-check-input" type="checkbox" name="bloques[]" value="<?= $bloque['id_bloque'] ?>" id="bloque_<?= $bloque['id_bloque'] ?>"
                    <?= in_array($bloque['id_bloque'], $idsBloquesSeleccionados) ? 'checked' : '' ?>>
                  <label class="form-check-label small fw-medium" for="bloque_<?= $bloque['id_bloque'] ?>">
                    <?= htmlspecialchars($bloque['nombre_bloque']) ?> (<?= substr($bloque['hora_inicio'], 0, 5) ?> - <?= substr($bloque['hora_fin'], 0, 5) ?>)
                    <?php if (!empty($bloque['descripcion'])): ?>
                      <span class="text-muted">— <?= htmlspecialchars($bloque['descripcion']) ?></span>
                    <?php endif; ?>
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
            <button type="submit" class="btn btn-primary btn-sm w-100">
              <i class="bi bi-check2 me-1"></i> Guardar Selección de Bloques
            </button>
          </form>
        <?php else: ?>
          <div class="text-center text-muted small">
            <i class="bi bi-exclamation-triangle me-1"></i>
            No hay bloques horarios configurados por coordinación. Contacte a su administrador.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Columna 2: Materias que domina -->
  <div class="col-lg-6">
    <div class="card card-custom p-4">
      <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-journal-check text-primary"></i>
        <span>Materias que Imparte</span>
      </h5>
      <form method="POST">
        <?php echo csrf_campo(); ?>
        <input type="hidden" name="accion" value="guardar_materias">
        <div class="mb-3" style="max-height: 220px; overflow-y: auto;">
          <?php foreach ($todasMaterias as $mat): ?>
            <div class="form-check py-1">
              <input class="form-check-input" type="checkbox" name="materias[]" value="<?= $mat['id_materia'] ?>" id="mat_<?= $mat['id_materia'] ?>"
                <?= in_array($mat['id_materia'], $idsMateriasAsignadas) ? 'checked' : '' ?>>
              <label class="form-check-label small fw-medium" for="mat_<?= $mat['id_materia'] ?>">
                <?= htmlspecialchars($mat['nombre_materia']) ?>
                <span class="text-muted">(<?= htmlspecialchars($mat['nombre_carrera'] ?? 'General') ?>)</span>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
          <i class="bi bi-save me-1"></i> Guardar Materias Asignadas
        </button>
      </form>
    </div>
 </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
