<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Gestión de Usuarios - Sistema de Tutorías';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$titulo = 'Usuarios del Sistema';
$descripcion = 'Administra las cuentas de administradores, tutores y estudiantes registrados.';
$icono = 'bi-people-fill';
$contador = $totalRegistros;
$accion = '<a href="usuarios_crear.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3"><i class="bi bi-person-plus-fill"></i><span class="fw-semibold">Nuevo Usuario</span></a>';
include __DIR__ . '/../partials/page_header.php';
?>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <?php
    // SPRINT 6: componente reutilizable; opciones de rol/estado dinámicas.
    $filtroQ = $q;
    $filtroQPlaceholder = 'Buscar por nombre, username o correo...';
    $filtroOcultos = ['orden' => $ordenActual, 'dir' => $dirActual];
    $filtroSelectores = [[
        'nombre' => 'estado',
        'etiqueta' => 'Estado',
        'opciones' => array_merge(
            [['valor' => '', 'texto' => 'Todos los estados']],
            array_map(fn($e) => ['valor' => $e, 'texto' => ucfirst($e)], $estados)
        ),
        'seleccionado' => $estado,
        'minWidth' => 150,
    ]];
    $filtroPorPagina = $pag['por_pagina'];
    $filtroLimpiarUrl = 'usuarios_listar.php';
    include __DIR__ . '/../partials/panel_filtros.php';
    ?>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0" id="tablaUsuarios">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('ID', 'id', $ordenActual, $dirActual, 'ps-4'); ?>
          <?php encabezadoOrdenable('Nombre', 'nombre', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Correo Electrónico', 'correo', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Rol Asignado', 'rol', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Estado', 'estado', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Fecha Registro', 'fecha', $ordenActual, $dirActual); ?>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($usuarios as $u): ?>
          <?php
            // Asignación de clases de badge según rol
            $badgeRol = 'badge-admin';
            if ($u['nombre_rol'] === 'tutor') $badgeRol = 'badge-tutor';
            if ($u['nombre_rol'] === 'estudiante') $badgeRol = 'badge-estudiante';
          ?>
          <tr>
            <td class="ps-4 text-muted fw-semibold">#<?= htmlspecialchars($u['id_usuario']) ?></td>
            <td>
              <div class="d-flex align-items-center gap-3">
                <?= avatar($u['nombre'], $u['apellido'], $u['nombre_rol']) ?>
                <div>
                  <div class="fw-bold text-dark"><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></div>
                  <small class="text-muted"><i class="bi bi-person me-1"></i><?= htmlspecialchars($u['usuario']) ?></small>
                </div>
              </div>
            </td>
            <td>
              <span class="text-secondary"><?= htmlspecialchars($u['correo']) ?></span>
            </td>
            <td>
              <?= rol_badge($u['nombre_rol']) ?>
            </td>
            <td>
              <?= estado_badge($u['estado']) ?>
            </td>
            <td class="text-muted small">
              <?= date('d/m/Y', strtotime($u['fecha_registro'])) ?>
            </td>
            <td class="text-end pe-4">
              <div class="btn-group" role="group">
                <a href="usuarios_editar.php?id=<?= $u['id_usuario'] ?>" class="btn btn-outline-primary btn-sm rounded-start-2" title="Editar">
                  <i class="bi bi-pencil-fill"></i>
                </a>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-end-2" 
                        onclick="confirmarEliminacion('usuarios_eliminar.php?id=<?= $u['id_usuario'] ?>', 'Se eliminará al usuario <?= htmlspecialchars($u['usuario']) ?> y sus accesos.')"
                        title="Eliminar">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($usuarios)): ?>
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">
              <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
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
