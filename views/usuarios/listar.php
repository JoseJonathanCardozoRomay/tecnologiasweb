<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Gestión de Usuarios - Sistema de Tutorías';
include __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
      <i class="bi bi-people-fill text-primary"></i>
      <span>Usuarios del Sistema</span>
      <span class="badge bg-primary bg-opacity-10 text-primary fs-6"><?= $totalRegistros ?></span>
    </h2>
    <p class="text-muted mb-0">Administra las cuentas de administradores, tutores y estudiantes registrados.</p>
  </div>
  <div>
    <a href="usuarios_crear.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3">
      <i class="bi bi-person-plus-fill"></i>
      <span class="fw-semibold">Nuevo Usuario</span>
    </a>
  </div>
</div>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
    <form method="GET" class="input-group" style="max-width: 420px;">
      <input type="hidden" name="orden" value="<?= htmlspecialchars($ordenActual) ?>">
      <input type="hidden" name="dir" value="<?= htmlspecialchars($dirActual) ?>">
      <input type="hidden" name="pagina" value="1">
      <select name="estado" class="form-select" aria-label="Filtrar por estado"><option value="">Todos los estados</option><option value="activo" <?= $estado === 'activo' ? 'selected' : '' ?>>Activos</option><option value="pendiente" <?= $estado === 'pendiente' ? 'selected' : '' ?>>Pendientes</option><option value="inactivo" <?= $estado === 'inactivo' ? 'selected' : '' ?>>Inactivos</option></select>
      <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
      <input type="search" name="q" value="<?= htmlspecialchars($q) ?>" class="form-control bg-light border-start-0" placeholder="Buscar por nombre, usuario...">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </form>
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
                <div class="monogram rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 0.9rem;">
                  <?= strtoupper(substr($u['nombre'], 0, 1) . substr($u['apellido'], 0, 1)) ?>
                </div>
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
              <span class="badge rounded-pill px-3 py-1 text-capitalize <?= $badgeRol ?>">
                <?= htmlspecialchars($u['nombre_rol']) ?>
              </span>
            </td>
            <td>
              <?php if ($u['estado'] === 'activo'): ?>
                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">
                  <i class="bi bi-check-circle me-1"></i>Activo
                </span>
              <?php else: ?>
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1">
                  <i class="bi bi-dash-circle me-1"></i>Inactivo
                </span>
              <?php endif; ?>
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
                <?php if ($u['estado'] === 'pendiente'): ?><button type="button" class="btn btn-outline-success btn-sm" onclick="enviarPostSeguro('usuarios_aprobar.php?id=<?= $u['id_usuario'] ?>')" title="Aprobar cuenta"><i class="bi bi-check2"></i></button><?php endif; ?>
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

<?php include __DIR__ . '/../partials/paginacion.php'; ?>

<?php include __DIR__ . '/../layouts/footer.php'; ?>