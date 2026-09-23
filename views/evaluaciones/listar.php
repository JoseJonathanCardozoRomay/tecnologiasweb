<?php require_once __DIR__.'/../layouts/header.php'; mostrarFlash(); ?>
<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <div><span class="eyebrow">Retroalimentación</span><h1 class="page-title mb-1">Evaluaciones</h1><p class="text-muted mb-0">Calificaciones y comentarios de tutorías realizadas. Una vez registradas, se conservan como historial.</p></div>
    <?php if(esEstudiante()): ?><a href="evaluaciones_crear.php" class="btn btn-outline-secondary"><i class="bi bi-star me-1"></i>Nueva evaluación</a><?php endif; ?>
</div>
<div class="alert alert-info border-0 shadow-sm"><i class="bi bi-info-circle me-2"></i>Las evaluaciones registradas no tienen opciones de edición o eliminación para proteger la trazabilidad de la retroalimentación.</div>

<?php
// Agrupamos las evaluaciones por tutor para que la retroalimentación de cada docente
// quede separada visualmente y sea más fácil revisar su historial.
$evaluacionesPorTutor = [];
foreach ($registros as $r) {
    $claveTutor = trim((string)($r['tutor'] ?? '')) ?: 'Tutor sin nombre';
    $evaluacionesPorTutor[$claveTutor][] = $r;
}
ksort($evaluacionesPorTutor, SORT_NATURAL | SORT_FLAG_CASE);
?>

<?php if($evaluacionesPorTutor): ?>
    <div class="d-flex flex-column gap-4">
    <?php foreach($evaluacionesPorTutor as $nombreTutor => $evaluaciones): ?>
        <section class="card card-custom overflow-hidden">
            <div class="table-card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <div class="eyebrow mb-1">Docente</div>
                    <h2 class="h5 mb-1"><?=e($nombreTutor)?></h2>
                    <p class="text-muted small mb-0">Historial de evaluaciones de las tutorías realizadas con este docente.</p>
                </div>
                <span class="badge badge-accent"><?=count($evaluaciones)?> <?=count($evaluaciones)===1?'evaluación':'evaluaciones'?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Fecha</th><th>Estudiante</th><th>Materia / Proyecto</th><th>Calificación</th><th>Comentario</th></tr></thead>
                    <tbody>
                    <?php foreach($evaluaciones as $r): ?>
                    <tr>
                        <td><?=date('d/m/Y',strtotime($r['fecha']))?></td>
                        <td><?=e($r['estudiante'])?></td>
                        <td>
                            <?php if (!empty($r['proyecto_grado'])): ?>
                                <span class="badge text-bg-success mb-1"><i class="bi bi-mortarboard me-1"></i>Proyecto de grado</span>
                                <div class="fw-semibold"><?=e($r['proyecto_grado'])?></div>
                            <?php elseif (!empty($r['nombre_materia'])): ?>
                                <span class="badge text-bg-light border text-dark mb-1"><i class="bi bi-book me-1"></i>Materia</span>
                                <div class="fw-semibold"><?=e($r['nombre_materia'])?></div>
                            <?php else: ?>
                                <span class="text-muted">Sin referencia académica</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="text-warning"><?=str_repeat('★',(int)$r['calificacion'])?></span> <strong><?=e($r['calificacion'])?>/5</strong></td>
                        <td><?=e($r['comentario']??'—')?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="card card-custom">
        <div class="text-center py-5 text-muted">No hay evaluaciones registradas.</div>
    </div>
<?php endif; ?>
<?php include __DIR__.'/../layouts/footer.php'; ?>
