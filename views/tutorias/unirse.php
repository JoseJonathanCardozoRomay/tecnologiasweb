<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Unirme a una Tutoría - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$migas = [
    ['texto' => 'Mis Tutorías', 'url' => '/views/estudiante/panel.php'],
    ['texto' => 'Unirme a una tutoría', 'actual' => true],
];
$titulo = 'Tutorías de Apoyo Abiertas';
$descripcion = 'Sumate a una tutoría de apoyo ya abierta en tu carrera. Cuando alcance el cupo mínimo se confirma.';
$icono = 'bi-people-fill';
include __DIR__ . '/../partials/page_header.php';
?>

<?php if (!empty($errores)): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
    <ul class="mb-0"><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<div class="card card-custom shadow-sm">
  <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-list-check me-1 text-primary"></i>Tutorías de apoyo de tu carrera</h5>
    <a href="/controllers/tutorias_solicitar.php" class="btn btn-sm btn-outline-primary rounded-3"><i class="bi bi-plus-lg me-1"></i>Solicitar una nueva</a>
  </div>
  <div class="card-header bg-white py-3 border-0">
    <?php
    $filtroQ = $q;
    $filtroQPlaceholder = 'Buscar por materia, tutor...';
    $filtroPorPagina = $pag['por_pagina'];
    $filtroLimpiarUrl = 'tutorias_unirse.php';
    include __DIR__ . '/../partials/panel_filtros.php';
    ?>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <th class="ps-4">Materia</th>
          <th>Docente Tutor</th>
          <th>Fecha y Horario</th>
          <th>Modalidad</th>
          <th>Cupo</th>
          <th class="text-end pe-4">Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tutorias as $t): ?>
          <tr>
            <td class="ps-4 fw-semibold text-primary"><?= htmlspecialchars($t['nombre_materia']) ?></td>
            <td>
              <div class="fw-medium text-dark">Prof. <?= htmlspecialchars($t['tutor_nombre'] . ' ' . $t['tutor_apellido']) ?></div>
              <?php if (!empty($t['especialidad'])): ?><small class="text-muted"><?= htmlspecialchars($t['especialidad']) ?></small><?php endif; ?>
            </td>
            <td>
              <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($t['fecha'])) ?></div>
              <small class="text-muted"><i class="bi bi-clock me-1"></i><?= substr($t['hora_inicio'], -8, 5) ?> - <?= substr($t['hora_fin'], -8, 5) ?></small>
            </td>
            <td>
              <?php if ($t['modalidad'] === 'virtual'): ?>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1"><i class="bi bi-camera-video me-1"></i>Virtual</span>
              <?php else: ?>
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1"><i class="bi bi-geo-alt me-1"></i>Presencial</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge <?= $t['habilitada'] ? 'bg-success bg-opacity-10 text-success border-success-subtle' : 'bg-warning bg-opacity-10 text-warning-emphasis border-warning-subtle' ?> px-2 py-1">
                <?= (int) $t['inscritos'] ?> / <?= (int) ($t['minimo_cupos'] ?? 3) ?> inscritos
              </span>
              <?php if ($t['habilitada']): ?>
                <small class="d-block text-success mt-1"><i class="bi bi-check-circle"></i> Confirmada</small>
              <?php else: ?>
                <small class="d-block text-muted mt-1">Se confirma al llegar al mínimo</small>
              <?php endif; ?>
            </td>
            <td class="text-end pe-4">
              <form method="POST" action="/controllers/tutorias_unirse.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id_tutoria" value="<?= (int) $t['id_tutoria'] ?>">
                <button type="submit" class="btn btn-primary btn-sm rounded-3"><i class="bi bi-person-plus me-1"></i>Unirme</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($tutorias)): ?>
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">
              <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
              Todavía no hay tutorías de apoyo abiertas en tu carrera.
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