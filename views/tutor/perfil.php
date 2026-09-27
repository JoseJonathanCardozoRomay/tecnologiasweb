<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Mi perfil - Sistema de Tutorías';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$titulo = 'Mi perfil';
$descripcion = 'Consulta y actualiza tus datos personales y académicos.';
$icono = 'bi-person-badge-fill';
$migas = [
    ['texto' => 'Mi panel', 'url' => '/views/tutor/panel.php'],
    ['texto' => 'Mi perfil', 'actual' => true],
];
include __DIR__ . '/../partials/page_header.php';
?>

<?php if (!empty($errores)): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
    <ul class="mb-0"><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-12 col-lg-4">
    <div class="card card-custom shadow-sm h-100">
      <div class="card-body text-center">
        <?= avatar($tutor['nombre'], $tutor['apellido'], 'tutor') ?>
        <h2 class="h5 fw-bold text-dark mt-3 mb-1"><?= htmlspecialchars($tutor['nombre'] . ' ' . $tutor['apellido']) ?></h2>
        <p class="text-muted small mb-3"><?= htmlspecialchars($tutor['especialidad'] ?: 'Sin especialidad registrada') ?></p>
        <?= estado_badge($tutor['estado']) ?>
      </div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
          <span class="text-muted small">Usuario</span>
          <span class="fw-semibold small"><?= htmlspecialchars($usuario['usuario'] ?? '') ?></span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
          <span class="text-muted small">Correo</span>
          <span class="fw-semibold small text-break"><?= htmlspecialchars($tutor['correo']) ?></span>
        </li>
        <?php if (!empty($tutor['telefono'])): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
            <span class="text-muted small">Teléfono</span>
            <span class="fw-semibold small"><?= htmlspecialchars($tutor['telefono']) ?></span>
          </li>
        <?php endif; ?>
        <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
          <span class="text-muted small">Carreras asignadas</span>
          <span class="fw-semibold small"><?= count($carreras) ?></span>
        </li>
      </ul>
      <div class="card-body border-top">
        <a href="/controllers/tutores_disponibilidad.php" class="btn btn-outline-primary btn-sm w-100">
          <i class="bi bi-clock-history me-1" aria-hidden="true"></i>Administrar materias y horarios
        </a>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-8">
    <form method="POST" class="card card-custom shadow-sm" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
      <div class="card-header bg-white border-0 pt-3">
        <h2 class="h6 fw-bold text-dark mb-0"><i class="bi bi-person-vcard me-1 text-primary"></i>Datos personales</h2>
        <p class="text-muted small mb-0">Tu usuario, rol y estado los administra la coordinación; no se modifican desde aquí.</p>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold" for="perfil_nombre">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="perfil_nombre" name="nombre" maxlength="100" required
                   value="<?= htmlspecialchars($valores['nombre'], ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold" for="perfil_apellido">Apellido <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="perfil_apellido" name="apellido" maxlength="100" required
                   value="<?= htmlspecialchars($valores['apellido'], ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold" for="perfil_correo">Correo <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="perfil_correo" name="correo" maxlength="150" required
                   value="<?= htmlspecialchars($valores['correo'], ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label fw-semibold" for="perfil_telefono">Teléfono</label>
            <input type="tel" class="form-control" id="perfil_telefono" name="telefono" maxlength="20"
                   placeholder="Opcional" value="<?= htmlspecialchars($valores['telefono'], ENT_QUOTES, 'UTF-8') ?>">
          </div>
        </div>
      </div>

      <div class="card-header bg-white border-0 pt-2">
        <h2 class="h6 fw-bold text-dark mb-0"><i class="bi bi-mortarboard me-1 text-primary"></i>Datos académicos</h2>
        <p class="text-muted small mb-0">Las materias y carreras se asignan desde la administración del sistema.</p>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label fw-semibold" for="perfil_especialidad">Especialidad</label>
          <input type="text" class="form-control" id="perfil_especialidad" name="especialidad" maxlength="150"
                 placeholder="Ej.: Inteligencia Artificial"
                 value="<?= htmlspecialchars($valores['especialidad'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="mb-0">
          <label class="form-label fw-semibold" for="perfil_biografia">Biografía</label>
          <textarea class="form-control" id="perfil_biografia" name="biografia" rows="4" maxlength="1000"
                    placeholder="Cuéntanos brevemente tu experiencia académica."><?= htmlspecialchars($valores['biografia'], ENT_QUOTES, 'UTF-8') ?></textarea>
          <div class="form-text">Máximo 1000 caracteres.</div>
        </div>
      </div>

      <div class="card-footer bg-white border-0 d-flex flex-wrap justify-content-end gap-2">
        <a href="/views/tutor/panel.php" class="btn btn-outline-secondary">Volver al panel</a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save me-1" aria-hidden="true"></i>Guardar cambios
        </button>
      </div>
    </form>

    <div class="card card-custom shadow-sm mt-4">
      <div class="card-header bg-white border-0 pt-3">
        <h2 class="h6 fw-bold text-dark mb-0"><i class="bi bi-diagram-3 me-1 text-primary"></i>Carreras asignadas</h2>
      </div>
      <div class="card-body">
        <?php if (empty($carreras)): ?>
          <p class="text-muted mb-0">Todavía no tienes carreras asignadas.</p>
        <?php else: ?>
          <div class="d-flex flex-wrap gap-2">
            <?php foreach ($carreras as $carrera): ?>
              <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2">
                <?= htmlspecialchars($carrera['nombre_carrera']) ?>
              </span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
