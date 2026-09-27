<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Gestión de Tutorías - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$titulo = 'Gestión de Tutorías';
$descripcion = 'Supervisa las solicitudes y el avance de las sesiones de tutoría académica.';
$icono = 'bi-calendar-check';
include __DIR__ . '/../partials/page_header.php';
?>

<!-- Métricas de Tutorías -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md">
    <div class="card card-custom p-3 text-center border-start border-primary border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Total</small>
      <h3 class="fw-bold mb-0 text-dark"><?= $metricas['total'] ?? 0 ?></h3>
    </div>
  </div>
  <div class="col-6 col-md">
    <div class="card card-custom p-3 text-center border-start border-accent border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Pendientes</small>
      <h3 class="fw-bold mb-0 text-accent"><?= $metricas['pendiente'] ?? 0 ?></h3>
    </div>
  </div>
  <div class="col-6 col-md">
    <div class="card card-custom p-3 text-center border-start border-info border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Confirmadas</small>
      <h3 class="fw-bold mb-0 text-info"><?= $metricas['confirmada'] ?? 0 ?></h3>
    </div>
  </div>
  <div class="col-6 col-md">
    <div class="card card-custom p-3 text-center border-start border-success border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Realizadas</small>
      <h3 class="fw-bold mb-0 text-success"><?= $metricas['realizada'] ?? 0 ?></h3>
    </div>
  </div>
  <div class="col-6 col-md">
    <div class="card card-custom p-3 text-center border-start border-danger border-4">
      <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Canceladas</small>
      <h3 class="fw-bold mb-0 text-danger"><?= $metricas['cancelada'] ?? 0 ?></h3>
    </div>
  </div>
</div>

<div class="card card-custom shadow-sm overflow-hidden">
  <!-- Filtros unificados (SPRINT 6): pestañas de estado dinámicas del modelo -->
  <div class="card-header bg-white py-3 border-0">
    <?php
    $gruposTabs = array_merge(
        [['valor' => '', 'texto' => 'Todas']],
        array_map(fn($etiqueta, $estado) => ['valor' => $estado, 'texto' => $etiqueta], $estadosFiltro, array_keys($estadosFiltro))
    );
    $filtroTabs = ['nombre' => 'estado', 'grupos' => $gruposTabs, 'activo' => $filtroEstado];
    $filtroQ = $q;
    $filtroQPlaceholder = 'Buscar por alumno, tutor, materia...';
    $filtroOcultos = ['orden' => $ordenActual, 'dir' => $dirActual];
    $filtroPorPagina = $pag['por_pagina'];
    $filtroLimpiarUrl = 'tutorias_listar.php';
    include __DIR__ . '/../partials/panel_filtros.php';
    ?>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0" id="tablaTutorias">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('Fecha y Horario', 'fecha', $ordenActual, $dirActual, 'ps-4'); ?>
          <?php encabezadoOrdenable('Materia', 'materia', $ordenActual, $dirActual); ?>
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
            $badgeEstado = 'badge-accent';
            if ($t['estado'] === 'confirmada') $badgeEstado = 'bg-info bg-opacity-10 text-info-emphasis border-info-subtle';
            if ($t['estado'] === 'realizada') $badgeEstado = 'bg-success bg-opacity-10 text-success border-success-subtle';
            if ($t['estado'] === 'cancelada') $badgeEstado = 'bg-danger bg-opacity-10 text-danger border-danger-subtle';
            $etiquetaEstado = [
                'pendiente'  => 'Pendiente',
                'confirmada' => 'Confirmada',
                'realizada'  => 'Realizada',
                'cancelada'  => 'Cancelada',
            ][$t['estado']] ?? ucfirst($t['estado']);
          ?>
          <tr>
            <td class="ps-4">
              <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($t['fecha'])) ?></div>
              <small class="text-muted"><i class="bi bi-clock me-1"></i><?= substr($t['hora_inicio'], -8, 5) ?> - <?= substr($t['hora_fin'], -8, 5) ?></small>
            </td>
            <td>
              <div class="fw-semibold text-primary"><?= htmlspecialchars($t['nombre_materia']) ?></div>
            </td>
            <td>
              <div class="fw-medium text-dark"><?= htmlspecialchars($t['estudiante_nombre'] . ' ' . $t['estudiante_apellido']) ?></div>
            </td>
            <td>
              <div class="fw-medium text-dark"><?= htmlspecialchars($t['tutor_nombre'] . ' ' . $t['tutor_apellido']) ?></div>
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
                <div class="small text-muted text-truncate" style="max-width: 120px;" title="<?= htmlspecialchars($t['lugar_o_enlace']) ?>">
                  <?= htmlspecialchars($t['lugar_o_enlace']) ?>
                </div>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge rounded-pill border px-3 py-1 <?= $badgeEstado ?>">
                <?= htmlspecialchars($etiquetaEstado) ?>
                <?= (!empty($t['estado_conclusion_nombre']) && $t['estado'] === 'finalizada') ? ' — ' . htmlspecialchars($t['estado_conclusion_nombre']) : '' ?>
              </span>
            </td>
            <td class="text-end pe-4">
              <div class="btn-group" role="group">
                <?php if (($t['tipo'] ?? 'apoyo') === 'grado' && $esAdministrador && $t['estado'] !== 'finalizada' && $t['estado'] !== 'cancelada'): ?>
                  <a href="tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=finalizada" onclick="confirmarFinalizacion(this.href, <?= htmlspecialchars(json_encode($estadosConclusion), ENT_QUOTES, 'UTF-8') ?>); return false;" class="btn btn-outline-primary btn-sm" title="Finalizar Modalidad de Grado">
                    <i class="bi bi-mortarboard"></i>&nbsp;Finalizar MG
                  </a>
                <?php endif; ?>
                <?php if (($t['tipo'] ?? 'apoyo') === 'grado' && ($esAdministrador || $esAuxiliar)): ?>
                  <a href="expediente_documentos.php?id=<?= $t['id_tutoria'] ?>" class="btn btn-outline-secondary btn-sm" title="Expediente de la tutoría">
                    <i class="bi bi-folder2-open"></i>&nbsp;Expediente
                  </a>
                <?php endif; ?>
                <?php if ($t['estado'] === 'pendiente'): ?>
                  <a href="tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=confirmada" onclick="enviarPostSeguro(this.href); return false;" class="btn btn-outline-success btn-sm" title="Confirmar sesión">
                    <i class="bi bi-check-lg"></i>
                  </a>
                <?php endif; ?>
                <?php if ($t['estado'] === 'confirmada'): ?>
                  <a href="tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=realizada" onclick="enviarPostSeguro(this.href); return false;" class="btn btn-outline-primary btn-sm" title="Registrar como realizada">
                    <i class="bi bi-check2-all"></i>
                  </a>
                <?php endif; ?>
                <?php if (in_array($t['estado'], ['pendiente', 'confirmada'], true)): ?>
                  <a href="tutorias_cambiar_estado.php?id=<?= $t['id_tutoria'] ?>&estado=cancelada" class="btn btn-outline-accent btn-sm" title="Cancelar" onclick="confirmarCancelacion(this.href); return false;">
                    <i class="bi bi-slash-circle"></i>
                  </a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($tutorias)): ?>
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">
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
  <?php
  $mostrarSelector = false;
  include __DIR__ . '/../partials/paginacion.php';
  ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>