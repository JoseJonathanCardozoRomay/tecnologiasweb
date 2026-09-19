<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Reportes Académicos - UPDS Tarija';
include __DIR__ . '/../layouts/header.php';
$total = (int) ($metricas['total'] ?? 0);
$realizadas = (int) ($metricas['realizadas'] ?? 0);
$porcentajeCumplimiento = $total > 0 ? round(($realizadas / $total) * 100) : 0;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
  <div>
    <div class="text-uppercase text-muted small fw-bold" style="letter-spacing: .12em;">Jefatura de Carrera</div>
    <h2 class="fw-bold mb-1">Informe Académico</h2>
    <p class="text-muted mb-0">Seguimiento de tutorías y satisfacción estudiantil por periodo.</p>
  </div>
  <div class="d-flex gap-2 align-items-center no-print">
    <form method="GET" class="d-flex gap-2">
      <label for="periodo" class="visually-hidden">Periodo académico</label>
      <select id="periodo" name="periodo" class="form-select" onchange="this.form.submit()">
        <?php if (empty($periodos)): ?><option value="I-<?= date('Y') ?>">I-<?= date('Y') ?></option><?php endif; ?>
        <?php foreach ($periodos as $periodo): ?>
          <option value="<?= htmlspecialchars($periodo) ?>" <?= $periodo === $periodoSeleccionado ? 'selected' : '' ?>><?= htmlspecialchars($periodo) ?></option>
        <?php endforeach; ?>
      </select>
    </form>
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2" onclick="window.print()">
      <i class="bi bi-printer-fill"></i><span>Imprimir Informe</span>
    </button>
  </div>
</div>

<div class="report-heading mb-3"><span>Periodo académico</span><strong><?= htmlspecialchars($periodoSeleccionado) ?></strong></div>

<div class="row g-3 mb-4">
 <div class="col-6 col-lg-3"><div class="card card-custom p-3 h-100"><small class="text-muted text-uppercase fw-bold">Sesiones totales</small><div class="fs-2 fw-bold text-primary mt-2"><?= $total ?></div></div></div>
 <div class="col-6 col-lg-3"><div class="card card-custom p-3 h-100"><small class="text-muted text-uppercase fw-bold">Cumplimiento</small><div class="fs-2 fw-bold text-success mt-2"><?= $porcentajeCumplimiento ?>%</div><div class="progress mt-2" style="height: 6px"><div class="progress-bar bg-success" style="width: <?= $porcentajeCumplimiento ?>%"></div></div></div></div>
 <div class="col-6 col-lg-3"><div class="card card-custom p-3 h-100"><small class="text-muted text-uppercase fw-bold">Horas dictadas</small><div class="fs-2 fw-bold text-primary mt-2"><?= number_format(array_sum(array_map(fn($t) => (float) $t['horas_dictadas'], $tutores)), 1) ?></div></div></div>
 <div class="col-6 col-lg-3"><div class="card card-custom p-3 h-100"><small class="text-muted text-uppercase fw-bold">Satisfacción</small><div class="fs-2 fw-bold text-accent mt-2"><?= $satisfaccion['promedio_satisfaccion'] ? number_format($satisfaccion['promedio_satisfaccion'], 2) : '—' ?><small class="fs-6 text-muted"> / 5</small></div></div></div>
 <div class="col-6 col-lg-3"><div class="card card-custom p-3 h-100"><small class="text-muted text-uppercase fw-bold">Asistencia</small><div class="fs-2 fw-bold text-info mt-2"><?= $porcentajeAsistencia !== null ? $porcentajeAsistencia . '%' : '—' ?></div><?php if ($porcentajeAsistencia !== null): ?><div class="progress mt-2" style="height: 6px"><div class="progress-bar bg-info" style="width: <?= $porcentajeAsistencia ?>%"></div></div><?php else: ?><small class="text-muted">Sin seguimientos</small><?php endif; ?></div></div>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <section class="card card-custom h-100">
      <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="fw-bold mb-1">Materias con mayor demanda</h5><p class="text-muted small mb-0">Solicitudes registradas en <?= htmlspecialchars($periodoSeleccionado) ?>.</p></div>
      <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th class="ps-4">Materia</th><th>Sesiones</th><th class="pe-4">Realizadas</th></tr></thead><tbody>
        <?php foreach ($materias as $materia): ?><tr><td class="ps-4 fw-semibold"><?= htmlspecialchars($materia['nombre_materia']) ?></td><td><?= (int) $materia['total_sesiones'] ?></td><td class="pe-4"><div class="d-flex align-items-center gap-2"><div class="progress flex-grow-1" style="height: 7px"><div class="progress-bar progress-bar-accent" style="width: <?= $materia['total_sesiones'] > 0 ? round(((int) $materia['realizadas'] / (int) $materia['total_sesiones']) * 100) : 0 ?>%"></div></div><?= (int) $materia['realizadas'] ?></div></td></tr><?php endforeach; ?>
        <?php if (empty($materias)): ?><tr><td colspan="3" class="text-center text-muted py-4">No hay sesiones para este periodo.</td></tr><?php endif; ?>
      </tbody></table></div>
    </section>
  </div>
  <div class="col-lg-5">
    <section class="card card-custom h-100">
      <div class="card-header bg-white border-0 pt-4 px-4"><h5 class="fw-bold mb-1">Desempeño docente</h5><p class="text-muted small mb-0">Horas de tutoría efectivamente impartidas.</p></div>
      <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th class="ps-4">Tutor</th><th>Sesiones</th><th class="pe-4">Horas</th></tr></thead><tbody>
        <?php foreach ($tutores as $tutor): ?><tr><td class="ps-4 fw-semibold"><?= htmlspecialchars($tutor['tutor']) ?></td><td><?= (int) $tutor['sesiones_realizadas'] ?></td><td class="pe-4 fw-bold text-primary"><?= number_format((float) $tutor['horas_dictadas'], 1) ?></td></tr><?php endforeach; ?>
        <?php if (empty($tutores)): ?><tr><td colspan="3" class="text-center text-muted py-4">No hay docentes con sesiones.</td></tr><?php endif; ?>
      </tbody></table></div>
    </section>
  </div>
</div>

<style>
  .report-heading { display: flex; justify-content: space-between; align-items: center; border-left: 4px solid #9fb8cc; background: #fff; padding: .8rem 1rem; }
  .report-heading strong { color: #002b49; font-size: 1.1rem; }
  @media print {
    .no-print, nav, footer { display: none !important; }
    body { background: #fff; color: #111827; }
    .app-content { max-width: none; width: 100%; padding: 1rem 0; }
    .card-custom { box-shadow: none; border: 1px solid #e2e8f0; }
    .report-heading { border: 1px solid #e2e8f0; border-left: 4px solid #9fb8cc; }
  }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
