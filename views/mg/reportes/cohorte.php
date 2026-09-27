<?php
require_once __DIR__ . '/../../../includes/verificar_sesion.php';
$tituloPagina = 'Reporte de Cohorte - Sistema de Tutorías';
include __DIR__ . '/../../layouts/header.php';
?>

<?php
$titulo = 'Reportes de Cohorte';
$descripcion = 'Reporte consolidado de expedientes por cohorte, exportable a CSV (HU-032).';
$icono = 'bi-file-earmark-bar-chart';
$contador = count($filas);
$accion = ($cohorteId > 0)
    ? '<a href="/controllers/mg_reportes_cohorte.php?cohorte=' . $cohorteId . '&exportar=1" class="btn btn-success d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3"><i class="bi bi-download"></i><span class="fw-semibold">Exportar CSV</span></a>'
    : '';
include __DIR__ . '/../../partials/page_header.php';
?>

<div class="card card-custom shadow-sm overflow-hidden mb-4">
  <div class="card-header bg-white py-3 border-0">
    <form method="GET" action="/controllers/mg_reportes_cohorte.php" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label fw-semibold text-secondary small text-uppercase mb-1" for="sel-rp-cohorte">Cohorte</label>
        <select id="sel-rp-cohorte" name="cohorte" class="form-select rounded-3" onchange="this.form.submit()">
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
      <div class="col-md-auto ms-auto">
        <a href="/controllers/mg_reportes_cohorte.php?cohorte=<?= (int) $cohorteId ?>&exportar=1" class="btn btn-success px-3 rounded-3">
          <i class="bi bi-download me-1"></i> Exportar CSV
        </a>
      </div>
    </form>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-2">
    <div class="card card-custom shadow-sm border-0"><div class="card-body py-3">
      <div class="text-muted small text-uppercase">Expedientes</div>
      <div class="fs-4 fw-bold"><?= (int) $kpi['total'] ?></div>
    </div></div>
  </div>
  <div class="col-6 col-md-2">
    <div class="card card-custom shadow-sm border-0"><div class="card-body py-3">
      <div class="text-muted small text-uppercase">Con tutor</div>
      <div class="fs-4 fw-bold text-success"><?= (int) $kpi['con_tutor'] ?></div>
    </div></div>
  </div>
  <div class="col-6 col-md-2">
    <div class="card card-custom shadow-sm border-0"><div class="card-body py-3">
      <div class="text-muted small text-uppercase">Sin tutor</div>
      <div class="fs-4 fw-bold text-danger"><?= (int) $kpi['sin_tutor'] ?></div>
    </div></div>
  </div>
  <div class="col-6 col-md-2">
    <div class="card card-custom shadow-sm border-0"><div class="card-body py-3">
      <div class="text-muted small text-uppercase">Defensas</div>
      <div class="fs-4 fw-bold"><?= (int) $kpi['programadas'] ?> / <?= (int) $kpi['evaluadas'] ?></div>
      <div class="small text-muted">prog. / eval.</div>
    </div></div>
  </div>
  <div class="col-6 col-md-2">
    <div class="card card-custom shadow-sm border-0"><div class="card-body py-3">
      <div class="text-muted small text-uppercase">Aprobados</div>
      <div class="fs-4 fw-bold text-success"><?= (int) $kpi['aprobados'] ?></div>
      <div class="small text-muted">Reprobados: <?= (int) $kpi['reprobados'] ?></div>
    </div></div>
  </div>
  <div class="col-6 col-md-2">
    <div class="card card-custom shadow-sm border-0"><div class="card-body py-3">
      <div class="text-muted small text-uppercase">Promedio</div>
      <div class="fs-4 fw-bold"><?= $kpi['promedio'] !== null ? number_format($kpi['promedio'], 2) : '—' ?></div>
      <div class="small text-muted">defensas evaluadas</div>
    </div></div>
  </div>
</div>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <th class="ps-4">Estudiante</th>
          <th>Carrera</th>
          <th>Modalidad</th>
          <th>Tutor</th>
          <th>Estado</th>
          <th>Defensa</th>
          <th class="text-end pe-4">Nota final</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($filas as $f): ?>
          <tr>
            <td class="ps-4">
              <a href="/controllers/mg_expediente.php?id=<?= (int) $f['id_expediente_mg'] ?>" class="text-decoration-none">
                <div class="fw-semibold text-dark"><?= htmlspecialchars($f['estudiante_apellido'] . ' ' . $f['estudiante_nombre']) ?></div>
              </a>
              <div class="small text-muted"><?= htmlspecialchars((string) $f['registro_universitario']) ?></div>
            </td>
            <td class="small"><?= htmlspecialchars($f['nombre_carrera']) ?></td>
            <td class="small"><?= htmlspecialchars($f['modalidad_nombre']) ?></td>
            <td class="small">
              <?= $f['tutor_nombre'] ? htmlspecialchars($f['tutor_apellido'] . ' ' . $f['tutor_nombre']) : '<span class="text-muted">Sin tutor</span>' ?>
            </td>
            <td><?= mgEstadoExpedienteBadge($f['estado_expediente']) ?></td>
            <td class="small">
              <?php if ($f['fecha_defensa']): ?>
                <?= htmlspecialchars($f['fecha_defensa']) ?><span class="text-muted"> <?= htmlspecialchars($f['hora_inicio']) ?></span>
                <div class="text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars($f['estado_defensa']) ?></div>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td class="text-end pe-4">
              <?php if ($f['nota_final'] !== null): ?>
                <span class="fw-bold <?= (float) $f['nota_final'] >= 51 ? 'text-success' : 'text-danger' ?>"><?= number_format((float) $f['nota_final'], 2) ?></span>
                <div><small><?= $f['resultado'] ? htmlspecialchars(ucfirst($f['resultado'])) : '' ?></small></div>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($filas)): ?>
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">
              <i class="bi bi-file-earmark-bar-chart fs-1 d-block mb-2 text-secondary"></i>
              No hay expedientes para esta cohorte.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>