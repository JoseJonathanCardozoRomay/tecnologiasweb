<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Cohortes de Modalidad de Grado - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<?php
$titulo = 'Cohortes de Modalidad de Grado';
$descripcion = 'Gestiones/periodos en los que se cursan las Modalidades de Grado.';
$icono = 'bi-calendar-range';
$contador = $totalRegistros;
$accion = '<a href="/controllers/mg_cohortes_crear.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3"><i class="bi bi-plus-circle-fill"></i><span class="fw-semibold">Nueva Cohorte</span></a>';
include __DIR__ . '/../../partials/page_header.php';
?>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <?php
    $filtroQ = $q;
    $filtroQPlaceholder = 'Buscar período o descripción...';
    $filtroOcultos = ['orden' => $ordenActual, 'dir' => $dirActual];
    $filtroSelectores = [[
        'nombre' => 'estado',
        'etiqueta' => 'Estado',
        'seleccionado' => $estado,
        'opciones' => [
            ['valor' => '', 'texto' => 'Todas'],
            ['valor' => 'planificada', 'texto' => 'Planificadas'],
            ['valor' => 'abierta', 'texto' => 'Abiertas'],
            ['valor' => 'cerrada', 'texto' => 'Cerradas'],
        ],
    ]];
    $filtroPorPagina = $pag['por_pagina'];
    $filtroLimpiarUrl = '/controllers/mg_cohortes_listar.php';
    include __DIR__ . '/../../partials/panel_filtros.php';
    ?>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('ID', 'id', $ordenActual, $dirActual, 'ps-4'); ?>
          <?php encabezadoOrdenable('Periodo', 'periodo', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Inicio', 'inicio', $ordenActual, $dirActual); ?>
          <th>Fin</th>
          <th>Estado</th>
          <?php encabezadoOrdenable('Expedientes', 'expedientes', $ordenActual, $dirActual); ?>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cohortes as $c): ?>
          <tr>
            <td class="ps-4 text-muted fw-semibold">#<?= htmlspecialchars($c['id_cohorte_mg']) ?></td>
            <td>
              <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i class="bi bi-calendar-range text-primary opacity-75"></i>
                <?= htmlspecialchars($c['nombre_periodo']) ?>
              </div>
            </td>
            <td class="text-muted"><?= htmlspecialchars($c['fecha_inicio']) ?></td>
            <td class="text-muted"><?= $c['fecha_fin'] ? htmlspecialchars($c['fecha_fin']) : '—' ?></td>
            <td>
              <?php if ($c['estado'] === 'abierta'): ?>
                <span class="status-badge status-active">Abierta</span>
              <?php elseif ($c['estado'] === 'cerrada'): ?>
                <span class="status-badge status-inactive">Cerrada</span>
              <?php else: ?>
                <span class="status-badge status-pending">Planificada</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge bg-light text-dark border px-2 py-1">
                <i class="bi bi-folder-check me-1 text-primary"></i><?= $c['total_expedientes'] ?> expediente(s)
              </span>
            </td>
            <td class="text-end pe-4">
              <div class="btn-group" role="group">
                <a href="/controllers/mg_cohortes_editar.php?id=<?= (int) $c['id_cohorte_mg'] ?>" class="btn btn-outline-primary btn-sm rounded-start-2" title="Editar">
                  <i class="bi bi-pencil-fill"></i>
                </a>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-end-2"
                        onclick="confirmarEliminacion('/controllers/mg_cohortes_eliminar.php?id=<?= (int) $c['id_cohorte_mg'] ?>', 'Se eliminará la cohorte <?= htmlspecialchars($c['nombre_periodo']) ?>.')"
                        title="Eliminar">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($cohortes)): ?>
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">
              <i class="bi bi-calendar-range fs-1 d-block mb-2 text-secondary"></i>
              <?php if ($q !== '' || $estado !== ''): ?>
                Sin resultados para tu búsqueda. <a href="<?= urlLista(['q' => null, 'estado' => null, 'pagina' => 1]) ?>">Limpiar filtros</a>
              <?php else: ?>
                No hay cohortes registradas.
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