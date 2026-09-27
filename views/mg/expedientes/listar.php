<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Expedientes de Modalidad de Grado - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<?php
$titulo = 'Expedientes de Modalidad de Grado';
$descripcion = 'Seguimiento de los expedientes MG y su asignación de tutor (HU-024/025).';
$icono = 'bi-folder2-open';
$contador = $totalRegistros;
$accion = '<a href="/controllers/mg_expedientes_crear.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3"><i class="bi bi-plus-circle-fill"></i><span class="fw-semibold">Nuevo Expediente</span></a>';
include __DIR__ . '/../../partials/page_header.php';
?>

<?php
$filtroCohorte = limpiarTexto($_GET['cohorte'] ?? '', 30);
$opcionesEstado = [['valor' => '', 'texto' => 'Todos']];
foreach (['solicitado', 'validado', 'tutor_asignado', 'en_curso', 'concluido', 'cerrado'] as $est) {
    $opcionesEstado[] = ['valor' => $est, 'texto' => mgExpEstadoLabel($est)];
}
$opcionesCohorte = [['valor' => '', 'texto' => 'Todas']];
foreach ($cohortes as $c) {
    $opcionesCohorte[] = ['valor' => $c['nombre_periodo'], 'texto' => $c['nombre_periodo']];
}
?>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <?php
    $filtroQ = $q;
    $filtroQPlaceholder = 'Buscar estudiante, modalidad o cohorte...';
    $filtroOcultos = ['orden' => $ordenActual, 'dir' => $dirActual];
    $filtroSelectores = [
        ['nombre' => 'estado', 'etiqueta' => 'Estado', 'seleccionado' => $estado, 'opciones' => $opcionesEstado],
        ['nombre' => 'cohorte', 'etiqueta' => 'Cohorte', 'seleccionado' => $filtroCohorte, 'opciones' => $opcionesCohorte],
    ];
    $filtroPorPagina = $pag['por_pagina'];
    $filtroLimpiarUrl = '/controllers/mg_expedientes_listar.php';
    include __DIR__ . '/../../partials/panel_filtros.php';
    ?>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('N°', 'id', $ordenActual, $dirActual, 'ps-4'); ?>
          <?php encabezadoOrdenable('Estudiante', 'estudiante', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Modalidad', 'modalidad', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Cohorte', 'cohorte', $ordenActual, $dirActual); ?>
          <th>Estado</th>
          <th>Tutor asignado</th>
          <?php encabezadoOrdenable('Fecha solicitud', 'fecha', $ordenActual, $dirActual); ?>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($expedientes as $e): ?>
          <tr>
            <td class="ps-4 fw-semibold"><?= htmlspecialchars($e['id_expediente_mg']) ?></td>
            <td>
              <div class="fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-person-circle text-primary opacity-75"></i>
                <div>
                  <div><?= htmlspecialchars($e['estudiante']) ?></div>
                  <div class="small text-muted">@<?= htmlspecialchars($e['usuario_estudiante']) ?></div>
                </div>
              </div>
            </td>
            <td>
              <div class="d-flex flex-column">
                <span><?= htmlspecialchars($e['modalidad']) ?></span>
                <span class="small text-muted"><?= (int) $e['modalidad_requiere_tutor'] ? 'con tutor' : 'sin tutor' ?></span>
              </div>
            </td>
            <td>
              <span class="badge bg-light text-dark border px-2 py-1">
                <i class="bi bi-calendar-range me-1 text-primary"></i><?= htmlspecialchars($e['cohorte']) ?>
              </span>
            </td>
            <td><?= mgEstadoExpedienteBadge($e['estado']) ?></td>
            <td>
              <?php if ($e['tutor_asignado']): ?>
                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">
                  <i class="bi bi-person-check me-1"></i><?= htmlspecialchars($e['tutor_asignado']) ?>
                </span>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td class="text-muted small"><?= htmlspecialchars(date('d/m/Y', strtotime($e['fecha_solicitud']))) ?></td>
            <td class="text-end pe-4">
              <a href="/controllers/mg_expediente.php?id=<?= (int) $e['id_expediente_mg'] ?>"
                 class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1" title="Ver expediente">
                <i class="bi bi-eye-fill"></i><span>Ver</span>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($expedientes)): ?>
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">
              <i class="bi bi-folder2-open fs-1 d-block mb-2 text-secondary"></i>
              <?php if ($q !== '' || $estado !== '' || $filtroCohorte !== ''): ?>
                Sin resultados para tus filtros. <a href="<?= urlLista(['q' => null, 'estado' => null, 'cohorte' => null, 'pagina' => 1]) ?>">Limpiar filtros</a>
              <?php else: ?>
                No hay expedientes registrados. <a href="/controllers/mg_expedientes_crear.php">Registrar el primero</a>
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