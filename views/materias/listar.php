<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Gestión de Materias - Sistema de Tutorías';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$titulo = 'Materias Académicas';
$descripcion = 'Catálogo de asignaturas disponibles para tutorías académicas.';
$icono = 'bi-journal-bookmark-fill';
$contador = $totalRegistros;
$accion = '<a href="carreras_listar.php" class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 py-2 rounded-3"><i class="bi bi-mortarboard"></i><span>Ver Carreras</span></a>'
        . '<a href="materias_crear.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3"><i class="bi bi-plus-circle-fill"></i><span class="fw-semibold">Nueva Materia</span></a>';
include __DIR__ . '/../partials/page_header.php';
?>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <?php
    // SPRINT 6: carrera y per_página dinámicos; búsqueda por teclado.
    $filtroQ = $q;
    $filtroQPlaceholder = 'Buscar materia...';
    $filtroOcultos = ['orden' => $ordenActual, 'dir' => $dirActual];
    $filtroSelectores = [[
        'nombre' => 'id_carrera',
        'etiqueta' => 'Carrera',
        'opciones' => array_merge(
            [['valor' => '0', 'texto' => 'Todas las carreras']],
            array_map(fn($c) => ['valor' => (string) $c['id_carrera'], 'texto' => $c['nombre_carrera']], $carreras)
        ),
        'seleccionado' => (string) $id_carrera,
        'minWidth' => 200,
    ]];
    $filtroPorPagina = $pag['por_pagina'];
    $filtroLimpiarUrl = 'materias_listar.php';
    include __DIR__ . '/../partials/panel_filtros.php';
    ?>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0" id="tablaMaterias">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('ID', 'id', $ordenActual, $dirActual, 'ps-4'); ?>
          <?php encabezadoOrdenable('Nombre de la Materia', 'nombre', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Carrera Universitaria', 'carrera', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Tutores Asignados', 'tutores', $ordenActual, $dirActual); ?>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($materias as $m): ?>
          <tr>
            <td class="ps-4 text-muted fw-semibold">#<?= htmlspecialchars($m['id_materia']) ?></td>
            <td>
              <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i class="bi bi-book text-primary opacity-75"></i>
                <?= htmlspecialchars($m['nombre_materia']) ?>
              </div>
            </td>
            <td>
              <?php if (!empty($m['nombre_carrera'])): ?>
                <span class="badge bg-light text-dark border px-3 py-1">
                  <i class="bi bi-mortarboard me-1 text-primary"></i><?= htmlspecialchars($m['nombre_carrera']) ?>
                </span>
              <?php else: ?>
                <span class="text-muted small italic">Sin carrera asignada</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge bg-info bg-opacity-10 text-info-emphasis border border-info-subtle px-2 py-1">
                <i class="bi bi-person-video3 me-1"></i><?= $m['total_tutores'] ?> tutor(es)
              </span>
            </td>
            <td class="text-end pe-4">
              <div class="btn-group" role="group">
                <a href="materias_editar.php?id=<?= $m['id_materia'] ?>" class="btn btn-outline-primary btn-sm rounded-start-2" title="Editar">
                  <i class="bi bi-pencil-fill"></i>
                </a>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-end-2" 
                        onclick="confirmarEliminacion('materias_eliminar.php?id=<?= $m['id_materia'] ?>', 'Se eliminará la materia <?= htmlspecialchars($m['nombre_materia']) ?> del catálogo.')"
                        title="Eliminar">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($materias)): ?>
          <tr>
            <td colspan="5" class="text-center py-5 text-muted">
              <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
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
</div>

<?php
$mostrarSelector = false;
include __DIR__ . '/../partials/paginacion.php';
?>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
