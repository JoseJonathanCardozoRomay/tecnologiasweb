<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Reportes Académicos - UPDS Tarija';
include __DIR__ . '/../layouts/header.php';
$total = (int) ($metricas['total'] ?? 0);
$realizadas = (int) ($metricas['realizadas'] ?? 0);
$porcentajeCumplimiento = $total > 0 ? round(($realizadas / $total) * 100) : 0;
?>

<?php
$titulo = 'Informe Académico';
$descripcion = 'Seguimiento de tutorías y satisfacción estudiantil por periodo.';
$icono = 'bi-bar-chart';
$migas = [
    ['texto' => 'Jefatura de Carrera'],
    ['texto' => 'Informe Académico', 'actual' => true],
];
ob_start();
?>
<div class="no-print d-flex gap-2 align-items-center flex-wrap">
<form method="GET" class="d-flex gap-2">
 <label for="periodo" class="visually-hidden">Periodo académico</label>
 <select id="periodo" name="periodo" class="form-select select2-enabled" style="width: 220px;" data-placeholder="Periodo académico" data-allow-clear="false" onchange="this.form.submit()">
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
<?php
$accion = ob_get_clean();
include __DIR__ . '/../partials/page_header.php';
?>

<div class="report-heading mb-3"><span>Periodo académico</span><strong><?= htmlspecialchars($periodoSeleccionado) ?></strong></div>

<div class="row g-3 mb-4">
 <div class="col-6 col-lg-3"><div class="card card-custom p-3 h-100"><small class="text-muted text-uppercase fw-bold">Sesiones totales</small><div class="fs-2 fw-bold text-primary mt-2"><?= $total ?></div></div></div>
 <div class="col-6 col-lg-3"><div class="card card-custom p-3 h-100"><small class="text-muted text-uppercase fw-bold">Cumplimiento</small><div class="fs-2 fw-bold text-success mt-2"><?= $porcentajeCumplimiento ?>%</div><div class="progress mt-2" style="height: 6px"><div class="progress-bar bg-success" style="width: <?= $porcentajeCumplimiento ?>%"></div></div></div></div>
 <div class="col-6 col-lg-3"><div class="card card-custom p-3 h-100"><small class="text-muted text-uppercase fw-bold">Horas dictadas</small><div class="fs-2 fw-bold text-primary mt-2"><?= number_format(array_sum(array_map(fn($t) => (float) $t['horas_dictadas'], $tutores)), 1) ?></div></div></div>
 <div class="col-6 col-lg-3"><div class="card card-custom p-3 h-100"><small class="text-muted text-uppercase fw-bold">Satisfacción</small><div class="fs-2 fw-bold text-accent mt-2"><?= $satisfaccion['promedio_satisfaccion'] ? number_format($satisfaccion['promedio_satisfaccion'], 2) : '—' ?><small class="fs-6 text-muted"> / 5</small></div></div></div>
 <div class="col-6 col-lg-3"><div class="card card-custom p-3 h-100"><small class="text-muted text-uppercase fw-bold">Asistencia</small><div class="fs-2 fw-bold text-info mt-2"><?= $porcentajeAsistencia !== null ? $porcentajeAsistencia . '%' : '—' ?></div><?php if ($porcentajeAsistencia !== null): ?><div class="progress mt-2" style="height: 6px"><div class="progress-bar bg-info" style="width: <?= $porcentajeAsistencia ?>%"></div></div><?php else: ?><small class="text-muted">Sin seguimientos</small><?php endif; ?></div></div>
</div>

