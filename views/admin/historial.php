<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Historial y Auditoría - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$migas = [
    ['texto' => 'Historial y Auditoría', 'actual' => true],
];
$titulo = 'Historial y Auditoría';
$descripcion = 'Registro de accesos al sistema y eventos críticos (asignaciones, cartas, tribunales, reuniones e informes).';
$icono = 'bi-shield-lock-fill';
$contador = count($registros);
include __DIR__ . '/../partials/page_header.php';
?>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <?php
    // SPRINT 6: filtros unificados vía componente reutilizable (panel_filtros).
    // Datos 100% dinámicos: categorías desde HistorialModel, roles desde la BD,
    // buscador de usuario con autocompletado en tiempo real (buscar_usuarios.php).
    $filtroTabs = [
        'nombre' => 'tipo_evento',
        'grupos' => array_merge(
            [['valor' => '', 'texto' => 'Todas las categorías']],
            array_map(fn($nombre) => ['valor' => 'categoria:' . $nombre, 'texto' => $nombre], array_keys($categorias))
        ),
        'activo' => $filtro_categoria !== '' ? 'categoria:' . $filtro_categoria : '',
    ];
    $filtroQ = $buscar_usuario;
    $filtroQNombre = 'buscar_usuario';
    $filtroQEtiqueta = 'Usuario';
    $filtroQPlaceholder = 'Nombre real o username (ej: Delfina, est24)...';
    $filtroQAuto = true;
    $filtroQAutocompleteUrl = '/controllers/buscar_usuarios.php';
    $filtroSelectores = [[
        'nombre' => 'id_rol',
        'etiqueta' => 'Rol',
        'opciones' => array_merge(
            [['valor' => '0', 'texto' => 'Todos los roles']],
            array_map(fn($r) => ['valor' => (string) $r['id_rol'], 'texto' => ucfirst($r['nombre_rol'])], $roles)
        ),
        'seleccionado' => (string) $id_rol,
        'minWidth' => 150,
    ]];
    $filtroFechas = [
        'desde' => ['nombre' => 'desde', 'valor' => $filtro_desde],
        'hasta' => ['nombre' => 'hasta', 'valor' => $filtro_hasta],
    ];
    $filtroPorPagina = $parametros['por_pagina'];
    $filtroLimpiarUrl = 'historial_listar.php';
    include __DIR__ . '/../partials/panel_filtros.php';
    ?>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('Fecha y Hora', 'fecha_hora', $parametros['orden'], $parametros['dir'], 'ps-4'); ?>
          <?php encabezadoOrdenable('Tipo de evento', 'tipo_evento', $parametros['orden'], $parametros['dir']); ?>
          <th>Usuario</th>
          <th>Descripción</th>
          <th>IP Origen</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($registrosPagina as $h): ?>
          <?php
            $tipo = $h['tipo_evento'];
            $iconoEvento = 'bi-shield-exclamation';
            $badgeClase = 'bg-secondary bg-opacity-10 text-secondary border-secondary-subtle';
            if (strpos($tipo, 'LOGIN') !== false || strpos($tipo, 'LOGOUT') !== false) { $iconoEvento = 'bi-key'; $badgeClase = 'bg-info bg-opacity-10 text-info-emphasis border-info-subtle'; }
            if (strpos($tipo, 'TUTORIA_') !== false) { $iconoEvento = 'bi-calendar-x'; $badgeClase = 'bg-warning bg-opacity-10 text-warning-emphasis border-warning-subtle'; }
            if (strpos($tipo, 'CARTA_') !== false) { $iconoEvento = 'bi-envelope-paper'; $badgeClase = 'bg-primary bg-opacity-10 text-primary border-primary-subtle'; }
            if (strpos($tipo, 'TRIBUNAL_') !== false) { $iconoEvento = 'bi-people'; $badgeClase = 'bg-accent bg-opacity-10 text-accent border-accent-subtle'; }
            if (strpos($tipo, 'REUNION_') !== false || strpos($tipo, 'INFORME_') !== false) { $iconoEvento = 'bi-journal-check'; $badgeClase = 'bg-success bg-opacity-10 text-success border-success-subtle'; }
          ?>
          <tr>
            <td class="ps-4">
              <div class="fw-bold text-dark"><?= date('d/m/Y H:i', strtotime($h['fecha_hora'])) ?></div>
            </td>
            <td>
              <span class="badge border px-3 py-1 <?= $badgeClase ?>"><i class="bi <?= $iconoEvento ?> me-1"></i><?= htmlspecialchars(str_replace('_', ' ', $tipo)) ?></span>
            </td>
            <td>
              <?php if (!empty($h['id_usuario'])): ?>
                <div class="d-flex align-items-center gap-2">
                  <?= avatar($h['usuario_nombre'] ?? '?', $h['usuario_apellido'] ?? '', '') ?>
                  <div>
                    <div class="fw-semibold text-dark"><?= htmlspecialchars(trim(($h['usuario_nombre'] ?? '') . ' ' . ($h['usuario_apellido'] ?? ''))) ?></div>
                    <small class="text-muted">@<?= htmlspecialchars($h['usuario'] ?? (string) $h['id_usuario']) ?></small>
                  </div>
                </div>
              <?php else: ?>
                <span class="text-muted small">—</span>
              <?php endif; ?>
            </td>
            <td class="text-muted"><?= htmlspecialchars($h['descripcion']) ?></td>
            <td><span class="text-secondary"><i class="bi bi-router me-1"></i><?= htmlspecialchars($h['ip_origen'] ?? '—') ?></span></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($registrosPagina)): ?>
          <tr>
            <td colspan="5" class="text-center py-5 text-muted">
              <i class="bi bi-shield-lock fs-1 d-block mb-2 text-secondary"></i>
              No hay eventos que coincidan con los filtros aplicados.
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