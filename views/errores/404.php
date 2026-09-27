<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$tituloPagina = 'Archivo no disponible - Sistema de Tutorías';
$mensajeError = isset($motivo) && $motivo !== ''
    ? $motivo
    : 'El archivo solicitado no existe o ya no está disponible en el servidor.';
require_once __DIR__ . '/../layouts/header.php';
?>
<div class="card card-custom p-5 text-center mx-auto" style="max-width: 620px;">
  <div class="display-4 fw-bold text-primary mb-3">404</div>
  <h1 class="h4 mb-2">Archivo no disponible</h1>
  <p class="text-muted mb-4"><?= htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8') ?></p>
  <div class="d-flex gap-2 justify-content-center flex-wrap">
    <a class="btn btn-primary" href="/views/login/login.php">Volver al inicio</a>
    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
      <i class="bi bi-arrow-left me-1"></i>Volver
    </button>
  </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
