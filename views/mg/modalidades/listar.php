<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Modalidades de Grado - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<?php
$titulo = 'Modalidades de Grado';
$descripcion = 'Catálogo oficial de la normativa RN-MG-01: las 5 modalidades de culminación de estudios.';
$icono = 'bi-diagram-3';
$contador = $totalRegistros;
$accion = '<a href="/controllers/mg_modalidades_crear.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3"><i class="bi bi-plus-circle-fill"></i><span class="fw-semibold">Nueva Modalidad</span></a>';
include __DIR__ . '/../../partials/page_header.php';
?>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <?php
    $filtroQ = $q;
    $filtroQPlaceholder = 'Buscar modalidad o flujo...';
    $filtroOcultos = ['orden' => $ordenActual, 'dir' => $dirActual];
    $filtroSelectores = [[
        'nombre' => 'estado',
        'etiqueta' => 'Estado',
        'seleccionado' => $estado,
        'opciones' => [
            ['valor' => '', 'texto' => 'Todas'],
            ['valor' => '1', 'texto' => 'Activas'],
            ['valor' => '0', 'texto' => 'Inactivas'],
        ],
    ]];
    $filtroPorPagina = $pag['por_pagina'];
    $filtroLimpiarUrl = '/controllers/mg_modalidades_listar.php';
    include __DIR__ . '/../../partials/panel_filtros.php';
    ?>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('ID', 'id', $ordenActual, $dirActual, 'ps-4'); ?>
          <?php encabezadoOrdenable('Nombre', 'nombre', $ordenActual, $dirActual); ?>
          <th>Tutor</th>
          <?php encabezadoOrdenable('Flujo', 'flujo', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Expedientes', 'expedientes', $ordenActual, $dirActual); ?>
          <th>Estado</th>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($modalidades as $m): ?>
          <tr>
            <td class="ps-4 text-muted fw-semibold">#<?= htmlspecialchars($m['id_modalidad_grado']) ?></td>
            <td>
              <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i class="bi bi-diagram-3 text-primary opacity-75"></i>
                <?= htmlspecialchars($m['nombre']) ?>
              </div>
            </td>
            <td>
              <?php if ((int) $m['requiere_tutor']): ?>
                <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle px-2 py-1">
                  <i class="bi bi-person-check me-1"></i>Requiere tutor
                </span>
              <?php else: ?>
                <span class="badge bg-light text-muted border px-2 py-1">
                  <i class="bi bi-person-dash me-1"></i>Sin tutor
                </span>
              <?php endif; ?>
            </td>
            <td class="text-muted"><?= htmlspecialchars(mgFlujoLabel($m['flujo'])) ?></td>
            <td>
              <span class="badge bg-light text-dark border px-2 py-1">
                <i class="bi bi-folder-check me-1 text-primary"></i><?= $m['total_expedientes'] ?> expediente(s)
              </span>
            </td>
            <td>
              <?php if ((int) $m['activa']): ?>
                <span class="status-badge status-active">Activa</span>
              <?php else: ?>
                <span class="status-badge status-inactive">Inactiva</span>
              <?php endif; ?>
            </td>
            <td class="text-end pe-4">
              <div class="btn-group" role="group">
                <a href="/controllers/mg_modalidades_editar.php?id=<?= (int) $m['id_modalidad_grado'] ?>" class="btn btn-outline-primary btn-sm rounded-start-2" title="Editar">
                  <i class="bi bi-pencil-fill"></i>
                </a>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-end-2"
                        onclick="confirmarEliminacion('/controllers/mg_modalidades_eliminar.php?id=<?= (int) $m['id_modalidad_grado'] ?>', 'Se eliminará la modalidad <?= htmlspecialchars($m['nombre']) ?> de forma permanente.')"
                        title="Eliminar">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($modalidades)): ?>
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">
              <i class="bi bi-diagram-3 fs-1 d-block mb-2 text-secondary"></i>
              <?php if ($q !== '' || $estado !== ''): ?>
                Sin resultados para tu búsqueda. <a href="<?= urlLista(['q' => null, 'estado' => null, 'pagina' => 1]) ?>">Limpiar filtros</a>
              <?php else: ?>
                No hay modalidades registradas.
              <?php endif; ?>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php
$mostrarSelector = false;
include __DIR__ . '/../../partials/paginacion.php';
?>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>