<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<div class="row justify-content-center py-5">
  <div class="col-lg-6">
    <div class="card card-custom p-5 text-center">
      <div class="display-4 text-danger mb-3"><i class="bi bi-shield-lock-fill"></i></div>
      <h1 class="fw-bold">Acceso no autorizado</h1>
      <p class="text-muted mb-4">Tu rol actual no tiene permisos para acceder a este módulo.</p>
      <a href="<?= e(dashboardPorRol()) ?>" class="btn btn-primary px-4"><i class="bi bi-house-door me-1"></i>Volver al inicio</a>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
