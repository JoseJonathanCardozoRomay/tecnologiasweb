<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Solicitar Tutoría Académica - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-8 col-xl-7">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h3 class="fw-bold mb-0 d-flex align-items-center gap-2">
        <i class="bi bi-calendar-plus-fill text-primary"></i>
        <span>Agendar Sesión de Tutoría</span>
      </h3>
      <a href="<?= ($_SESSION['rol'] === 'estudiante') ? '../views/estudiante/panel.php' : 'tutorias_listar.php' ?>" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

    <?php if (!empty($errores)): ?>
      <div class="alert alert-danger py-2 px-3 rounded-3 shadow-sm mb-4">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-1"></i> Corrige los siguientes datos:</div>
        <ul class="mb-0 ps-3 small">
          <?php foreach ($errores as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="card card-custom p-4 p-md-5">
      <form method="POST" autocomplete="off">
        <?php require_once __DIR__ . '/../../includes/csrf.php'; echo csrf_campo(); ?>
        <div class="row g-3">
          <?php if (($_SESSION['rol'] ?? '') === 'administrador'): ?>
            <div class="col-md-12">
              <label class="form-label fw-semibold text-secondary small text-uppercase">Estudiante *</label>
              <select name="id_estudiante" class="form-select rounded-3 py-2" required>
                <option value="" disabled <?= empty($_POST['id_estudiante']) ? 'selected' : '' ?>>Selecciona al estudiante...</option>
                <?php foreach ($estudiantes as $estudianteItem): ?>
                  <option value="<?= $estudianteItem['id_estudiante'] ?>" <?= (($_POST['id_estudiante'] ?? '') == $estudianteItem['id_estudiante']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($estudianteItem['nombre'] . ' ' . $estudianteItem['apellido']) ?> — <?= htmlspecialchars($estudianteItem['registro_universitario']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php endif; ?>
          <!-- Materia -->
          <div class="col-md-12">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Materia Académica *</label>
            <select name="id_materia" class="form-select rounded-3 py-2" required>
              <option value="" disabled selected>Selecciona la materia que deseas reforzar...</option>
              <?php foreach ($materias as $m): ?>
                <option value="<?= $m['id_materia'] ?>" <?= (isset($_POST['id_materia']) && $_POST['id_materia'] == $m['id_materia']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($m['nombre_materia']) ?> (<?= htmlspecialchars($m['nombre_carrera'] ?? 'General') ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Periodo Académico *</label>
            <select name="periodo" class="form-select rounded-3 py-2" required>
              <?php $periodoActual = $_POST['periodo'] ?? 'I-' . date('Y'); ?>
              <?php foreach ($periodos as $periodo): ?>
                <option value="<?= $periodo ?>" <?= $periodoActual === $periodo ? 'selected' : '' ?>><?= $periodo ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Tutor -->
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Docente Tutor *</label>
            <select name="id_tutor" class="form-select rounded-3 py-2" required>
              <option value="" disabled selected>Selecciona al tutor académico...</option>
              <?php foreach ($tutores as $t): ?>
                <option value="<?= $t['id_tutor'] ?>" <?= (isset($_POST['id_tutor']) && $_POST['id_tutor'] == $t['id_tutor']) ? 'selected' : '' ?>>
                  Prof. <?= htmlspecialchars($t['nombre'] . ' ' . $t['apellido']) ?> <?= !empty($t['especialidad']) ? '— ' . htmlspecialchars($t['especialidad']) : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Fecha -->
          <div class="col-md-4">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Fecha de la Sesión *</label>
            <input type="date" name="fecha" class="form-control rounded-3 py-2" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['fecha'] ?? date('Y-m-d')) ?>" required>
          </div>

          <!-- Hora Inicio -->
          <div class="col-md-4">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Hora Inicio *</label>
            <input type="time" name="hora_inicio" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['hora_inicio'] ?? '15:00') ?>" required>
          </div>

          <!-- Hora Fin -->
          <div class="col-md-4">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Hora Fin *</label>
            <input type="time" name="hora_fin" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['hora_fin'] ?? '16:00') ?>" required>
          </div>

          <!-- Modalidad -->
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Modalidad *</label>
            <select name="modalidad" class="form-select rounded-3 py-2" required>
              <option value="presencial" <?= (isset($_POST['modalidad']) && $_POST['modalidad'] === 'presencial') ? 'selected' : '' ?>>Presencial (En campus UPDS)</option>
              <option value="virtual" <?= (isset($_POST['modalidad']) && $_POST['modalidad'] === 'virtual') ? 'selected' : '' ?>>Virtual (Meet / Teams / Zoom)</option>
            </select>
          </div>

          <!-- Lugar o Enlace -->
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Lugar o Enlace</label>
            <input type="text" name="lugar_o_enlace" class="form-control rounded-3 py-2" placeholder="Ej: Aula 204 o https://meet.google.com/..." value="<?= htmlspecialchars($_POST['lugar_o_enlace'] ?? '') ?>">
          </div>

          <!-- Observaciones / Temas -->
          <div class="col-12">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Temas o Preguntas a Tratar</label>
            <textarea name="observaciones" class="form-control rounded-3" rows="3" placeholder="Describe brevemente las dudas o temas puntuales que necesitas repasar con el tutor..."><?= htmlspecialchars($_POST['observaciones'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
          <a href="<?= ($_SESSION['rol'] === 'estudiante') ? '../views/estudiante/panel.php' : 'tutorias_listar.php' ?>" class="btn btn-light px-4 py-2 rounded-3">Cancelar</a>
          <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-send-fill"></i>
            <span>Confirmar y Enviar Solicitud</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
