<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Registrar Materia - Sistema de Tutorías';
include __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-7 col-xl-6">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h3 class="fw-bold mb-0 d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle-fill text-primary"></i>
        <span>Nueva Materia</span>
      </h3>
      <a href="materias_listar.php" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Volver al listado
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
        <div class="mb-4">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Nombre de la Materia *</label>
          <input type="text" name="nombre_materia" class="form-control rounded-3 py-2" placeholder="Ej: Redes de Computadoras, Algoritmos..." required autofocus>
        </div>

        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label fw-semibold text-secondary small text-uppercase mb-0">Carrera Perteneciente</label>
            <a href="carreras_crear.php" class="small text-decoration-none">+ Nueva carrera</a>
          </div>
          <select name="id_carrera" class="form-select rounded-3 py-2">
            <option value="">-- Selecciona una carrera (Opcional) --</option>
            <?php foreach ($carreras as $c): ?>
              <option value="<?= $c['id_carrera'] ?>">
                <?= htmlspecialchars($c['nombre_carrera']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">Asocia la materia para que los estudiantes de esa carrera la encuentren fácilmente.</div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
          <a href="materias_listar.php" class="btn btn-light px-4 py-2 rounded-3">Cancelar</a>
          <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-save"></i>
            <span>Guardar Materia</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
