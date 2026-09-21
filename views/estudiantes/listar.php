<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Gestión de Estudiantes - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$titulo = 'Estudiantes Registrados';
$descripcion = 'Listado de alumnos habilitados para solicitar tutorías académicas.';
$icono = 'bi-mortarboard';
$contador = $totalRegistros;
$colorContador = 'success';
$accion = '<a href="usuarios_crear.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3"><i class="bi bi-person-plus-fill"></i><span class="fw-semibold">+ Nuevo Estudiante</span></a>';
include __DIR__ . '/../partials/page_header.php';
?>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <form method="GET" class="input-group" style="max-width: 320px;">
      <input type="hidden" name="orden" value="<?= htmlspecialchars($ordenActual) ?>">
      <input type="hidden" name="dir" value="<?= htmlspecialchars($dirActual) ?>">
      <input type="hidden" name="pagina" value="1">
      <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
      <input type="search" name="q" value="<?= htmlspecialchars($q) ?>" class="form-control bg-light border-start-0" placeholder="Buscar estudiante...">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('Estudiante', 'nombre', $ordenActual, $dirActual, 'ps-4'); ?>
          <?php encabezadoOrdenable('Reg. Universitario', 'registro', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Carrera', 'carrera', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Semestre', 'semestre', $ordenActual, $dirActual); ?>
          <th>Contacto</th>
          <?php encabezadoOrdenable('Tutorías Solicitadas', 'tutorias', $ordenActual, $dirActual); ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($estudiantes as $e): ?>
          <tr>
            <td class="ps-4">
              <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success fw-bold" style="width: 42px; height: 42px;">
                  <?= strtoupper(substr($e['nombre'], 0, 1) . substr($e['apellido'], 0, 1)) ?>
                </div>
                <div>
                  <div class="fw-bold text-dark"><?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?></div>
                  <small class="text-muted"><i class="bi bi-person me-1"></i><?= htmlspecialchars($e['usuario']) ?></small>
                </div>
              </div>
            </td>
            <td>
              <span class="badge bg-light text-dark border px-2 py-1 font-monospace">
                <?= htmlspecialchars($e['registro_universitario'] ?? 'S/R') ?>
              </span>
            </td>
            <td>
              <span class="fw-medium text-dark"><?= htmlspecialchars($e['nombre_carrera']) ?></span>
            </td>
            <td>
              <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1">
                Semestre <?= $e['semestre'] ?>
              </span>
            </td>
            <td>
              <div><i class="bi bi-envelope me-1 text-muted"></i><?= htmlspecialchars($e['correo']) ?></div>
              <?php if (!empty($e['telefono'])): ?>
                <small class="text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($e['telefono']) ?></small>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">
                <i class="bi bi-calendar-event me-1"></i><?= $e['total_tutorias'] ?> sesiones
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($estudiantes)): ?>
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">
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
  <?php include __DIR__ . '/../partials/paginacion.php'; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
