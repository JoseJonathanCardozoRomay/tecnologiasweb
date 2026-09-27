<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$tituloPagina = 'Acceso no autorizado - Sistema de Tutorías';
require_once __DIR__ . '/../layouts/header.php';
?>
<div class="card card-custom p-5 text-center mx-auto" style="max-width: 620px;">
  <div class="display-4 fw-bold text-primary mb-3">403</div>
  <h1 class="h4 mb-2">Acceso no autorizado</h1>
  <p class="text-muted mb-4">No tienes permisos para acceder a este recurso.</p>
  <a class="btn btn-primary" href="/views/login/login.php">Volver al inicio</a>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
