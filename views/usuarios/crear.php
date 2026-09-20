<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Nuevo Usuario - Sistema de Tutorías';
include __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-8 col-xl-7">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h3 class="fw-bold mb-0 d-flex align-items-center gap-2">
        <i class="bi bi-person-plus-fill text-primary"></i>
        <span>Registrar Nuevo Usuario</span>
      </h3>
      <a href="usuarios_listar.php" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

    <?php if (!empty($errores)): ?>
      <div class="alert alert-danger py-2 px-3 rounded-3 shadow-sm mb-4">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-1"></i> Corrige los siguientes errores:</div>
        <ul class="mb-0 ps-3 small">
          <?php foreach ($errores as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="card card-custom p-4 p-md-5">
      <form method="POST" autocomplete="off"><?=csrfField()?>
        <div class="row g-3">
          <div class="col-md-12">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Rol del Usuario *</label>
            <select name="id_rol" class="form-select rounded-3 py-2" required>
              <option value="" disabled selected>Selecciona un rol...</option>
              <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id_rol'] ?>" <?= (isset($_POST['id_rol']) && $_POST['id_rol'] == $r['id_rol']) ? 'selected' : '' ?>>
                  <?= ucfirst(htmlspecialchars($r['nombre_rol'])) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Nombre *</label>
            <input type="text" name="nombre" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Apellido *</label>
            <input type="text" name="apellido" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Correo Electrónico *</label>
            <input type="email" name="correo" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" placeholder="ejemplo@upds.edu.bo" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Nombre de Usuario *</label>
            <input type="text" name="usuario" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>" placeholder="usuario123" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Teléfono</label>
            <input type="text" name="telefono" maxlength="20" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>" placeholder="70000000">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Contraseña Inicial *</label>
            <input type="password" name="clave" class="form-control rounded-3 py-2" minlength="6" placeholder="Mínimo 6 caracteres" required>
            <div class="form-text">La contraseña se guardará encriptada con Bcrypt de forma segura.</div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
          <a href="usuarios_listar.php" class="btn btn-light px-4 py-2 rounded-3">Cancelar</a>
          <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-save"></i>
            <span>Guardar Usuario</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>