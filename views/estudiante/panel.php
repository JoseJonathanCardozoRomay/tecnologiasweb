<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Panel del Estudiante - Sistema de Tutorías';
include __DIR__ . '/../layouts/header.php';
?>

<div class="row g-4">
  <div class="col-12">
    <div class="card card-custom p-4 text-white shadow" style="background: linear-gradient(135deg, #0d5c3a 0%, #198754 100%) !important;">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
          <h2 class="fw-bold mb-1">¡Hola, <?= htmlspecialchars($_SESSION['nombre']) ?>! 👋</h2>
          <p class="mb-0 text-white-50">Portal de Apoyo Académico y Tutorías Estudiantiles - UPDS</p>
        </div>
        <span class="badge bg-white text-success px-3 py-2 fs-6 rounded-pill">Rol: Estudiante</span>
      </div>
    </div>
  </div>

  <!-- Acciones rápidas -->
  <div class="col-md-6">
    <div class="card card-custom p-4 h-100">
      <div class="d-flex align-items-center gap-3 mb-3">
        <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle fs-3">
          <i class="bi bi-calendar-plus"></i>
        </div>
        <div>
          <h5 class="fw-bold mb-0">Solicitar Nueva Tutoría</h5>
          <small class="text-muted">Elige materia, docente y horario disponible</small>
        </div>
      </div>
      <p class="text-secondary small">Explora las materias de tu carrera y agenda una sesión de reforzamiento con los tutores asignados.</p>
      <button class="btn btn-outline-success mt-auto" onclick="Swal.fire('Próximo Módulo', 'El agendamiento de tutorías estará habilitado en el siguiente sprint.', 'info')">
        <i class="bi bi-arrow-right-circle me-1"></i> Agendar sesión
      </button>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card card-custom p-4 h-100">
      <div class="d-flex align-items-center gap-3 mb-3">
        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle fs-3">
          <i class="bi bi-journal-text"></i>
        </div>
        <div>
          <h5 class="fw-bold mb-0">Mis Tutorías Registradas</h5>
          <small class="text-muted">Historial y estado de tus solicitudes</small>
        </div>
      </div>
      <p class="text-secondary small">Revisa el estado de tus solicitudes (pendientes, confirmadas o realizadas) y califica a tus tutores.</p>
      <button class="btn btn-outline-primary mt-auto" onclick="Swal.fire('Historial', 'Aún no tienes tutorías agendadas.', 'info')">
        <i class="bi bi-eye me-1"></i> Ver mi historial
      </button>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
