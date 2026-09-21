<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Nuevo Usuario - Sistema de Tutorías';
include __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-8 col-xl-7">
    <?php
    $titulo = 'Registrar Nuevo Usuario';
    $descripcion = 'Crea una cuenta de administrador, tutor o estudiante.';
    $icono = 'bi-person-plus-fill';
    $accion = '<a href="usuarios_listar.php" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"><i class="bi bi-arrow-left"></i> Volver</a>';
    include __DIR__ . '/../partials/page_header.php';
    ?>

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
      <form method="POST" autocomplete="off">
        <?php require_once __DIR__ . '/../../includes/csrf.php'; echo csrf_campo(); ?>
        <div class="row g-3">
          <div class="col-md-12">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Rol del Usuario *</label>
            <select name="id_rol" class="form-select rounded-3 py-2 select2-enabled" data-placeholder="Selecciona un rol" required>
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
            <input type="text" name="nombre" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" maxlength="100" required>
          </div>

          <div class="col-md-6 d-none" id="datosEstudiante">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Carrera</label>
            <select name="id_carrera" class="form-select rounded-3 py-2 select2-enabled" data-placeholder="Selecciona una carrera">
              <option value="">Selecciona una carrera</option>
              <?php foreach ($carreras as $carrera): ?><option value="<?= $carrera['id_carrera'] ?>" <?= (($_POST['id_carrera'] ?? '') == $carrera['id_carrera']) ? 'selected' : '' ?>><?= htmlspecialchars($carrera['nombre_carrera']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3 d-none" id="semestreEstudiante"><label class="form-label fw-semibold text-secondary small text-uppercase">Semestre</label><input type="number" name="semestre" class="form-control" min="1" max="12" value="<?= htmlspecialchars($_POST['semestre'] ?? '') ?>"></div>
          <div class="col-md-3 d-none" id="ruEstudiante"><label class="form-label fw-semibold text-secondary small text-uppercase">R.U.</label><input type="text" name="registro_universitario" class="form-control" maxlength="30" value="<?= htmlspecialchars($_POST['registro_universitario'] ?? '') ?>"></div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Apellido *</label>
            <input type="text" name="apellido" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>" maxlength="100" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Correo Electrónico *</label>
            <input type="email" name="correo" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" maxlength="150" placeholder="ejemplo@upds.edu.bo" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Nombre de Usuario *</label>
            <input type="text" name="usuario" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>" minlength="4" maxlength="50" pattern="[a-z0-9._-]+" placeholder="usuario123" required>
          </div>

          <div class="col-md-12">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Contraseña Inicial *</label>
            <input type="password" name="clave" class="form-control rounded-3 py-2" minlength="8" pattern="(?=.*[A-Za-z])(?=.*\d).{8,}" placeholder="Mínimo 8 caracteres, una letra y un número" required>
            <div class="form-text">La contraseña se guardará encriptada con Bcrypt de forma segura.</div>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-secondary small text-uppercase">Teléfono</label>
            <input type="tel" name="telefono" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>" maxlength="15" pattern="\d{7,15}">
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
<script>
  const rolNuevo = document.querySelector('[name="id_rol"]');
  const camposEstudiante = document.querySelectorAll('#datosEstudiante, #semestreEstudiante, #ruEstudiante');
  function alternarDatosEstudiante() {
    const esEstudiante = rolNuevo?.selectedOptions[0]?.textContent.trim().toLowerCase() === 'estudiante';
    camposEstudiante.forEach((campo) => campo.classList.toggle('d-none', !esEstudiante));
  }
  rolNuevo?.addEventListener('change', alternarDatosEstudiante);
  alternarDatosEstudiante();
</script>