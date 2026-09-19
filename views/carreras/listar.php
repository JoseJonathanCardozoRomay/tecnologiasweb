<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Gestión de Carreras - Sistema de Tutorías';
include __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
      <i class="bi bi-mortarboard text-primary"></i>
      <span>Carreras Universitarias</span>
      <span class="badge bg-primary bg-opacity-10 text-primary fs-6"><?= $totalRegistros ?></span>
    </h2>
    <p class="text-muted mb-0">Programas académicos de la Universidad Privada Domingo Savio.</p>
  </div>
  <div class="d-flex gap-2">
    <a href="materias_listar.php" class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 py-2 rounded-3">
      <i class="bi bi-journal-bookmark"></i>
      <span>Ver Materias</span>
    </a>
    <a href="carreras_crear.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3">
      <i class="bi bi-plus-circle-fill"></i>
      <span class="fw-semibold">Nueva Carrera</span>
    </a>
  </div>
</div>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <form method="GET" class="input-group" style="max-width: 320px;">
      <input type="hidden" name="orden" value="<?= htmlspecialchars($ordenActual) ?>">
      <input type="hidden" name="dir" value="<?= htmlspecialchars($dirActual) ?>">
      <input type="hidden" name="pagina" value="1">
      <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
      <input type="search" name="q" value="<?= htmlspecialchars($q) ?>" class="form-control bg-light border-start-0" placeholder="Buscar carrera...">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('ID', 'id', $ordenActual, $dirActual, 'ps-4'); ?>
          <?php encabezadoOrdenable('Nombre de la Carrera', 'nombre', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Total Materias', 'materias', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Estudiantes Inscritos', 'estudiantes', $ordenActual, $dirActual); ?>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($carreras as $c): ?>
          <tr>
            <td class="ps-4 text-muted fw-semibold">#<?= htmlspecialchars($c['id_carrera']) ?></td>
            <td>
              <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i class="bi bi-building text-primary opacity-75"></i>
                <?= htmlspecialchars($c['nombre_carrera']) ?>
              </div>
            </td>
            <td>
              <span class="badge bg-light text-dark border px-2 py-1">
                <i class="bi bi-journal-check me-1 text-primary"></i><?= $c['total_materias'] ?> materias
              </span>
            </td>
            <td>
              <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">
                <i class="bi bi-people me-1"></i><?= $c['total_estudiantes'] ?> estudiante(s)
              </span>
            </td>
            <td class="text-end pe-4">
              <div class="btn-group" role="group">
                <a href="carreras_editar.php?id=<?= $c['id_carrera'] ?>" class="btn btn-outline-primary btn-sm rounded-start-2" title="Editar">
                  <i class="bi bi-pencil-fill"></i>
                </a>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-end-2" 
                        onclick="confirmarEliminacion('carreras_eliminar.php?id=<?= $c['id_carrera'] ?>', 'Se eliminará la carrera <?= htmlspecialchars($c['nombre_carrera']) ?>.')"
                        title="Eliminar">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($carreras)): ?>
          <tr>
            <td colspan="5" class="text-center py-5 text-muted">
              <i class="bi bi-mortarboard fs-1 d-block mb-2 text-secondary"></i>
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
