<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <div>
        <span class="eyebrow">Oferta académica</span>
        <h1 class="page-title mb-1">Materias</h1>
        <p class="text-muted mb-0">Las materias inactivas se conservan para el historial y no se ofrecen en nuevas tutorías.</p>
    </div>
    <a href="materias_crear.php" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nueva materia</a>
</div>
<div class="card card-custom mb-4 p-3">
    <form class="row g-2 align-items-end" method="GET">
        <div class="col-md-6"><label class="form-label fw-semibold mb-1">Buscar</label><input name="q" class="form-control" value="<?=e($_GET['q']??'')?>" placeholder="Buscar por nombre de materia..."></div>
        <div class="col-md-3"><label class="form-label fw-semibold mb-1">Estado</label><select name="estado" class="form-select"><option value="">Todas</option><option value="activo" <?=($_GET['estado']??'')==='activo'?'selected':''?>>Activas</option><option value="inactivo" <?=($_GET['estado']??'')==='inactivo'?'selected':''?>>Inactivas</option></select></div>
        <div class="col-auto"><button class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        <div class="col-auto"><a href="materias_listar.php" class="btn btn-light">Limpiar</a></div>
    </form>
</div>
<div class="card card-custom overflow-hidden"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>N.º materia</th><th>Carrera</th><th>Estado</th><th>Tutores asignados</th><th class="text-end">Acciones</th></tr></thead><tbody>
<?php foreach($materias as $m): ?>
<tr><td><strong><?=e($m['id_materia'])?>. <?=e($m['nombre_materia'])?></strong></td><td><?=e($m['nombre_carrera'])?></td><td><span class="badge text-bg-<?=estadoBadge($m['estado'])?>"><?=e(estadoEtiqueta($m['estado']))?></span></td><td><span class="badge bg-primary-subtle text-primary"><?=e($m['total_tutores'])?></span></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" title="Editar nombre" href="materias_editar.php?id=<?=$m['id_materia']?>"><i class="bi bi-pencil"></i></a> <form class="d-inline" method="POST" action="materias_eliminar.php" onsubmit="return confirm('¿Deseas eliminar esta materia? Se conservará en el historial y quedará registrada en auditoría.')"><?=csrfField()?><input type="hidden" name="id" value="<?=$m['id_materia']?>"><button class="btn btn-sm btn-outline-<?=$m['estado']==='activo'?'danger':'success'?>" title="<?=$m['estado']==='activo'?'Eliminar':'Restaurar'?>"><i class="bi bi-toggle-<?=$m['estado']==='activo'?'off':'on'?>"></i></button></form></td></tr>
<?php endforeach; if(!$materias): ?><tr><td colspan="5" class="text-center py-5 text-muted">No se encontraron materias.</td></tr><?php endif; ?></tbody></table></div></div>
<?php include __DIR__.'/../layouts/footer.php'; ?>
