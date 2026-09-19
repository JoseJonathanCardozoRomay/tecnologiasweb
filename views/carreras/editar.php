<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Editar Carrera - Sistema de Tutorías';
include __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h3 class="fw-bold mb-0 d-flex align-items-center gap-2">
        <i class="bi bi-pencil-square text-primary"></i>
        <span>Editar Carrera</span>
      </h3>
      <a href="carreras_listar.php" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

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
        <input type="hidden" name="id_carrera" value="<?= htmlspecialchars($carrera_actual['id_carrera']) ?>">

        <div class="mb-4">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Nombre de la Carrera *</label>
          <input type="text" name="nombre_carrera" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['nombre_carrera'] ?? $carrera_actual['nombre_carrera']) ?>" maxlength="150" required>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
          <a href="carreras_listar.php" class="btn btn-light px-4 py-2 rounded-3">Cancelar</a>
          <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-arrow-repeat"></i>
            <span>Actualizar Carrera</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
