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
      <span class="badge bg-primary bg-opacity-10 text-primary fs-6"><?= $totalRegistros ?></span>
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
  <div class="card-header bg-white py-3 border-0">
    <form method="GET" class="input-group" style="max-width: 320px;">
      <input type="hidden" name="orden" value="<?= htmlspecialchars($ordenActual) ?>">
      <input type="hidden" name="dir" value="<?= htmlspecialchars($dirActual) ?>">
      <input type="hidden" name="pagina" value="1">
      <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
      <input type="search" name="q" value="<?= htmlspecialchars($q) ?>" class="form-control bg-light border-start-0" placeholder="Buscar tutor...">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('Tutor Docente', 'nombre', $ordenActual, $dirActual, 'ps-4'); ?>
          <?php encabezadoOrdenable('Especialidad', 'especialidad', $ordenActual, $dirActual); ?>
          <th>Contacto</th>
          <?php encabezadoOrdenable('Materias', 'materias', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Horarios', 'horarios', $ordenActual, $dirActual); ?>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tutores as $t): ?>
          <tr>
            <td class="ps-4">
              <div class="d-flex align-items-center gap-3">
                <?= avatar($t['nombre'], $t['apellido'], 'tutor') ?>
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
              <?php if ($q !== ''): ?>
                Sin resultados para tu búsqueda. <a href="<?= urlLista(['q' => null, 'pagina' => 1]) ?>">Limpiar búsqueda</a>
              <?php else: ?>
                No hay registros.
              <?php endif; ?>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php include __DIR__ . '/../partials/paginacion.php'; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
