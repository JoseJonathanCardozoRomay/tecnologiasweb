<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <div>
        <span class="eyebrow">Oferta académica</span>
        <h1 class="page-title mb-1">Carreras universitarias</h1>
        <p class="text-muted mb-0">Las carreras inactivas se conservan para proteger el historial académico.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/controllers/materias_listar.php" class="btn btn-outline-primary"><i class="bi bi-journal-bookmark me-1"></i>Materias</a>
        <a href="carreras_crear.php" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nueva carrera</a>
    </div>
</div>

<div class="card card-custom p-3 mb-4">
    <form class="row g-2 align-items-end" method="GET">
        <div class="col-md-4">
            <label class="form-label fw-semibold mb-1">Estado</label>
            <select name="estado" class="form-select">
                <option value="">Todas</option>
                <option value="activo" <?=($_GET['estado'] ?? '') === 'activo' ? 'selected' : ''?>>Activas</option>
                <option value="inactivo" <?=($_GET['estado'] ?? '') === 'inactivo' ? 'selected' : ''?>>Inactivas</option>
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        <div class="col-auto"><a href="carreras_listar.php" class="btn btn-light">Limpiar</a></div>
    </form>
</div>

<div class="card card-custom overflow-hidden">
    <div class="p-3 border-bottom"><div class="small text-muted"><strong><?=count($carreras)?></strong> carrera(s) mostrada(s)</div></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>N.º</th><th>Carrera</th><th>Estado</th><th>Materias</th><th>Estudiantes</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
            <?php foreach($carreras as $c): ?>
                <tr>
                    <td class="text-muted fw-semibold"><?=e($c['id_carrera'])?></td>
                    <td><strong><?=e($c['nombre_carrera'])?></strong></td>
                    <td><span class="badge text-bg-<?=estadoBadge($c['estado'])?>"><?=e(estadoEtiqueta($c['estado']))?></span></td>
                    <td><span class="badge text-bg-light border"><?=e($c['total_materias'])?> materia(s)</span></td>
                    <td><span class="badge bg-success-subtle text-success"><?=e($c['total_estudiantes'])?> estudiante(s)</span></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" title="Editar nombre" href="carreras_editar.php?id=<?=$c['id_carrera']?>"><i class="bi bi-pencil"></i></a>
                        <form class="d-inline" method="POST" action="carreras_eliminar.php" onsubmit="return confirm('¿Deseas eliminar esta carrera? Se conservará en el historial y quedará registrada en auditoría.')">
                            <?=csrfField()?><input type="hidden" name="id" value="<?=$c['id_carrera']?>">
                            <button class="btn btn-sm btn-outline-<?=$c['estado']==='activo'?'danger':'success'?>" title="<?=$c['estado']==='activo'?'Eliminar':'Restaurar'?>"><i class="bi bi-toggle-<?=$c['estado']==='activo'?'off':'on'?>"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; if(!$carreras): ?>
                <tr><td colspan="6" class="text-center py-5 text-muted">No hay carreras para el filtro seleccionado.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__.'/../layouts/footer.php'; ?>
