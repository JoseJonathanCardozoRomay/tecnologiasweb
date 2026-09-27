<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Gestión de Docentes Tutores - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$titulo = 'Docentes Tutores Académicos';
$descripcion = 'Cuerpo docente capacitado para brindar asesorías y reforzamiento académico.';
$icono = 'bi-person-video3';
$contador = $totalRegistros;
$accion = '<a href="tutores_crear.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3"><i class="bi bi-person-plus-fill"></i><span class="fw-semibold">+ Nuevo Tutor</span></a>';
include __DIR__ . '/../partials/page_header.php';
?>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <?php
    $filtroQ = $q;
    $filtroQPlaceholder = 'Buscar tutor...';
    $filtroOcultos = ['orden' => $ordenActual, 'dir' => $dirActual];
    $filtroPorPagina = $pag['por_pagina'];
    $filtroLimpiarUrl = 'tutores_listar.php';
    include __DIR__ . '/../partials/panel_filtros.php';
    ?>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('Tutor Docente', 'nombre', $ordenActual, $dirActual, 'ps-4'); ?>
          <?php encabezadoOrdenable('Especialidad', 'especialidad', $ordenActual, $dirActual); ?>
          <th>Carreras</th>
          <th>Contacto</th>
          <th>Materias</th>
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
                  <small class="text-muted"><i class="bi bi-person-video3 me-1"></i>Tutor académico #<?= $t['id_tutor'] ?></small>
                </div>
              </div>
            </td>
            <td>
              <span class="fw-medium text-secondary">
                <?= htmlspecialchars($t['especialidad'] ?? 'Docencia Universitaria') ?>
              </span>
            </td>
            <td>
              <?php $carreras = $t['carreras'] ?? []; ?>
              <?php if (empty($carreras)): ?>
                <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle px-2 py-1">Sin carreras</span>
              <?php else: ?>
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1">
                  <i class="bi bi-mortarboard me-1"></i><?= count($carreras) ?> carreras
                </span>
                <small class="d-block text-muted mt-1"><?= htmlspecialchars(implode(', ', array_column($carreras, 'nombre_carrera'))) ?></small>
              <?php endif; ?>
            </td>
            <td>
              <div><i class="bi bi-envelope me-1 text-muted"></i><?= htmlspecialchars($t['correo']) ?></div>
              <?php if (!empty($t['telefono'])): ?>
                <small class="text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($t['telefono']) ?></small>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">
                <i class="bi bi-book me-1"></i><?= count($t['materias'] ?? []) ?> materias
              </span>
            </td>
            <td class="text-end pe-4">
              <a href="tutores_disponibilidad.php?id=<?= $t['id_tutor'] ?>" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1">
                <i class="bi bi-sliders"></i>
                <span>Gestionar Horarios, Materias y Carreras</span>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($tutores)): ?>
          <tr>
            <td colspan="5" class="text-center py-5 text-muted">
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
  <?php
$mostrarSelector = false;
include __DIR__ . '/../partials/paginacion.php';
?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
