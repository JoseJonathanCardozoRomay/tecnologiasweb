<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Bitácora de Usuarios - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$titulo = 'Bitácora de Usuarios';
$descripcion = 'Registro de auditoría de todas las altas y modificaciones de cuentas de usuario del sistema.';
$icono = 'bi-shield-lock-fill';
$contador = $totalRegistros;
include __DIR__ . '/../partials/page_header.php';
?>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <?php
    // SPRINT 6: componente reutilizable; acciones dinámicas desde la BD.
    $filtroQ = $q;
    $filtroQPlaceholder = 'Buscar por operador, afectado, acción...';
    $filtroOcultos = ['orden' => $ordenActual, 'dir' => $dirActual];
    $filtroSelectores = [[
        'nombre' => 'accion',
        'etiqueta' => 'Acción',
        'opciones' => array_merge(
            [['valor' => '', 'texto' => 'Todas las acciones']],
            array_map(fn($a) => ['valor' => $a, 'texto' => ucfirst($a)], $acciones)
        ),
        'seleccionado' => $accion,
        'minWidth' => 150,
    ]];
    $filtroPorPagina = $pag['por_pagina'];
    $filtroLimpiarUrl = 'bitacora_listar.php';
    include __DIR__ . '/../partials/panel_filtros.php';
    ?>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('Fecha/Hora', 'fecha', $ordenActual, $dirActual, 'ps-4'); ?>
          <?php encabezadoOrdenable('Operador', 'operador', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Afectado', 'afectado', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Acción', 'accion', $ordenActual, $dirActual); ?>
          <th>Detalles</th>
          <th class="text-end pe-4">IP origen</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($registros as $r): ?>
          <?php
            $claseAccion = 'badge-admin';
            if ($r['accion'] === 'crear') $claseAccion = 'badge-estudiante';
            if ($r['accion'] === 'editar') $claseAccion = 'badge-tutor';
          ?>
          <tr>
            <td class="ps-4 small text-muted"><?= date('d/m/Y H:i:s', strtotime($r['fecha_hora'])) ?></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <?= avatar($r['operador_nombre'], $r['operador_apellido'], 'administrador') ?>
                <div>
                  <div class="fw-semibold"><?= htmlspecialchars($r['operador_nombre'] . ' ' . $r['operador_apellido']) ?></div>
                  <small class="text-muted">@<?= htmlspecialchars($r['operador_usuario']) ?></small>
                </div>
              </div>
            </td>
            <td>
              <?php if (!empty($r['afectado_nombre'])): ?>
                <div class="d-flex align-items-center gap-2">
                  <?= avatar($r['afectado_nombre'], $r['afectado_apellido']) ?>
                  <div>
                    <div class="fw-semibold"><?= htmlspecialchars($r['afectado_nombre'] . ' ' . $r['afectado_apellido']) ?></div>
                    <small class="text-muted">@<?= htmlspecialchars($r['afectado_usuario'] ?? '') ?></small>
                  </div>
                </div>
              <?php else: ?>
                <span class="text-muted small">Usuario eliminado (#<?= (int) $r['id_afectado'] ?>)</span>
              <?php endif; ?>
            </td>
            <td><span class="badge rounded-pill border px-3 py-1 <?= $claseAccion ?>"><?= ucfirst(htmlspecialchars($r['accion'])) ?></span></td>
            <td>
              <button type="button" class="btn btn-sm btn-outline-secondary rounded-2" data-bs-toggle="modal" data-bs-target="#detalleModal<?= (int) $r['id_registro'] ?>" title="Ver detalles"><i class="bi bi-eye"></i></button>
            </td>
            <td class="text-end pe-4">
              <span class="badge bg-dark bg-opacity-10 text-dark"><i class="bi bi-hdd-network me-1"></i><?= htmlspecialchars($r['ip_origen'] ?? '-') ?></span>
            </td>
          </tr>

          <div class="modal fade" id="detalleModal<?= (int) $r['id_registro'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Detalle del registro #<?= (int) $r['id_registro'] ?></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <pre class="bg-light rounded-3 p-3 mb-0 text-start" style="white-space: pre-wrap;"><?= htmlspecialchars($r['detalles'] ?? 'Sin detalles', ENT_QUOTES, 'UTF-8') ?></pre>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($registros)): ?>
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">
              <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
              No hay registros de auditoría.
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