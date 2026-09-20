<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
    <div><span class="eyebrow">Seguridad y trazabilidad</span><h1 class="page-title mb-1">Auditoría</h1><p class="text-muted mb-0">Consulta accesos y acciones registradas. Puedes mostrar todos los registros o filtrar por un rango de fechas.</p></div>
    <div class="d-flex gap-2"><button class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimir</button><a class="btn btn-primary" href="auditoria_descargar.php?desde=<?=urlencode($desde)?>&hasta=<?=urlencode($hasta)?>"><i class="bi bi-download me-1"></i>Descargar TXT</a></div>
</div>

<?php if($errores): ?><div class="alert alert-danger"><?php foreach($errores as $error): ?><div><?=e($error)?></div><?php endforeach; ?></div><?php endif; ?>

<div class="card card-custom p-3 mb-4 audit-filters">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4"><label class="form-label fw-semibold">Desde</label><input type="date" name="desde" class="form-control" value="<?=e($desde)?>"></div>
        <div class="col-md-4"><label class="form-label fw-semibold">Hasta</label><input type="date" name="hasta" class="form-control" value="<?=e($hasta)?>"></div>
        <div class="col-auto"><button class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        <div class="col-auto"><a href="auditoria_listar.php" class="btn btn-light">Mostrar todos</a></div>
    </form>
    <div class="small text-muted mt-3"><i class="bi bi-info-circle me-1"></i>Si dejas ambas fechas vacías, la descarga incluye todos los registros de auditoría.</div>
</div>

<div class="card card-custom overflow-hidden">
    <div class="p-3 border-bottom"><strong><?=count($registros)?></strong> registro(s) mostrado(s). La vista web está limitada a 1000 filas; la descarga TXT no aplica ese límite.</div>
    <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Módulo</th><th>Resultado</th><th>Descripción</th><th>IP</th></tr></thead><tbody>
    <?php foreach($registros as $r): ?><tr><td><?=date('d/m/Y H:i',strtotime($r['fecha_hora']))?></td><td><?=e($r['usuario'])?></td><td><span class="badge text-bg-light border"><?=e($r['accion']??'ACCESO')?></span></td><td><?=e($r['modulo']??'Autenticación')?></td><td><span class="badge text-bg-<?=$r['resultado']==='exitoso'?'success':'danger'?>"><?=e(ucfirst($r['resultado']))?></span></td><td><?=e($r['descripcion']??'—')?></td><td class="text-muted small"><?=e($r['ip_origen']??'—')?></td></tr><?php endforeach; if(!$registros): ?><tr><td colspan="7" class="text-center py-5 text-muted">No hay registros para el filtro seleccionado.</td></tr><?php endif; ?>
    </tbody></table></div>
</div>
<style>@media print{.navbar,.audit-filters,.btn,footer{display:none!important}main{padding:0!important}.card-custom{box-shadow:none;border:1px solid #ddd}.table{font-size:10px}}</style>
<?php include __DIR__.'/../layouts/footer.php'; ?>
