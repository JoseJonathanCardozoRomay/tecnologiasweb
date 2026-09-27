<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Registro de Accesos - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$migas = [
    ['texto' => 'Registro de Accesos', 'actual' => true],
];
$titulo = 'Registro de Accesos';
$descripcion = 'Historial de intentos de inicio de sesión en el sistema (todos los usuarios).';
$icono = 'bi-key';
$contador = $totalRegistros;
include __DIR__ . '/../partials/page_header.php';
?>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-4">
    <div class="card card-custom p-3 text-center border-start border-primary border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Intentos totales</small>
      <h3 class="fw-bold mb-0 text-dark"><?= $totalRegistros ?></h3>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="card card-custom p-3 text-center border-start border-success border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Exitosos</small>
      <h3 class="fw-bold mb-0 text-success"><?= $totalExitosos ?></h3>
    </div>
  </div>
  <div class="col-6 col-md-4">
    <div class="card card-custom p-3 text-center border-start border-danger border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Fallidos</small>
      <h3 class="fw-bold mb-0 text-danger"><?= $totalFallidos ?></h3>
    </div>
  </div>
</div>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <?php
    // SPRINT 6: pestañas de resultado (dinámicas) + búsqueda + por_página.
    $filtroTabs = [
        'nombre' => 'estado',
        'grupos' => array_merge(
            [['valor' => '', 'texto' => 'Todos']],
            array_map(fn($r) => ['valor' => $r, 'texto' => ucfirst($r) . 's'], $resultados)
        ),
        'activo' => $resultado,
    ];
    $filtroQ = $q;
    $filtroQPlaceholder = 'Buscar usuario o IP...';
    $filtroOcultos = ['orden' => $ordenActual, 'dir' => $dirActual];
    $filtroPorPagina = $pag['por_pagina'];
    $filtroLimpiarUrl = 'accesos_listar.php';
    include __DIR__ . '/../partials/panel_filtros.php';
    ?>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('Fecha y Hora', 'fecha', $ordenActual, $dirActual, 'ps-4'); ?>
          <th>ID</th>
          <th>Usuario</th>
          <th>Dirección IP</th>
          <?php encabezadoOrdenable('Resultado', 'resultado', $ordenActual, $dirActual); ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($accesos as $a): ?>
          <?php $exitoso = ($a['resultado'] ?? '') === 'exitoso'; ?>
          <tr>
            <td class="ps-4">
              <div class="fw-bold text-dark"><?= date('d/m/Y H:i', strtotime($a['fecha_hora'])) ?></div>
            </td>
            <td><span class="text-muted fw-semibold">#<?= (int) ($a['id_acceso'] ?? 0) ?></span></td>
            <td>
              <?php if (!empty($a['id_usuario'])): ?>
                <div class="d-flex align-items-center gap-2">
                  <?= avatar($a['nombre'] ?? '?', $a['apellido'] ?? '', '') ?>
                  <div>
                    <div class="fw-semibold text-dark"><?= htmlspecialchars(trim(($a['nombre'] ?? '') . ' ' . ($a['apellido'] ?? ''))) ?></div>
                    <small class="text-muted"><i class="bi bi-person me-1"></i><?= htmlspecialchars($a['usuario'] ?? (string) $a['id_usuario']) ?></small>
                  </div>
                </div>
              <?php else: ?>
                <span class="text-muted small">#<?= (int) $a['id_usuario'] ?> <span class="badge text-bg-light">usuario eliminado</span></span>
              <?php endif; ?>
            </td>
            <td><span class="text-secondary"><i class="bi bi-router me-1"></i><?= htmlspecialchars($a['ip_origen']) ?></span></td>
            <td>
              <?php if ($exitoso): ?>
                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle me-1"></i>Exitoso</span>
              <?php else: ?>
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-x-circle me-1"></i>Fallido</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($accesos)): ?>
          <tr>
            <td colspan="5" class="text-center py-5 text-muted">
              <i class="bi bi-key fs-1 d-block mb-2 text-secondary"></i>
              <?php if ($q !== ''): ?>
                Sin resultados para tu búsqueda. <a href="<?= urlLista(['q' => null, 'pagina' => 1]) ?>">Limpiar búsqueda</a>
              <?php else: ?>
                No hay registros de acceso todavía.
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