<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Editar Modalidad de Grado - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-7">
    <?php
    $titulo = 'Editar Modalidad de Grado';
    $descripcion = 'Actualiza los datos de la modalidad seleccionada.';
    $icono = 'bi-pencil-square';
    $accion = '<a href="/controllers/mg_modalidades_listar.php" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"><i class="bi bi-arrow-left"></i> Volver</a>';
    include __DIR__ . '/../../partials/page_header.php';
    ?>

    <?php if (!empty($errores)): ?>
      <div class="alert alert-danger py-2 px-3 rounded-3 shadow-sm mb-4">
        <ul class="mb-0 ps-3 small">
          <?php foreach ($errores as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php
    $nombre = $_POST['nombre'] ?? $modalidad_actual['nombre'] ?? '';
    $flujoSeleccionado = $_POST['flujo'] ?? $modalidad_actual['flujo'] ?? 'perfil_mg';
    $requiereTutor = isset($_POST['requiere_tutor']) ? 1 : (int) ($modalidad_actual['requiere_tutor'] ?? 1);
    $activa = isset($_POST['activa']) ? 1 : (int) ($modalidad_actual['activa'] ?? 1);
    ?>

    <div class="card card-custom p-4 p-md-5">
      <form method="POST" autocomplete="off">
        <?php require_once __DIR__ . '/../../../includes/csrf.php'; echo csrf_campo(); ?>
        <input type="hidden" name="id_modalidad_grado" value="<?= (int) $modalidad_actual['id_modalidad_grado'] ?>">

        <div class="mb-4">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Nombre de la Modalidad *</label>
          <input type="text" name="nombre" class="form-control rounded-3 py-2"
                 value="<?= htmlspecialchars($nombre) ?>" maxlength="80" required autofocus>
        </div>

        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Flujo del proceso *</label>
            <select name="flujo" class="form-select rounded-3 py-2" required>
              <?php foreach ($flujos as $flujo): ?>
                <option value="<?= htmlspecialchars($flujo['valor']) ?>"
                        <?= $flujoSeleccionado === $flujo['valor'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($flujo['label']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase d-block">Estado</label>
            <div class="form-check form-switch mt-2">
              <input class="form-check-input" type="checkbox" name="activa" id="mod-activa" value="1" <?= $activa ? 'checked' : '' ?>>
              <label class="form-check-label" for="mod-activa">Modalidad activa</label>
            </div>
          </div>
        </div>

        <div class="mb-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="requiere_tutor" id="mod-tutor" value="1" <?= $requiereTutor ? 'checked' : '' ?>>
            <label class="form-check-label" for="mod-tutor">
              <strong>Requiere asignación de tutor</strong>
              <span class="d-block small text-muted">Se habilita la asignación de tutor en el expediente (flujo con perfil de grado).</span>
            </label>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
          <a href="/controllers/mg_modalidades_listar.php" class="btn btn-light px-4 py-2 rounded-3">Cancelar</a>
          <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-save"></i>
            <span>Guardar Cambios</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>