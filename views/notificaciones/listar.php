<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="d-flex justify-content-between align-items-end gap-3 mb-4">
    <div><span class="eyebrow">Cuenta</span><h1 class="page-title mb-1">Notificaciones</h1><p class="text-muted mb-0">Avisos relacionados con tu actividad en el sistema.</p></div>
</div>
<div class="card card-custom overflow-hidden">
    <div class="list-group list-group-flush">
        <?php foreach ($registros as $n): ?>
            <div class="list-group-item p-4 <?=empty($n['leida']) ? 'bg-light' : ''?>">
                <div class="d-flex gap-3 align-items-start">
                    <div class="metric-icon text-primary bg-primary-subtle"><i class="bi bi-bell"></i></div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between gap-3"><strong><?=e($n['tipo'])?></strong><small class="text-muted"><?=e(date('d/m/Y H:i', strtotime($n['fecha_creacion'])))?></small></div>
                        <p class="mb-2 mt-1"><?=e($n['mensaje'])?></p>
                        <?php if (!empty($n['url'])): ?><a href="<?=e($n['url'])?>" class="small text-decoration-none">Ver detalle</a><?php endif; ?>
                    </div>
                    <?php if (empty($n['leida'])): ?>
                        <form method="post"><?=csrfField()?><input type="hidden" name="id_notificacion" value="<?=e($n['id_notificacion'])?>"><button class="btn btn-sm btn-outline-primary">Marcar leída</button></form>
                    <?php else: ?><span class="badge text-bg-light border">Leída</span><?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (!$registros): ?><div class="text-center py-5 text-muted"><i class="bi bi-bell-slash fs-1 d-block mb-2"></i>No tienes notificaciones.</div><?php endif; ?>
    </div>
</div>
<?php include __DIR__.'/../layouts/footer.php'; ?>
