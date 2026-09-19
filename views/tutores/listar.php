<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Gestión de Docentes Tutores - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
      <i class="bi bi-person-video3 text-primary"></i>
      <span>Docentes Tutores Académicos</span>
      <span class="badge bg-primary bg-opacity-10 text-primary fs-6"><?= count($tutores) ?></span>
    </h2>
    <p class="text-muted mb-0">Cuerpo docente capacitado para brindar asesorías y reforzamiento académico.</p>
  </div>
  <div>
    <a href="usuarios_crear.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3">
      <i class="bi bi-person-plus-fill"></i>
      <span class="fw-semibold">+ Nuevo Tutor</span>
    </a>
  </div>
</div>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <th class="ps-4">Tutor Docente</th>
          <th>Especialidad</th>
          <th>Contacto</th>
          <th>Materias</th>
          <th>Horarios</th>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tutores as $t): ?>
          <tr>
            <td class="ps-4">
              <div class="d-flex align-items-center gap-3">
                <div class="monogram rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px;">
                  <?= strtoupper(substr($t['nombre'], 0, 1) . substr($t['apellido'], 0, 1)) ?>
                </div>
                <div>
                  <div class="fw-bold text-dark">Prof. <?= htmlspecialchars($t['nombre'] . ' ' . $t['apellido']) ?></div>
                  <small class="text-muted"><i class="bi bi-person me-1"></i><?= htmlspecialchars($t['usuario']) ?></small>
                </div>
              </div>
            </td>
            <td>
              <span class="fw-medium text-secondary">
                <?= htmlspecialchars($t['especialidad'] ?? 'Docencia Universitaria') ?>
              </span>
            </td>
            <td>
              <div><i class="bi bi-envelope me-1 text-muted"></i><?= htmlspecialchars($t['correo']) ?></div>
              <?php if (!empty($t['telefono'])): ?>
                <small class="text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($t['telefono']) ?></small>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">
                <i class="bi bi-book me-1"></i><?= $t['total_materias'] ?> materias
              </span>
            </td>
            <td>
              <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">
                <i class="bi bi-clock me-1"></i><?= $t['total_horarios'] ?> bloques
              </span>
            </td>
            <td class="text-end pe-4">
              <a href="tutores_disponibilidad.php?id=<?= $t['id_tutor'] ?>" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1">
                <i class="bi bi-sliders"></i>
                <span>Gestionar Horarios y Materias</span>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($tutores)): ?>
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">
              <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary"></i>
              No hay tutores registrados en el sistema.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
