<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Panel del Tutor - Sistema de Tutorías';
include __DIR__ . '/../layouts/header.php';
?>

<div class="row g-4">
  <div class="col-12">
    <div class="card card-custom p-4 bg-primary text-white shadow" style="background: linear-gradient(135deg, #1e3a5f 0%, #0d6efd 100%) !important;">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
          <h2 class="fw-bold mb-1">¡Bienvenido(a), <?= htmlspecialchars($_SESSION['nombre']) ?>! 👋</h2>
          <p class="mb-0 text-white-50">Panel de Control Docente / Tutor Académico - UPDS</p>
        </div>
        <span class="badge bg-white text-primary px-3 py-2 fs-6 rounded-pill">Rol: Tutor</span>
      </div>
    </div>
  </div>

  <!-- Tarjetas métricas -->
  <div class="col-md-4">
    <div class="card card-custom p-4 text-center">
      <div class="text-primary fs-1 mb-2"><i class="bi bi-calendar-check"></i></div>
      <h3 class="fw-bold mb-0">3</h3>
      <p class="text-muted small mb-0">Días con Disponibilidad</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-custom p-4 text-center">
      <div class="text-success fs-1 mb-2"><i class="bi bi-journal-check"></i></div>
      <h3 class="fw-bold mb-0">2</h3>
      <p class="text-muted small mb-0">Materias Asignadas</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-custom p-4 text-center">
      <div class="text-warning fs-1 mb-2"><i class="bi bi-clock-history"></i></div>
      <h3 class="fw-bold mb-0">0</h3>
      <p class="text-muted small mb-0">Tutorías Pendientes</p>
    </div>
  </div>

  <!-- Próximos pasos -->
  <div class="col-12">
    <div class="card card-custom p-4">
      <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-info-circle-fill text-primary"></i>
        <span>Próximas funcionalidades para este módulo:</span>
      </h5>
      <ul class="list-group list-group-flush">
        <li class="list-group-item d-flex align-items-center gap-2">
          <i class="bi bi-check2-circle text-success"></i> Gestión de horarios de disponibilidad semanales (`disponibilidad_tutor`).
        </li>
        <li class="list-group-item d-flex align-items-center gap-2">
          <i class="bi bi-check2-circle text-success"></i> Aceptación y confirmación de tutorías solicitadas por estudiantes (`tutorias`).
        </li>
        <li class="list-group-item d-flex align-items-center gap-2">
          <i class="bi bi-check2-circle text-success"></i> Registro de observaciones y estado de las sesiones realizadas.
        </li>
      </ul>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
