<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../includes/csrf.php';
?>

<?php
$titulo = 'Notificaciones';
$descripcion = 'Avisos internos sobre el estado de tus tutorías.';
$icono = 'bi-bell-fill';
$accion = null;
if (!empty($notificaciones)) {
    ob_start();
    ?>
    <form method="POST" action="/controllers/notificaciones_marcar.php" class="d-flex">
      <?php echo csrf_campo(); ?>
      <input type="hidden" name="todas" value="1">
      <button type="submit" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-check2-all"></i> Marcar todas como leídas
      </button>
    </form>
    <?php
    $accion = ob_get_clean();
}
include __DIR__ . '/../partials/page_header.php';
?>

<div class="card card-custom shadow-sm overflow-hidden">
 <div class="list-group list-group-flush">
    <?php foreach ($notificaciones as $n): ?>
      <div class="list-group-item d-flex align-items-start gap-3 py-3 <?= empty($n['leida']) ? 'bg-light' : '' ?>">
        <div class="fs-4 text-primary">
          <i class="bi <?= empty($n['leida']) ? 'bi-bell-fill' : 'bi-bell' ?>" aria-hidden="true"></i>
        </div>
        <div class="flex-grow-1">
          <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border-secondary-subtle text-uppercase"><?= htmlspecialchars($n['tipo']) ?></span>
            <?php if (empty($n['leida'])): ?>
              <span class="badge rounded-pill bg-danger">Nueva</span>
            <?php else: ?>
              <span class="badge rounded-pill bg-light text-muted border">Leída</span>
            <?php endif; ?>
            <small class="text-muted ms-auto"><i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($n['fecha_creacion'])) ?></small>
          </div>
          <div class="mt-1 text-dark"><?= htmlspecialchars($n['mensaje']) ?></div>
        </div>
        <div>
          <form method="POST" action="/controllers/notificaciones_marcar.php">
            <?php echo csrf_campo(); ?>
            <input type="hidden" name="id" value="<?= (int) $n['id_notificacion'] ?>">
            <button type="submit" class="btn btn-sm btn-outline-primary" title="<?= !empty($n['url']) ? 'Ver' : 'Marcar como leída' ?>">
              <i class="bi <?= !empty($n['url']) ? 'bi-box-arrow-up-right' : 'bi-check2' ?>"></i>
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
 </div>

  <?php if (empty($notificaciones)): ?>
    <?php
    $emptyIcono = 'bi-bell-slash';
    $emptyTitulo = 'Sin notificaciones';
    $emptyTexto = 'Aquí verás los avisos sobre tus tutorías.';
    include __DIR__ . '/../partials/empty_state.php';
    ?>
  <?php endif; ?>

  <?php $mostrarSelector = false; include __DIR__ . '/../partials/paginacion.php'; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
