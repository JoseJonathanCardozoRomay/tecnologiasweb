<?php
require_once __DIR__ . '/includes/verificar_sesion.php';
$tituloPagina = 'Materias - Sistema de Tutorías';
include __DIR__ . '/views/layouts/header.php';
?>

<div class="row g-4">
  <div class="col-12">
    <div class="card card-custom p-4 text-white shadow" style="background: linear-gradient(135deg, #1e3a5f 0%, #0d6efd 100%) !important;">
      <h2 class="fw-bold mb-1"><i class="bi bi-journal-bookmark-fill me-2"></i>Materias Académicas</h2>
      <p class="mb-0 text-white-50">Gestión del catálogo de materias - UPDS</p>
    </div>
  </div>
  <div class="col-12">
    <div class="card card-custom p-4">
      <p class="text-muted">Desde aquí puedes gestionar las materias académicas. Usa el panel de administración para crear, editar o eliminar materias.</p>
      <a href="/controllers/materias_listar.php" class="btn btn-primary">
        <i class="bi bi-arrow-right me-1"></i> Ir a Gestión de Materias
      </a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>
