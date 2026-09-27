<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Calendario de Modalidad de Grado - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<?php
$titulo = 'Calendario de Modalidad de Grado';
$descripcion = 'Hitos/calendario de la cohorte seleccionada (HU-022).';
$icono = 'bi-calendar-event';
$contador = $totalHitos;
$accion = $cohorteId > 0
    ? '<a href="/controllers/mg_calendario_crear.php?cohorte=' . $cohorteId . '" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3"><i class="bi bi-plus-circle-fill"></i><span class="fw-semibold">Nuevo Hito</span></a>'
    : '';
include __DIR__ . '/../../partials/page_header.php';
?>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <form method="GET" action="/controllers/mg_calendario_listar.php" class="row g-2 align-items-end">
      <div class="col-md-5 col-lg-4">
        <label for="sel-cohorte" class="form-label fw-semibold text-secondary small text-uppercase mb-1">Cohorte</label>
        <select id="sel-cohorte" name="cohorte" class="form-select rounded-3" onchange="this.form.submit()">
          <?php foreach ($cohortes as $c): ?>
            <option value="<?= (int) $c['id_cohorte_mg'] ?>" <?= (int) $c['id_cohorte_mg'] === $cohorteId ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nombre_periodo']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-auto">
        <button type="submit" class="btn btn-outline-primary px-3 rounded-3"><i class="bi bi-filter"></i> Ver</button>
      </div>
    </form>
    <?php if ($cohorteActual): ?>
      <div class="small text-muted mt-2">
        <?= htmlspecialchars($cohorteActual['fecha_inicio']) ?> al
        <?= $cohorteActual['fecha_fin'] ? htmlspecialchars($cohorteActual['fecha_fin']) : '—' ?>
        · <span class="fw-semibold"><?= htmlspecialchars(ucfirst($cohorteActual['estado'])) ?></span>
      </div>
    <?php endif; ?>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <th class="ps-4" style="width: 45px;">Ord.</th>
          <th>Hito</th>
          <th style="width: 190px;">Tipo</th>
          <th style="width: 140px;">Fecha</th>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($hitos as $h): ?>
          <tr>
            <td class="ps-4 text-muted fw-semibold"><?= (int) $h['orden'] ?></td>
            <td>
              <div class="fw-bold text-dark"><?= htmlspecialchars($h['titulo']) ?></div>
              <?php if ($h['descripcion']): ?>
                <div class="small text-muted"><?= htmlspecialchars($h['descripcion']) ?></div>
              <?php endif; ?>
            </td>
            <td><?= mgTipoHitoBadge($h['tipo_hito']) ?></td>
            <td class="text-muted"><?= htmlspecialchars($h['fecha_hito']) ?></td>
            <td class="text-end pe-4">
              <div class="btn-group" role="group">
                <a href="/controllers/mg_calendario_editar.php?id=<?= (int) $h['id_hito'] ?>" class="btn btn-outline-primary btn-sm rounded-start-2" title="Editar">
                  <i class="bi bi-pencil-fill"></i>
                </a>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-end-2"
                        onclick="confirmarEliminacion('/controllers/mg_calendario_eliminar.php?id=<?= (int) $h['id_hito'] ?>', 'Se eliminará el hito <?= htmlspecialchars($h['titulo']) ?>.')"
                        title="Eliminar">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($hitos)): ?>
          <tr>
            <td colspan="5" class="text-center py-5 text-muted">
              <i class="bi bi-calendar-event fs-1 d-block mb-2 text-secondary"></i>
              No hay hitos registrados para esta cohorte.
              <?php if ($cohorteId > 0): ?>
                <a href="/controllers/mg_calendario_crear.php?cohorte=<?= $cohorteId ?>">Registrar el primero</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>