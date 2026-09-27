<?php
if (!isset($pendientes)) {
    header('Location: /controllers/notificaciones_listar.php');
    exit;
}
require_once __DIR__ . '/../../includes/verificar_sesion.php';
require_once __DIR__ . '/../../includes/notificaciones_helper.php';
$tituloPagina = 'Mis Notificaciones - UPDS';
include __DIR__ . '/../layouts/header.php';
?>

<?php
$titulo = 'Mis Notificaciones';
$descripcion = 'Avisos sobre tribunales, cartas, comprobantes y documentos de tus tutorías.';
$icono = 'bi-bell-fill';
$contador = $pendientes;
include __DIR__ . '/../partials/page_header.php';
?>

<?php if ($pendientes > 0): ?>
  <div class="d-flex justify-content-end mb-3">
    <form method="POST" action="../controllers/notificaciones_marcar.php">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="todas" value="1">
      <button class="btn btn-outline-primary btn-sm rounded-3" type="submit">
        <i class="bi bi-check2-all me-1"></i>Marcar todas como leídas
      </button>
    </form>
  </div>
<?php endif; ?>

<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white py-3 border-0">
    <?php
    $filtroQ = $params['q'];
    $filtroQPlaceholder = 'Buscar por mensaje, tipo...';
    $filtroQAuto = true;
    $filtroOcultos = ['orden' => $params['orden'], 'dir' => $params['dir']];
    $filtroPorPagina = $params['por_pagina'];
    $filtroBotones = false;
    include __DIR__ . '/../partials/panel_filtros.php';
    ?>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
          <?php encabezadoOrdenable('Fecha', 'fecha', $params['orden'], $params['dir'], 'ps-4'); ?>
          <?php encabezadoOrdenable('Tipo', 'tipo', $params['orden'], $params['dir']); ?>
          <th>Mensaje</th>
          <th>Origen</th>
          <th class="text-end pe-4">Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($registrosPagina as $n): ?>
          <tr class="<?= empty($n['leida']) ? 'table-primary' : '' ?>">
            <td class="ps-4">
              <div class="fw-bold text-dark"><?= date('d/m/Y H:i', strtotime($n['fecha'])) ?></div>
            </td>
            <td>
              <span class="badge border px-3 py-1 badge-accent"><?= htmlspecialchars(str_replace('_', ' ', $n['tipo'])) ?></span>
            </td>
            <td class="text-muted">
              <?= htmlspecialchars($n['mensaje']) ?>
              <button type="button"
                      class="btn btn-link btn-sm p-0 ms-1 align-baseline text-decoration-none"
                      data-bs-toggle="modal"
                      data-bs-target="#modalNotificacion"
                      data-notif="<?= htmlspecialchars(json_encode([
                          'id'      => (int) $n['id_notificacion'],
                          'tipo'    => (string) $n['tipo'],
                          'mensaje' => (string) $n['mensaje'],
                          'fecha'   => (string) $n['fecha'],
                          'origen'  => trim(($n['origen_nombre'] ?? '') . ' ' . ($n['origen_apellido'] ?? '')) ?: 'Sistema',
                          'leida'   => !empty($n['leida']),
                          'destino' => notificacion_enlace_seguro($n['enlace'] ?? '', $_SESSION['rol'] ?? ''),
                      ], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>">Ver</button>
            </td>
            <td>
              <?php if (!empty($n['id_origen'])): ?>
                <div class="d-flex align-items-center gap-2">
                  <?= avatar($n['origen_nombre'] ?? '?', $n['origen_apellido'] ?? '', '') ?>
                  <span class="small text-dark"><?= htmlspecialchars(trim(($n['origen_nombre'] ?? '') . ' ' . ($n['origen_apellido'] ?? ''))) ?></span>
                </div>
              <?php else: ?>
                <span class="text-muted small">Sistema</span>
              <?php endif; ?>
            </td>
            <td class="text-end pe-4">
              <?php if (empty($n['leida'])): ?>
                <form method="POST" action="../controllers/notificaciones_marcar.php" class="d-inline">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="id_notificacion" value="<?= $n['id_notificacion'] ?>">
                  <button class="btn btn-sm btn-outline-secondary rounded-3" type="submit" title="Marcar como leída">
                    <i class="bi bi-circle me-1"></i>No leída
                  </button>
                </form>
              <?php else: ?>
                <span class="text-muted small"><i class="bi bi-check-circle me-1"></i>Leída</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($registrosPagina)): ?>
          <tr>
            <td colspan="5" class="text-center py-5 text-muted">
              <i class="bi bi-bell-slash fs-1 d-block mb-2 text-secondary"></i>
              No tienes notificaciones todavía.
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

<div class="modal fade" id="modalNotificacion" tabindex="-1" aria-labelledby="modalNotificacionTitulo" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalNotificacionTitulo">
          <i class="bi bi-bell-fill me-1 text-primary"></i>Detalle de la notificación
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
          <span class="badge border px-3 py-1 badge-accent" id="modalNotifTipo">—</span>
          <span class="small text-muted" id="modalNotifFecha">—</span>
        </div>
        <p class="mb-3" id="modalNotifMensaje" style="white-space: pre-line; line-height: 1.55;">—</p>
        <div class="d-flex align-items-center gap-2 border-top pt-3">
          <i class="bi bi-person-circle text-secondary"></i>
          <span class="small text-muted">Origen: <strong id="modalNotifOrigen">Sistema</strong></span>
        </div>
      </div>
      <div class="modal-footer">
        <span class="small text-muted me-auto" id="modalNotifEstado"></span>
        <button type="button" class="btn btn-outline-secondary rounded-3 px-3" data-bs-dismiss="modal">Cerrar</button>
        <a href="#" id="modalNotifDestino" class="btn btn-primary rounded-3 px-3 d-none">
          <i class="bi bi-arrow-right-circle me-1"></i>Ir al destino
        </a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const modalEl = document.getElementById('modalNotificacion');
  if (!modalEl) return;
  const campos = {
    tipo: document.getElementById('modalNotifTipo'),
    fecha: document.getElementById('modalNotifFecha'),
    mensaje: document.getElementById('modalNotifMensaje'),
    origen: document.getElementById('modalNotifOrigen'),
    estado: document.getElementById('modalNotifEstado'),
    destino: document.getElementById('modalNotifDestino')
  };

  document.querySelectorAll('[data-notif]').forEach((boton) => {
    boton.addEventListener('click', function () {
      let datos;
      try {
        datos = JSON.parse(boton.dataset.notif);
      } catch (e) {
        return;
      }
      if (!datos) return;

      campos.tipo.textContent = String(datos.tipo || '').replace(/_/g, ' ');
      campos.fecha.textContent = datos.fecha ? new Date(datos.fecha.replace(' ', 'T')).toLocaleString('es-BO') : '—';
      campos.mensaje.textContent = datos.mensaje || 'Sin detalle disponible.';
      campos.origen.textContent = datos.origen || 'Sistema';
      campos.estado.innerHTML = datos.leida
        ? '<i class="bi bi-check-circle me-1"></i>Leída'
        : '<i class="bi bi-circle me-1"></i>No leída';

      if (datos.destino) {
        campos.destino.href = datos.destino;
        campos.destino.classList.remove('d-none');
      } else {
        campos.destino.classList.add('d-none');
        campos.destino.removeAttribute('href');
      }
    });
  });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>