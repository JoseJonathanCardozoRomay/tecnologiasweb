<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Nueva Cohorte - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-7">
    <?php
    $titulo = 'Nueva Cohorte';
    $descripcion = 'Registra un período/gestión para las Modalidades de Grado.';
    $icono = 'bi-plus-circle-fill';
    $accion = '<a href="/controllers/mg_cohortes_listar.php" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"><i class="bi bi-arrow-left"></i> Volver</a>';
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
          <label class="form-label fw-semibold text-secondary small text-uppercase">Período *</label>
          <input type="text" name="nombre_periodo" class="form-control rounded-3 py-2"
                 value="<?= htmlspecialchars($_POST['nombre_periodo'] ?? '') ?>" maxlength="20"
                 placeholder="Ej: 2026-I" required autofocus>
          <div class="form-text">Formato sugerido: Año-Semestre (ej: 2026-I, 2027-II).</div>
        </div>

        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Fecha de inicio *</label>
            <input type="date" name="fecha_inicio" class="form-control rounded-3 py-2"
                   value="<?= htmlspecialchars($_POST['fecha_inicio'] ?? '') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Fecha de fin</label>
            <input type="date" name="fecha_fin" class="form-control rounded-3 py-2"
                   value="<?= htmlspecialchars($_POST['fecha_fin'] ?? '') ?>">
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Estado *</label>
          <select name="estado" class="form-select rounded-3 py-2" required>
            <?php foreach (['planificada' => 'Planificada', 'abierta' => 'Abierta', 'cerrada' => 'Cerrada'] as $valor => $texto): ?>
              <option value="<?= $valor ?>" <?= (($_POST['estado'] ?? '') === $valor) ? 'selected' : '' ?>><?= $texto ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Observaciones</label>
          <textarea name="observaciones" class="form-control rounded-3 py-2" rows="3" maxlength="255"
                    placeholder="Anotaciones opcionales de la cohorte."><?= htmlspecialchars($_POST['observaciones'] ?? '') ?></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
          <a href="/controllers/mg_cohortes_listar.php" class="btn btn-light px-4 py-2 rounded-3">Cancelar</a>
          <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-save"></i>
            <span>Guardar Cohorte</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>