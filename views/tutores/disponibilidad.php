<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Gestión de Tutor y Horarios - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
      <i class="bi bi-calendar-range text-primary"></i>
      <span>Horarios y Especialidades: Prof. <?= htmlspecialchars($tutor['nombre'] . ' ' . $tutor['apellido']) ?></span>
    </h2>
    <p class="text-muted mb-0">Configura la disponibilidad horaria semanal y las materias que domina este docente.</p>
  </div>
  <a href="<?= ($_SESSION['rol'] === 'tutor') ? '../views/tutor/panel.php' : 'tutores_listar.php' ?>" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
    <i class="bi bi-arrow-left"></i> Volver
  </a>
</div>

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
  <!-- Columna 1: Horarios de Disponibilidad -->
  <div class="col-lg-6">
    <div class="card card-custom p-4 h-100">
      <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-clock-history text-primary"></i>
        <span>Bloques de Horarios Semanales</span>
      </h5>

      <!-- Lista de horarios actuales -->
      <div class="mb-4">
        <?php if (!empty($disponibilidades)): ?>
          <div class="list-group list-group-flush">
            <?php foreach ($disponibilidades as $d): ?>
              <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                <div>
                  <span class="badge bg-primary px-2 py-1 me-2"><?= htmlspecialchars($d['dia_semana']) ?></span>
                  <span class="fw-semibold text-dark"><?= substr($d['hora_inicio'], 0, 5) ?> - <?= substr($d['hora_fin'], 0, 5) ?></span>
                </div>
                 <a href="tutores_disponibilidad.php?id=<?= $idTutor ?>&accion=eliminar_horario&id_disponibilidad=<?= $d['id_disponibilidad'] ?>" 
                   class="btn btn-outline-danger btn-sm rounded-circle p-1" style="width: 28px; height: 28px;"
                   onclick="confirmarEliminacion(this.href, '¿Eliminar este bloque horario?'); return false;" title="Eliminar">
                  <i class="bi bi-trash"></i>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="p-3 bg-light rounded-3 text-center text-muted small">
            No hay horarios registrados. Agrega uno a continuación.
          </div>
        <?php endif; ?>
      </div>

      <!-- Formulario para agregar horario -->
      <div class="p-3 bg-light rounded-3 border">
        <h6 class="fw-bold mb-2 small text-uppercase text-secondary">Agregar Nuevo Bloque</h6>
        <form method="POST" class="row g-2">
          <?php require_once __DIR__ . '/../../includes/csrf.php'; echo csrf_campo(); ?>
          <input type="hidden" name="accion" value="agregar_horario">
          <div class="col-12">
            <select name="dia_semana" class="form-select form-select-sm" required>
              <option value="" disabled selected>Selecciona día de la semana...</option>
              <option value="Lunes">Lunes</option>
              <option value="Martes">Martes</option>
              <option value="Miercoles">Miércoles</option>
              <option value="Jueves">Jueves</option>
              <option value="Viernes">Viernes</option>
              <option value="Sabado">Sábado</option>
            </select>
          </div>
          <div class="col-6">
            <label class="small text-muted">Hora Inicio</label>
            <input type="time" name="hora_inicio" class="form-control form-control-sm" required>
          </div>
          <div class="col-6">
            <label class="small text-muted">Hora Fin</label>
            <input type="time" name="hora_fin" class="form-control form-control-sm" required>
          </div>
          <div class="col-12 mt-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">
              <i class="bi bi-plus-lg me-1"></i> Agregar Horario
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Columna 2: Materias que domina y Perfil -->
  <div class="col-lg-6">
    <div class="card card-custom p-4 mb-4">
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

    <!-- Perfil docente -->
    <div class="card card-custom p-4">
      <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-person-lines-fill text-primary"></i>
        <span>Perfil Profesional</span>
      </h5>
      <form method="POST">
        <?php echo csrf_campo(); ?>
        <input type="hidden" name="accion" value="actualizar_perfil">
        <div class="mb-3">
          <label class="form-label small text-muted">Especialidad</label>
          <input type="text" name="especialidad" class="form-control form-control-sm" value="<?= htmlspecialchars($tutor['especialidad'] ?? '') ?>" placeholder="Ej: Bases de Datos, Redes...">
        </div>
        <div class="mb-3">
          <label class="form-label small text-muted">Biografía / Presentación</label>
          <textarea name="biografia" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($tutor['biografia'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-light btn-sm w-100 border">
          <i class="bi bi-check2 me-1"></i> Actualizar Perfil
        </button>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
