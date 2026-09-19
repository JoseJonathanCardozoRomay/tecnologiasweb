<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Gestión de Tutorías - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<!-- Métricas de Tutorías -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card card-custom p-3 text-center border-start border-primary border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Total Sesiones</small>
      <h3 class="fw-bold mb-0 text-dark"><?= $metricas['total'] ?? 0 ?></h3>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card card-custom p-3 text-center border-start border-warning border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Pendientes</small>
      <h3 class="fw-bold mb-0 text-warning"><?= $metricas['pendientes'] ?? 0 ?></h3>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card card-custom p-3 text-center border-start border-info border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Confirmadas</small>
      <h3 class="fw-bold mb-0 text-info"><?= $metricas['confirmadas'] ?? 0 ?></h3>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card card-custom p-3 text-center border-start border-success border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Realizadas</small>
      <h3 class="fw-bold mb-0 text-success"><?= $metricas['realizadas'] ?? 0 ?></h3>
    </div>
  </div>
</div>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">
  <div>
    <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
      <i class="bi bi-calendar-check-fill text-primary"></i>
      <span>Registro General de Tutorías</span>
    </h2>
    <p class="text-muted mb-0">Supervisión académica de sesiones solicitadas, agendadas y completadas.</p>
  </div>
  <a href="tutorias_solicitar.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3">
    <i class="bi bi-calendar-plus-fill"></i>
    <span class="fw-semibold">+ Agendar Tutoría</span>
  </a>
</div>

<!-- Filtros de Estado y periodo -->
<div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
  <a href="tutorias_listar.php<?= $filtroPeriodo ? '?periodo=' . urlencode($filtroPeriodo) : '' ?>" class="btn btn-sm <?= empty($filtroEstado) ? 'btn-dark' : 'btn-outline-secondary' ?> rounded-pill px-3">
    Todas
  </a>
  <a href="tutorias_listar.php?estado=pendiente<?= $filtroPeriodo ? '&periodo=' . urlencode($filtroPeriodo) : '' ?>" class="btn btn-sm <?= $filtroEstado === 'pendiente' ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning text-dark' ?> rounded-pill px-3">
    <i class="bi bi-clock me-1"></i>Pendientes
  </a>
  <a href="tutorias_listar.php?estado=confirmada<?= $filtroPeriodo ? '&periodo=' . urlencode($filtroPeriodo) : '' ?>" class="btn btn-sm <?= $filtroEstado === 'confirmada' ? 'btn-info text-white fw-bold' : 'btn-outline-info' ?> rounded-pill px-3">
    <i class="bi bi-check2 me-1"></i>Confirmadas
  </a>
  <a href="tutorias_listar.php?estado=realizada<?= $filtroPeriodo ? '&periodo=' . urlencode($filtroPeriodo) : '' ?>" class="btn btn-sm <?= $filtroEstado === 'realizada' ? 'btn-success fw-bold' : 'btn-outline-success' ?> rounded-pill px-3">
    <i class="bi bi-check-circle me-1"></i>Realizadas
  </a>
  <a href="tutorias_listar.php?estado=cancelada<?= $filtroPeriodo ? '&periodo=' . urlencode($filtroPeriodo) : '' ?>" class="btn btn-sm <?= $filtroEstado === 'cancelada' ? 'btn-danger fw-bold' : 'btn-outline-danger' ?> rounded-pill px-3">
    <i class="bi bi-x-circle me-1"></i>Canceladas
  </a>
  <form method="GET" class="ms-md-auto">
    <input type="hidden" name="estado" value="<?= htmlspecialchars($filtroEstado ?? '') ?>">
    <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">
    <input type="hidden" name="orden" value="<?= htmlspecialchars($ordenActual) ?>">
    <input type="hidden" name="dir" value="<?= htmlspecialchars($dirActual) ?>">
    <input type="hidden" name="pagina" value="1">
    <select name="periodo" class="form-select form-select-sm" onchange="this.form.submit()">
      <option value="">Todos los periodos</option>
      <?php foreach ($periodos as $periodo): ?><option value="<?= htmlspecialchars($periodo) ?>" <?= $filtroPeriodo === $periodo ? 'selected' : '' ?>><?= htmlspecialchars($periodo) ?></option><?php endforeach; ?>
    </select>
  </form>
