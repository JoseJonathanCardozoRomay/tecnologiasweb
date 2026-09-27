<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Nuevo Docente Tutor - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$migas = [
    ['texto' => 'Tutores', 'url' => '/controllers/tutores_listar.php'],
    ['texto' => 'Nuevo Docente Tutor', 'actual' => true],
];
$titulo = 'Registrar Docente Tutor';
$descripcion = 'Crea el perfil de un docente que impartirá tutorías académicas.';
$icono = 'bi-person-plus-fill';
$accion = '<a href="/controllers/tutores_listar.php" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3"><i class="bi bi-arrow-left"></i>Volver</a>';
include __DIR__ . '/../partials/page_header.php';
?>

<?php if (!empty($errores)): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
    <strong>No se pudo guardar:</strong>
    <ul class="mb-0 mt-1">
      <?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<div class="card card-custom shadow-sm">
  <div class="card-body p-4">
    <form method="POST" action="../controllers/tutores_crear.php" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

      <h5 class="fw-bold text-dark mb-3"><i class="bi bi-person-badge me-1 text-primary"></i>Datos de acceso</h5>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Nombre *</label>
          <input type="text" name="nombre" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($nombre) ?>" maxlength="100" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Apellido *</label>
          <input type="text" name="apellido" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($apellido) ?>" maxlength="100" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Correo electrónico *</label>
          <input type="email" name="correo" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($correo) ?>" maxlength="150" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Nombre de usuario *</label>
          <input type="text" name="usuario" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($usuario) ?>" maxlength="50" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Contraseña *</label>
          <input type="password" name="clave" class="form-control rounded-3 py-2" minlength="6" maxlength="100" required>
          <small class="text-muted">Mínimo 6 caracteres.</small>
        </div>
      </div>

      <hr class="my-4">
      <h5 class="fw-bold text-dark mb-3"><i class="bi bi-mortarboard me-1 text-primary"></i>Perfil académico</h5>
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label fw-semibold text-secondary small text-uppercase">Especialidad</label>
          <input type="text" name="especialidad" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($especialidad) ?>" maxlength="100" placeholder="Ej: Docencia Universitaria">
        </div>
      </div>

      <div class="mt-4 d-flex gap-2 justify-content-end">
        <a href="../controllers/tutores_listar.php" class="btn btn-outline-secondary rounded-3 px-3">Cancelar</a>
        <button type="submit" class="btn btn-primary rounded-3 px-4"><i class="bi bi-check-lg me-1"></i>Registrar Tutor</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>