<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Nueva Modalidad de Grado - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-7">
    <?php
    $titulo = 'Nueva Modalidad de Grado';
    $descripcion = 'Registra una modalidad del catálogo RN-MG-01 en el sistema.';
    $icono = 'bi-plus-circle-fill';
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

    <div class="card card-custom p-4 p-md-5">
      <form method="POST" autocomplete="off">
        <?php require_once __DIR__ . '/../../../includes/csrf.php'; echo csrf_campo(); ?>

        <div class="mb-4">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Nombre de la Modalidad *</label>
          <input type="text" name="nombre" class="form-control rounded-3 py-2"
                 value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" maxlength="80"
                 placeholder="Ej: Proyecto de Grado, Tesis..." required autofocus>
        </div>

        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Flujo del proceso *</label>
            <select name="flujo" class="form-select rounded-3 py-2" required>
              <?php foreach ($flujos as $flujo): ?>
                <option value="<?= htmlspecialchars($flujo['valor']) ?>"
                        <?= (($_POST['flujo'] ?? '') === $flujo['valor']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($flujo['label']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase d-block">Estado</label>
            <div class="form-check form-switch mt-2">
              <input class="form-check-input" type="checkbox" name="activa" id="mod-activa" value="1" checked>
              <label class="form-check-label" for="mod-activa">Modalidad activa</label>
            </div>
          </div>
        </div>

        <div class="mb-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="requiere_tutor" id="mod-tutor" value="1" checked>
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
            <span>Guardar Modalidad</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>