</div>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <form method="GET" class="input-group" style="max-width: 320px;">
      <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
      <input type="hidden" name="estado" value="<?= htmlspecialchars($filtroEstado ?? '') ?>">
      <input type="hidden" name="periodo" value="<?= htmlspecialchars($filtroPeriodo) ?>">
      <input type="hidden" name="orden" value="<?= htmlspecialchars($ordenActual) ?>">
      <input type="hidden" name="dir" value="<?= htmlspecialchars($dirActual) ?>">
      <input type="hidden" name="pagina" value="1">
      <input type="search" name="q" value="<?= htmlspecialchars($q) ?>" class="form-control bg-light border-start-0" placeholder="Buscar por alumno, materia...">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </form>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0" id="tablaTutorias">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('Fecha y Horario', 'fecha', $ordenActual, $dirActual, 'ps-4'); ?>
          <th>Periodo</th>
          <?php encabezadoOrdenable('Materia y Carrera', 'materia', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Estudiante', 'estudiante', $ordenActual, $dirActual); ?>
          <?php encabezadoOrdenable('Docente Tutor', 'tutor', $ordenActual, $dirActual); ?>
          <th>Modalidad</th>
          <?php encabezadoOrdenable('Estado', 'estado', $ordenActual, $dirActual); ?>
          <th class="text-end pe-4">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tutorias as $t): ?>
          <?php
            $badgeEstado = 'bg-warning text-dark border-warning';
            if ($t['estado'] === 'confirmada') $badgeEstado = 'bg-info bg-opacity-10 text-info-emphasis border-info-subtle';
            if ($t['estado'] === 'realizada') $badgeEstado = 'bg-success bg-opacity-10 text-success border-success-subtle';
            if ($t['estado'] === 'cancelada') $badgeEstado = 'bg-danger bg-opacity-10 text-danger border-danger-subtle';
          ?>
          <tr>
            <td class="ps-4">
              <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($t['fecha'])) ?></div>
              <small class="text-muted"><i class="bi bi-clock me-1"></i><?= substr($t['hora_inicio'], 0, 5) ?> - <?= substr($t['hora_fin'], 0, 5) ?></small>
            </td>
            <td><span class="badge rounded-pill border px-2 py-1" style="color: #002b49; border-color: #f5a623 !important; background: #fff8e8;"><?= htmlspecialchars($t['periodo'] ?? 'I-' . date('Y')) ?></span></td>
            <td>
              <div class="fw-semibold text-primary"><?= htmlspecialchars($t['nombre_materia']) ?></div>
              <small class="text-muted"><?= htmlspecialchars($t['nombre_carrera'] ?? 'General') ?></small>
            </td>
            <td>
              <div class="fw-medium text-dark"><?= htmlspecialchars($t['est_nombre'] . ' ' . $t['est_apellido']) ?></div>
              <small class="text-muted"><?= htmlspecialchars($t['est_correo']) ?></small>
            </td>
            <td>
              <div class="fw-medium text-dark"><?= htmlspecialchars($t['tut_nombre'] . ' ' . $t['tut_apellido']) ?></div>
              <small class="text-muted"><?= htmlspecialchars($t['tut_correo']) ?></small>
            </td>
            <td>
              <?php if ($t['modalidad'] === 'virtual'): ?>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">
                  <i class="bi bi-camera-video me-1"></i>Virtual
                </span>
              <?php else: ?>
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1">
                  <i class="bi bi-geo-alt me-1"></i>Presencial
                </span>
              <?php endif; ?>
              <?php if (!empty($t['lugar_o_enlace'])): ?>
                <div class="small text-muted text-truncate" style="max-width: 140px;" title="<?= htmlspecialchars($t['lugar_o_enlace']) ?>">
                  <?= htmlspecialchars($t['lugar_o_enlace']) ?>
                </div>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge rounded-pill border px-3 py-1 text-capitalize <?= $badgeEstado ?>">
                <?= htmlspecialchars($t['estado']) ?>
              </span>
              <?php if (!empty($t['calificacion'])): ?>
                <div class="text-warning small mt-1">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="bi bi-star<?= $i <= $t['calificacion'] ? '-fill' : '' ?>"></i>
                  <?php endfor; ?>
                </div>
              <?php endif; ?>
            </td>
            <td class="text-end pe-4">
              <div class="btn-group" role="group">
                <?php if ($t['estado'] === 'pendiente'): ?>
                  <a href="tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=confirmada" class="btn btn-outline-success btn-sm" title="Confirmar sesión">
                    <i class="bi bi-check-lg"></i>
                  </a>
                <?php endif; ?>
                <?php if ($t['estado'] === 'confirmada'): ?>
                  <a href="tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=realizada" class="btn btn-outline-primary btn-sm" title="Marcar como realizada">
                    <i class="bi bi-check2-all"></i>
                  </a>
                <?php endif; ?>
                <?php if ($t['estado'] !== 'cancelada' && $t['estado'] !== 'realizada'): ?>
                  <a href="tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=cancelada" class="btn btn-outline-warning btn-sm" title="Cancelar sesión" onclick="return confirm('¿Cancelar esta tutoría?');">
                    <i class="bi bi-slash-circle"></i>
                  </a>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-danger btn-sm" 
                        onclick="confirmarEliminacion('tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=cancelada', '¿Deseas cancelar definitivamente esta tutoría?')"
                        title="Eliminar">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($tutorias)): ?>
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">
              <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
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
