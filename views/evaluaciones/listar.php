<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <div><span class="eyebrow">Retroalimentación</span><h1 class="page-title mb-1">Evaluaciones</h1><p class="text-muted mb-0">Calificaciones y comentarios de tutorías realizadas. Una vez registradas, se conservan como historial.</p></div>
    <?php if(esEstudiante()): ?><a href="evaluaciones_crear.php" class="btn btn-primary"><i class="bi bi-star me-1"></i>Nueva evaluación</a><?php endif; ?>
</div>
<div class="alert alert-info border-0 shadow-sm"><i class="bi bi-info-circle me-2"></i>Las evaluaciones registradas no tienen opciones de edición o eliminación para proteger la trazabilidad de la retroalimentación.</div>
<div class="card card-custom overflow-hidden"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Fecha</th><th>Estudiante</th><th>Tutor</th><th>Materia</th><th>Calificación</th><th>Comentario</th></tr></thead><tbody>
<?php foreach($registros as $r): ?>
<tr><td><?=date('d/m/Y',strtotime($r['fecha']))?></td><td><?=e($r['estudiante'])?></td><td><?=e($r['tutor'])?></td><td><?=e($r['nombre_materia'])?></td><td><span class="text-warning"><?=str_repeat('★',(int)$r['calificacion'])?></span> <strong><?=e($r['calificacion'])?>/5</strong></td><td><?=e($r['comentario']??'—')?></td></tr>
<?php endforeach; if(!$registros): ?><tr><td colspan="6" class="text-center py-5 text-muted">No hay evaluaciones registradas.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php include __DIR__.'/../layouts/footer.php'; ?>