<!-- Panel visual: gráficos Chart.js alimentados por la api de reportes -->
<div class="row g-4 mb-4" data-periodo="<?= htmlspecialchars($periodoSeleccionado) ?>">
 <div class="col-12 col-lg-7">
    <section class="card card-custom h-100">
      <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-1">Tutorías por estado</h5>
        <p class="text-muted small mb-0">Distribución de las sesiones según su estado actual.</p>
      </div>
      <div class="card-body">
        <div class="chart-container"><canvas id="graficoEstados" aria-label="Gráfico de tutorías por estado" role="img"></canvas></div>
      </div>
    </section>
 </div>
 <div class="col-12 col-lg-5">
    <section class="card card-custom h-100">
      <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-1">Distribución por tutor</h5>
        <p class="text-muted small mb-0">Carga de tutorías asumida por cada docente.</p>
      </div>
      <div class="card-body">
        <div class="chart-container"><canvas id="graficoTutores" aria-label="Gráfico de distribución de tutorías por tutor" role="img"></canvas></div>
      </div>
    </section>
 </div>
 <div class="col-12">
    <section class="card card-custom">
      <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-1">Evolución mensual de tutorías</h5>
        <p class="text-muted small mb-0">Cantidad de sesiones registradas por mes en el periodo seleccionado.</p>
      </div>
      <div class="card-body">
        <div class="chart-container chart-container-wide"><canvas id="graficoMensual" aria-label="Gráfico de evolución mensual de tutorías" role="img"></canvas></div>
      </div>
    </section>
 </div>
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
  .chart-container { position: relative; height: 300px; }
  .chart-container .chart-cargando { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: .85rem; }
  @media (max-width: 575.98px) { .chart-container { height: 240px; } }
  @media print {
    .no-print, nav, footer { display: none !important; }
    body { background: #fff; color: #111827; }
    .app-content { max-width: none; width: 100%; padding: 1rem 0; }
    .card-custom { box-shadow: none; border: 1px solid #e2e8f0; }
    .report-heading { border: 1px solid #e2e8f0; border-left: 4px solid #9fb8cc; }
    .chart-container { height: 260px; }
  }
</style>

<script>
// Gráficos del informe académico. Los datos se obtienen de /controllers/api_reportes.php,
// respetando el periodo seleccionado en el filtro superior.
(() => {
  const contenedor = document.querySelector('[data-periodo]');
  if (!contenedor || typeof Chart === 'undefined') return;

  const periodo = contenedor.dataset.periodo;
  const paleta = ['#002b49', '#0ea5e9', '#f59e0b', '#22c55e', '#64748b', '#ef4444', '#6366f1', '#7d9db8'];
  const baseUrl = '/controllers/api_reportes.php';

  // Estado -> color de marca coherente con los badges del sistema.
  const colorPorEstado = {
    'Pendiente': '#f59e0b',
    'Confirmada': '#0ea5e9',
    'En Proceso': '#6366f1',
    'Realizada': '#22c55e',
    'Detenido': '#64748b',
    'Cancelada': '#ef4444'
  };

  // Opciones comunes a todos los gráficos (leyenda inferior, responsive).
  const leyendaBase = {
    position: 'bottom',
    labels: { usePointStyle: true, boxWidth: 8, font: { size: 12 } }
  };

  // Ejes para gráficos con valores enteros (barras y líneas). Se aplican en options.scales.
  const escalasBase = {
    y: {
      beginAtZero: true,
      ticks: { precision: 0 },
      grid: { color: '#eef2f7' }
    },
    x: {
      grid: { display: false }
    }
  };

  async function cargar(tipo) {
    const url = new URL(baseUrl, window.location.origin);
    url.searchParams.set('tipo', tipo);
    if (periodo) { url.searchParams.set('periodo', periodo); }
    const cabeceras = new Headers();
    cabeceras.append('Accept', 'application/json');
    const respuesta = await fetch(url.toString(), { headers: cabeceras });
    if (!respuesta.ok) { throw new Error('No se pudo cargar el reporte: ' + tipo); }
    return respuesta.json();
  }

  function vacio(canvas, mensaje) {
    const aviso = document.createElement('div');
    aviso.className = 'chart-cargando';
    aviso.textContent = mensaje;
    canvas.parentElement.appendChild(aviso);
  }

  async function iniciar() {
    try {
      const [estados, tutores, mensual] = await Promise.all([
        cargar('estados'),
        cargar('tutores'),
        cargar('mensual')
      ]);

      // 1) Barras: tutorías por estado.
      const canvasEstados = document.getElementById('graficoEstados');
      if (estados.valores.some(v => v > 0)) {
        new Chart(canvasEstados, {
          type: 'bar',
          data: {
            labels: estados.etiquetas,
            datasets: [{
              label: 'Tutorías',
              data: estados.valores,
              backgroundColor: estados.etiquetas.map(e => colorPorEstado[e] || '#002b49'),
              borderRadius: 6,
              maxBarThickness: 56
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: escalasBase
          }
        });
      } else {
        vacio(canvasEstados, 'Sin tutorías registradas en este periodo.');
      }

      // 2) Pastel: distribución por tutor.
      const canvasTutores = document.getElementById('graficoTutores');
      if (tutores.valores.some(v => v > 0)) {
        new Chart(canvasTutores, {
          type: 'doughnut',
          data: {
            labels: tutores.etiquetas,
            datasets: [{
              data: tutores.valores,
              backgroundColor: tutores.etiquetas.map((_, i) => paleta[i % paleta.length]),
              borderColor: '#fff',
              borderWidth: 2
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: leyendaBase }
          }
        });
      } else {
        vacio(canvasTutores, 'Sin datos de docentes en este periodo.');
      }

      // 3) Líneas: evolución mensual.
      const canvasMensual = document.getElementById('graficoMensual');
      if (mensual.valores.some(v => v > 0)) {
        new Chart(canvasMensual, {
          type: 'line',
          data: {
            labels: mensual.etiquetas,
            datasets: [{
              label: 'Tutorías por mes',
              data: mensual.valores,
              borderColor: '#002b49',
              backgroundColor: 'rgba(159, 184, 204, .35)',
              fill: true,
              tension: 0.35,
              pointBackgroundColor: '#002b49',
              pointRadius: 4,
              pointHoverRadius: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: escalasBase
          }
        });
      } else {
        vacio(canvasMensual, 'Sin tutorías registradas en este periodo.');
      }
    } catch (error) {
      console.error(error);
    }
  }

  iniciar();
})();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
